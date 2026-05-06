<?php
/**
 * Route : Tableau de bord administration.
 * URL : /NutriSmart/backoffice/index.php
 */
require_once __DIR__ . '/../config/bootstrap.php';

$recetteModel    = new Recette();
$ingredientModel = new Ingredient();

$controller = new class extends Controller {
    public function show(array $sR, array $sI): void {
        $this->render('backoffice/dashboard', [
            'pageTitle'        => 'Tableau de bord',
            'statsRecettes'    => $sR,
            'statsIngredients' => $sI,
        ], 'back');
    }
};
$controller->show($recetteModel->stats(), $ingredientModel->stats());
