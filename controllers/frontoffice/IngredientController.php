<?php
/**
 * ============================================================
 *  NutriSmart - Controller Ingredients (Front office)
 *  /controllers/frontoffice/IngredientController.php
 * ============================================================
 */

class IngredientController extends Controller
{
    private Ingredient $model;

    public function __construct()
    {
        $this->model = new Ingredient();
    }

    public function index(): void
    {
        $opts = [
            'q'         => trim($_GET['q']    ?? ''),
            'categorie' => $_GET['categorie'] ?? '',
            'sort'      => $_GET['sort']      ?? 'nom',
            'dir'       => $_GET['dir']       ?? 'asc',
            'page'      => (int)($_GET['page']?? 1),
            'per_page'  => 12,
        ];
        $result = $this->model->paginate($opts);

        $this->render('frontoffice/ingredients/index', [
            'pageTitle'  => 'Nos ingrédients',
            'result'     => $result,
            'opts'       => $opts,
            'categories' => Ingredient::CATEGORIES,
        ], 'front');
    }

    public function show(int $id): void
    {
        $ing = $this->model->findWithRecettes($id);
        if (!$ing) {
            flash('error', 'Ingrédient introuvable.');
            redirect('ingredients.php');
        }
        $this->render('frontoffice/ingredients/show', [
            'pageTitle'  => $ing['nom'],
            'ingredient' => $ing,
        ], 'front');
    }
}
