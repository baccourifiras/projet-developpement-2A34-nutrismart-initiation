<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Stock.php';
require_once BASE_PATH . '/models/ListeCourses.php';
require_once BASE_PATH . '/controllers/AdminController.php';

try {
    $ctrl = new AdminController();
    $ctrl->dashboard();
} catch (PDOException $e) {
    echo '<div style="font-family:sans-serif;padding:40px;background:#fee2e2;color:#991b1b;border-radius:12px;margin:40px">';
    echo '<h2>⚠️ Erreur de connexion PDO</h2>';
    echo '<p>Vérifiez que MySQL est lancé et que la base <strong>nutrismart</strong> existe.</p>';
    echo '<p>Modifiez les identifiants dans <code>config/database.php</code></p>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '</div>';
}
