<?php
class Database {
    private static ?PDO $instance = null;
    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn     = "mysql:host=localhost;dbname=nutrismart;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$instance = new PDO($dsn, 'root', '', $options);
        }
        return self::$instance;
    }
}
