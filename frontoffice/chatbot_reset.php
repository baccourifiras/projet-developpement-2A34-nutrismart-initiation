<?php
/**
 * Route AJAX : remise à zéro de la conversation.
 * URL : /NutriSmart/frontoffice/chatbot_reset.php (POST)
 */
require_once __DIR__ . '/../config/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}
(new ChatbotController())->reset();
