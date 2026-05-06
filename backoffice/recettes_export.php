<?php
/** Route : Export recettes (csv|excel|pdf). */
require_once __DIR__ . '/../config/bootstrap.php';
(new RecetteAdminController())->export($_GET['format'] ?? 'csv');
