<?php

declare(strict_types=1);

require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Recipe.php';

/**
 * Recipe Repository
 * Handles database operations for recipes
 */
class RecipeRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->ensureRecipeTable();
    }

    private function ensureRecipeTable(): void
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS recipe (
                id_recipe INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                ingredients JSON NOT NULL,
                steps JSON NOT NULL,
                calories INT NOT NULL,
                macros JSON NOT NULL,
                difficulty ENUM('easy','medium','hard') DEFAULT 'medium',
                prep_time INT NOT NULL,
                cook_time INT NOT NULL,
                dietary_restrictions JSON,
                image_url VARCHAR(500),
                video_url VARCHAR(500),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_difficulty (difficulty),
                INDEX idx_calories (calories),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public function save(Recipe $recipe): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO recipe (
                title, description, ingredients, steps, calories, macros,
                difficulty, prep_time, cook_time, dietary_restrictions, image_url, video_url
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $recipe->getTitle(),
            $recipe->getDescription(),
            json_encode($recipe->getIngredients(), JSON_UNESCAPED_UNICODE),
            json_encode($recipe->getSteps(), JSON_UNESCAPED_UNICODE),
            $recipe->getCalories(),
            json_encode($recipe->getMacros(), JSON_UNESCAPED_UNICODE),
            $recipe->getDifficulty(),
            $recipe->getPrepTime(),
            $recipe->getCookTime(),
            json_encode($recipe->getDietaryRestrictions(), JSON_UNESCAPED_UNICODE),
            $recipe->getImageUrl(),
            $recipe->getVideoUrl()
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?Recipe
    {
        $stmt = $this->db->prepare("SELECT * FROM recipe WHERE id_recipe = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? new Recipe($data) : null;
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM recipe ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$limit, $offset]);

        $recipes = [];
        while ($row = $stmt->fetch()) {
            $recipes[] = new Recipe($row);
        }

        return $recipes;
    }

    public function findByDietaryRestrictions(array $restrictions, int $limit = 20): array
    {
        if (empty($restrictions)) {
            return $this->findAll($limit);
        }

        // Build JSON search conditions
        $conditions = [];
        $params = [];
        foreach ($restrictions as $restriction) {
            $conditions[] = "JSON_CONTAINS(dietary_restrictions, ?)";
            $params[] = json_encode($restriction);
        }

        $whereClause = implode(' AND ', $conditions);
        $stmt = $this->db->prepare(
            "SELECT * FROM recipe WHERE {$whereClause} ORDER BY created_at DESC LIMIT ?"
        );

        $params[] = $limit;
        $stmt->execute($params);

        $recipes = [];
        while ($row = $stmt->fetch()) {
            $recipes[] = new Recipe($row);
        }

        return $recipes;
    }

    public function findByDifficulty(string $difficulty, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM recipe WHERE difficulty = ? ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$difficulty, $limit]);

        $recipes = [];
        while ($row = $stmt->fetch()) {
            $recipes[] = new Recipe($row);
        }

        return $recipes;
    }

    public function findByCalorieRange(int $minCalories, int $maxCalories, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM recipe 
             WHERE calories BETWEEN ? AND ? 
             ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$minCalories, $maxCalories, $limit]);

        $recipes = [];
        while ($row = $stmt->fetch()) {
            $recipes[] = new Recipe($row);
        }

        return $recipes;
    }

    public function search(string $query, int $limit = 20): array
    {
        $searchTerm = "%{$query}%";
        $stmt = $this->db->prepare(
            "SELECT * FROM recipe 
             WHERE title LIKE ? OR description LIKE ?
             ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->execute([$searchTerm, $searchTerm, $limit]);

        $recipes = [];
        while ($row = $stmt->fetch()) {
            $recipes[] = new Recipe($row);
        }

        return $recipes;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM recipe WHERE id_recipe = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM recipe");
        return (int)$stmt->fetchColumn();
    }
}
