<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Model/Database.php';
require_once dirname(__DIR__) . '/Model/Meal.php';
require_once dirname(__DIR__) . '/Repository/MealRepository.php';
require_once dirname(__DIR__) . '/Service/AIService.php';
require_once dirname(__DIR__) . '/Service/MealService.php';
require_once dirname(__DIR__) . '/Exception/AIServiceException.php';
require_once dirname(__DIR__) . '/Exception/RepositoryException.php';
require_once dirname(__DIR__) . '/Exception/ValidationException.php';

class MealController
{
    private MealService $mealService;
    private MealRepository $mealRepository;

    public function __construct()
    {
        $pdo = Database::getConnection();
        $this->mealRepository = new MealRepository($pdo);
        $this->mealService = new MealService(new AIService(), $this->mealRepository);
    }

    public function generate(): void
    {
        try {
            $input = $this->getRequestInput();
            $meal = $this->mealService->generateAndSave($input);
            $this->jsonResponse([
                'success' => true,
                'message' => 'Repas genere avec succes.',
                'data' => $meal->toArray(),
            ], 201);
        } catch (ValidationException $e) {
            $this->logError($e);
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 422);
        } catch (AIServiceException | RepositoryException $e) {
            $this->logError($e);
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 500);
        } catch (Throwable $e) {
            $this->logError($e);
            $this->jsonResponse(['success' => false, 'error' => 'Erreur interne serveur.'], 500);
        }
    }

    public function list(): void
    {
        try {
            $meals = array_map(
                static fn (Meal $meal): array => $meal->toArray(),
                $this->mealRepository->findAll()
            );
            $this->jsonResponse(['success' => true, 'data' => $meals], 200);
        } catch (Throwable $e) {
            $this->logError($e);
            $this->jsonResponse(['success' => false, 'error' => 'Impossible de recuperer les repas.'], 500);
        }
    }

    public function show(int $id): void
    {
        try {
            if ($id <= 0) {
                throw new ValidationException('ID invalide.');
            }

            $meal = $this->mealRepository->findById($id);
            if (!$meal) {
                $this->jsonResponse(['success' => false, 'error' => 'Repas introuvable.'], 404);
            }

            $this->jsonResponse(['success' => true, 'data' => $meal->toArray()], 200);
        } catch (ValidationException $e) {
            $this->jsonResponse(['success' => false, 'error' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            $this->logError($e);
            $this->jsonResponse(['success' => false, 'error' => 'Erreur interne serveur.'], 500);
        }
    }

    private function getRequestInput(): array
    {
        $rawInput = file_get_contents('php://input');
        if (is_string($rawInput) && trim($rawInput) !== '') {
            $decoded = json_decode($rawInput, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $_POST;
    }

    private function jsonResponse(array $payload, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function logError(Throwable $exception): void
    {
        $logDir = dirname(__DIR__) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $message = sprintf(
            "[%s] %s in %s:%d\n",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        error_log($message, 3, $logDir . '/meal-module-error.log');
    }
}
