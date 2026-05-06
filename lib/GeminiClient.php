<?php
/**
 * ============================================================
 *  NutriSmart - Service Gemini
 *  /lib/GeminiClient.php
 *
 *  Classe d'accès à l'API Google Gemini (REST).
 *  Encapsule l'appel HTTP, la sérialisation des messages et
 *  la gestion des erreurs.
 *
 *  Documentation : https://ai.google.dev/api/generate-content
 * ============================================================
 */

class GeminiClient
{
    private string $apiKey;
    private string $model;
    private string $systemPrompt;

    public function __construct(array $config)
    {
        $this->apiKey       = $config['api_key']       ?? '';
        $this->model        = $config['model']         ?? 'gemini-2.0-flash';
        $this->systemPrompt = $config['system_prompt'] ?? '';
    }

    /**
     * Vérifie que la configuration est valide.
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== '' && $this->apiKey !== 'VOTRE_CLE_API_ICI';
    }

    /**
     * Envoie un message à Gemini, en tenant compte de l'historique.
     *
     * @param string $userMessage  Le nouveau message de l'utilisateur.
     * @param array  $history      Historique : [['role'=>'user'|'model', 'text'=>'...'], ...]
     *
     * @return array ['success'=>bool, 'reply'=>string, 'error'=>string|null]
     */
    public function chat(string $userMessage, array $history = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'reply'   => null,
                'error'   => "La clé API Gemini n'est pas configurée. Modifiez /config/chatbot.php.",
            ];
        }

        // Construction du tableau "contents" attendu par l'API
        $contents = [];
        foreach ($history as $msg) {
            $role = ($msg['role'] === 'model') ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => (string)$msg['text']]],
            ];
        }
        // Ajout du nouveau message
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature'      => 0.7,
                'maxOutputTokens'  => 500,
                'topP'             => 0.95,
            ],
        ];

        // System prompt (en option, pour Gemini 1.5+)
        if ($this->systemPrompt !== '') {
            $payload['systemInstruction'] = [
                'parts' => [['text' => $this->systemPrompt]],
            ];
        }

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            urlencode($this->model),
            urlencode($this->apiKey)
        );

        $response = $this->httpPost($url, $payload);
        if (!$response['success']) {
            return $response;
        }

        // Extraction de la réponse
        $data = json_decode($response['body'], true);
        if (!is_array($data)) {
            return ['success' => false, 'reply' => null, 'error' => 'Réponse invalide reçue de l\'API.'];
        }
        if (isset($data['error'])) {
            return [
                'success' => false,
                'reply'   => null,
                'error'   => 'Gemini : ' . ($data['error']['message'] ?? 'erreur inconnue'),
            ];
        }
        $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        if (!$reply) {
            return [
                'success' => false,
                'reply'   => null,
                'error'   => 'Aucune réponse générée. Veuillez reformuler votre question.',
            ];
        }
        return [
            'success' => true,
            'reply'   => trim($reply),
            'error'   => null,
        ];
    }

    /**
     * Envoie une requête HTTP POST en JSON, en utilisant cURL si dispo
     * sinon file_get_contents (compatible XAMPP par défaut).
     */
    private function httpPost(string $url, array $payload): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_UNICODE);

        // Méthode 1 : cURL (préférée, gère mieux les timeouts)
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);
            $resp = curl_exec($ch);
            $err  = curl_error($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($resp === false) {
                return ['success' => false, 'reply' => null, 'error' => 'Erreur réseau : ' . $err];
            }
            if ($code >= 400) {
                $detail = '';
                $j = json_decode($resp, true);
                if (isset($j['error']['message'])) $detail = ' (' . $j['error']['message'] . ')';
                return ['success' => false, 'reply' => null, 'error' => "API Gemini a renvoyé HTTP $code$detail"];
            }
            return ['success' => true, 'body' => $resp, 'error' => null];
        }

        // Méthode 2 : file_get_contents fallback
        $opts = [
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/json\r\n",
                'content'       => $body,
                'timeout'       => 30,
                'ignore_errors' => true,
            ],
        ];
        $ctx = stream_context_create($opts);
        $resp = @file_get_contents($url, false, $ctx);
        if ($resp === false) {
            return ['success' => false, 'reply' => null, 'error' => 'Erreur réseau (file_get_contents).'];
        }
        return ['success' => true, 'body' => $resp, 'error' => null];
    }
}
