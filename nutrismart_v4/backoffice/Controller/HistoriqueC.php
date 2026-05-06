<?php
/**
 * =====================================================================
 *  NutriSmart - Controller/HistoriqueC.php
 *  CRUD historique_recommandation. INNER JOIN avec regime.
 * =====================================================================
 */

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Historique.php';

class HistoriqueC
{
    /* -----------------------------------------------------------------
     * AFFICHER toutes les recommandations avec le type de regime
     * --------------------------------------------------------------- */
    public function afficherHistoriques(): array
    {
        $sql = "SELECT h.id_historique,
                       h.id_regime,
                       h.recommandation,
                       h.date,
                       r.type_regime
                FROM   historique_recommandation h
                INNER JOIN regime r ON r.id_regime = h.id_regime
                ORDER BY h.date DESC";
        try {
            return Config::getConnexion()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            die('Erreur afficherHistoriques : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * Les N dernieres recommandations (dashboard)
     * --------------------------------------------------------------- */
    public function dernieresRecommandations(int $limit = 5): array
    {
        $sql = "SELECT h.*, r.type_regime
                FROM   historique_recommandation h
                INNER JOIN regime r ON r.id_regime = h.id_regime
                ORDER BY h.date DESC
                LIMIT :lim";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            die('Erreur dernieresRecommandations : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * AJOUTER
     * --------------------------------------------------------------- */
    public function ajouterHistorique(Historique $h): bool
    {
        $sql = "INSERT INTO historique_recommandation (id_regime, recommandation, date)
                VALUES (:id_regime, :reco, :date)";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([
                ':id_regime' => $h->getIdRegime(),
                ':reco'      => $h->getRecommandation(),
                ':date'      => $h->getDate(),
            ]);
        } catch (PDOException $e) {
            die('Erreur ajouterHistorique : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * RECUPERER par id
     * --------------------------------------------------------------- */
    public function getHistoriqueById(int $id): ?array
    {
        $sql = "SELECT * FROM historique_recommandation WHERE id_historique = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            die('Erreur getHistoriqueById : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * MODIFIER
     * --------------------------------------------------------------- */
    public function modifierHistorique(Historique $h): bool
    {
        $sql = "UPDATE historique_recommandation
                SET id_regime      = :id_regime,
                    recommandation = :reco,
                    date           = :date
                WHERE id_historique = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([
                ':id_regime' => $h->getIdRegime(),
                ':reco'      => $h->getRecommandation(),
                ':date'      => $h->getDate(),
                ':id'        => $h->getIdHistorique(),
            ]);
        } catch (PDOException $e) {
            die('Erreur modifierHistorique : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * SUPPRIMER
     * --------------------------------------------------------------- */
    public function supprimerHistorique(int $id): bool
    {
        $sql = "DELETE FROM historique_recommandation WHERE id_historique = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die('Erreur supprimerHistorique : ' . $e->getMessage());
        }
    }
}
