<?php
/**
 * UploadController — gère l'upload d'images pour les événements.
 * Retourne un JSON avec l'URL de l'image uploadée.
 *
 * Sécurité :
 * - Vérifie le type MIME réel (pas seulement l'extension)
 * - Limite la taille à 5 Mo
 * - Génère un nom de fichier unique
 * - Stocke dans /uploads/events/
 */

header('Content-Type: application/json; charset=utf-8');

// Dossier de destination (relatif à la racine du projet)
$uploadDir = __DIR__ . '/../uploads/events/';
$uploadUrl = '/nutrismart_evenement/uploads/events/';

// Créer le dossier si nécessaire
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Vérifier qu'un fichier a été envoyé
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    $code = isset($_FILES['image']) ? $_FILES['image']['error'] : 0;
    http_response_code(400);
    echo json_encode(['error' => 'Aucun fichier reçu (code ' . $code . ').']);
    exit;
}

$file = $_FILES['image'];

// Limite de taille : 5 Mo
$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['error' => 'Fichier trop volumineux (max 5 Mo).']);
    exit;
}

// Vérifier le type MIME réel
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
$allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if (!in_array($mimeType, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Type de fichier non autorisé. Utilisez JPG, PNG, WEBP ou GIF.']);
    exit;
}

// Extension sécurisée basée sur le MIME
$extensions = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];
$ext = $extensions[$mimeType];

// Nom de fichier unique
$filename = 'event_' . uniqid('', true) . '.' . $ext;
$dest     = $uploadDir . $filename;

// Déplacer le fichier
if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la sauvegarde du fichier.']);
    exit;
}

// Succès — retourner l'URL
echo json_encode([
    'url'      => $uploadUrl . $filename,
    'filename' => $filename,
]);
exit;
?>
