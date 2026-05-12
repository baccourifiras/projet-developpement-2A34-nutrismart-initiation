<?php

declare(strict_types=1);

/**
 * FoodAnalysis Model
 * Stores analyzed food data from images
 */
class FoodAnalysis
{
    private ?int $id = null;
    private ?int $userId = null;
    private string $imagePath;
    private array $detectedFoods;
    private int $totalCalories;
    private array $macros;
    private array $micronutrients;
    private string $analysisDate;
    private ?string $mealType = null; // breakfast, lunch, dinner, snack

    public function __construct(array $data = [])
    {
        $this->id = $data['id_analysis'] ?? null;
        $this->userId = $data['user_id'] ?? null;
        $this->imagePath = $data['image_path'] ?? '';
        $this->detectedFoods = isset($data['detected_foods']) 
            ? (is_string($data['detected_foods']) ? json_decode($data['detected_foods'], true) : $data['detected_foods'])
            : [];
        $this->totalCalories = $data['total_calories'] ?? 0;
        $this->macros = isset($data['macros']) 
            ? (is_string($data['macros']) ? json_decode($data['macros'], true) : $data['macros'])
            : ['protein' => 0, 'carbs' => 0, 'fats' => 0, 'fiber' => 0];
        $this->micronutrients = isset($data['micronutrients']) 
            ? (is_string($data['micronutrients']) ? json_decode($data['micronutrients'], true) : $data['micronutrients'])
            : [];
        $this->analysisDate = $data['analysis_date'] ?? date('Y-m-d H:i:s');
        $this->mealType = $data['meal_type'] ?? null;
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getUserId(): ?int { return $this->userId; }
    public function getImagePath(): string { return $this->imagePath; }
    public function getDetectedFoods(): array { return $this->detectedFoods; }
    public function getTotalCalories(): int { return $this->totalCalories; }
    public function getMacros(): array { return $this->macros; }
    public function getMicronutrients(): array { return $this->micronutrients; }
    public function getAnalysisDate(): string { return $this->analysisDate; }
    public function getMealType(): ?string { return $this->mealType; }

    // Setters
    public function setUserId(?int $userId): void { $this->userId = $userId; }
    public function setImagePath(string $imagePath): void { $this->imagePath = $imagePath; }
    public function setDetectedFoods(array $foods): void { $this->detectedFoods = $foods; }
    public function setTotalCalories(int $calories): void { $this->totalCalories = $calories; }
    public function setMacros(array $macros): void { $this->macros = $macros; }
    public function setMicronutrients(array $micronutrients): void { $this->micronutrients = $micronutrients; }
    public function setMealType(?string $mealType): void { $this->mealType = $mealType; }

    public function toArray(): array
    {
        return [
            'id_analysis' => $this->id,
            'user_id' => $this->userId,
            'image_path' => $this->imagePath,
            'detected_foods' => $this->detectedFoods,
            'total_calories' => $this->totalCalories,
            'macros' => $this->macros,
            'micronutrients' => $this->micronutrients,
            'analysis_date' => $this->analysisDate,
            'meal_type' => $this->mealType
        ];
    }
}
