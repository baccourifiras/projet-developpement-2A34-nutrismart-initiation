<?php
/**
 * ============================================================
 *  NutriSmart - Database (PDO singleton)
 *  /config/Database.php
 *
 *  Centralise la connexion PDO à MySQL avec les bonnes options
 *  de sécurité (prepared statements émulation OFF, exceptions ON,
 *  fetch ASSOC par défaut).
 * ============================================================
 */

class Database
{
    /** @var PDO|null Instance unique de connexion */
    private static ?PDO $instance = null;

    // ---- Paramètres de connexion (à adapter si besoin) ----
    private const DB_HOST    = '127.0.0.1';
    private const DB_PORT    = 3306;
    private const DB_NAME    = 'nutrismart';
    private const DB_USER    = 'root';
    private const DB_PASS    = '';
    private const DB_CHARSET = 'utf8mb4';

    /**
     * Empêche l'instanciation directe (singleton).
     */
    private function __construct() {}
    private function __clone() {}

    /**
     * Retourne l'instance PDO unique.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . self::DB_HOST
                 . ';port='      . self::DB_PORT
                 . ';dbname='    . self::DB_NAME
                 . ';charset='   . self::DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // vraies requêtes préparées
            ];

            try {
                self::$instance = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                // En production : logguer puis afficher un message générique
                http_response_code(500);
                die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()));
            }
        }

        return self::$instance;
    }
}
