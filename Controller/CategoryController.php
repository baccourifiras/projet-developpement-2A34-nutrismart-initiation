<?php
require_once __DIR__ . '/../Model/Category.php';

class CategoryController
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        return $this->pdo->query(
            "SELECT id_categorie AS id, nom_categorie AS name, description
             FROM categorie
             ORDER BY id_categorie"
        )->fetchAll();
    }

    public function categoryNameById($categories, $categoryId)
    {
        foreach ($categories as $category) {
            if ((int) $category['id'] === (int) $categoryId) {
                return $category['name'];
            }
        }

        return 'Sans categorie';
    }

    public function categoryInitials($name)
    {
        $words = preg_split('/\s+/', trim((string) $name));
        $first = isset($words[0][0]) ? $words[0][0] : 'N';
        $second = isset($words[1][0]) ? $words[1][0] : ''; 
        return strtoupper($first . $second);
    }

    public function add($data)
    {
        $category = $this->prepareCategoryData($data);
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorie (nom_categorie, description) VALUES (?, ?)"
        );
        $stmt->execute(array(
            $category['name'],
            $category['description']
        ));
    }

    public function update($data)
    {
        $category = $this->prepareCategoryData($data);
        $stmt = $this->pdo->prepare(
            "UPDATE categorie SET nom_categorie = ?, description = ? WHERE id_categorie = ?"
        );
        $stmt->execute(array(
            $category['name'],
            $category['description'],
            $this->requireId($data)
        ));
    }

    public function delete($data)
    {
        $id = $this->requireId($data);
        $this->pdo->beginTransaction();

        $eventIds = $this->pdo->prepare("SELECT id_evenement FROM evenement WHERE id_categorie = ?");
        $eventIds->execute(array($id));
        $ids = $eventIds->fetchAll(PDO::FETCH_COLUMN);

        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $this->pdo->prepare("DELETE FROM participant WHERE id_evenement IN ($placeholders)")->execute($ids);
            $this->pdo->prepare("DELETE FROM evenement WHERE id_evenement IN ($placeholders)")->execute($ids);
        }

        $this->pdo->prepare("DELETE FROM categorie WHERE id_categorie = ?")->execute(array($id));
        $this->pdo->commit();
    }

    /**
     * Recherche exacte par id + tri securise via whitelist.
     *
     * @param int|null $id
     * @param string $sortField
     * @param string $sortDir
     * @return array{array<string,mixed>}
     */
    public function searchByIdAndSort(?int $id, string $sortField = 'id', string $sortDir = 'ASC'): array
    {
        $sortDir = strtoupper(trim((string) $sortDir));
        if (!in_array($sortDir, ['ASC', 'DESC'], true)) {
            $sortDir = 'ASC';
        }

        // Map UI -> colonnes DB (whitelist)
        $sortMap = [
            'id' => 'id_categorie',
            'name' => 'nom_categorie',
            'description' => 'description',
        ];
        $sortColumn = $sortMap[$sortField] ?? 'id_categorie';

        $sql = "SELECT id_categorie AS id, nom_categorie AS name, description
                FROM categorie";
        $params = [];

        if ($id !== null && $id > 0) {
            $sql .= " WHERE id_categorie = ?";
            $params[] = $id;
        }

        $sql .= " ORDER BY {$sortColumn} {$sortDir}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function showCategory($category)
    {
        echo '<table border="1">';
        echo '<tr><th>ID</th><th>Nom</th><th>Description</th></tr>';
        echo '<tr>';
        echo '<td>' . $category->getId() . '</td>';
        echo '<td>' . $category->getName() . '</td>';
        echo '<td>' . $category->getDescription() . '</td>';
        echo '</tr>';
        echo '</table>';
    }

    private function requireId($data)
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        if ($id <= 0) {
            throw new InvalidArgumentException('ID invalide.');
        }
        return $id;
    }

    private function prepareCategoryData($data)
    {
        return array(
            'name' => trim((string) ($data['name'] ?? '')),
            'description' => trim((string) ($data['description'] ?? ''))
        );
    }
}
?>
