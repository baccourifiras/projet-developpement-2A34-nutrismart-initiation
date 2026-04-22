<?php
echo "=== VERIFICATION DATABASE ===\n\n";

$mysqli = new mysqli("127.0.0.1", "root", "", "nutrismart");
if ($mysqli->connect_error) {
    echo "ERROR: " . $mysqli->connect_error;
    exit(1);
}

// Check regime table
echo "REGIME TABLE:\n";
$r = $mysqli->query("SELECT * FROM regime");
$count = 0;
while ($row = $r->fetch_assoc()) {
    echo "ID: " . $row['id_regime'] . " | Type: " . $row['type_regime'] . " | Cals: " . $row['calories_cible'] . "\n";
    $count++;
}
echo "Total records: $count\n\n";

// Check schema
echo "REGIME COLUMNS:\n";
$r = $mysqli->query("SHOW COLUMNS FROM regime");
while ($row = $r->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\nSUIVI_REGIME COLUMNS:\n";
$r = $mysqli->query("SHOW COLUMNS FROM suivi_regime");
while ($row = $r->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\nHISTORIQUE_RECOMMANDATION COLUMNS:\n";
$r = $mysqli->query("SHOW COLUMNS FROM historique_recommandation");
while ($row = $r->fetch_assoc()) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

// Try insert
echo "\n\n=== TEST INSERT ===\n";
$stmt = $mysqli->prepare("INSERT INTO regime (type_regime, calories_cible, date_debut, poids_initial, duree) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    echo "PREPARE ERROR: " . $mysqli->error . "\n";
    exit(1);
}

$type_regime = 'cut';
$calories_cible = 2000;
$date_debut = '2026-04-12';
$poids_initial = 75.5;
$duree = 30;

// bind_param: 's' = string, 'i' = int, 'd' = double
// Format pour: type_regime(s), calories_cible(i), date_debut(s), poids_initial(d), duree(i)
$stmt->bind_param("sisdi", $type_regime, $calories_cible, $date_debut, $poids_initial, $duree);

echo "Params: type=$type_regime, cal=$calories_cible, date=$date_debut, poids=$poids_initial, duree=$duree\n";

if ($stmt->execute()) {
    echo "SUCCESS! New ID: " . $mysqli->insert_id . "\n";
} else {
    echo "EXECUTE ERROR: " . $stmt->error . "\n";
}

$stmt->close();
$mysqli->close();
?>
