<?php
/**
 * =====================================================================
 *  NutriSmart - View/regimes/delete.php
 *  CASCADE supprime aussi les suivis et les recommandations.
 * =====================================================================
 */
require_once __DIR__ . '/../../Controller/RegimeC.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    (new RegimeC())->supprimerRegime($id);
}
header('Location: list.php');
exit;
