<?php

declare(strict_types=1);

/**
 * Recipe Model
 * Handles recipe data and database operations
 */
class Recipe
{
    private ?int $id = null;
    private string $title;
    private string $description;
    private array $ingredients;
    private array $steps;
    private int $calories;
    private array $macros; // protein, carbs, fats
    private string $difficulty; // easy, medium, hard
    private int $prepTime; // in minutes
    private int $cookTime; // in minutes
    private array $dietaryRestrictions; // vegan, gluten-free, halal, etc.
    private ?string $imageUrl = null;
    private ?string $videoUrl = null;
    private string $createdAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id_recipe'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->ingredients = isset($data['ingredients']) 
            ? (is_string($data['ingredients']) ? json_decode($data['ingredients'], true) : $data['ingredients'])
            : [];
        $this->steps = isset($data['steps']) 
            ? (is_string($data['steps']) ? json_decode($data['steps'], true) : $data['steps'])
            : [];
        $this->calories = $data['calories'] ?? 0;
        $this->macros = isset($data['macros']) 
            ? (is_string($data['macros']) ? json_decode($data['macros'], true) : $data['macros'])
            : ['protein' => 0, 'carbs' => 0, 'fats' => 0];
        $this->difficulty = $data['difficulty'] ?? 'medium';
        $this->prepTime = $data['prep_time'] ?? 0;
        $this->cookTime = $data['cook_time'] ?? 0;
        $this->dietaryRestrictions = isset($data['dietary_restrictions']) 
            ? (is_string($data['dietary_restrictions']) ? json_decode($data['dietary_restrictions'], true) : $data['dietary_restrictions'])
            : [];
        $this->imageUrl = $data['image_url'] ?? null;
        $this->videoUrl = $data['video_url'] ?? null;
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    // Getters
    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->description; }
    public function getIngredients(): array { return $this->ingredients; }
    public function getSteps(): array { return $this->steps; }
    public function getCalories(): int { return $this->calories; }
    public function getMacros(): array { return $this->macros; }
    public function getDifficulty(): string { return $this->difficulty; }
    public function getPrepTime(): int { return $this->prepTime; }
    public function getCookTime(): int { return $this->cookTime; }
    public function getDietaryRestrictions(): array { return $this->dietaryRestrictions; }
    public function getImageUrl(): ?string { return $this->imageUrl; }
    public function getVideoUrl(): ?string { return $this->videoUrl; }
    public function getCreatedAt(): string { return $this->createdAt; }

    // Setters
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(string $description): void { $this->description = $description; }
    public function setIngredients(array $ingredients): void { $this->ingredients = $ingredients; }
    public function setSteps(array $steps): void { $this->steps = $steps; }
    public function setCalories(int $calories): void { $this->calories = $calories; }
    public function setMacros(array $macros): void { $this->macros = $macros; }
    public function setDifficulty(string $difficulty): void { $this->difficulty = $difficulty; }
    public function setPrepTime(int $prepTime): void { $this->prepTime = $prepTime; }
    public function setCookTime(int $cookTime): void { $this->cookTime = $cookTime; }
    public function setDietaryRestrictions(array $restrictions): void { $this->dietaryRestrictions = $restrictions; }
    public function setImageUrl(?string $imageUrl): void { $this->imageUrl = $imageUrl; }
    public function setVideoUrl(?string $videoUrl): void { $this->videoUrl = $videoUrl; }

    public function toArray(): array
    {
        return [
            'id_recipe' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'ingredients' => $this->ingredients,
            'steps' => $this->steps,
            'calories' => $this->calories,
            'macros' => $this->macros,
            'difficulty' => $this->difficulty,
            'prep_time' => $this->prepTime,
            'cook_time' => $this->cookTime,
            'total_time' => $this->prepTime + $this->cookTime,
            'dietary_restrictions' => $this->dietaryRestrictions,
            'image_url' => $this->imageUrl,
            'video_url' => $this->videoUrl,
            'created_at' => $this->createdAt
        ];
    }
}
