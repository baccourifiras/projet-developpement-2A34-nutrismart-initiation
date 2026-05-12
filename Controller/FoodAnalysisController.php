<?php

declare(strict_types=1);

require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Service/FoodAnalysisService.php';
require_once __DIR__ . '/../Repository/FoodAnalysisRepository.php';
require_once __DIR__ . '/../Exception/AIServiceException.php';
require_once __DIR__ . '/../Exception/ValidationException.php';

/**
 * Food Analysis Controller
 * Handles food image analysis and barcode scanning
 */
class FoodAnalysisController
{
    private FoodAnalysisService $analysisService;
    private FoodAnalysisRepository $analysisRepository;

    public function __construct()
    {
        $this->analysisService = new FoodAnalysisService();
        $this->analysisRepository = new FoodAnalysisRepository();
    }

    /**
     * Analyze food image
     * POST /api-food-analysis.php?action=analyze
     */
    public function analyzeImage(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // Validate file upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                throw new ValidationException('No image uploaded or upload error');
            }

            $file = $_FILES['image'];

            // Validate file type
            if (!in_array($file['type'], UPLOAD_ALLOWED_TYPES)) {
                throw new ValidationException('Invalid file type. Allowed: JPEG, PNG, WebP');
            }

            // Validate file size
            if ($file['size'] > UPLOAD_MAX_SIZE) {
                throw new ValidationException('File too large. Max size: ' . (UPLOAD_MAX_SIZE / 1024 / 1024) . 'MB');
            }

            // Save uploaded file
            $filename = uniqid('food_', true) . '_' . basename($file['name']);
            $filepath = UPLOAD_DIR . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new ValidationException('Failed to save uploaded file');
            }

            // Get optional parameters
            $mealType = $_POST['meal_type'] ?? null;
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

            // Analyze image
            $analysis = $this->analysisService->analyzeFoodImage($filepath, $mealType);
            
            if ($userId) {
                $analysis->setUserId($userId);
            }

            // Save to database
            $analysisId = $this->analysisRepository->save($analysis);

            echo json_encode([
                'success' => true,
                'analysis_id' => $analysisId,
                'analysis' => $analysis->toArray(),
                'image_url' => '/uploads/food-images/' . $filename,
                'note' => 'Vision API is deprecated. For better results, use the text description feature.'
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
     * Analyze food from text description
     * POST /api-food-analysis.php?action=analyze-text
     */
    public function analyzeFromText(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $input = $this->getJsonInput();

            if (empty($input['description'])) {
                throw new ValidationException('Food description is required');
            }

            $description = $input['description'];
            $mealType = $input['meal_type'] ?? null;
            $userId = isset($input['user_id']) ? (int)$input['user_id'] : null;

            // Analyze using AI
            $analysisData = $this->analysisService->analyzeFoodFromDescription($description, $mealType);

            // Create FoodAnalysis object
            $analysis = new FoodAnalysis([
                'user_id' => $userId,
                'image_path' => 'text_description',
                'detected_foods' => $analysisData['foods'],
                'total_calories' => $analysisData['total_calories'],
                'macros' => $analysisData['macros'],
                'micronutrients' => $analysisData['micronutrients'] ?? [],
                'meal_type' => $mealType
            ]);

            // Save to database
            $analysisId = $this->analysisRepository->save($analysis);

            echo json_encode([
                'success' => true,
                'analysis_id' => $analysisId,
                'analysis' => $analysis->toArray(),
                'suggestions' => $analysisData['meal_suggestions'] ?? null
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

    private function getJsonInput(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ValidationException('Invalid JSON input');
        }

        return $data ?? [];
    }

    /**
     * Scan barcode
     * GET /api-food-analysis.php?action=barcode&code=123456789
     */
    public function scanBarcode(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $barcode = $_GET['code'] ?? '';

            if (empty($barcode)) {
                throw new ValidationException('Barcode is required');
            }

            $productInfo = $this->analysisService->analyzeBarcode($barcode);

            echo json_encode([
                'success' => true,
                'product' => $productInfo
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } catch (AIServiceException $e) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
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
     * Estimate portion size
     * POST /api-food-analysis.php?action=portion
     */
    public function estimatePortion(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                throw new ValidationException('No image uploaded');
            }

            $file = $_FILES['image'];
            $filename = uniqid('portion_', true) . '_' . basename($file['name']);
            $filepath = UPLOAD_DIR . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new ValidationException('Failed to save uploaded file');
            }

            $portionData = $this->analysisService->estimatePortionSize($filepath);

            echo json_encode([
                'success' => true,
                'portions' => $portionData
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
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
     * Get analysis history
     * GET /api-food-analysis.php?action=history&user_id=1
     */
    public function getHistory(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;
            $limit = (int)($_GET['limit'] ?? 50);

            if (!$userId) {
                throw new ValidationException('User ID is required');
            }

            $analyses = $this->analysisRepository->findByUserId($userId, $limit);
            $analysesArray = array_map(fn($a) => $a->toArray(), $analyses);

            echo json_encode([
                'success' => true,
                'count' => count($analyses),
                'history' => $analysesArray
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
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
     * Get daily calorie summary
     * GET /api-food-analysis.php?action=daily&date=2024-01-01&user_id=1
     */
    public function getDailySummary(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $date = $_GET['date'] ?? date('Y-m-d');
            $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

            $totalCalories = $this->analysisRepository->getTotalCaloriesByDate($date, $userId);
            $analyses = $this->analysisRepository->findByDateRange($date, $date, $userId);

            $analysesArray = array_map(fn($a) => $a->toArray(), $analyses);

            echo json_encode([
                'success' => true,
                'date' => $date,
                'total_calories' => $totalCalories,
                'meal_count' => count($analyses),
                'meals' => $analysesArray
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
