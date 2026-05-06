<?php
/**
 * ============================================================
 *  NutriSmart - Controller Ingredients (Back office)
 *  /controllers/backoffice/IngredientAdminController.php
 * ============================================================
 */

class IngredientAdminController extends Controller
{
    private Ingredient $model;

    public function __construct()
    {
        $this->model = new Ingredient();
    }

    public function index(): void
    {
        $opts = [
            'q'         => trim($_GET['q']         ?? ''),
            'categorie' => $_GET['categorie']      ?? '',
            'stock_min' => $_GET['stock_min']      ?? '',
            'sort'      => $_GET['sort']           ?? 'nom',
            'dir'       => $_GET['dir']            ?? 'asc',
            'page'      => (int)($_GET['page']     ?? 1),
            'per_page'  => 12,
        ];
        $result = $this->model->paginate($opts);
        $stats  = $this->model->stats();

        $this->render('backoffice/ingredients/index', [
            'pageTitle'  => 'Ingrédients - Administration',
            'result'     => $result,
            'opts'       => $opts,
            'stats'      => $stats,
            'categories' => Ingredient::CATEGORIES,
        ], 'back');
    }

    public function show(int $id): void
    {
        $ing = $this->model->findWithRecettes($id);
        if (!$ing) {
            flash('error', 'Ingrédient introuvable.');
            redirect('ingredients.php');
        }
        $this->render('backoffice/ingredients/show', [
            'pageTitle'  => $ing['nom'],
            'ingredient' => $ing,
        ], 'back');
    }

    public function create(): void
    {
        $this->render('backoffice/ingredients/form', [
            'pageTitle'  => 'Ajouter un ingrédient',
            'ingredient' => null,
            'errors'     => errors_get(),
            'categories' => Ingredient::CATEGORIES,
            'unitesList' => Ingredient::UNITES,
        ], 'back');
        old_clear();
    }

    public function store(): void
    {
        csrf_check();
        $data = $this->collect();
        $v = $this->validate($data);

        // Unicité du nom
        if ($v->ok() && $this->model->nameExists($data['nom'])) {
            errors_set(['nom' => "Un ingrédient « {$data['nom']} » existe déjà."]);
            old_set($_POST);
            redirect('ingredient_form.php');
        }
        if (!$v->ok()) {
            errors_set($v->errors());
            old_set($_POST);
            redirect('ingredient_form.php');
        }
        $this->model->create($data);
        flash('success', "Ingrédient « {$data['nom']} » ajouté.");
        redirect('ingredients.php');
    }

    public function edit(int $id): void
    {
        $ing = $this->model->find($id);
        if (!$ing) {
            flash('error', 'Ingrédient introuvable.');
            redirect('ingredients.php');
        }
        $this->render('backoffice/ingredients/form', [
            'pageTitle'  => 'Modifier - ' . $ing['nom'],
            'ingredient' => $ing,
            'errors'     => errors_get(),
            'categories' => Ingredient::CATEGORIES,
            'unitesList' => Ingredient::UNITES,
        ], 'back');
        old_clear();
    }

    public function update(int $id): void
    {
        csrf_check();
        $existing = $this->model->find($id);
        if (!$existing) {
            flash('error', 'Ingrédient introuvable.');
            redirect('ingredients.php');
        }
        $data = $this->collect();
        $v = $this->validate($data);
        if ($v->ok() && $this->model->nameExists($data['nom'], $id)) {
            errors_set(['nom' => "Un autre ingrédient porte déjà ce nom."]);
            old_set($_POST);
            redirect('ingredient_form.php?id=' . $id);
        }
        if (!$v->ok()) {
            errors_set($v->errors());
            old_set($_POST);
            redirect('ingredient_form.php?id=' . $id);
        }
        $this->model->update($id, $data);
        flash('success', 'Ingrédient mis à jour.');
        redirect('ingredients.php');
    }

    public function destroy(int $id): void
    {
        csrf_check();
        if ($this->model->delete($id)) {
            flash('success', 'Ingrédient supprimé.');
        } else {
            flash('error', 'Suppression impossible.');
        }
        redirect('ingredients.php');
    }

    public function export(string $format): void
    {
        $opts = [
            'q'         => trim($_GET['q']    ?? ''),
            'categorie' => $_GET['categorie'] ?? '',
            'sort'      => $_GET['sort']      ?? 'nom',
            'dir'       => $_GET['dir']       ?? 'asc',
        ];
        $rows = $this->model->all($opts);

        $headers = ['ID','Nom','Catégorie','Stock','Unité','Date ajout'];
        $data = array_map(function ($r) {
            return [
                $r['id'], $r['nom'], $r['categorie'],
                $r['quantite_stock'], $r['unite'], $r['date_ajout']
            ];
        }, $rows);

        $stamp = date('Ymd_His');
        switch ($format) {
            case 'csv':   Exporter::csv("ingredients_{$stamp}.csv",   $headers, $data); break;
            case 'xls':
            case 'excel': Exporter::excel("ingredients_{$stamp}.xls", $headers, $data, 'Ingredients'); break;
            case 'pdf':   Exporter::pdf("ingredients_{$stamp}.pdf",   'Ingrédients NutriSmart', $headers, $data); break;
            default:      http_response_code(400); echo 'Format non supporté.';
        }
    }

    // -------- Helpers --------

    private function collect(): array
    {
        return [
            'nom'            => trim($_POST['nom']            ?? ''),
            'categorie'      => trim($_POST['categorie']      ?? 'Autre'),
            'quantite_stock' => $_POST['quantite_stock']      ?? 0,
            'unite'          => trim($_POST['unite']          ?? 'g'),
        ];
    }

    private function validate(array $d): Validator
    {
        $v = new Validator($d);
        $v->required('nom')->minLen('nom', 2)->max('nom', 120);
        $v->required('categorie')->in('categorie', Ingredient::CATEGORIES);
        $v->required('quantite_stock')->numeric('quantite_stock')->min('quantite_stock', 0);
        $v->required('unite')->in('unite', Ingredient::UNITES);
        return $v;
    }
}
