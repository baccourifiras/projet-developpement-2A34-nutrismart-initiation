<?php
/**
 * ============================================================
 *  NutriSmart - Controller Recettes (Front office)
 *  /controllers/frontoffice/RecetteController.php
 *
 *  Lecture seule (le grand public n'écrit rien).
 *   - index() : grille de cards + recherche + filtres + tri
 *   - show()  : page de détail
 * ============================================================
 */

class RecetteController extends Controller
{
    private Recette $recetteModel;
    private Ingredient $ingredientModel;

    public function __construct()
    {
        $this->recetteModel    = new Recette();
        $this->ingredientModel = new Ingredient();
    }

    public function index(): void
    {
        $opts = [
            'q'             => trim($_GET['q']             ?? ''),
            'niveau'        => $_GET['niveau']             ?? '',
            'duree_max'     => $_GET['duree_max']          ?? '',
            'ingredient_id' => $_GET['ingredient_id']      ?? '',
            'sort'          => $_GET['sort']               ?? 'date_creation',
            'dir'           => $_GET['dir']                ?? 'desc',
            'page'          => (int)($_GET['page']         ?? 1),
            'per_page'      => 9,
        ];
        $result = $this->recetteModel->paginate($opts);

        $this->render('frontoffice/recettes/index', [
            'pageTitle'   => 'Nos recettes',
            'result'      => $result,
            'opts'        => $opts,
            'ingredients' => $this->ingredientModel->options(),
        ], 'front');
    }

    public function show(int $id): void
    {
        $recette = $this->recetteModel->findWithIngredients($id);
        if (!$recette) {
            flash('error', 'Recette introuvable.');
            redirect('recettes.php');
        }
        $this->render('frontoffice/recettes/show', [
            'pageTitle' => $recette['nom'],
            'recette'   => $recette,
        ], 'front');
    }
}
