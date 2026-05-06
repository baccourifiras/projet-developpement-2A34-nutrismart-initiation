<?php
/** Route : Export ingrédients (csv|excel|pdf). */
require_once __DIR__ . '/../config/bootstrap.php';
(new IngredientAdminController())->export($_GET['format'] ?? 'csv');
