<?php

declare(strict_types=1);

require_once __DIR__ . '/../Service/RecipeGeneratorService.php';
require_once __DIR__ . '/../Repository/RecipeRepository.php';
require_once __DIR__ . '/../Exception/AIServiceException.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

/**
 * Recipe Controller
 * Handles recipe generation and management endpoints
 */
class RecipeController
{
    private RecipeGeneratorService $recipeService;
    private RecipeRepository $recipeRepository;

    public function __construct()
    {
        $this->recipeService = new RecipeGeneratorService();
        $this->recipeRepository = new RecipeRepository();
    }

    /**
     * Generate a new recipe based on ingredients
     * POST /api-recipe.php?action=generate
     */
    public function generate(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = $this->getJsonInput();

            // Validate input
            if (empty($input['ingredients']) || !is_array($input['ingredients'])) {
                throw new ValidationException('Ingredients array is required');
            }

            $ingredients = $input['ingredients'];
            $dietaryRestrictions = $input['dietary_restrictions'] ?? [];
            $difficulty = $input['difficulty'] ?? 'medium';
            $targetCalories = (int)($input['target_calories'] ?? 500);
            $mealType = $input['meal_type'] ?? 'lunch';

            // Generate recipe
            $recipe = $this->recipeService->generateRecipe(
                $ingredients,
                $dietaryRestrictions,
                $difficulty,
                $targetCalories,
                $mealType
            );

            // Get video tutorial if available
            $videoUrl = $this->recipeService->getRecipeWithVideo($recipe->getTitle());
            if ($videoUrl) {
                $recipe->setVideoUrl($videoUrl);
            }

            // Save to database
            $recipeId = $this->recipeRepository->save($recipe);

            echo json_encode([
                'success' => true,
                'recipe_id' => $recipeId,
                'recipe' => $recipe->toArray()
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } catch (AIServiceException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'AI Service Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get recipe suggestions
     * GET /api-recipe.php?action=suggest
     */
    public function suggest(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $dietaryRestrictions = isset($_GET['dietary_restrictions']) 
                ? explode(',', $_GET['dietary_restrictions']) 
                : [];
            $difficulty = $_GET['difficulty'] ?? 'medium';
            $count = (int)($_GET['count'] ?? 3);

            $recipes = $this->recipeService->suggestRecipes($dietaryRestrictions, $difficulty, $count);

            $recipesArray = array_map(fn($recipe) => $recipe->toArray(), $recipes);

            echo json_encode([
                'success' => true,
                'count' => count($recipes),
                'recipes' => $recipesArray
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (AIServiceException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'AI Service Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get recipe by ID
     * GET /api-recipe.php?action=show&id=1
     */
    public function show(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $recipe = $this->recipeRepository->findById($id);

            if (!$recipe) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Recipe not found'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            echo json_encode([
                'success' => true,
                'recipe' => $recipe->toArray()
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * List all recipes with filters
     * GET /api-recipe.php?action=list
     */
    public function list(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $limit = (int)($_GET['limit'] ?? 20);
            $offset = (int)($_GET['offset'] ?? 0);
            $difficulty = $_GET['difficulty'] ?? null;
            $minCalories = isset($_GET['min_calories']) ? (int)$_GET['min_calories'] : null;
            $maxCalories = isset($_GET['max_calories']) ? (int)$_GET['max_calories'] : null;
            $search = $_GET['search'] ?? null;
            $dietaryRestrictions = isset($_GET['dietary_restrictions']) 
                ? explode(',', $_GET['dietary_restrictions']) 
                : null;

            // Apply filters
            if ($search) {
                $recipes = $this->recipeRepository->search($search, $limit);
            } elseif ($dietaryRestrictions) {
                $recipes = $this->recipeRepository->findByDietaryRestrictions($dietaryRestrictions, $limit);
            } elseif ($difficulty) {
                $recipes = $this->recipeRepository->findByDifficulty($difficulty, $limit);
            } elseif ($minCalories && $maxCalories) {
                $recipes = $this->recipeRepository->findByCalorieRange($minCalories, $maxCalories, $limit);
            } else {
                $recipes = $this->recipeRepository->findAll($limit, $offset);
            }

            $recipesArray = array_map(fn($recipe) => $recipe->toArray(), $recipes);

            echo json_encode([
                'success' => true,
                'count' => count($recipes),
                'total' => $this->recipeRepository->count(),
                'recipes' => $recipesArray
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Delete a recipe
     * DELETE /api-recipe.php?action=delete&id=1
     */
    public function delete(int $id): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $success = $this->recipeRepository->delete($id);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Recipe deleted successfully'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Recipe not found'
                ], JSON_UNESCAPED_UNICODE);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidationException('Invalid JSON input');
        }

        return $data ?? [];
    }
}
