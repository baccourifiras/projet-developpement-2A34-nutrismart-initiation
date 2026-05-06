<?php
// Test d'insertion dans la base de données
header('Content-Type: application/json; charset=utf-8');

$data = [
    'type_regime' => 'cut',
    'calories_cible' => 2000,
    'date_debut' => '2026-04-12',
    'poids_initial' => 75.5,
    'duree' => 30
];

$ch = curl_init('http://localhost/files/Controller/api-regime.php?action=regime');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response:\n";
echo $response ? $response : "Erreur: $error";
?>
