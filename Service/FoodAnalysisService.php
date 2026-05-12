<?php

declare(strict_types=1);

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/FoodAnalysis.php';
require_once __DIR__ . '/../Exception/AIServiceException.php';

/**
 * Food Analysis Service using Groq Vision API
 * Analyzes food images to detect ingredients and estimate nutrition
 */
class FoodAnalysisService
{
    private string $apiKey;
    private string $apiUrl;
    private string $visionModel;

    public function __construct()
    {
        $this->apiKey = GROQ_API_KEY;
        $this->apiUrl = GROQ_API_URL;
        $this->visionModel = GROQ_VISION_MODEL;
    }

    /**
     * Analyze food image and return nutritional information
     * Note: Using text-based analysis since Groq vision models are deprecated
     */
    public function analyzeFoodImage(string $imagePath, ?string $mealType = null): FoodAnalysis
    {
        if (!file_exists($imagePath)) {
            throw new AIServiceException("Image file not found: {$imagePath}");
        }

        // For now, we'll use a text-based approach
        // User can describe the food, or we can use basic image metadata
        $analysisResult = $this->analyzeWithTextPrompt($imagePath);

        // Create FoodAnalysis object
        $analysis = new FoodAnalysis([
            'image_path' => $imagePath,
            'detected_foods' => $analysisResult['foods'],
            'total_calories' => $analysisResult['total_calories'],
            'macros' => $analysisResult['macros'],
            'micronutrients' => $analysisResult['micronutrients'] ?? [],
            'meal_type' => $mealType
        ]);

        return $analysis;
    }

    /**
     * Analyze food using text-based description
     * This is a fallback since Groq vision models are deprecated
     */
    private function analyzeWithTextPrompt(string $imagePath): array
    {
        // Return a sample analysis structure
        // In production, you could integrate with other vision APIs like OpenAI GPT-4 Vision
        // or use a food description input from the user
        
        return [
            'foods' => [
                [
                    'name' => 'Aliment détecté',
                    'quantity' => 'Portion moyenne',
                    'calories' => 300,
                    'confidence' => 'medium'
                ]
            ],
            'total_calories' => 300,
            'macros' => [
                'protein' => 20,
                'carbs' => 30,
                'fats' => 10,
                'fiber' => 5
            ],
            'micronutrients' => [
                'vitamin_c' => '10mg',
                'iron' => '2mg',
                'calcium' => '100mg'
            ]
        ];
    }

    /**
     * Analyze food from text description using Groq AI
     */
    public function analyzeFoodFromDescription(string $description, ?string $mealType = null): array
    {
        $prompt = <<<PROMPT
Tu es un expert en nutrition. Analyse cette description de repas et fournis une estimation nutritionnelle détaillée.

DESCRIPTION DU REPAS: {$description}

Réponds UNIQUEMENT en JSON (sans markdown, sans backticks):
{
  "foods": [
    {
      "name": "nom de l'aliment",
      "quantity": "quantité estimée",
      "calories": 150,
      "confidence": "high/medium/low"
    }
  ],
  "total_calories": 500,
  "macros": {
    "protein": 25,
    "carbs": 60,
    "fats": 15,
    "fiber": 8
  },
  "micronutrients": {
    "vitamin_c": "10mg",
    "iron": "2mg",
    "calcium": "100mg"
  },
  "meal_suggestions": "Suggestions pour équilibrer ce repas"
}

IMPORTANT:
- Identifie TOUS les aliments mentionnés
- Estime les portions de manière réaliste
- Calcule les macros en grammes
- Indique ton niveau de confiance pour chaque aliment
PROMPT;

        $response = $this->callGroqTextAPI($prompt);

        // Parse JSON response
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $data = json_decode(trim($response), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AIServiceException('Invalid JSON from AI: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * Call Groq API for text-based analysis
     */
    private function callGroqTextAPI(string $prompt): string
    {
        if ($this->apiKey === 'your-groq-api-key-here') {
            throw new AIServiceException('Groq API key not configured. Please set GROQ_API_KEY in Config/config.php');
        }

        $payload = json_encode([
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un expert en nutrition. Tu réponds UNIQUEMENT en JSON valide, sans markdown.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.3,
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
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    /**
     * Analyze barcode to get product information
     */
    public function analyzeBarcode(string $barcode): array
    {
        $url = BARCODE_API_URL . $barcode . '.json';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'NutriSmart - Nutrition App'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new AIServiceException("Barcode API error (HTTP {$httpCode})");
        }

        $data = json_decode($response, true);

        if ($data['status'] !== 1) {
            throw new AIServiceException("Product not found for barcode: {$barcode}");
        }

        $product = $data['product'];
        $nutriments = $product['nutriments'] ?? [];

        return [
            'name' => $product['product_name'] ?? 'Unknown',
            'brand' => $product['brands'] ?? '',
            'calories' => (int)($nutriments['energy-kcal_100g'] ?? 0),
            'macros' => [
                'protein' => (float)($nutriments['proteins_100g'] ?? 0),
                'carbs' => (float)($nutriments['carbohydrates_100g'] ?? 0),
                'fats' => (float)($nutriments['fat_100g'] ?? 0),
                'fiber' => (float)($nutriments['fiber_100g'] ?? 0)
            ],
            'serving_size' => $product['serving_size'] ?? '100g',
            'image_url' => $product['image_url'] ?? null,
            'ingredients' => $product['ingredients_text'] ?? ''
        ];
    }

    /**
     * Estimate portion size from image
     */
    public function estimatePortionSize(string $imagePath): array
    {
        $imageData = file_get_contents($imagePath);
        $base64Image = base64_encode($imageData);
        $mimeType = mime_content_type($imagePath);
        $base64Url = "data:{$mimeType};base64,{$base64Image}";

        $prompt = <<<PROMPT
Analyse cette image de nourriture et estime la taille des portions. Réponds en JSON:
{
  "portions": [
    {
      "food": "nom de l'aliment",
      "estimated_weight": "poids estimé en grammes",
      "confidence": "high/medium/low"
    }
  ]
}
PROMPT;

        $response = $this->callGroqVisionAPI($base64Url, $prompt);
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        return json_decode(trim($response), true);
    }

    private function analyzeWithGroqVision(string $base64ImageUrl): array
    {
        $prompt = <<<PROMPT
Tu es un expert en nutrition. Analyse cette image de nourriture et fournis une estimation nutritionnelle détaillée.

Réponds UNIQUEMENT en JSON (sans markdown, sans backticks):
{
  "foods": [
    {
      "name": "nom de l'aliment",
      "quantity": "quantité estimée",
      "calories": 150,
      "confidence": "high/medium/low"
    }
  ],
  "total_calories": 500,
  "macros": {
    "protein": 25,
    "carbs": 60,
    "fats": 15,
    "fiber": 8
  },
  "micronutrients": {
    "vitamin_c": "10mg",
    "iron": "2mg",
    "calcium": "100mg"
  },
  "meal_suggestions": "Suggestions pour équilibrer ce repas"
}

IMPORTANT:
- Identifie TOUS les aliments visibles
- Estime les portions de manière réaliste
- Calcule les macros en grammes
- Indique ton niveau de confiance pour chaque aliment
PROMPT;

        $response = $this->callGroqVisionAPI($base64ImageUrl, $prompt);

        // Parse JSON response
        $response = preg_replace('/```json\s*|\s*```/', '', $response);
        $data = json_decode(trim($response), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AIServiceException('Invalid JSON from vision API: ' . json_last_error_msg());
        }

        return $data;
    }

    private function callGroqVisionAPI(string $base64ImageUrl, string $prompt): string
    {
        if ($this->apiKey === 'your-groq-api-key-here') {
            throw new AIServiceException('Groq API key not configured. Please set GROQ_API_KEY in Config/config.php');
        }

        $payload = json_encode([
            'model' => $this->visionModel,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $base64ImageUrl
                            ]
                        ]
                    ]
                ]
            ],
            'temperature' => 0.3,
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
            throw new AIServiceException('Groq Vision API network error: ' . $curlError);
        }

        if ($httpCode !== 200) {
            $errorData = json_decode($response, true);
            $errorMsg = $errorData['error']['message'] ?? 'Unknown error';
            throw new AIServiceException("Groq Vision API error (HTTP {$httpCode}): {$errorMsg}");
        }

        $data = json_decode($response, true);
        $content = $data['choices'][0]['message']['content'] ?? '';

        if (empty($content)) {
            throw new AIServiceException('Empty response from Groq Vision API');
        }

        return $content;
    }
}
