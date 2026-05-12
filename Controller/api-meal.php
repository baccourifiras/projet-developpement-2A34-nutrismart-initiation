<?php

declare(strict_types=1);

require_once __DIR__ . '/MealController.php';

$controller = new MealController();
$action = $_GET['action'] ?? 'generate';

switch ($action) {
    case 'generate':
        $controller->generate();
        break;

    case 'list':
        $controller->list();
        break;

    case 'show':
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $controller->show($id);
        break;

    default:
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            ['success' => false, 'error' => 'Action invalide.'],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
        exit;
}
