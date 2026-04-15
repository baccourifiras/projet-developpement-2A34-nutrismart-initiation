<?php
// Configuration AVANT tout
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Pas d'affichage HTML des erreurs
ini_set('log_errors', 1);
header('Content-Type: application/json; charset=utf-8');

// Fonction pour retourner du JSON (toujours)
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    require_once __DIR__ . '/db.php';
    $pdo = getDb();
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null) {
        $input = $_POST;
    }

switch ($action) {
        case 'regimes':
            $stmt = $pdo->query('SELECT * FROM regime ORDER BY id_regime ASC');
            jsonResponse($stmt->fetchAll());
            break;
        case 'suivis':
            $stmt = $pdo->query('SELECT * FROM suivi_regime ORDER BY date DESC, id_suivi DESC');
            jsonResponse($stmt->fetchAll());
            break;
        case 'histos':
            $stmt = $pdo->query('SELECT * FROM historique_recommandation ORDER BY id_historique ASC');
            jsonResponse($stmt->fetchAll());
            break;
        case 'stats':
            $regimes = $pdo->query('SELECT COUNT(*) AS count, AVG(calories_cible) AS avg_cal FROM regime')->fetch();
            $suivis = $pdo->query('SELECT COUNT(*) AS count FROM suivi_regime')->fetch();
            $histos = $pdo->query('SELECT COUNT(*) AS count FROM historique_recommandation')->fetch();
            jsonResponse([
                'regimes' => (int)$regimes['count'],
                'suivis' => (int)$suivis['count'],
                'histos' => (int)$histos['count'],
                'avg_calories' => $regimes['avg_cal'] !== null ? round($regimes['avg_cal']) . ' kcal' : '—'
            ]);
            break;
        case 'regimeSelect':
            $stmt = $pdo->query('SELECT id_regime, type_regime, calories_cible FROM regime ORDER BY id_regime ASC');
            jsonResponse($stmt->fetchAll());
            break;
        case 'regime':
            $stmt = $pdo->prepare('INSERT INTO regime (type_regime, calories_cible, date_debut, poids_initial, duree) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([
                $input['type_regime'],
                (int)$input['calories_cible'],
                $input['date_debut'],
                (float)$input['poids_initial'],
                (int)$input['duree']
            ]);
            $newId = (int)$pdo->lastInsertId();
            $recommendation = generateRecommendation($input['type_regime'], (int)$input['calories_cible']);
            $stmt = $pdo->prepare('INSERT INTO historique_recommandation (id_regime, recommandation, date) VALUES (?, ?, CURRENT_DATE())');
            $stmt->execute([$newId, $recommendation]);
            jsonResponse(['success' => true, 'newId' => $newId]);
            break;
        case 'suivi':
            $stmt = $pdo->prepare('INSERT INTO suivi_regime (id_regime, date, poids, calories_consommees) VALUES (?, ?, ?, ?)');
            $stmt->execute([
                (int)$input['id_regime'],
                $input['date'],
                (float)$input['poids'],
                (int)$input['calories_consommees']
            ]);
            $newId = (int)$pdo->lastInsertId();
            jsonResponse(['success' => true, 'newId' => $newId]);
            break;
        case 'getSuivi':
            $stmt = $pdo->prepare('SELECT * FROM suivi_regime WHERE id_suivi = ?');
            $stmt->execute([(int)$_GET['id']]);
            $suivi = $stmt->fetch();
            jsonResponse($suivi ?: ['error' => 'Suivi non trouvé'], $suivi ? 200 : 404);
            break;
        case 'editSuivi':
            $stmt = $pdo->prepare('UPDATE suivi_regime SET id_regime = ?, date = ?, poids = ?, calories_consommees = ? WHERE id_suivi = ?');
            $stmt->execute([
                (int)$input['id_regime'],
                $input['date'],
                (float)$input['poids'],
                (int)$input['calories_consommees'],
                (int)$input['id_suivi']
            ]);
            jsonResponse(['success' => true]);
            break;
        case 'histo':
            $stmt = $pdo->prepare('INSERT INTO historique_recommandation (id_regime, recommandation, date) VALUES (?, ?, CURRENT_DATE())');
            $stmt->execute([
                (int)$input['id_regime'],
                trim($input['recommandation'])
            ]);
            jsonResponse(['success' => true]);
            break;
        case 'editRegime':
            $stmt = $pdo->prepare('UPDATE regime SET type_regime = ?, calories_cible = ?, date_debut = ?, poids_initial = ?, duree = ? WHERE id_regime = ?');
            $stmt->execute([
                $input['type_regime'],
                (int)$input['calories_cible'],
                $input['date_debut'],
                (float)$input['poids_initial'],
                (int)$input['duree'],
                (int)$input['id_regime']
            ]);
            jsonResponse(['success' => true]);
            break;
        case 'editHisto':
            $stmt = $pdo->prepare('UPDATE historique_recommandation SET recommandation = ? WHERE id_historique = ?');
            $stmt->execute([
                trim($input['recommandation']),
                (int)$input['id_historique']
            ]);
            jsonResponse(['success' => true]);
            break;
        case 'delete':
            $type = isset($input['type']) ? $input['type'] : '';
            $id = isset($input['id']) ? (int)$input['id'] : 0;
            if ($type === 'regime') {
                $stmt = $pdo->prepare('DELETE FROM regime WHERE id_regime = ?');
                $stmt->execute([$id]);
            } elseif ($type === 'suivi') {
                $stmt = $pdo->prepare('DELETE FROM suivi_regime WHERE id_suivi = ?');
                $stmt->execute([$id]);
            } elseif ($type === 'histo') {
                $stmt = $pdo->prepare('DELETE FROM historique_recommandation WHERE id_historique = ?');
                $stmt->execute([$id]);
            }
            jsonResponse(['success' => true]);
            break;
        case 'reset':
            $pdo->exec('DELETE FROM historique_recommandation');
            $pdo->exec('DELETE FROM suivi_regime');
            $pdo->exec('DELETE FROM regime');
            jsonResponse(['success' => true]);
            break;
        default:
            jsonResponse(['error' => 'Action invalide'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => $e->getMessage()], 500);
}

function generateRecommendation($type, $calories) {
    if ($type === 'cut') {
        return 'Maintenez un déficit calorique de ' . ($calories + 300) . ' kcal. Privilégiez les protéines maigres et les légumes.';
    }
    if ($type === 'bulk') {
        return 'Visez un surplus de 200-300 kcal/j. Consommez des protéines (1,6-2,2 g/kg) et des glucides complexes.';
    }
    return 'Répartissez vos macros : 50% glucides, 25% protéines, 25% lipides. Hydratez-vous bien.';
}
