<?php
/**
 * ChatbotController.php — Chatbot NutriSmart propulsé par Gemini
 *
 * Le chatbot connaît :
 * - Les événements réels de la BDD (titre, date, lieu, places)
 * - Les catégories disponibles
 * - Le contexte NutriSmart (nutrition, bien-être, régimes)
 *
 * Il répond UNIQUEMENT sur NutriSmart et la nutrition.
 * Pour toute autre question, il redirige poliment.
 *
 * Usage : POST /nutrismart_evenement/Controller/ChatbotController.php
 * Body  : JSON { "message": "...", "history": [...] }
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/EventController.php';
require_once __DIR__ . '/CategoryController.php';

// ── Clé API Gemini (même clé que GeminiController) ──────────
define('CB_GEMINI_KEY', 'AIzaSyAtnvMxI13TCePbPFKpfb5n9rL5TlZ50rk');
define('CB_GEMINI_MODEL', 'gemini-2.5-flash');
define('CB_GEMINI_URL',
    'https://generativelanguage.googleapis.com/v1beta/models/'
    . CB_GEMINI_MODEL
    . ':generateContent?key='
    . CB_GEMINI_KEY
);
// ────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Message manquant.']);
    exit;
}

$userMessage = trim((string) $data['message']);
$history     = isset($data['history']) && is_array($data['history']) ? $data['history'] : [];

// Limiter l'historique aux 10 derniers échanges pour ne pas dépasser les tokens
$history = array_slice($history, -10);

// ── Charger les données réelles de la BDD ───────────────────
$contextEvents     = '';
$contextCategories = '';

try {
    $pdo = config::getConnexion();
    $eventCtrl    = new EventController($pdo);
    $categoryCtrl = new CategoryController($pdo);

    $events     = $eventCtrl->getAll();
    $categories = $categoryCtrl->getAll();

    // Construire le contexte des catégories
    if (!empty($categories)) {
        $contextCategories = "Catégories disponibles sur NutriSmart :\n";
        foreach ($categories as $cat) {
            $contextCategories .= '- ' . $cat['name'];
            if (!empty($cat['description'])) {
                $contextCategories .= ' : ' . $cat['description'];
            }
            $contextCategories .= "\n";
        }
    }

    // Construire le contexte des événements (prochains en priorité)
    if (!empty($events)) {
        $today = date('Y-m-d');
        // Trier : événements futurs d'abord
        usort($events, function($a, $b) use ($today) {
            $aFutur = $a['date'] >= $today ? 0 : 1;
            $bFutur = $b['date'] >= $today ? 0 : 1;
            if ($aFutur !== $bFutur) return $aFutur - $bFutur;
            return strcmp($a['date'], $b['date']);
        });

        $contextEvents = "Événements disponibles sur NutriSmart :\n";
        foreach (array_slice($events, 0, 15) as $ev) {
            // Trouver le nom de la catégorie
            $catNom = 'Non catégorisé';
            foreach ($categories as $cat) {
                if ((int)$cat['id'] === (int)$ev['categoryId']) {
                    $catNom = $cat['name'];
                    break;
                }
            }
            $dateFr = $ev['date'] ? date('d/m/Y', strtotime($ev['date'])) : 'Date à confirmer';
            $heure  = $ev['time'] ? substr($ev['time'], 0, 5) : '';
            $contextEvents .= '- "' . $ev['title'] . '"'
                . ' | Catégorie : ' . $catNom
                . ' | Date : ' . $dateFr . ($heure ? ' à ' . $heure : '')
                . ' | Lieu : ' . $ev['location']
                . ' | Places disponibles : ' . $ev['seats']
                . "\n";
        }
    } else {
        $contextEvents = "Aucun événement n'est encore programmé sur NutriSmart.\n";
    }
} catch (Throwable $e) {
    $contextEvents     = "Données des événements temporairement indisponibles.\n";
    $contextCategories = "Données des catégories temporairement indisponibles.\n";
}

// ── Prompt système — définit le comportement du chatbot ─────
$systemPrompt = <<<SYSTEM
Tu es NutriBot, l'assistant virtuel officiel de NutriSmart — une plateforme dédiée à la nutrition, au bien-être et aux événements santé.

TON RÔLE :
- Aider les utilisateurs à trouver des événements NutriSmart
- Répondre aux questions sur la nutrition, les régimes alimentaires et le bien-être
- Donner des conseils nutritionnels simples et pratiques
- Présenter les catégories et événements disponibles

TES RÈGLES STRICTES :
1. Tu réponds UNIQUEMENT sur NutriSmart, la nutrition, les régimes, le bien-être et la santé alimentaire
2. Si quelqu'un pose une question hors sujet (politique, sport, technologie, etc.), réponds poliment : "Je suis spécialisé dans la nutrition et les événements NutriSmart. Je ne peux pas répondre à cette question, mais je peux vous aider à trouver un événement ou vous donner des conseils nutritionnels !"
3. Tu es chaleureux, encourageant et professionnel
4. Tu réponds en français
5. Tes réponses sont concises (3-5 phrases maximum sauf si on te demande plus de détails)
6. Tu utilises des emojis avec modération pour rendre la conversation agréable 🥗

DONNÉES ACTUELLES DE LA PLATEFORME :

$contextCategories
$contextEvents

CONSEILS NUTRITIONNELS QUE TU PEUX DONNER :
- Conseils sur l'hydratation, les macronutriments, les vitamines
- Informations sur les régimes (végétarien, méditerranéen, cétogène, etc.)
- Conseils pour une alimentation équilibrée
- Informations sur les aliments et leurs bienfaits

Commence toujours par une réponse utile et directe. Si l'utilisateur cherche un événement, propose-lui les événements les plus pertinents de la liste ci-dessus.
SYSTEM;

// ── Construire les messages pour l'API Gemini ────────────────
$contents = [];

// Ajouter l'historique de conversation
foreach ($history as $msg) {
    if (isset($msg['role']) && isset($msg['text'])) {
        $role = $msg['role'] === 'user' ? 'user' : 'model';
        $contents[] = [
            'role'  => $role,
            'parts' => [['text' => (string) $msg['text']]],
        ];
    }
}

// Ajouter le message actuel de l'utilisateur
$contents[] = [
    'role'  => 'user',
    'parts' => [['text' => $userMessage]],
];

$payload = json_encode([
    'system_instruction' => [
        'parts' => [['text' => $systemPrompt]]
    ],
    'contents'           => $contents,
    'generationConfig'   => [
        'temperature'     => 0.8,
        'topP'            => 0.9,
        'maxOutputTokens' => 800,
        'thinkingConfig'  => ['thinkingBudget' => 0],
    ],
    'safetySettings' => [
        ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
    ],
]);

// ── Appel API Gemini ─────────────────────────────────────────
$ch = curl_init(CB_GEMINI_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_SSL_VERIFYPEER => false,  // désactivé pour XAMPP local
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Erreur réseau
if ($response === false || $curlError !== '') {
    $replyLocal = "Je ne peux pas me connecter à mon service IA en ce moment. 🙏\n\n";
    if (!empty($contextEvents) && strpos($contextEvents, 'Aucun') === false) {
        $replyLocal .= "Voici les événements disponibles :\n" . $contextEvents;
    } else {
        $replyLocal .= "Aucun événement n'est programmé pour le moment.";
    }
    echo json_encode(['reply' => $replyLocal, 'source' => 'local']);
    exit;
}

// Erreur API
if ($httpCode !== 200) {
    // Log l'erreur pour debug
    $errData = json_decode($response, true);
    $errMsg  = isset($errData['error']['message']) ? $errData['error']['message'] : 'HTTP ' . $httpCode;

    // Si quota dépassé (429), répondre avec les données locales
    if ($httpCode === 429) {
        $replyLocal = "Je suis momentanément limité par mon quota API. Voici ce que je sais :\n\n";
        if ($contextEvents !== '' && strpos($contextEvents, 'Aucun') === false) {
            $replyLocal .= "📅 **Événements disponibles :**\n" . $contextEvents;
        } else {
            $replyLocal .= "Aucun événement n'est programmé pour le moment. Revenez bientôt ! 🌿";
        }
        echo json_encode(['reply' => $replyLocal, 'source' => 'local']);
    } else {
        echo json_encode([
            'reply'  => 'Je rencontre une difficulté technique. En attendant, n\'hésitez pas à parcourir nos événements directement sur la page ! 🌿',
            'source' => 'fallback',
        ]);
    }
    exit;
}

// Parser la réponse
$result = json_decode($response, true);
$reply  = isset($result['candidates'][0]['content']['parts'][0]['text'])
    ? trim($result['candidates'][0]['content']['parts'][0]['text'])
    : '';

if ($reply === '') {
    $reply = 'Je n\'ai pas pu générer une réponse. Pouvez-vous reformuler votre question ? 😊';
}

echo json_encode(['reply' => $reply, 'source' => 'gemini']);
exit;
?>
