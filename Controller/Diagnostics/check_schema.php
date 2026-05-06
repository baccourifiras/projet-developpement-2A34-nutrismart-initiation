<?php
$m = new mysqli('127.0.0.1', 'root', '', 'nutrismart');
if ($m->connect_error) {
    echo "ERROR: " . $m->connect_error;
    exit(1);
}

echo "=== REGIME ===\n";
$r = $m->query('SHOW COLUMNS FROM regime');
while($row = $r->fetch_assoc()){ 
    echo $row['Field'].'|'.$row['Type'].'|'.$row['Null'].'|'.$row['Default']."\n";
}

echo "\n=== SUIVI_REGIME ===\n";
$r = $m->query('SHOW COLUMNS FROM suivi_regime');
while($row = $r->fetch_assoc()){ 
    echo $row['Field'].'|'.$row['Type'].'|'.$row['Null'].'|'.$row['Default']."\n";
}

echo "\n=== HISTORIQUE_RECOMMANDATION ===\n";
$r = $m->query('SHOW COLUMNS FROM historique_recommandation');
while($row = $r->fetch_assoc()){ 
    echo $row['Field'].'|'.$row['Type'].'|'.$row['Null'].'|'.$row['Default']."\n";
}
?>
