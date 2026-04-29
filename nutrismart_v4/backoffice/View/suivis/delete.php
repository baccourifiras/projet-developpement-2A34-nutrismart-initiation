<?php
/**
 * NutriSmart - View/suivis/delete.php
 */
require_once __DIR__ . '/../../Controller/SuiviC.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    (new SuiviC())->supprimerSuivi($id);
}
header('Location: list.php');
exit;
