<?php
/**
 * NutriSmart - Connexion a la base de donnees via PDO
 * Cours PHP Chapitre 4 : connexion a une BD avec PDO
 */
class Database {

    // Proprietes privees de connexion
    private static $host    = 'localhost';
    private static $dbname  = 'nutrismart';
    private static $user    = 'root';
    private static $pass    = '';
    private static $pdo     = null;

    /**
     * Constructeur prive : empeche l'instanciation directe
     * On utilise uniquement getConnection()
     */
    private function __construct() {}

    /**
     * Retourne la connexion PDO unique
     * PDO est obligatoire - cours PHP chapitre 4
     */
    public static function getConnection() {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . self::$host
                 . ';dbname=' . self::$dbname
                 . ';charset=utf8mb4';

            self::$pdo = new PDO($dsn, self::$user, self::$pass);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        return self::$pdo;
    }
}
