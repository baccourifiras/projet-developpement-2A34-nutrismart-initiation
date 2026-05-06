<?php
/** Route : Formulaire ajout/édition d'une recette. */
require_once __DIR__ . '/../config/bootstrap.php';
$ctrl = new RecetteAdminController();
if (isset($_GET['id'])) {
    $ctrl->edit((int)$_GET['id']);
} else {
    $ctrl->create();
}
