<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Model/Meal.php';
require_once dirname(__DIR__) . '/Service/AIService.php';
require_once dirname(__DIR__) . '/Repository/MealRepository.php';
require_once dirname(__DIR__) . '/Exception/ValidationException.php';

class MealService
{
    private AIService $aiService;
    private MealRepository $mealRepository;

    public function __construct(AIService $aiService, MealRepository $mealRepository)
    {
        $this->aiService = $aiService;
        $this->mealRepository = $mealRepository;
    }

    public function generateAndSave(array $input): Meal
    {
        $typeRepas = $this->validateTypeRepas($input['typeRepas'] ?? null);
        $calories = $this->validateCalories($input['calories'] ?? null);
        $typeRegime = $this->validateTypeRegime($input['typeRegime'] ?? null);

        $rawAiResponse = $this->aiService->generateMeal($typeRepas, $calories, $typeRegime);
        $parsed = $this->parseAiResponse($rawAiResponse);

        $meal = new Meal(
            null,
            $typeRepas,
            (int) $parsed['calories'],
            $typeRegime,
            json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: $rawAiResponse,
            date('Y-m-d H:i:s')
        );

        return $this->mealRepository->save($meal);
    }

    public function parseAiResponse(string $response): array
    {
        $title = $this->extractSingle('/TITRE\s*:\s*(.+)/iu', $response, 'Repas IA');
        $caloriesValue = $this->extractSingle('/CALORIES\s*:\s*(\d{2,5})/iu', $response, null);

        preg_match('/INGREDIENTS\s*:\s*(.*?)\nETAPES\s*:/isu', $response, $ingredientsMatch);
        preg_match('/ETAPES\s*:\s*(.*?)\nCALORIES\s*:/isu', $response, $stepsMatch);
        $conseil = $this->extractSingle('/CONSEIL\s*:\s*(.+)/iu', $response, '');

        $ingredients = $this->toCleanList($ingredientsMatch[1] ?? '');
        $etapes = $this->toCleanList($stepsMatch[1] ?? '');

        return [
            'titre' => $title,
            'ingredients' => $ingredients,
            'etapes' => $etapes,
            'calories' => $caloriesValue !== null ? (int) $caloriesValue : 0,
            'conseil' => $conseil,
            'brut' => $response,
        ];
    }

    private function validateTypeRepas($value): string
    {
        $normalized = strtolower(trim(strip_tags((string) $value)));
        $allowed = ['petit_dejeuner', 'dejeuner', 'diner', 'collation'];

        if (!in_array($normalized, $allowed, true)) {
            throw new ValidationException('typeRepas invalide.');
        }

        return $normalized;
    }

    private function validateCalories($value): int
    {
        $validated = filter_var($value, FILTER_VALIDATE_INT);
        if ($validated === false || $validated < 100 || $validated > 2500) {
            throw new ValidationException('calories doit etre un entier entre 100 et 2500.');
        }

        return (int) $validated;
    }

    private function validateTypeRegime($value): string
    {
        $normalized = strtolower(trim(strip_tags((string) $value)));
        $allowed = ['equilibre', 'cut', 'bulk', 'vegetarien', 'vegan', 'keto', 'mediterraneen'];

        if (!in_array($normalized, $allowed, true)) {
            throw new ValidationException('typeRegime invalide.');
        }

        return $normalized;
    }

    private function extractSingle(string $pattern, string $text, ?string $default): ?string
    {
        if (preg_match($pattern, $text, $match) === 1) {
            return trim((string) $match[1]);
        }

        return $default;
    }

    private function toCleanList(string $block): array
    {
        $lines = preg_split('/\R/', trim($block)) ?: [];
        $clean = [];

        foreach ($lines as $line) {
            $candidate = trim(preg_replace('/^[-0-9.\s]+/u', '', (string) $line) ?? '');
            if ($candidate !== '') {
                $clean[] = $candidate;
            }
        }

        return $clean;
    }
}
