<?php

declare(strict_types=1);

require_once __DIR__ . '/RecipeController.php';

$controller = new RecipeController();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'generate':
        $controller->generate();
        break;

    case 'suggest':
        $controller->suggest();
        break;

    case 'list':
        $controller->list();
        break;

    case 'show':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->show($id);
        break;

    case 'delete':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $controller->delete($id);
        break;

    default:
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            ['success' => false, 'error' => 'Invalid action'],
            JSON_UNESCAPED_UNICODE
        );
        exit;
}
