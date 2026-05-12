<?php

declare(strict_types=1);

require_once __DIR__ . '/FoodAnalysisController.php';

$controller = new FoodAnalysisController();
$action = $_GET['action'] ?? 'analyze';

switch ($action) {
    case 'analyze':
        $controller->analyzeImage();
        break;

    case 'analyze-text':
        $controller->analyzeFromText();
        break;

    case 'barcode':
        $controller->scanBarcode();
        break;

    case 'portion':
        $controller->estimatePortion();
        break;

    case 'history':
        $controller->getHistory();
        break;

    case 'daily':
        $controller->getDailySummary();
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
