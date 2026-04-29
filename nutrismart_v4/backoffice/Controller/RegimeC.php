<?php
/**
 * =====================================================================
 *  NutriSmart - Controller/RegimeC.php
 *  Gere le CRUD de l'entite Regime (PDO, requetes preparees).
 * =====================================================================
 */

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Regime.php';

class RegimeC
{
    /* -----------------------------------------------------------------
     * AFFICHER tous les regimes (version simple)
     * --------------------------------------------------------------- */
    public function afficherRegimes(): array
    {
        $sql = "SELECT * FROM regime ORDER BY date_debut DESC";
        try {
            return Config::getConnexion()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            die('Erreur afficherRegimes : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * LISTER pour remplir un <select> (id + libelle court)
     * --------------------------------------------------------------- */
    public function listerRegimes(): array
    {
        $sql = "SELECT id_regime, type_regime, date_debut
                FROM regime
                ORDER BY date_debut DESC";
        try {
            return Config::getConnexion()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            die('Erreur listerRegimes : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * DETAILS d'un regime : les donnees + ses suivis + ses recos
     * (2 INNER JOIN dans deux requetes separees pour rester lisible).
     * --------------------------------------------------------------- */
    public function getDetailsRegime(int $id): ?array
    {
        $pdo = Config::getConnexion();

        try {
            /* Regime lui-meme */
            $stmt = $pdo->prepare("SELECT * FROM regime WHERE id_regime = :id");
            $stmt->execute([':id' => $id]);
            $regime = $stmt->fetch();
            if (!$regime) { return null; }

            /* Suivis (INNER JOIN avec regime, filtre sur l'id) */
            $sqlSuivis = "SELECT s.*, r.type_regime
                          FROM suivi_regime s
                          INNER JOIN regime r ON r.id_regime = s.id_regime
                          WHERE s.id_regime = :id
                          ORDER BY s.date DESC";
            $stmt = $pdo->prepare($sqlSuivis);
            $stmt->execute([':id' => $id]);
            $regime['suivis'] = $stmt->fetchAll();

            /* Recommandations (INNER JOIN avec regime) */
            $sqlReco = "SELECT h.*, r.type_regime
                        FROM historique_recommandation h
                        INNER JOIN regime r ON r.id_regime = h.id_regime
                        WHERE h.id_regime = :id
                        ORDER BY h.date DESC";
            $stmt = $pdo->prepare($sqlReco);
            $stmt->execute([':id' => $id]);
            $regime['recommandations'] = $stmt->fetchAll();

            return $regime;
        } catch (PDOException $e) {
            die('Erreur getDetailsRegime : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * AJOUTER un regime
     * --------------------------------------------------------------- */
    public function ajouterRegime(Regime $r): bool
    {
        $sql = "INSERT INTO regime (type_regime, calories_cible, date_debut, poids_initial, duree)
                VALUES (:type, :cal, :date, :poids, :duree)";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([
                ':type'  => $r->getTypeRegime(),
                ':cal'   => $r->getCaloriesCible(),
                ':date'  => $r->getDateDebut(),
                ':poids' => $r->getPoidsInitial(),
                ':duree' => $r->getDuree(),
            ]);
        } catch (PDOException $e) {
            die('Erreur ajouterRegime : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * RECUPERER par id
     * --------------------------------------------------------------- */
    public function getRegimeById(int $id): ?array
    {
        $sql = "SELECT * FROM regime WHERE id_regime = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        } catch (PDOException $e) {
            die('Erreur getRegimeById : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * MODIFIER un regime
     * --------------------------------------------------------------- */
    public function modifierRegime(Regime $r): bool
    {
        $sql = "UPDATE regime
                SET type_regime    = :type,
                    calories_cible = :cal,
                    date_debut     = :date,
                    poids_initial  = :poids,
                    duree          = :duree
                WHERE id_regime = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([
                ':type'  => $r->getTypeRegime(),
                ':cal'   => $r->getCaloriesCible(),
                ':date'  => $r->getDateDebut(),
                ':poids' => $r->getPoidsInitial(),
                ':duree' => $r->getDuree(),
                ':id'    => $r->getIdRegime(),
            ]);
        } catch (PDOException $e) {
            die('Erreur modifierRegime : ' . $e->getMessage());
        }
    }

    /* -----------------------------------------------------------------
     * SUPPRIMER (CASCADE supprime aussi suivis et recos)
     * --------------------------------------------------------------- */
    public function supprimerRegime(int $id): bool
    {
        $sql = "DELETE FROM regime WHERE id_regime = :id";
        try {
            $stmt = Config::getConnexion()->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die('Erreur supprimerRegime : ' . $e->getMessage());
        }
    }
}
