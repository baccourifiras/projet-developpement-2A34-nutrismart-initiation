<?php
/**
 * =====================================================================
 *  NutriSmart - Controller/SuiviC.php
 *  CRUD suivi_regime. L'affichage utilise INNER JOIN avec la table regime
 *  pour afficher le type de regime associe.
 * =====================================================================
 */

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Suivi.php';

class SuiviC
{
    /* -----------------------------------------------------------------
     * AFFICHER tous les suivis avec le type de regime (INNER JOIN)
     * --------------------------------------------------------------- */
    public function afficherSuivis(): array
    {
        $sql = "SELECT s.id_suivi,
                       s.id_regime,
                       s.date,
                       s.poids,
                       s.calories_consommees,
                       r.type_regime,
                       r.calories_cible
                FROM   suivi_regime s
                INNER JOIN regime r ON r.id_regime = s.id_regime
                ORDER BY s.date DESC";
        try {
            return Config::getConnexion()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            die('Erreur afficherSuivis : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * Les N derniers suivis (pour le dashboard)
     * --------------------------------------------------------------- */
    public function dernieresEntrees(int $limit = 5): array
    {
        $sql = "SELECT s.*, r.type_regime
                FROM   suivi_regime s
                INNER JOIN regime r ON r.id_regime = s.id_regime
                ORDER BY s.date DESC
                LIMIT :lim";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die('Erreur dernieresEntrees : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * AJOUTER
     * --------------------------------------------------------------- */
    public function ajouterSuivi(Suivi $s): bool
    {
        $sql = "INSERT INTO suivi_regime (id_regime, date, poids, calories_consommees)
                VALUES (:id_regime, :date, :poids, :cal)";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([
                ':id_regime' => $s->getIdRegime(),
                ':date'      => $s->getDate(),
                ':poids'     => $s->getPoids(),
                ':cal'       => $s->getCaloriesConsommees(),
            ]);
        } catch (PDOException $e) {
            die('Erreur ajouterSuivi : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * RECUPERER par id
     * --------------------------------------------------------------- */
    public function getSuiviById(int $id): ?array
    {
        $sql = "SELECT * FROM suivi_regime WHERE id_suivi = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            die('Erreur getSuiviById : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * MODIFIER
     * --------------------------------------------------------------- */
    public function modifierSuivi(Suivi $s): bool
    {
        $sql = "UPDATE suivi_regime
                SET id_regime           = :id_regime,
                    date                = :date,
                    poids               = :poids,
                    calories_consommees = :cal
                WHERE id_suivi = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([
                ':id_regime' => $s->getIdRegime(),
                ':date'      => $s->getDate(),
                ':poids'     => $s->getPoids(),
                ':cal'       => $s->getCaloriesConsommees(),
                ':id'        => $s->getIdSuivi(),
            ]);
        } catch (PDOException $e) {
            die('Erreur modifierSuivi : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * SUPPRIMER
     * --------------------------------------------------------------- */
    public function supprimerSuivi(int $id): bool
    {
        $sql = "DELETE FROM suivi_regime WHERE id_suivi = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die('Erreur supprimerSuivi : ' . $e->getMessage());
        }
    }
}
