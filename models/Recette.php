<?php
/**
 * ============================================================
 *  NutriSmart - Model Recette
 *  /models/Recette.php
 *
 *  Accès et manipulations de la table `recettes`.
 *  Toutes les requêtes utilisent PDO::prepare() (anti SQL injection).
 * ============================================================
 */

class Recette
{
    /** @var PDO */
    private PDO $db;

    /** Niveaux autorisés (utilisé en validation et dans les vues). */
    public const NIVEAUX = ['facile', 'moyen', 'difficile'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // -------------------- Lectures --------------------

    /**
     * Liste paginée + filtres + tri + recherche.
     *
     * @param array $opts Clés acceptées :
     *   - q        (string) recherche dans nom/description
     *   - niveau   (string) filtre exact ('facile'|'moyen'|'difficile')
     *   - duree_min, duree_max (int)
     *   - ingredient_id (int) filtre par ingrédient utilisé
     *   - sort     (string) 'nom' | 'duree' | 'niveau' | 'date_creation'
     *   - dir      ('asc'|'desc')
     *   - page     (int, defaut 1)
     *   - per_page (int, defaut 10)
     *
     * @return array{rows:array,total:int,page:int,per_page:int,pages:int}
     */
    public function paginate(array $opts = []): array
    {
        $where = [];
        $params = [];

        // Recherche
        if (!empty($opts['q'])) {
            $where[] = '(r.nom LIKE :q OR r.description LIKE :q)';
            $params[':q'] = '%' . $opts['q'] . '%';
        }

        // Filtre niveau
        if (!empty($opts['niveau']) && in_array($opts['niveau'], self::NIVEAUX, true)) {
            $where[] = 'r.niveau = :niveau';
            $params[':niveau'] = $opts['niveau'];
        }

        // Filtres durée
        if (isset($opts['duree_min']) && $opts['duree_min'] !== '' && is_numeric($opts['duree_min'])) {
            $where[] = 'r.duree >= :dmin';
            $params[':dmin'] = (int)$opts['duree_min'];
        }
        if (isset($opts['duree_max']) && $opts['duree_max'] !== '' && is_numeric($opts['duree_max'])) {
            $where[] = 'r.duree <= :dmax';
            $params[':dmax'] = (int)$opts['duree_max'];
        }

        // Filtre par ingrédient
        $join = '';
        if (!empty($opts['ingredient_id']) && is_numeric($opts['ingredient_id'])) {
            $join = ' INNER JOIN recette_ingredient ri ON ri.id_recette = r.id ';
            $where[] = 'ri.id_ingredient = :iid';
            $params[':iid'] = (int)$opts['ingredient_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Tri sécurisé (whitelist)
        $sortMap = [
            'nom'           => 'r.nom',
            'duree'         => 'r.duree',
            'niveau'        => 'r.niveau',
            'date_creation' => 'r.date_creation',
            'id'            => 'r.id',
        ];
        $sortKey = $opts['sort'] ?? 'date_creation';
        $sortCol = $sortMap[$sortKey] ?? 'r.date_creation';
        $dir     = (strtolower($opts['dir'] ?? 'desc') === 'asc') ? 'ASC' : 'DESC';

        // Pagination
        $page    = max(1, (int)($opts['page'] ?? 1));
        $perPage = max(1, min(100, (int)($opts['per_page'] ?? 10)));
        $offset  = ($page - 1) * $perPage;

        // Total
        $countSql = "SELECT COUNT(DISTINCT r.id) FROM recettes r $join $whereSql";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        // Données
        $sql = "SELECT DISTINCT r.* FROM recettes r $join $whereSql
                ORDER BY $sortCol $dir LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return [
            'rows'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int)ceil($total / $perPage),
        ];
    }

    /** Toutes les recettes (sans pagination) - utilisé pour les exports. */
    public function all(array $opts = []): array
    {
        $opts['per_page'] = 100000;
        $opts['page']     = 1;
        return $this->paginate($opts)['rows'];
    }

    /** Trouve une recette par ID, ou null. */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM recettes WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Récupère une recette + ses ingrédients liés (avec quantité/unité). */
    public function findWithIngredients(int $id): ?array
    {
        $recette = $this->find($id);
        if (!$recette) return null;
        $recette['ingredients'] = $this->ingredients($id);
        return $recette;
    }

    /** Liste des ingrédients liés à une recette, avec leurs détails. */
    public function ingredients(int $idRecette): array
    {
        $sql = 'SELECT i.id, i.nom, i.categorie, i.unite AS unite_stock,
                       ri.quantite, ri.unite
                FROM recette_ingredient ri
                INNER JOIN ingredients i ON i.id = ri.id_ingredient
                WHERE ri.id_recette = :id
                ORDER BY i.nom ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idRecette]);
        return $stmt->fetchAll();
    }

    /** Statistiques rapides pour le dashboard. */
    public function stats(): array
    {
        $stats = [];
        $stats['total']      = (int)$this->db->query('SELECT COUNT(*) FROM recettes')->fetchColumn();
        $stats['facile']     = (int)$this->db->query("SELECT COUNT(*) FROM recettes WHERE niveau='facile'")->fetchColumn();
        $stats['moyen']      = (int)$this->db->query("SELECT COUNT(*) FROM recettes WHERE niveau='moyen'")->fetchColumn();
        $stats['difficile']  = (int)$this->db->query("SELECT COUNT(*) FROM recettes WHERE niveau='difficile'")->fetchColumn();
        $stats['duree_moy']  = (int)$this->db->query('SELECT COALESCE(AVG(duree),0) FROM recettes')->fetchColumn();
        return $stats;
    }

    // -------------------- Écritures --------------------

    /**
     * Crée une recette et retourne son ID.
     */
    public function create(array $data): int
    {
        $sql = 'INSERT INTO recettes (nom, description, duree, niveau, image, date_creation)
                VALUES (:nom, :description, :duree, :niveau, :image, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom'         => $data['nom'],
            ':description' => $data['description'],
            ':duree'       => (int)$data['duree'],
            ':niveau'      => $data['niveau'],
            ':image'       => $data['image'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /** Met à jour une recette existante. */
    public function update(int $id, array $data): bool
    {
        // Ne mettre à jour l'image que si une nouvelle est fournie
        if (array_key_exists('image', $data) && $data['image'] !== null) {
            $sql = 'UPDATE recettes SET nom=:nom, description=:description, duree=:duree,
                    niveau=:niveau, image=:image WHERE id=:id';
            $params = [
                ':nom'         => $data['nom'],
                ':description' => $data['description'],
                ':duree'       => (int)$data['duree'],
                ':niveau'      => $data['niveau'],
                ':image'       => $data['image'],
                ':id'          => $id,
            ];
        } else {
            $sql = 'UPDATE recettes SET nom=:nom, description=:description, duree=:duree,
                    niveau=:niveau WHERE id=:id';
            $params = [
                ':nom'         => $data['nom'],
                ':description' => $data['description'],
                ':duree'       => (int)$data['duree'],
                ':niveau'      => $data['niveau'],
                ':id'          => $id,
            ];
        }
        return $this->db->prepare($sql)->execute($params);
    }

    /** Supprime une recette (cascade sur le pivot via FK). */
    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM recettes WHERE id=:id')->execute([':id' => $id]);
    }

    /**
     * Synchronise les ingrédients liés à une recette.
     * Remplace toutes les associations existantes par celles fournies.
     *
     * @param int   $idRecette
     * @param array $items     Liste de ['id_ingredient'=>int, 'quantite'=>float, 'unite'=>string]
     */
    public function syncIngredients(int $idRecette, array $items): void
    {
        $this->db->beginTransaction();
        try {
            $this->db->prepare('DELETE FROM recette_ingredient WHERE id_recette=:id')
                     ->execute([':id' => $idRecette]);

            $ins = $this->db->prepare(
                'INSERT INTO recette_ingredient (id_recette, id_ingredient, quantite, unite)
                 VALUES (:rid, :iid, :q, :u)'
            );
            foreach ($items as $it) {
                if (empty($it['id_ingredient'])) continue;
                $ins->execute([
                    ':rid' => $idRecette,
                    ':iid' => (int)$it['id_ingredient'],
                    ':q'   => (float)($it['quantite'] ?? 0),
                    ':u'   => $it['unite'] ?? 'g',
                ]);
            }
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
