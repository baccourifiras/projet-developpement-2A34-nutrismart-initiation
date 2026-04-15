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

    public function add($data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categorie (nom_categorie, description) VALUES (?, ?)"
        );
        $stmt->execute(array(
            trim($data['name'] ?? ''),
            trim($data['description'] ?? '')
        ));
    }

    public function update($data)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE categorie SET nom_categorie = ?, description = ? WHERE id_categorie = ?"
        );
        $stmt->execute(array(
            trim($data['name'] ?? ''),
            trim($data['description'] ?? ''),
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
}
?>