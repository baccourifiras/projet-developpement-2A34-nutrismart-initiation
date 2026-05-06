<?php
/**
 * Route : Liste publique des recettes.
 * URL : /NutriSmart/frontoffice/recettes.php
 */
require_once __DIR__ . '/../config/bootstrap.php';
(new RecetteController())->index();
