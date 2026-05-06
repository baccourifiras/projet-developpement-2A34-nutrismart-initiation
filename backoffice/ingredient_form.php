<?php
/** Route : Formulaire ajout/édition ingrédient. */
require_once __DIR__ . '/../config/bootstrap.php';
$ctrl = new IngredientAdminController();
if (isset($_GET['id'])) {
    $ctrl->edit((int)$_GET['id']);
} else {
    $ctrl->create();
}
