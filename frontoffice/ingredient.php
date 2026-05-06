<?php
/**
 * Route : Détail public d'un ingrédient.
 * URL : /NutriSmart/frontoffice/ingredient.php?id=X
 */
require_once __DIR__ . '/../config/bootstrap.php';
(new IngredientController())->show((int)($_GET['id'] ?? 0));
