<?php
/**
 * ============================================================
 *  NutriSmart - Model PlanningMenu
 *  /models/PlanningMenu.php
 *
 *  Gère le planning hebdomadaire des menus.
 * ============================================================
 */

class PlanningMenu
{
    private PDO $db;

    /** Moments de la journée. */
    public const MOMENTS = ['petit_dej', 'dejeuner', 'diner', 'collation'];

    /** Libellés en français pour affichage. */
    public const MOMENT_LABELS = [
        'petit_dej' => 'Petit-déjeuner',
        'dejeuner'  => 'Déjeuner',
        'diner'     => 'Dîner',
        'collation' => 'Collation',
    ];

    /** Jours de la semaine (lundi = 1). */
    public const JOURS = [
        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi', 4 => 'Jeudi',
        5 => 'Vendredi', 6 => 'Samedi', 7 => 'Dimanche'
    ];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Calcule la date du lundi de la semaine contenant $date.
     * Si $date est null, retourne le lundi de la semaine actuelle.
     */
    public static function lundiDeLaSemaine(?string $date = null): string
    {
        $d = $date ? new DateTime($date) : new DateTime();
        $jourSemaine = (int)$d->format('N'); // 1=lundi, 7=dimanche
        if ($jourSemaine > 1) {
            $d->modify('-' . ($jourSemaine - 1) . ' days');
        }
        return $d->format('Y-m-d');
    }

    /**
     * Retourne les 7 jours d'une semaine à partir de la date du lundi.
     *
     * @return array<int, array{date:string, jour_num:int, jour_nom:string, est_aujourdhui:bool}>
     */
    public static function joursDeLaSemaine(string $lundi): array
    {
        $d = new DateTime($lundi);
        $today = (new DateTime())->format('Y-m-d');
        $out = [];
        for ($i = 1; $i <= 7; $i++) {
            $out[] = [
                'date'           => $d->format('Y-m-d'),
                'jour_num'       => $i,
                'jour_nom'       => self::JOURS[$i],
                'jour_court'     => $d->format('d/m'),
                'est_aujourdhui' => $d->format('Y-m-d') === $today,
            ];
            $d->modify('+1 day');
        }
        return $out;
    }

    /**
     * Récupère tout le planning d'une semaine (du lundi au dimanche).
     * Retourne une matrice [moment][date] => entrée de planning.
     *
     * @return array<string, array<string, array|null>>
     */
    public function getSemaine(string $lundi): array
    {
        $sql = "SELECT pm.*, r.nom AS recette_nom, r.duree, r.niveau, r.image
                FROM planning_menu pm
                INNER JOIN recettes r ON r.id = pm.id_recette
                WHERE pm.date_jour BETWEEN :debut AND DATE_ADD(:debut2, INTERVAL 6 DAY)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':debut' => $lundi, ':debut2' => $lundi]);
        $rows = $stmt->fetchAll();

        // Construction de la matrice [moment][date]
        $grid = [];
        foreach (self::MOMENTS as $m) {
            $grid[$m] = [];
            foreach (self::joursDeLaSemaine($lundi) as $j) {
                $grid[$m][$j['date']] = null;
            }
        }
        foreach ($rows as $r) {
            $grid[$r['moment']][$r['date_jour']] = $r;
        }
        return $grid;
    }

    /** Trouve un élément de planning par ID. */
    public function find(int $id): ?array
    {
        $sql = 'SELECT pm.*, r.nom AS recette_nom FROM planning_menu pm
                INNER JOIN recettes r ON r.id = pm.id_recette
                WHERE pm.id = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Récupère un élément par date+moment (la contrainte UNIQUE garantit l'unicité).
     */
    public function findByDateMoment(string $date, string $moment): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM planning_menu WHERE date_jour=:d AND moment=:m LIMIT 1');
        $stmt->execute([':d' => $date, ':m' => $moment]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Crée OU remplace une assignation (upsert via DELETE + INSERT). */
    public function assigner(string $date, string $moment, int $idRecette, int $nbPersonnes, ?string $notes): int
    {
        // On retire l'éventuelle assignation existante pour cette case
        $this->db->prepare('DELETE FROM planning_menu WHERE date_jour=:d AND moment=:m')
                 ->execute([':d' => $date, ':m' => $moment]);

        $sql = 'INSERT INTO planning_menu (date_jour, moment, id_recette, nb_personnes, notes)
                VALUES (:d, :m, :r, :n, :notes)';
        $this->db->prepare($sql)->execute([
            ':d' => $date, ':m' => $moment, ':r' => $idRecette,
            ':n' => $nbPersonnes, ':notes' => $notes
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Supprime une assignation. */
    public function supprimer(int $id): bool
    {
        return $this->db->prepare('DELETE FROM planning_menu WHERE id=:id')->execute([':id' => $id]);
    }

    /** Duplique un planning d'une semaine vers une autre. */
    public function dupliquerSemaine(string $lundiSource, string $lundiCible): int
    {
        $stmt = $this->db->prepare(
            "SELECT moment, id_recette, nb_personnes, notes,
                    DATEDIFF(date_jour, :src) AS offset_jours
             FROM planning_menu
             WHERE date_jour BETWEEN :src2 AND DATE_ADD(:src3, INTERVAL 6 DAY)"
        );
        $stmt->execute([':src' => $lundiSource, ':src2' => $lundiSource, ':src3' => $lundiSource]);
        $items = $stmt->fetchAll();

        if (empty($items)) return 0;

        // Supprimer la cible avant duplication
        $this->db->prepare(
            "DELETE FROM planning_menu
             WHERE date_jour BETWEEN :c1 AND DATE_ADD(:c2, INTERVAL 6 DAY)"
        )->execute([':c1' => $lundiCible, ':c2' => $lundiCible]);

        $ins = $this->db->prepare(
            'INSERT INTO planning_menu (date_jour, moment, id_recette, nb_personnes, notes)
             VALUES (DATE_ADD(:cible, INTERVAL :offset DAY), :m, :r, :n, :notes)'
        );
        $count = 0;
        foreach ($items as $it) {
            $ins->execute([
                ':cible'  => $lundiCible,
                ':offset' => (int)$it['offset_jours'],
                ':m'      => $it['moment'],
                ':r'      => (int)$it['id_recette'],
                ':n'      => (int)$it['nb_personnes'],
                ':notes'  => $it['notes'],
            ]);
            $count++;
        }
        return $count;
    }

    // ---------------- Logique métier pour notifications ----------------

    /** Vérifie si la journée donnée a au moins une recette planifiée. */
    public function jourEstPlanifie(string $date): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM planning_menu WHERE date_jour=:d');
        $stmt->execute([':d' => $date]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** Compte combien de recettes ont une assignation cette semaine. */
    public function countSemaine(string $lundi): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM planning_menu
             WHERE date_jour BETWEEN :d1 AND DATE_ADD(:d2, INTERVAL 6 DAY)"
        );
        $stmt->execute([':d1' => $lundi, ':d2' => $lundi]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Détecte les recettes répétées (≥ 2 fois) sur une semaine donnée.
     * @return array de [id, nom, occurrences]
     */
    public function recettesRepetees(string $lundi): array
    {
        $sql = "SELECT pm.id_recette, r.nom, COUNT(*) AS occurrences
                FROM planning_menu pm
                INNER JOIN recettes r ON r.id = pm.id_recette
                WHERE pm.date_jour BETWEEN :d1 AND DATE_ADD(:d2, INTERVAL 6 DAY)
                GROUP BY pm.id_recette, r.nom
                HAVING occurrences >= 2";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':d1' => $lundi, ':d2' => $lundi]);
        return $stmt->fetchAll();
    }

    /**
     * Calcule la liste agrégée des ingrédients nécessaires pour une semaine,
     * en multipliant par nb_personnes / 2 (les recettes sont supposées pour 2).
     * Retourne [id_ingredient => [nom, unite, quantite_necessaire, stock]]
     */
    public function besoinsIngredients(string $lundi): array
    {
        $sql = "SELECT
                    i.id, i.nom, i.unite AS unite_stock, i.quantite_stock,
                    SUM(ri.quantite * (pm.nb_personnes / 2.0)) AS quantite_necessaire,
                    ri.unite AS unite_recette
                FROM planning_menu pm
                INNER JOIN recette_ingredient ri ON ri.id_recette = pm.id_recette
                INNER JOIN ingredients i ON i.id = ri.id_ingredient
                WHERE pm.date_jour BETWEEN :d1 AND DATE_ADD(:d2, INTERVAL 6 DAY)
                GROUP BY i.id, i.nom, i.unite, i.quantite_stock, ri.unite
                ORDER BY i.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':d1' => $lundi, ':d2' => $lundi]);
        return $stmt->fetchAll();
    }

    /**
     * Liste les ingrédients dont le stock est insuffisant pour la semaine.
     * (Comparaison faite seulement quand l'unité du stock = unité dans la recette)
     */
    public function ingredientsManquants(string $lundi): array
    {
        $besoins = $this->besoinsIngredients($lundi);
        $manquants = [];
        foreach ($besoins as $b) {
            if ($b['unite_stock'] === $b['unite_recette']
                && (float)$b['quantite_necessaire'] > (float)$b['quantite_stock']) {
                $b['ecart'] = (float)$b['quantite_necessaire'] - (float)$b['quantite_stock'];
                $manquants[] = $b;
            }
        }
        return $manquants;
    }
}
