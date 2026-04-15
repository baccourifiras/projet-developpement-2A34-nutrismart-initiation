<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Ne pas afficher les erreurs en HTML
header('Content-Type: application/json; charset=utf-8');

// Test 1: Vérifier si db.php existe
try {
    $db_path = __DIR__ . '/db.php';
    if (!file_exists($db_path)) {
        echo json_encode(['error' => 'db.php not found at: ' . $db_path]);
        exit(1);
    }
    
    require_once $db_path;
    echo json_encode(['status' => 'db.php loaded successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
