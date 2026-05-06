<?php
/**
 * Route : Liste publique des ingrédients.
 * URL : /NutriSmart/frontoffice/ingredients.php
 */
require_once __DIR__ . '/../config/bootstrap.php';
(new IngredientController())->index();
