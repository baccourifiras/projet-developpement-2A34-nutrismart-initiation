<?php
class config
{
    private static $pdo    = null;
    private static $schema = false; // évite de vérifier le schéma plusieurs fois

    public static function getConnexion()
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=nutrismart',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (Exception $e) {
                throw new Exception('Erreur connexion base de donnees: ' . $e->getMessage());
            }

            // Appliquer les migrations de schéma une seule fois par requête PHP
            self::applySchema(self::$pdo);
        }

        return self::$pdo;
    }

    /**
     * Vérifie et crée les colonnes manquantes dans la BDD.
     * Appelé automatiquement à la première connexion — toutes les pages en bénéficient.
     */
    private static function applySchema(PDO $pdo)
    {
        if (self::$schema) return;
        self::$schema = true;

        $migrations = [
            // table, colonne, définition SQL
            ['evenement',  'heure_evenement', "TIME NULL AFTER date_evenement"],
            ['evenement',  'places',          "INT NOT NULL DEFAULT 0 AFTER id_categorie"],
            ['evenement',  'google_maps_link',"TEXT NULL AFTER places"],
            ['evenement',  'latitude',        "DECIMAL(10,7) NULL AFTER google_maps_link"],
            ['evenement',  'longitude',       "DECIMAL(10,7) NULL AFTER latitude"],
            ['participant','date_inscription',"DATE NULL AFTER id_evenement"],
        ];

        foreach ($migrations as [$table, $col, $def]) {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME   = ?
                   AND COLUMN_NAME  = ?"
            );
            $stmt->execute([$table, $col]);
            if ((int) $stmt->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$col}` {$def}");
            }
        }

        // S'assurer que la colonne image est de type TEXT
        $stmt = $pdo->prepare(
            "SELECT DATA_TYPE FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME   = 'evenement'
               AND COLUMN_NAME  = 'image'"
        );
        $stmt->execute();
        $type = $stmt->fetchColumn();
        if ($type && strtolower($type) !== 'text') {
            $pdo->exec("ALTER TABLE evenement MODIFY image TEXT NULL");
        }
    }
}
?>
