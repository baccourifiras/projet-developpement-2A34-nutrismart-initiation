<?php
require_once __DIR__ . '/../config/database.php';

class ListeCourses {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array {
        return $this->pdo->query(
            "SELECT lc.*, s.type as stock_type
             FROM liste_courses lc
             LEFT JOIN stock s ON lc.stock_id = s.id
             ORDER BY lc.id DESC"
        )->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT lc.*, s.type as stock_type
             FROM liste_courses lc
             LEFT JOIN stock s ON lc.stock_id = s.id
             WHERE lc.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO liste_courses (articles_a_acheter, budget, date_creation, stock_id)
             VALUES (:articles_a_acheter, :budget, :date_creation, :stock_id)"
        );
        return $stmt->execute([
            ':articles_a_acheter' => $data['articles_a_acheter'],
            ':budget'             => $data['budget'],
            ':date_creation'      => $data['date_creation'],
            ':stock_id'           => !empty($data['stock_id']) ? $data['stock_id'] : null,
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE liste_courses SET articles_a_acheter=:articles_a_acheter,
             budget=:budget, date_creation=:date_creation, stock_id=:stock_id WHERE id=:id"
        );
        return $stmt->execute([
            ':articles_a_acheter' => $data['articles_a_acheter'],
            ':budget'             => $data['budget'],
            ':date_creation'      => $data['date_creation'],
            ':stock_id'           => !empty($data['stock_id']) ? $data['stock_id'] : null,
            ':id'                 => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM liste_courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM liste_courses")->fetchColumn();
    }


}
