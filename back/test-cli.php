<?php
// Test direct d'insertion via l'API
$data = [
    'type_regime' => 'cut',
    'calories_cible' => 2000,
    'date_debut' => '2026-04-12',
    'poids_initial' => 75.5,
    'duree' => 30
];

echo "=== TEST INSERTION ===\n";
echo "Données:\n";
print_r($data);
echo "\nAppel API...\n";

// Simuler l'appel API
require_once __DIR__ . '/api-regime.php';
?>
