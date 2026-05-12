<?php

declare(strict_types=1);

require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/FoodAnalysis.php';

/**
 * Food Analysis Repository
 * Handles database operations for food analysis records
 */
class FoodAnalysisRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->ensureFoodAnalysisTable();
    }

    private function ensureFoodAnalysisTable(): void
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS food_analysis (
                id_analysis INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                image_path VARCHAR(500) NOT NULL,
                detected_foods JSON NOT NULL,
                total_calories INT NOT NULL,
                macros JSON NOT NULL,
                micronutrients JSON,
                meal_type ENUM('breakfast','lunch','dinner','snack') NULL,
                analysis_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_date (analysis_date),
                INDEX idx_meal_type (meal_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public function save(FoodAnalysis $analysis): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO food_analysis (
                user_id, image_path, detected_foods, total_calories, 
                macros, micronutrients, meal_type
            ) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $analysis->getUserId(),
            $analysis->getImagePath(),
            json_encode($analysis->getDetectedFoods(), JSON_UNESCAPED_UNICODE),
            $analysis->getTotalCalories(),
            json_encode($analysis->getMacros(), JSON_UNESCAPED_UNICODE),
            json_encode($analysis->getMicronutrients(), JSON_UNESCAPED_UNICODE),
            $analysis->getMealType()
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?FoodAnalysis
    {
        $stmt = $this->db->prepare("SELECT * FROM food_analysis WHERE id_analysis = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        return $data ? new FoodAnalysis($data) : null;
    }

    public function findByUserId(int $userId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM food_analysis 
             WHERE user_id = ? 
             ORDER BY analysis_date DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);

        $analyses = [];
        while ($row = $stmt->fetch()) {
            $analyses[] = new FoodAnalysis($row);
        }

        return $analyses;
    }

    public function findByDateRange(string $startDate, string $endDate, ?int $userId = null): array
    {
        if ($userId) {
            $stmt = $this->db->prepare(
                "SELECT * FROM food_analysis 
                 WHERE user_id = ? AND DATE(analysis_date) BETWEEN ? AND ?
                 ORDER BY analysis_date DESC"
            );
            $stmt->execute([$userId, $startDate, $endDate]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT * FROM food_analysis 
                 WHERE DATE(analysis_date) BETWEEN ? AND ?
                 ORDER BY analysis_date DESC"
            );
            $stmt->execute([$startDate, $endDate]);
        }

        $analyses = [];
        while ($row = $stmt->fetch()) {
            $analyses[] = new FoodAnalysis($row);
        }

        return $analyses;
    }

    public function getTotalCaloriesByDate(string $date, ?int $userId = null): int
    {
        if ($userId) {
            $stmt = $this->db->prepare(
                "SELECT SUM(total_calories) FROM food_analysis 
                 WHERE user_id = ? AND DATE(analysis_date) = ?"
            );
            $stmt->execute([$userId, $date]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT SUM(total_calories) FROM food_analysis 
                 WHERE DATE(analysis_date) = ?"
            );
            $stmt->execute([$date]);
        }

        return (int)$stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM food_analysis WHERE id_analysis = ?");
        return $stmt->execute([$id]);
    }
}
