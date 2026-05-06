<?php

declare(strict_types=1);

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = 'mysql:host=127.0.0.1;dbname=nutrismart;charset=utf8mb4';

        try {
            self::$connection = new PDO($dsn, 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $exception) {
            throw new RuntimeException('Connexion base impossible: ' . $exception->getMessage());
        }

        self::ensureTables(self::$connection);

        return self::$connection;
    }

    private static function ensureTables(PDO $pdo): void
    {
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS regime (
                id_regime INT AUTO_INCREMENT PRIMARY KEY,
                type_regime ENUM('cut','bulk','equilibre') NOT NULL,
                calories_cible INT NOT NULL,
                date_debut DATE NOT NULL,
                poids_initial FLOAT NOT NULL,
                duree INT NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS suivi_regime (
                id_suivi INT AUTO_INCREMENT PRIMARY KEY,
                id_regime INT NOT NULL,
                date DATE NOT NULL,
                poids DECIMAL(6,2) NOT NULL,
                calories_consommees INT NOT NULL,
                CONSTRAINT fk_suivi_regime
                    FOREIGN KEY (id_regime) REFERENCES regime(id_regime) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS historique_recommandation (
                id_historique INT AUTO_INCREMENT PRIMARY KEY,
                id_regime INT NOT NULL,
                recommandation TEXT NOT NULL,
                date DATE NOT NULL DEFAULT CURRENT_DATE,
                CONSTRAINT fk_histo_regime
                    FOREIGN KEY (id_regime) REFERENCES regime(id_regime) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        self::migrateHistoriqueTable($pdo);
    }

    private static function migrateHistoriqueTable(PDO $pdo): void
    {
        $columns = [];
        $statement = $pdo->query('SHOW COLUMNS FROM historique_recommandation');

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $columns[$row['Field']] = $row;
        }

        if (isset($columns['id_suivi'])) {
            $foreignKeys = $pdo->query(
                "SELECT CONSTRAINT_NAME
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'historique_recommandation'
                   AND COLUMN_NAME = 'id_suivi'
                   AND REFERENCED_TABLE_NAME IS NOT NULL"
            )->fetchAll(PDO::FETCH_COLUMN);

            foreach ($foreignKeys as $foreignKey) {
                $pdo->exec(sprintf('ALTER TABLE historique_recommandation DROP FOREIGN KEY `%s`', $foreignKey));
            }

            if (!isset($columns['id_regime'])) {
                $pdo->exec('ALTER TABLE historique_recommandation ADD COLUMN id_regime INT NULL AFTER id_historique');
            }

            $pdo->exec('ALTER TABLE historique_recommandation DROP COLUMN id_suivi');
        }

        if (!isset($columns['id_regime'])) {
            $pdo->exec('ALTER TABLE historique_recommandation ADD COLUMN id_regime INT NOT NULL AFTER id_historique');
        }

        if (!isset($columns['date'])) {
            $pdo->exec(
                "ALTER TABLE historique_recommandation
                 ADD COLUMN date DATE NOT NULL DEFAULT CURRENT_DATE AFTER recommandation"
            );
        }

        $foreignKey = $pdo->query(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'historique_recommandation'
               AND COLUMN_NAME = 'id_regime'
               AND REFERENCED_TABLE_NAME = 'regime'"
        )->fetch(PDO::FETCH_ASSOC);

        if (!$foreignKey) {
            $pdo->exec(
                "ALTER TABLE historique_recommandation
                 ADD CONSTRAINT fk_histo_regime
                 FOREIGN KEY (id_regime) REFERENCES regime(id_regime) ON DELETE CASCADE"
            );
        }
    }
}
