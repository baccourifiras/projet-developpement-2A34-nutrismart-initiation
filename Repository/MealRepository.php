<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Model/Meal.php';
require_once dirname(__DIR__) . '/Exception/RepositoryException.php';

class MealRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->ensureTable();
    }

    public function save(Meal $meal): Meal
    {
        try {
            $sql = 'INSERT INTO meals (type_repas, calories, type_regime, contenu_genere, date_creation)
                    VALUES (:type_repas, :calories, :type_regime, :contenu_genere, :date_creation)';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':type_repas' => $meal->getTypeRepas(),
                ':calories' => $meal->getCalories(),
                ':type_regime' => $meal->getTypeRegime(),
                ':contenu_genere' => $meal->getContenuGenere(),
                ':date_creation' => $meal->getDateCreation(),
            ]);

            $meal->setId((int) $this->pdo->lastInsertId());
            return $meal;
        } catch (PDOException $e) {
            throw new RepositoryException('Erreur de sauvegarde du repas.', 0, $e);
        }
    }

    public function findAll(): array
    {
        try {
            $stmt = $this->pdo->query('SELECT * FROM meals ORDER BY id DESC');
            $rows = $stmt->fetchAll();
            return array_map([$this, 'mapRowToEntity'], $rows);
        } catch (PDOException $e) {
            throw new RepositoryException('Erreur de lecture des repas.', 0, $e);
        }
    }

    public function findById(int $id): ?Meal
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM meals WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return is_array($row) ? $this->mapRowToEntity($row) : null;
        } catch (PDOException $e) {
            throw new RepositoryException('Erreur de lecture du repas.', 0, $e);
        }
    }

    private function ensureTable(): void
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS meals (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type_repas VARCHAR(50) NOT NULL,
                calories INT NOT NULL,
                type_regime VARCHAR(50) NOT NULL,
                contenu_genere TEXT NOT NULL,
                date_creation DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    private function mapRowToEntity(array $row): Meal
    {
        return new Meal(
            (int) $row['id'],
            (string) $row['type_repas'],
            (int) $row['calories'],
            (string) $row['type_regime'],
            (string) $row['contenu_genere'],
            (string) $row['date_creation']
        );
    }
}
