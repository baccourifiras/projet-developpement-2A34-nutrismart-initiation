<?php
// db.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'nutrismart';
$DB_USER = 'root';
$DB_PASS = '';
$DB_CHAR = 'utf8mb4';

function getDb() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = "mysql:host={$GLOBALS['DB_HOST']};dbname={$GLOBALS['DB_NAME']};charset={$GLOBALS['DB_CHAR']}";
    try {
        $pdo = new PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Connexion BDD impossible: ' . $e->getMessage()]);
        exit;
    }

    ensureTables($pdo);
    return $pdo;
}

function ensureTables(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS regime (
        id_regime INT AUTO_INCREMENT PRIMARY KEY,
        type_regime ENUM('cut','bulk','equilibre') NOT NULL,
        calories_cible INT NOT NULL,
        date_debut DATE NOT NULL,
        poids_initial FLOAT NOT NULL,
        duree INT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS suivi_regime (
        id_suivi INT AUTO_INCREMENT PRIMARY KEY,
        id_regime INT NOT NULL,
        date DATE NOT NULL,
        poids DECIMAL(6,2) NOT NULL,
        calories_consommees INT NOT NULL,
        FOREIGN KEY (id_regime) REFERENCES regime(id_regime) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS historique_recommandation (
        id_historique INT AUTO_INCREMENT PRIMARY KEY,
        id_regime INT NOT NULL,
        recommandation TEXT NOT NULL,
        date DATE NOT NULL DEFAULT CURRENT_DATE,
        FOREIGN KEY (id_regime) REFERENCES regime(id_regime) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    migrateHistoriqueRecommandationSchema($pdo);
}

function migrateHistoriqueRecommandationSchema(PDO $pdo) {
    try {
        $columns = [];
        $stmt = $pdo->query("SHOW COLUMNS FROM historique_recommandation");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $columns[$row['Field']] = $row;
        }

        // Si la colonne id_suivi existe, il faut l'enlever et ajouter id_regime
        if (isset($columns['id_suivi'])) {
            // Ajouter id_regime avant de supprimer id_suivi
            if (!isset($columns['id_regime'])) {
                $pdo->exec("ALTER TABLE historique_recommandation ADD COLUMN id_regime INT NULL AFTER id_historique");
            }
            // Supprimer les contraintes de clé étrangère
            $pdo->exec("ALTER TABLE historique_recommandation DROP FOREIGN KEY historique_recommandation_ibfk_1");
            $pdo->exec("ALTER TABLE historique_recommandation DROP COLUMN id_suivi");
        }

        // Ajouter id_regime si elle n'existe pas
        if (!isset($columns['id_regime'])) {
            $pdo->exec("ALTER TABLE historique_recommandation ADD COLUMN id_regime INT NOT NULL AFTER id_historique");
        }

        // Ajouter colonne date si elle n'existe pas
        if (!isset($columns['date'])) {
            $pdo->exec("ALTER TABLE historique_recommandation ADD COLUMN date DATE NOT NULL DEFAULT CURRENT_DATE AFTER recommandation");
        }

        // Ajouter la contrainte de clé étrangère si elle n'existe pas
        $fk = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'historique_recommandation' AND COLUMN_NAME = 'id_regime' AND REFERENCED_TABLE_NAME = 'regime'")->fetch(PDO::FETCH_ASSOC);
        if (!$fk) {
            $pdo->exec("ALTER TABLE historique_recommandation ADD CONSTRAINT fk_histo_regime FOREIGN KEY (id_regime) REFERENCES regime(id_regime) ON DELETE CASCADE");
        }
    } catch (Exception $e) {
        // Silencieusement ignorer les erreurs de migration (colonnes qui existent déjà, etc.)
        // echo "Migration warning: " . $e->getMessage();
    }
}
