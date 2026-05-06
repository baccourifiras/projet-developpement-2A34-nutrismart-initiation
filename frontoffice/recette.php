<?php
/**
 * Route : Détail public d'une recette.
 * URL : /NutriSmart/frontoffice/recette.php?id=X
 */
require_once __DIR__ . '/../config/bootstrap.php';
(new RecetteController())->show((int)($_GET['id'] ?? 0));
