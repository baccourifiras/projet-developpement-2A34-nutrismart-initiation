<?php
/** Route : Détail admin d'une recette. */
require_once __DIR__ . '/../config/bootstrap.php';
(new RecetteAdminController())->show((int)($_GET['id'] ?? 0));
