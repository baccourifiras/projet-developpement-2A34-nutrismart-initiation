<?php

declare(strict_types=1);

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Recipe.php';
require_once __DIR__ . '/../Exception/AIServiceException.php';

/**
 * Recipe Generator Service using Groq API
 * Generates recipes based on ingredients and dietary restrictions
 */
class RecipeGeneratorService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = GROQ_API_KEY;
        $this->apiUrl = GROQ_API_URL;
        $this->model = GROQ_MODEL;
    }

    /**
     * Generate a recipe based on available ingredients
     */
    public function generateRecipe(
        array $ingredients,
        array $dietaryRestrictions = [],
        string $difficulty = 'medium',
        int $targetCalories = 500,
        string $mealType = 'lunch'
    ): Recipe {
        $prompt = $this->buildRecipePrompt($ingredients, $dietaryRestrictions, $difficulty, $targetCalories, $mealType);
        $response = $this->callGroqAPI($prompt);
        return $this->parseRecipeResponse($response);
    }

    /**
     * Generate recipe suggestions based on dietary preferences
     */
    public function suggestRecipes(
        array $dietaryRestrictions = [],
        string $difficulty = 'medium',
        int $count = 3
    ): array {
        $prompt = $this->buildSuggestionPrompt($dietaryRestrictions, $difficulty, $count);
        $response = $this->callGroqAPI($prompt);
        return $this->parseMultipleRecipes($response);
    }

    /**
     * Get recipe with video tutorial
     */
    public function getRecipeWithVideo(string $recipeName): ?string
    {
        if (!defined('YOUTUBE_API_KEY') || YOUTUBE_API_KEY === 'your-youtube-api-key-here') {
            return null;
        }

        $searchQuery = urlencode($recipeName . ' recipe tutorial');
        $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q={$searchQuery}&type=video&maxResults=1&key=" . YOUTUBE_API_KEY;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data['items'][0]['id']['videoId'])) {
                return 'https://www.youtube.com/watch?v=' . $data['items'][0]['id']['videoId'];
            }
        }

        return null;
    }

    private function buildRecipePrompt(
        array $ingredients,
        array $dietaryRestrictions,
        string $difficulty,
        int $targetCalories,
        string $mealType
    ): string {
        $ingredientsList = implode(', ', $ingredients);
        $restrictionsList = !empty($dietaryRestrictions) ? implode(', ', $dietaryRestrictions) : 'aucune';

        return <<<PROMPT
Tu es un chef cuisinier expert et nutritionniste. Génère une recette détaillée en français avec les contraintes suivantes:

INGRÉDIENTS DISPONIBLES: {$ingredientsList}
RESTRICTIONS ALIMENTAIRES: {$restrictionsList}
DIFFICULTÉ: {$difficulty}
CALORIES CIBLES: {$targetCalories} kcal
TYPE DE REPAS: {$mealType}

Réponds UNIQUEMENT au format JSON suivant (sans markdown, sans backticks):
{
  "title": "Nom de la recette",
  "description": "Description courte et appétissante",
  "ingredients": [
    {"name": "ingrédient 1", "quantity": "100g"},
    {"name": "ingrédient 2", "quantity": "2 unités"}
  ],
  "steps": [
    "Étape 1 détaillée",
    "Étape 2 détaillée"
  ],
  "calories": 500,
  "macros": {
    "protein": 30,
    "carbs": 50,
    "fats": 15
  },
  "difficulty": "medium",
  "prep_time": 15,
  "cook_time": 20,
  "dietary_restrictions": ["vegan", "gluten-free"]
}

IMPORTANT: 
- Utilise UNIQUEMENT les ingrédients fournis
- Respecte STRICTEMENT les restrictions alimentaires
- Les macros doivent correspondre aux calories (4 kcal/g protéines et glucides, 9 kcal/g lipides)
- Temps en minutes
PROMPT;
    }

    private function buildSuggestionPrompt(array $dietaryRestrictions, string $difficulty, int $count): string
    {
        $restrictionsList = !empty($dietaryRestrictions) ? implode(', ', $dietaryRestrictions) : 'aucune';

        return <<<PROMPT
Tu es un chef cuisinier expert. Suggère {$count} recettes variées en français:

RESTRICTIONS ALIMENTAIRES: {$restrictionsList}
DIFFICULTÉ: {$difficulty}

Réponds avec un tableau JSON (sans markdown, sans backticks) contenant {$count} recettes au format:
[
  {
    "title": "Nom recette 1",
    "description": "Description",
    "ingredients": [{"name": "ingrédient", "quantity": "quantité"}],
    "steps": ["étape 1", "étape 2"],
    "calories": 500,
    "macros": {"protein": 30, "carbs": 50, "fats": 15},
    "difficulty": "medium",
    "prep_time": 15,
    "cook_time": 20,
    "dietary_restrictions": []
  }
]
PROMPT;
    }

    private function callGroqAPI(string $prompt): string
    {
        if ($this->apiKey === 'your-groq-api-key-here') {
            throw new AIServiceException('Groq API key not configured. Please set GROQ_API_KEY in Config/config.php');
        }

        $payload = json_encode([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un chef cuisinier expert et nutritionniste. Tu réponds UNIQUEMENT en JSON valide, sans markdown.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false, // For development only
            CURLOPT_SSL_VERIFYHOST => false, // For development only
        ]);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new AIServiceException('Groq API network error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? 'Unknown error';
            throw new AIServiceException("Groq API error (HTTP {$httpCode}): {$errorMsg}");
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        if (empty($content)) {
            throw new AIServiceException('Empty response from Groq API');
        }

        return $content;
    }

    private function parseRecipeResponse(string $response): Recipe
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AIServiceException('Invalid JSON response from AI: ' . json_last_error_msg());
        }

        return new Recipe($data);
    }

    private function parseMultipleRecipes(string $response): array
    {
        // Remove markdown code blocks if present
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $response = trim($response);

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AIServiceException('Invalid JSON response from AI: ' . json_last_error_msg());
        }

        $recipes = [];
        foreach ($data as $recipeData) {
            $recipes[] = new Recipe($recipeData);
        }

        return $recipes;
    }
}
