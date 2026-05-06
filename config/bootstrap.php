<?php
/**
 * ============================================================
 *  NutriSmart - Bootstrap
 *  /config/bootstrap.php
 *
 *  Inclus en tête de chaque "route" (ex: backoffice/recettes.php).
 *  Démarre la session, branche un autoloader simple basé sur
 *  les conventions de nommage, et charge les helpers globaux.
 * ============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Racine absolue du projet (un cran au-dessus de /config)
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL',  '/' . basename(BASE_PATH)); // ex: /NutriSmart, sert pour les URLs absolues

// ---- Autoloader minimal : Models, Controllers, Core ----
spl_autoload_register(function (string $class): void {
    $candidates = [
        BASE_PATH . '/core/'                          . $class . '.php',
        BASE_PATH . '/models/'                        . $class . '.php',
        BASE_PATH . '/controllers/frontoffice/'       . $class . '.php',
        BASE_PATH . '/controllers/backoffice/'        . $class . '.php',
        BASE_PATH . '/config/'                        . $class . '.php',
        BASE_PATH . '/lib/'                           . $class . '.php',
    ];
    foreach ($candidates as $file) {
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

// ---- Helpers globaux ----
require_once BASE_PATH . '/core/helpers.php';
