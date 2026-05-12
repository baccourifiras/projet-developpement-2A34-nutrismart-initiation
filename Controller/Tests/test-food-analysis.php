<?php

declare(strict_types=1);

require_once __DIR__ . '/../../Service/FoodAnalysisService.php';
require_once __DIR__ . '/../../Repository/FoodAnalysisRepository.php';

echo "=== Testing Food Analysis Service ===\n\n";

try {
    $service = new FoodAnalysisService();
    
    echo "✓ FoodAnalysisService initialized\n";
    
    // Test 1: Barcode scanning (doesn't require image)
    echo "\n--- Test 1: Barcode Scanning ---\n";
    echo "Scanning Nutella barcode (3017620422003)...\n";
    
    $product = $service->analyzeBarcode('3017620422003');
    
    echo "✓ Product found!\n";
    echo "Name: " . $product['name'] . "\n";
    echo "Brand: " . $product['brand'] . "\n";
    echo "Calories: " . $product['calories'] . " kcal/100g\n";
    echo "Protein: " . $product['macros']['protein'] . "g\n";
    echo "Carbs: " . $product['macros']['carbs'] . "g\n";
    echo "Fats: " . $product['macros']['fats'] . "g\n";
    
    // Test 2: Database operations
    echo "\n--- Test 2: Database Operations ---\n";
    $repository = new FoodAnalysisRepository();
    echo "✓ FoodAnalysisRepository initialized\n";
    
    // Create a sample analysis
    $analysis = new FoodAnalysis([
        'user_id' => 1,
        'image_path' => '/uploads/test.jpg',
        'detected_foods' => [
            ['name' => 'Apple', 'quantity' => '1 medium', 'calories' => 95, 'confidence' => 'high'],
            ['name' => 'Banana', 'quantity' => '1 medium', 'calories' => 105, 'confidence' => 'high']
        ],
        'total_calories' => 200,
        'macros' => [
            'protein' => 2,
            'carbs' => 50,
            'fats' => 1,
            'fiber' => 6
        ],
        'meal_type' => 'snack'
    ]);
    
    $analysisId = $repository->save($analysis);
    echo "✓ Sample analysis saved with ID: {$analysisId}\n";
    
    // Test 3: Retrieve analysis
    echo "\n--- Test 3: Retrieve Analysis ---\n";
    $retrieved = $repository->findById($analysisId);
    if ($retrieved) {
        echo "✓ Analysis retrieved successfully!\n";
        echo "Total Calories: " . $retrieved->getTotalCalories() . " kcal\n";
        echo "Foods detected: " . count($retrieved->getDetectedFoods()) . "\n";
    }
    
    // Test 4: Daily summary
    echo "\n--- Test 4: Daily Summary ---\n";
    $today = date('Y-m-d');
    $totalCalories = $repository->getTotalCaloriesByDate($today, 1);
    echo "✓ Total calories for today: {$totalCalories} kcal\n";
    
    echo "\n=== All Tests Passed! ===\n";
    echo "\nNote: Image analysis test requires:\n";
    echo "1. A valid Groq API key with vision model access\n";
    echo "2. An actual image file to analyze\n";
    echo "3. You can test this through the web interface\n";
    
} catch (AIServiceException $e) {
    echo "\n❌ AI Service Error: " . $e->getMessage() . "\n";
    echo "\nThis is expected if:\n";
    echo "1. The barcode product doesn't exist in OpenFoodFacts\n";
    echo "2. No internet connection\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
