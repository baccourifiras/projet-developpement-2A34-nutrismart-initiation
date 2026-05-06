<?php
/**
 * ============================================================
 *  NutriSmart - Controller Recettes (Back office)
 *  /controllers/backoffice/RecetteAdminController.php
 *
 *  Toutes les actions admin pour les recettes :
 *   - index()  : liste paginée + filtres + recherche + tri
 *   - show()   : détail (avec ingrédients liés)
 *   - create() : formulaire d'ajout
 *   - store()  : POST -> insertion + sync ingrédients
 *   - edit()   : formulaire édition
 *   - update() : POST -> mise à jour
 *   - destroy(): POST -> suppression
 *   - export() : CSV / Excel / PDF
 * ============================================================
 */

class RecetteAdminController extends Controller
{
    private Recette $recetteModel;
    private Ingredient $ingredientModel;

    public function __construct()
    {
        $this->recetteModel    = new Recette();
        $this->ingredientModel = new Ingredient();
    }

    /** Liste paginée + recherche + filtres + tri. */
    public function index(): void
    {
        $opts = [
            'q'         => trim($_GET['q']         ?? ''),
            'niveau'    => $_GET['niveau']         ?? '',
            'duree_min' => $_GET['duree_min']      ?? '',
            'duree_max' => $_GET['duree_max']      ?? '',
            'sort'      => $_GET['sort']           ?? 'date_creation',
            'dir'       => $_GET['dir']            ?? 'desc',
            'page'      => (int)($_GET['page']     ?? 1),
            'per_page'  => 10,
        ];
        $result = $this->recetteModel->paginate($opts);
        $stats  = $this->recetteModel->stats();

        $this->render('backoffice/recettes/index', [
            'pageTitle' => 'Recettes - Administration',
            'result'    => $result,
            'opts'      => $opts,
            'stats'     => $stats,
        ], 'back');
    }

    /** Détail d'une recette + ingrédients liés. */
    public function show(int $id): void
    {
        $recette = $this->recetteModel->findWithIngredients($id);
        if (!$recette) {
            flash('error', "Recette introuvable.");
            redirect('recettes.php');
        }
        $this->render('backoffice/recettes/show', [
            'pageTitle' => 'Recette - ' . $recette['nom'],
            'recette'   => $recette,
        ], 'back');
    }

    /** Affiche le formulaire d'ajout. */
    public function create(): void
    {
        $this->render('backoffice/recettes/form', [
            'pageTitle'   => 'Ajouter une recette',
            'recette'     => null,
            'ingredients' => $this->ingredientModel->options(),
            'selected'    => [], // pas d'ingrédient pré-sélectionné
            'errors'      => errors_get(),
            'unitesList'  => Ingredient::UNITES,
        ], 'back');
        old_clear();
    }

    /** Traitement POST de la création. */
    public function store(): void
    {
        csrf_check();

        $data = $this->collect();

        $v = $this->validate($data);
        if (!$v->ok()) {
            errors_set($v->errors());
            old_set($_POST);
            redirect('recette_form.php');
        }

        // Upload image (optionnel)
        $imgPath = $this->handleImageUpload();

        $id = $this->recetteModel->create([
            'nom'         => $data['nom'],
            'description' => $data['description'],
            'duree'       => $data['duree'],
            'niveau'      => $data['niveau'],
            'image'       => $imgPath,
        ]);

        // Synchroniser les ingrédients liés
        $this->recetteModel->syncIngredients($id, $this->collectIngredientLinks());

        flash('success', "Recette « {$data['nom']} » ajoutée avec succès.");
        redirect('recettes.php');
    }

    /** Affiche le formulaire d'édition. */
    public function edit(int $id): void
    {
        $recette = $this->recetteModel->findWithIngredients($id);
        if (!$recette) {
            flash('error', 'Recette introuvable.');
            redirect('recettes.php');
        }
        // Préparer les ingrédients sélectionnés sous forme normalisée
        $selected = array_map(function ($i) {
            return [
                'id_ingredient' => (int)$i['id'],
                'nom'           => $i['nom'],
                'quantite'      => $i['quantite'],
                'unite'         => $i['unite'],
            ];
        }, $recette['ingredients']);

        $this->render('backoffice/recettes/form', [
            'pageTitle'   => 'Modifier - ' . $recette['nom'],
            'recette'     => $recette,
            'ingredients' => $this->ingredientModel->options(),
            'selected'    => $selected,
            'errors'      => errors_get(),
            'unitesList'  => Ingredient::UNITES,
        ], 'back');
        old_clear();
    }

    /** Traitement POST de l'édition. */
    public function update(int $id): void
    {
        csrf_check();
        $existing = $this->recetteModel->find($id);
        if (!$existing) {
            flash('error', 'Recette introuvable.');
            redirect('recettes.php');
        }

        $data = $this->collect();
        $v = $this->validate($data);
        if (!$v->ok()) {
            errors_set($v->errors());
            old_set($_POST);
            redirect('recette_form.php?id=' . $id);
        }

        $imgPath = $this->handleImageUpload();
        $payload = [
            'nom'         => $data['nom'],
            'description' => $data['description'],
            'duree'       => $data['duree'],
            'niveau'      => $data['niveau'],
        ];
        if ($imgPath !== null) $payload['image'] = $imgPath;

        $this->recetteModel->update($id, $payload);
        $this->recetteModel->syncIngredients($id, $this->collectIngredientLinks());

        flash('success', "Recette mise à jour.");
        redirect('recettes.php');
    }

    /** Suppression. */
    public function destroy(int $id): void
    {
        csrf_check();
        if ($this->recetteModel->delete($id)) {
            flash('success', "Recette supprimée.");
        } else {
            flash('error', "Suppression impossible.");
        }
        redirect('recettes.php');
    }

    /** Export CSV / Excel / PDF (selon ?format=). */
    public function export(string $format): void
    {
        $opts = [
            'q'         => trim($_GET['q']    ?? ''),
            'niveau'    => $_GET['niveau']    ?? '',
            'duree_min' => $_GET['duree_min'] ?? '',
            'duree_max' => $_GET['duree_max'] ?? '',
            'sort'      => $_GET['sort']      ?? 'nom',
            'dir'       => $_GET['dir']       ?? 'asc',
        ];
        $rows = $this->recetteModel->all($opts);

        $headers = ['ID', 'Nom', 'Description', 'Durée (min)', 'Niveau', 'Date création'];
        $data = array_map(function ($r) {
            return [
                $r['id'],
                $r['nom'],
                $r['description'],
                $r['duree'],
                $r['niveau'],
                $r['date_creation'],
            ];
        }, $rows);

        $stamp = date('Ymd_His');
        switch ($format) {
            case 'csv':
                Exporter::csv("recettes_{$stamp}.csv", $headers, $data);
                break;
            case 'xls':
            case 'excel':
                Exporter::excel("recettes_{$stamp}.xls", $headers, $data, 'Recettes');
                break;
            case 'pdf':
                Exporter::pdf("recettes_{$stamp}.pdf", 'Recettes NutriSmart', $headers, $data);
                break;
            default:
                http_response_code(400);
                echo 'Format non supporté.';
        }
    }

    // -------------------- Helpers privés --------------------

    private function collect(): array
    {
        return [
            'nom'         => trim($_POST['nom']         ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'duree'       => $_POST['duree']            ?? '',
            'niveau'      => $_POST['niveau']           ?? '',
        ];
    }

    private function validate(array $d): Validator
    {
        $v = new Validator($d);
        $v->required('nom')->minLen('nom', 2)->max('nom', 150);
        $v->required('description')->minLen('description', 10);
        $v->required('duree')->numeric('duree')->min('duree', 1);
        $v->required('niveau')->in('niveau', Recette::NIVEAUX);
        $v->image('image', 4096); // 4 Mo
        return $v;
    }

    /** Gère l'upload d'image et renvoie le chemin relatif (ou null). */
    private function handleImageUpload(): ?string
    {
        $f = $_FILES['image'] ?? null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE || $f['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($ext, $allowed, true)) return null;

        $name = 'rec_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = BASE_PATH . '/public/uploads/recettes/' . $name;

        if (!move_uploaded_file($f['tmp_name'], $dest)) return null;
        // Chemin web (sera utilisé tel quel dans <img src="">)
        return BASE_URL . '/public/uploads/recettes/' . $name;
    }

    /** Reconstruit les liens recette<->ingrédient depuis $_POST. */
    private function collectIngredientLinks(): array
    {
        $ids   = $_POST['ing_id']  ?? [];
        $qts   = $_POST['ing_qty'] ?? [];
        $units = $_POST['ing_uni'] ?? [];
        $out = [];
        foreach ($ids as $i => $id) {
            $id = (int)$id;
            if ($id <= 0) continue;
            $out[] = [
                'id_ingredient' => $id,
                'quantite'      => is_numeric($qts[$i] ?? null) ? (float)$qts[$i] : 0,
                'unite'         => $units[$i] ?? 'g',
            ];
        }
        return $out;
    }
}
