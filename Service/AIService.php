<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Exception/AIServiceException.php';

class AIService
{
    public function generateMeal(string $typeRepas, int $calories, string $typeRegime): string
    {
        $prompt = $this->buildPrompt($typeRepas, $calories, $typeRegime);
        $apiKey = getenv('OPENAI_API_KEY') ?: getenv('GROQ_API_KEY');

        if (!$apiKey) {
            return $this->simulateResponse($typeRepas, $calories, $typeRegime);
        }

        $response = $this->callExternalApi($prompt, $apiKey);
        if (trim($response) === '') {
            throw new AIServiceException('La reponse IA est vide.');
        }

        return $response;
    }

    private function buildPrompt(string $typeRepas, int $calories, string $typeRegime): string
    {
        return sprintf(
            "Genere un repas nutritionnel en francais.\nType de repas: %s\nCalories cible: %d\nType de regime: %s\n\nReponds strictement avec ce format:\nTITRE: ...\nINGREDIENTS:\n- ingredient 1\n- ingredient 2\nETAPES:\n1. ...\n2. ...\nCALORIES: <nombre>\nCONSEIL: ...",
            $typeRepas,
            $calories,
            $typeRegime
        );
    }

    private function callExternalApi(string $prompt, string $apiKey): string
    {
        $url = getenv('OPENAI_API_URL') ?: 'https://api.openai.com/v1/chat/completions';
        $model = getenv('OPENAI_MODEL') ?: 'gpt-4o-mini';
        $payload = json_encode([
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'Tu es un assistant nutrition.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.3,
        ], JSON_UNESCAPED_UNICODE);

        if ($payload === false) {
            throw new AIServiceException('Impossible de serializer la requete IA.');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new AIServiceException('Initialisation CURL impossible.');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 25,
        ]);

        $raw = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            throw new AIServiceException('Erreur reseau IA: ' . $curlError);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new AIServiceException('Erreur API IA (HTTP ' . $statusCode . ').');
        }

        $decoded = json_decode($raw, true);
        $content = $decoded['choices'][0]['message']['content'] ?? '';

        if (!is_string($content) || trim($content) === '') {
            throw new AIServiceException('Format de reponse IA invalide.');
        }

        return $content;
    }

    private function simulateResponse(string $typeRepas, int $calories, string $typeRegime): string
    {
        return "TITRE: Bol proteine " . $typeRepas . "\n"
            . "INGREDIENTS:\n"
            . "- 120g blanc de poulet grille\n"
            . "- 80g quinoa cuit\n"
            . "- 100g legumes verts\n"
            . "- 1 c. a soupe huile d'olive\n"
            . "ETAPES:\n"
            . "1. Cuire le quinoa et griller le poulet.\n"
            . "2. Assembler avec les legumes et assaisonner.\n"
            . "CALORIES: " . $calories . "\n"
            . "CONSEIL: Variante adaptee au regime " . $typeRegime . ".";
    }
}
