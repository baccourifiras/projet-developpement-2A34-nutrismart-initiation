<?php
require_once __DIR__ . '/../config/database.php';

class Stock {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array {
        return $this->pdo->query("SELECT * FROM stock ORDER BY id DESC")->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM stock WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO stock (type, produits, date_expiration, seuil_minimum)
             VALUES (:type, :produits, :date_expiration, :seuil_minimum)"
        );
        return $stmt->execute([
            ':type'            => $data['type'],
            ':produits'        => $data['produits'],
            ':date_expiration' => $data['date_expiration'],
            ':seuil_minimum'   => $data['seuil_minimum'],
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE stock SET type=:type, produits=:produits,
             date_expiration=:date_expiration, seuil_minimum=:seuil_minimum
             WHERE id=:id"
        );
        return $stmt->execute([
            ':type'            => $data['type'],
            ':produits'        => $data['produits'],
            ':date_expiration' => $data['date_expiration'],
            ':seuil_minimum'   => $data['seuil_minimum'],
            ':id'              => $id,
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM stock WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM stock")->fetchColumn();
    }

    public function getExpiresSoon(int $jours = 7): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM stock
             WHERE date_expiration BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :j DAY)
             ORDER BY date_expiration ASC"
        );
        $stmt->execute([':j' => $jours]);
        return $stmt->fetchAll();
    }

    // Taux de gaspillage = nb produits expirés / nb total produits × 100
    public function getTauxGaspillage(): float {
        $row = $this->pdo->query(
            "SELECT COUNT(*) as total,
                    SUM(CASE WHEN date_expiration < CURDATE() THEN 1 ELSE 0 END) as expire
             FROM stock"
        )->fetch();
        if (!$row || !$row['total']) return 0.0;
        return round(($row['expire'] / $row['total']) * 100, 2);
    }
}
