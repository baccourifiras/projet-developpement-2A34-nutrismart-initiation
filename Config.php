<?php
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=nutrismart',
                    'root',
                    '',
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    )
                );
            } catch (Exception $e) {
                throw new Exception('Erreur connexion base de donnees: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
