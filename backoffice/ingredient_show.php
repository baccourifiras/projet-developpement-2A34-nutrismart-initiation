<?php
/** Route : Détail admin ingrédient. */
require_once __DIR__ . '/../config/bootstrap.php';
(new IngredientAdminController())->show((int)($_GET['id'] ?? 0));
