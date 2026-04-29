<?php
/**
 * =====================================================================
 *  NutriSmart - Config/config.php
 *  Connexion PDO avec le design pattern Singleton.
 *
 *  Utilisation :
 *      $pdo = Config::getConnexion();
 * =====================================================================
 */

class Config
{
    /** Unique instance PDO partagee par toute l'application */
    private static ?PDO $pdo = null;

    /* -----------------------------------------------------------------
     * Parametres de connexion (a adapter a ton environnement WAMP/XAMPP)
     * --------------------------------------------------------------- */
    private const DB_HOST    = 'localhost';
    private const DB_NAME    = 'nutrismart';
    private const DB_USER    = 'root';
    private const DB_PASS    = '';
    private const DB_CHARSET = 'utf8mb4';

    /**
     * Le constructeur est prive : on ne peut pas instancier Config
     * directement, ce qui garantit qu'il n'y ait qu'une seule connexion.
     */
    private function __construct() {}

    /** On empeche aussi le clonage de l'instance. */
    private function __clone() {}

    /**
     * Retourne la connexion PDO (la cree si elle n'existe pas encore).
     */
    public static function getConnexion(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . self::DB_HOST
                 . ';dbname='    . self::DB_NAME
                 . ';charset='   . self::DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, self::DB_USER, self::DB_PASS, $options);
            } catch (PDOException $e) {
                // En prod on logguerait ; en TP on affiche l'erreur.
                die('Erreur de connexion a la base : ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
