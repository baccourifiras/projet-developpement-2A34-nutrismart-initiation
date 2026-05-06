<?php
/**
 * ============================================================
 *  NutriSmart - Controller Chatbot (Front office)
 *  /controllers/frontoffice/ChatbotController.php
 *
 *  Gère les appels AJAX du widget de chatbot :
 *   - send()  : envoie un message à Gemini, retourne la réponse JSON
 *   - reset() : vide l'historique de la session
 * ============================================================
 */

class ChatbotController extends Controller
{
    private array $config;
    private GeminiClient $client;

    public function __construct()
    {
        $this->config = require BASE_PATH . '/config/chatbot.php';
        $this->client = new GeminiClient($this->config);
    }

    /**
     * Endpoint AJAX : reçoit un message JSON, appelle Gemini, renvoie la réponse.
     *
     * Format attendu (POST JSON ou form-encoded) :
     *   { "message": "Comment cuire un riz ?" }
     *
     * Format de réponse :
     *   { "success": true, "reply": "Pour cuire un riz parfait..." }
     *   ou
     *   { "success": false, "error": "message d'erreur" }
     */
    public function send(): void
    {
        // Acceptation JSON ou form-encoded
        $message = '';
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $data = json_decode($raw, true);
            $message = $data['message'] ?? '';
        } else {
            $message = $_POST['message'] ?? '';
        }
        $message = trim((string)$message);

        // Validation
        if ($message === '') {
            $this->json(['success' => false, 'error' => 'Message vide.'], 400);
        }
        $maxLen = (int)($this->config['max_message_length'] ?? 1000);
        if (mb_strlen($message) > $maxLen) {
            $this->json([
                'success' => false,
                'error'   => "Message trop long (max $maxLen caractères).",
            ], 413);
        }

        // Récupération de l'historique en session
        $history = $_SESSION['_chat_history'] ?? [];

        // Appel à Gemini
        $result = $this->client->chat($message, $history);

        if (!$result['success']) {
            $this->json([
                'success' => false,
                'error'   => $result['error'] ?? 'Erreur inconnue.',
            ], 500);
        }

        // Stockage dans l'historique de session
        $history[] = ['role' => 'user',  'text' => $message];
        $history[] = ['role' => 'model', 'text' => $result['reply']];

        // Tronquer l'historique si trop long
        $maxHistory = (int)($this->config['max_history'] ?? 10) * 2;
        if (count($history) > $maxHistory) {
            $history = array_slice($history, -$maxHistory);
        }
        $_SESSION['_chat_history'] = $history;

        $this->json([
            'success' => true,
            'reply'   => $result['reply'],
        ]);
    }

    /** Vide l'historique de la conversation. */
    public function reset(): void
    {
        unset($_SESSION['_chat_history']);
        $this->json(['success' => true]);
    }
}
