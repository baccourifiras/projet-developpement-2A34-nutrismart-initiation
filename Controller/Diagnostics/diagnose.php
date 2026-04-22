<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

try {
    $dbPath = dirname(__DIR__, 2) . '/Model/db.php';

    if (!file_exists($dbPath)) {
        echo json_encode(['error' => 'db.php not found at: ' . $dbPath]);
        exit(1);
    }

    require_once $dbPath;
    echo json_encode(['status' => 'db.php loaded successfully']);
} catch (Exception $exception) {
    echo json_encode(['error' => $exception->getMessage()]);
}
?>
