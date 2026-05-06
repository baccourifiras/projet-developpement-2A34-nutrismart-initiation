<?php
/**
 * GeminiController.php — Proxy sécurisé pour l'API Google Gemini (gratuite)
 *
 * Google AI Studio offre un quota gratuit généreux :
 * - Gemini 2.0 Flash : 15 requêtes/minute, 1500/jour — GRATUIT
 *
 * La clé API reste côté serveur, jamais exposée au navigateur.
 *
 * Usage : POST /nutrismart_evenement/Controller/GeminiController.php
 * Body  : JSON { "titre": "...", "categorie": "...", "lieu": "...", "date": "..." }
 */

header('Content-Type: application/json; charset=utf-8');

// ============================================================
// ⚙️  CONFIGURATION — clé API Google AI Studio (GRATUITE)
// Obtenir une clé : https://aistudio.google.com/apikey
// ⚠️  Ne partagez jamais ce fichier publiquement
// ============================================================
define('GEMINI_API_KEY', 'AIzaSyAtnvMxI13TCePbPFKpfb5n9rL5TlZ50rk');
define('GEMINI_MODEL',   'gemini-2.5-flash');
define('GEMINI_API_URL',
    'https://generativelanguage.googleapis.com/v1beta/models/'
    . GEMINI_MODEL
    . ':generateContent?key='
    . GEMINI_API_KEY
);
// ============================================================

// Accepter uniquement les requêtes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée.']);
    exit;
}

// Lire le corps JSON
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Corps JSON invalide.']);
    exit;
}

// Récupérer et nettoyer les champs
$titre     = isset($data['titre'])     ? trim((string) $data['titre'])     : '';
$categorie = isset($data['categorie']) ? trim((string) $data['categorie']) : '';
$lieu      = isset($data['lieu'])      ? trim((string) $data['lieu'])      : '';
$date      = isset($data['date'])      ? trim((string) $data['date'])      : '';

if ($titre === '' || $lieu === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Le titre et le lieu sont requis.']);
    exit;
}

// Fonction fallback — description créative sans API
function descriptionLocale($titre, $categorie, $lieu) {
    $cat = ($categorie !== '') ? $categorie : 'nutrition et bien-être';
    $templates = [
        'Une expérience unique autour de %s vous attend à %s — %s promet de transformer votre rapport à la santé.',
        'Plongez au cœur de %s à %s : %s est l\'occasion idéale d\'explorer de nouvelles habitudes alimentaires.',
        'À %s, %s réunit passionnés et experts de %s pour une journée riche en découvertes et en échanges.',
        'Vivez %s différemment à %s : cet événement dédié à %s vous offre des outils concrets pour mieux vivre.',
    ];
    $i = abs(crc32($titre)) % count($templates);
    return sprintf($templates[$i], $cat, $lieu, $titre);
}

// Vérifier que la clé est configurée
if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === '' || GEMINI_API_KEY === 'VOTRE_CLE_ICI') {
    echo json_encode(['description' => descriptionLocale($titre, $categorie, $lieu), 'source' => 'local']);
    exit;
}

// Formater la date en français
$dateFr = '';
if ($date !== '') {
    $ts = strtotime($date);
    if ($ts !== false) {
        $mois = ['janvier','février','mars','avril','mai','juin',
                 'juillet','août','septembre','octobre','novembre','décembre'];
        $dateFr = (int)date('d', $ts) . ' ' . $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
    }
}

// Construire le prompt — créatif et engageant
$prompt = 'Tu es un expert en communication événementielle pour NutriSmart, une plateforme de nutrition et bien-être.'
    . ' Rédige une description d\'événement courte (2-3 phrases, 40 mots max) qui soit :'
    . ' engageante, inspirante, et qui donne envie de participer.'
    . ' Utilise un ton dynamique et positif. Mentionne le lieu et la thématique de façon naturelle.'
    . ' Évite les formules génériques comme "Rejoignez-nous" ou "Ne manquez pas".'
    . "\n\n"
    . 'Informations de l\'événement :'
    . "\n- Titre : \"" . $titre . '"'
    . ($categorie !== '' ? "\n- Thématique : " . $categorie : '')
    . "\n- Lieu : " . $lieu
    . ($dateFr !== '' ? "\n- Date : " . $dateFr : '')
    . "\n\nRéponds uniquement avec la description, sans guillemets, sans titre, sans introduction.";

// Construire le payload pour l'API Gemini
$payload = json_encode([
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature'     => 1.1,
        'topP'            => 0.95,
        'maxOutputTokens' => 500,
        'thinkingConfig'  => ['thinkingBudget' => 0],
    ],
    'safetySettings' => [
        ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_NONE'],
        ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_NONE'],
    ],
]);

// Appel à l'API Gemini via cURL
$ch = curl_init(GEMINI_API_URL);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_SSL_VERIFYPEER => false,  // désactivé pour XAMPP local
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
    ],
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Erreur réseau cURL
if ($response === false || $curlError !== '') {
    echo json_encode([
        'description' => descriptionLocale($titre, $categorie, $lieu),
        'source'      => 'local',
        'curl_error'  => $curlError,
    ]);
    exit;
}

// Erreur HTTP de l'API
if ($httpCode !== 200) {
    $errDetail = json_decode($response, true);
    $errMsg    = isset($errDetail['error']['message']) ? $errDetail['error']['message'] : 'HTTP ' . $httpCode;
    echo json_encode([
        'description' => descriptionLocale($titre, $categorie, $lieu),
        'source'      => 'local',
        'api_error'   => $errMsg,
    ]);
    exit;
}

// Parser la réponse JSON de Gemini
$result = json_decode($response, true);
$texte  = isset($result['candidates'][0]['content']['parts'][0]['text'])
    ? trim($result['candidates'][0]['content']['parts'][0]['text'])
    : '';

if ($texte === '') {
    echo json_encode(['description' => descriptionLocale($titre, $categorie, $lieu), 'source' => 'local']);
    exit;
}

// Succès — retourner la description générée par Gemini
echo json_encode(['description' => $texte, 'source' => 'gemini']);
exit;
?>
