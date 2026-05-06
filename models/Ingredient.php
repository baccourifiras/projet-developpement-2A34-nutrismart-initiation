<?php
/**
 * ============================================================
 *  NutriSmart - Model Ingredient
 *  /models/Ingredient.php
 *
 *  Accès et manipulations de la table `ingredients`.
 * ============================================================
 */

class Ingredient
{
    private PDO $db;

    /** Catégories suggérées (whitelist en validation). */
    public const CATEGORIES = [
        'Légume', 'Fruit', 'Viande', 'Poisson',
        'Céréale', 'Épice', 'Produit laitier', 'Autre'
    ];

    /** Unités de mesure courantes. */
    public const UNITES = ['g', 'kg', 'ml', 'L', 'unité', 'càs', 'càc', 'pincée'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // -------------------- Lectures --------------------

    /**
     * Liste paginée + filtres + recherche + tri.
     *
     * Clés dans $opts :
     *   q, categorie, stock_min, sort, dir, page, per_page
     */
    public function paginate(array $opts = []): array
    {
        $where = [];
        $params = [];

        if (!empty($opts['q'])) {
            $where[] = 'i.nom LIKE :q';
            $params[':q'] = '%' . $opts['q'] . '%';
        }
        if (!empty($opts['categorie'])) {
            $where[] = 'i.categorie = :cat';
            $params[':cat'] = $opts['categorie'];
        }
        if (isset($opts['stock_min']) && $opts['stock_min'] !== '' && is_numeric($opts['stock_min'])) {
            $where[] = 'i.quantite_stock >= :smin';
            $params[':smin'] = (float)$opts['stock_min'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sortMap = [
            'nom'            => 'i.nom',
            'categorie'      => 'i.categorie',
            'quantite_stock' => 'i.quantite_stock',
            'date_ajout'     => 'i.date_ajout',
            'id'             => 'i.id',
        ];
        $sortKey = $opts['sort'] ?? 'nom';
        $sortCol = $sortMap[$sortKey] ?? 'i.nom';
        $dir = (strtolower($opts['dir'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';

        $page    = max(1, (int)($opts['page'] ?? 1));
        $perPage = max(1, min(100, (int)($opts['per_page'] ?? 10)));
        $offset  = ($page - 1) * $perPage;

        $countSql = "SELECT COUNT(*) FROM ingredients i $whereSql";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)$stmt->fetchColumn();

        $sql = "SELECT i.* FROM ingredients i $whereSql
                ORDER BY $sortCol $dir LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
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

    public function all(array $opts = []): array
    {
        $opts['per_page'] = 100000;
        $opts['page']     = 1;
        return $this->paginate($opts)['rows'];
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM ingredients WHERE id=:id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Liste simple (id, nom, unite) pour les <select>. */
    public function options(): array
    {
        return $this->db->query('SELECT id, nom, unite FROM ingredients ORDER BY nom ASC')->fetchAll();
    }

    /** Trouve un ingrédient + les recettes qui l'utilisent. */
    public function findWithRecettes(int $id): ?array
    {
        $ing = $this->find($id);
        if (!$ing) return null;
        $ing['recettes'] = $this->recettes($id);
        return $ing;
    }

    /** Recettes utilisant cet ingrédient. */
    public function recettes(int $idIngredient): array
    {
        $sql = 'SELECT r.id, r.nom, r.duree, r.niveau, r.image,
                       ri.quantite, ri.unite
                FROM recette_ingredient ri
                INNER JOIN recettes r ON r.id = ri.id_recette
                WHERE ri.id_ingredient = :id
                ORDER BY r.nom ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $idIngredient]);
        return $stmt->fetchAll();
    }

    /** Stats pour le dashboard. */
    public function stats(): array
    {
        $stats = [];
        $stats['total']   = (int)$this->db->query('SELECT COUNT(*) FROM ingredients')->fetchColumn();
        $stats['en_rupture'] = (int)$this->db->query('SELECT COUNT(*) FROM ingredients WHERE quantite_stock <= 0')->fetchColumn();
        $cats = $this->db->query('SELECT categorie, COUNT(*) AS n FROM ingredients GROUP BY categorie ORDER BY n DESC')->fetchAll();
        $stats['par_categorie'] = $cats;
        return $stats;
    }

    // -------------------- Écritures --------------------

    public function create(array $data): int
    {
        $sql = 'INSERT INTO ingredients (nom, categorie, quantite_stock, unite, date_ajout)
                VALUES (:nom, :categorie, :stock, :unite, NOW())';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom'       => $data['nom'],
            ':categorie' => $data['categorie'],
            ':stock'     => (float)$data['quantite_stock'],
            ':unite'     => $data['unite'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE ingredients SET nom=:nom, categorie=:categorie,
                quantite_stock=:stock, unite=:unite WHERE id=:id';
        return $this->db->prepare($sql)->execute([
            ':nom'       => $data['nom'],
            ':categorie' => $data['categorie'],
            ':stock'     => (float)$data['quantite_stock'],
            ':unite'     => $data['unite'],
            ':id'        => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM ingredients WHERE id=:id')->execute([':id' => $id]);
    }

    /** Vérifie l'unicité du nom (insensible casse), excluant éventuellement un id. */
    public function nameExists(string $nom, ?int $excludeId = null): bool
    {
        if ($excludeId === null) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM ingredients WHERE LOWER(nom)=LOWER(:n)');
            $stmt->execute([':n' => $nom]);
        } else {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM ingredients WHERE LOWER(nom)=LOWER(:n) AND id<>:id');
            $stmt->execute([':n' => $nom, ':id' => $excludeId]);
        }
        return (int)$stmt->fetchColumn() > 0;
    }
}
