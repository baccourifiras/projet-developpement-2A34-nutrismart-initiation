<?php
/**
 * NutriSmart - View/historiques/delete.php
 */
require_once __DIR__ . '/../../Controller/HistoriqueC.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    (new HistoriqueC())->supprimerHistorique($id);
}
header('Location: list.php');
exit;
