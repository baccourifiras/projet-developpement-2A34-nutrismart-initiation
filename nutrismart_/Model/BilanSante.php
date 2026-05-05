<?php
require_once __DIR__ . '/../config.php';

class BilanSante
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = getPDO();
        $this->creerTableSiAbsente();
    }

    // Crée la table automatiquement si elle n'existe pas encore
    private function creerTableSiAbsente()
    {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS `bilan_sante` (
              `id_bilan`       INT(11)      NOT NULL AUTO_INCREMENT,
              `id_user`        INT(11)      NOT NULL,
              `date_bilan`     DATE         NOT NULL,
              `fatigue`        TINYINT(1)   DEFAULT NULL,
              `humeur`         TINYINT(1)   DEFAULT NULL,
              `hydratation`    TINYINT(1)   DEFAULT NULL,
              `appetit`        TINYINT(1)   DEFAULT NULL,
              `sommeil`        TINYINT(1)   DEFAULT NULL,
              `conseil_genere` TEXT         DEFAULT NULL,
              PRIMARY KEY (`id_bilan`),
              UNIQUE KEY `unique_user_day` (`id_user`, `date_bilan`),
              FOREIGN KEY (`id_user`) REFERENCES `utilisateur`(`id_user`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    // Vérifie si un bilan existe déjà pour aujourd'hui
    public function bilanDuJourExiste($id_user)
    {
        $stmt = $this->pdo->prepare(
            "SELECT id_bilan FROM bilan_sante WHERE id_user = ? AND date_bilan = CURDATE()"
        );
        $stmt->execute([$id_user]);
        return $stmt->fetch() !== false;
    }

    // Enregistre ou met à jour le bilan du jour
    public function sauvegarder($id_user, $data)
    {
        $conseil = $this->genererConseil($data);

        $stmt = $this->pdo->prepare("
            INSERT INTO bilan_sante (id_user, date_bilan, fatigue, humeur, hydratation, appetit, sommeil, conseil_genere)
            VALUES (?, CURDATE(), ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              fatigue        = VALUES(fatigue),
              humeur         = VALUES(humeur),
              hydratation    = VALUES(hydratation),
              appetit        = VALUES(appetit),
              sommeil        = VALUES(sommeil),
              conseil_genere = VALUES(conseil_genere)
        ");
        $stmt->execute([
            $id_user,
            $data['fatigue']     ?? null,
            $data['humeur']      ?? null,
            $data['hydratation'] ?? null,
            $data['appetit']     ?? null,
            $data['sommeil']     ?? null,
            $conseil
        ]);

        return $conseil;
    }

    // Génère un conseil personnalisé basé sur les réponses
    public function genererConseil($data)
    {
        $conseils = [];

        $fatigue     = isset($data['fatigue'])     ? (int)$data['fatigue']     : 3;
        $humeur      = isset($data['humeur'])       ? (int)$data['humeur']      : 3;
        $hydratation = isset($data['hydratation'])  ? (int)$data['hydratation'] : 3;
        $appetit     = isset($data['appetit'])      ? (int)$data['appetit']     : 3;
        $sommeil     = isset($data['sommeil'])       ? (int)$data['sommeil']     : 3;

        // Fatigue
        if ($fatigue <= 2) {
            $conseils[] = "💤 Tu sembles fatigué(e) — essaie de te coucher 30 min plus tôt ce soir et évite les écrans avant de dormir.";
        } elseif ($fatigue == 3) {
            $conseils[] = "☕ Énergie normale. Une courte pause de 10 min en milieu de journée peut booster ta productivité.";
        } else {
            $conseils[] = "⚡ Super énergie aujourd'hui ! C'est le bon moment pour une activité physique douce.";
        }

        // Humeur
        if ($humeur <= 2) {
            $conseils[] = "🍫 Pour améliorer ton humeur, essaie de manger quelques noix ou un carré de chocolat noir — riches en magnésium, excellents contre le stress.";
        } elseif ($humeur >= 4) {
            $conseils[] = "😊 Bonne humeur = bonne journée ! Profites-en pour préparer un repas sain et équilibré.";
        }

        // Hydratation
        if ($hydratation <= 2) {
            $conseils[] = "💧 Attention à l'hydratation ! Bois au moins 1,5L d'eau aujourd'hui. Pose une bouteille sur ton bureau pour t'en rappeler.";
        } elseif ($hydratation <= 3) {
            $conseils[] = "🥤 Hydratation correcte. Pense à boire régulièrement tout au long de la journée, pas seulement quand tu as soif.";
        } else {
            $conseils[] = "✅ Excellente hydratation ! Continue ainsi, ton corps te remerciera.";
        }

        // Appétit
        if ($appetit <= 2) {
            $conseils[] = "🥗 Peu d'appétit ? Essaie des petits repas fractionnés toutes les 3h plutôt qu'un grand repas.";
        } elseif ($appetit >= 4) {
            $conseils[] = "🍽️ Bon appétit ! Pense à privilégier les protéines et les fibres pour te sentir rassasié(e) plus longtemps.";
        }

        // Sommeil
        if ($sommeil <= 2) {
            $conseils[] = "🌙 Mauvais sommeil détecté. Évite la caféine après 14h et les repas lourds le soir. Une tisane à la camomille peut aider.";
        } elseif ($sommeil >= 4) {
            $conseils[] = "🌟 Excellent sommeil ! Un bon repos aide ton corps à mieux assimiler les nutriments.";
        }

        // Score global
        $score = ($fatigue + $humeur + $hydratation + $appetit + $sommeil) / 5;
        if ($score >= 4) {
            $conseils[] = "🏆 Ton bilan du jour est excellent ! Continue sur cette lancée.";
        } elseif ($score <= 2) {
            $conseils[] = "🤗 Ne t'inquiète pas — chaque jour est une nouvelle chance. Ton nutritionniste est là pour t'accompagner.";
        }

        return implode("\n", $conseils);
    }

    // Récupère les derniers bilans d'un utilisateur
    public function getHistorique($id_user, $limite = 7)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM bilan_sante
            WHERE id_user = ?
            ORDER BY date_bilan DESC
            LIMIT ?
        ");
        $stmt->execute([$id_user, $limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
