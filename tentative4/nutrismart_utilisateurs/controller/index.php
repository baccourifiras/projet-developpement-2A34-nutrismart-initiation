<?php
require_once __DIR__ . '/PageController.php';

$controller = new PageController();
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

if ($page === 'inscription') {
    $controller->inscription();
} elseif ($page === 'dashboard') {
    $controller->dashboard();
} else {
    $controller->accueil();
}
