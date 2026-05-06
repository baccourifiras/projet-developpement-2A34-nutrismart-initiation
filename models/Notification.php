<?php
/**
 * ============================================================
 *  NutriSmart - Model Notification
 *  /models/Notification.php
 *
 *  Gère les notifications d'alerte de stock bas.
 *  Génération automatique sans doublon : pour chaque ingrédient
 *  dont le stock est <= seuil, on crée une notification SI il
 *  n'existe pas déjà une notification non-lue pour cet ingrédient.
 * ============================================================
 */

class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère le seuil critique depuis la table `parametres`.
     * Valeur par défaut : 5.
     */
    public function getSeuilCritique(): int
    {
        $stmt = $this->db->prepare("SELECT valeur FROM parametres WHERE cle = 'stock_seuil_critique'");
        $stmt->execute();
        $val = $stmt->fetchColumn();
        return $val !== false ? (int)$val : 5;
    }

    /**
     * Génère les notifications de stock bas pour les ingrédients concernés.
     * N'insère pas de doublon : si une notification non-lue existe déjà
     * pour un ingrédient donné, on ne la recrée pas.
     *
     * @return int  Nombre de nouvelles notifications créées.
     */
    public function genererAlertesStock(): int
    {
        $seuil = $this->getSeuilCritique();

        // Ingrédients en alerte ET sans notification non-lue active
        $sql = "SELECT i.id, i.nom, i.quantite_stock, i.unite
                FROM ingredients i
                WHERE i.quantite_stock <= :seuil
                  AND NOT EXISTS (
                      SELECT 1 FROM notifications n
                      WHERE n.id_ingredient = i.id AND n.est_lue = 0
                  )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':seuil' => $seuil]);
        $aAlerter = $stmt->fetchAll();

        if (empty($aAlerter)) return 0;

        $ins = $this->db->prepare(
            'INSERT INTO notifications (id_ingredient, message)
             VALUES (:id, :msg)'
        );

        $count = 0;
        foreach ($aAlerter as $ing) {
            $msg = "Stock bas : « {$ing['nom']} » ({$ing['quantite_stock']} {$ing['unite']} restant)";
            $ins->execute([':id' => (int)$ing['id'], ':msg' => $msg]);
            $count++;
        }
        return $count;
    }

    /**
     * Compteur de notifications non-lues (utilisé pour le badge).
     */
    public function countNonLues(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM notifications WHERE est_lue = 0')->fetchColumn();
    }

    /**
     * Liste des notifications, plus récentes d'abord.
     *
     * @param bool $nonLuesUniquement  Si true, ne retourne que celles non-lues.
     * @param int  $limit              Nombre max de résultats.
     */
    public function lister(bool $nonLuesUniquement = false, int $limit = 50): array
    {
        $where = $nonLuesUniquement ? 'WHERE n.est_lue = 0' : '';
        $sql = "SELECT n.*, i.nom AS ingredient_nom, i.quantite_stock, i.unite
                FROM notifications n
                LEFT JOIN ingredients i ON i.id = n.id_ingredient
                $where
                ORDER BY n.date_creation DESC, n.id DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Marque une notification comme lue.
     */
    public function marquerLue(int $id): bool
    {
        return $this->db->prepare('UPDATE notifications SET est_lue = 1 WHERE id = :id')
                        ->execute([':id' => $id]);
    }

    /**
     * Marque TOUTES les notifications comme lues.
     */
    public function marquerToutesLues(): int
    {
        $stmt = $this->db->prepare('UPDATE notifications SET est_lue = 1 WHERE est_lue = 0');
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * Supprime une notification.
     */
    public function supprimer(int $id): bool
    {
        return $this->db->prepare('DELETE FROM notifications WHERE id = :id')
                        ->execute([':id' => $id]);
    }

    /**
     * Récente = ajoutée dans les N derniers jours.
     * Sert pour le bandeau frontoffice "nouveautés".
     */
    public function recettesRecentes(int $jours = 7): array
    {
        $sql = "SELECT id, nom, image, date_creation
                FROM recettes
                WHERE date_creation >= DATE_SUB(NOW(), INTERVAL :j DAY)
                ORDER BY date_creation DESC
                LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':j', $jours, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ============================================================
    //  Notifications liées au module Planning de menus
    // ============================================================

    /**
     * Insère une notification "planning" si elle n'existe pas déjà sous
     * forme non-lue (anti-doublon par type + message).
     */
    private function insererSiNouvelle(string $type, string $message, ?string $lien = null): bool
    {
        $check = $this->db->prepare(
            'SELECT COUNT(*) FROM notifications
             WHERE type = :t AND message = :m AND est_lue = 0'
        );
        $check->execute([':t' => $type, ':m' => $message]);
        if ((int)$check->fetchColumn() > 0) return false;

        $ins = $this->db->prepare(
            'INSERT INTO notifications (type, id_ingredient, message, lien)
             VALUES (:t, NULL, :m, :l)'
        );
        $ins->execute([':t' => $type, ':m' => $message, ':l' => $lien]);
        return true;
    }

    /**
     * Génère toutes les alertes liées au planning de la semaine courante.
     * @return int Nombre de nouvelles notifications créées.
     */
    public function genererAlertesPlanning(): int
    {
        $planning = new PlanningMenu();
        $lundi    = PlanningMenu::lundiDeLaSemaine();
        $today    = date('Y-m-d');
        $count    = 0;

        // 1. Aucune recette planifiée pour aujourd'hui
        if (!$planning->jourEstPlanifie($today)) {
            if ($this->insererSiNouvelle(
                'planning_jour_vide',
                "Aucune recette planifiée pour aujourd'hui (" . date('d/m/Y') . ").",
                'planning.php'
            )) $count++;
        }

        // 2. Semaine prochaine totalement vide (anticipation)
        $lundiProchain = date('Y-m-d', strtotime($lundi . ' +7 days'));
        if ($planning->countSemaine($lundiProchain) === 0) {
            if ($this->insererSiNouvelle(
                'planning_semaine_vide',
                "Semaine prochaine non planifiée (à partir du " . date('d/m', strtotime($lundiProchain)) . ").",
                'planning.php?semaine=' . $lundiProchain
            )) $count++;
        }

        // 3. Recette répétée trop souvent dans la semaine
        foreach ($planning->recettesRepetees($lundi) as $rep) {
            if ($this->insererSiNouvelle(
                'planning_repetition',
                "Recette répétée : « {$rep['nom']} » prévue {$rep['occurrences']}× cette semaine.",
                'planning.php?semaine=' . $lundi
            )) $count++;
        }

        // 4. Ingrédients manquants pour la semaine en cours
        $manquants = $planning->ingredientsManquants($lundi);
        if (!empty($manquants)) {
            $noms = array_map(fn($m) => $m['nom'], array_slice($manquants, 0, 3));
            $extra = count($manquants) > 3 ? ' (+' . (count($manquants) - 3) . ' autres)' : '';
            if ($this->insererSiNouvelle(
                'planning_ingredients_manquants',
                "Ingrédients insuffisants pour le menu de la semaine : " . implode(', ', $noms) . $extra . '.',
                'planning.php?semaine=' . $lundi
            )) $count++;
        }

        return $count;
    }
}
