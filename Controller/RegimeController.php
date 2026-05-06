<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/Model/Database.php';
require_once dirname(__DIR__) . '/Model/Regime.php';
require_once dirname(__DIR__) . '/Model/SuiviRegime.php';
require_once dirname(__DIR__) . '/Model/HistoriqueRecommandation.php';

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getRequestInput(): array
{
    $rawInput = file_get_contents('php://input');

    if ($rawInput !== false && $rawInput !== '') {
        $decoded = json_decode($rawInput, true);

        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return $_POST;
}

function requirePositiveInt(array $input, string $field): int
{
    if (!isset($input[$field]) || filter_var($input[$field], FILTER_VALIDATE_INT) === false) {
        throw new InvalidArgumentException('Champ entier invalide: ' . $field);
    }

    $value = (int) $input[$field];

    if ($value <= 0) {
        throw new InvalidArgumentException('Valeur invalide pour ' . $field . '.');
    }

    return $value;
}

function requireIntRange(array $input, string $field, int $min, int $max): int
{
    if (!isset($input[$field]) || filter_var($input[$field], FILTER_VALIDATE_INT) === false) {
        throw new InvalidArgumentException('Champ entier invalide: ' . $field);
    }

    $value = (int) $input[$field];

    if ($value < $min || $value > $max) {
        throw new InvalidArgumentException('Valeur hors plage pour ' . $field . '.');
    }

    return $value;
}

function requireFloatRange(array $input, string $field, float $min, float $max): float
{
    if (!isset($input[$field]) || !is_numeric($input[$field])) {
        throw new InvalidArgumentException('Champ numerique invalide: ' . $field);
    }

    $value = (float) $input[$field];

    if ($value < $min || $value > $max) {
        throw new InvalidArgumentException('Valeur hors plage pour ' . $field . '.');
    }

    return $value;
}

function requireDateValue(array $input, string $field): string
{
    $value = isset($input[$field]) ? trim((string) $input[$field]) : '';
    $date = DateTime::createFromFormat('Y-m-d', $value);

    if (!$date || $date->format('Y-m-d') !== $value) {
        throw new InvalidArgumentException('Date invalide pour ' . $field . '.');
    }

    return $value;
}

function buildRegimeFromInput(array $input): Regime
{
    $typeRegime = isset($input['type_regime']) ? strtolower(trim((string) $input['type_regime'])) : '';
    $allowedTypes = ['cut', 'bulk', 'equilibre'];

    if (!in_array($typeRegime, $allowedTypes, true)) {
        throw new InvalidArgumentException('Type de regime invalide.');
    }

    return new Regime(
        null,
        $typeRegime,
        requireIntRange($input, 'calories_cible', 500, 6000),
        requireDateValue($input, 'date_debut'),
        requireFloatRange($input, 'poids_initial', 20, 300),
        requireIntRange($input, 'duree', 1, 365)
    );
}

function buildSuiviFromInput(array $input): SuiviRegime
{
    return new SuiviRegime(
        null,
        requirePositiveInt($input, 'id_regime'),
        requireDateValue($input, 'date'),
        requireFloatRange($input, 'poids', 20, 300),
        requireIntRange($input, 'calories_consommees', 0, 10000)
    );
}

function buildHistoriqueFromInput(array $input, bool $requireRegime = true): HistoriqueRecommandation
{
    $idRegime = $requireRegime ? requirePositiveInt($input, 'id_regime') : (int) ($input['id_regime'] ?? 0);
    $recommandation = isset($input['recommandation']) ? trim((string) $input['recommandation']) : '';

    if ($recommandation === '') {
        throw new InvalidArgumentException('La recommandation est obligatoire.');
    }

    return new HistoriqueRecommandation(null, $idRegime, $recommandation, date('Y-m-d'));
}

function mapRegimeRow(array $row): Regime
{
    return new Regime(
        isset($row['id_regime']) ? (int) $row['id_regime'] : null,
        (string) $row['type_regime'],
        (int) $row['calories_cible'],
        (string) $row['date_debut'],
        (float) $row['poids_initial'],
        (int) $row['duree']
    );
}

function mapSuiviRow(array $row): SuiviRegime
{
    return new SuiviRegime(
        isset($row['id_suivi']) ? (int) $row['id_suivi'] : null,
        (int) $row['id_regime'],
        (string) $row['date'],
        (float) $row['poids'],
        (int) $row['calories_consommees']
    );
}

function mapHistoriqueRow(array $row): HistoriqueRecommandation
{
    return new HistoriqueRecommandation(
        isset($row['id_historique']) ? (int) $row['id_historique'] : null,
        (int) $row['id_regime'],
        (string) $row['recommandation'],
        (string) $row['date']
    );
}

function regimeToArray(Regime $regime): array
{
    return $regime->toArray();
}

function suiviToArray(SuiviRegime $suivi): array
{
    return $suivi->toArray();
}

function historiqueToArray(HistoriqueRecommandation $historique): array
{
    return $historique->toArray();
}

function findAllRegimes(PDO $pdo): array
{
    $statement = $pdo->query('SELECT * FROM regime ORDER BY id_regime ASC');
    return array_map('mapRegimeRow', $statement->fetchAll());
}

function findRegimeById(PDO $pdo, int $idRegime): ?Regime
{
    $statement = $pdo->prepare('SELECT * FROM regime WHERE id_regime = :id_regime');
    $statement->execute(['id_regime' => $idRegime]);
    $row = $statement->fetch();

    return $row ? mapRegimeRow($row) : null;
}

function findAllSuivis(PDO $pdo): array
{
    $statement = $pdo->query('SELECT * FROM suivi_regime ORDER BY date DESC, id_suivi DESC');
    return array_map('mapSuiviRow', $statement->fetchAll());
}

function findSuiviById(PDO $pdo, int $idSuivi): ?SuiviRegime
{
    $statement = $pdo->prepare('SELECT * FROM suivi_regime WHERE id_suivi = :id_suivi');
    $statement->execute(['id_suivi' => $idSuivi]);
    $row = $statement->fetch();

    return $row ? mapSuiviRow($row) : null;
}

function findAllHistoriques(PDO $pdo): array
{
    $statement = $pdo->query('SELECT * FROM historique_recommandation ORDER BY id_historique DESC');
    return array_map('mapHistoriqueRow', $statement->fetchAll());
}

function findHistoriqueById(PDO $pdo, int $idHistorique): ?HistoriqueRecommandation
{
    $statement = $pdo->prepare('SELECT * FROM historique_recommandation WHERE id_historique = :id_historique');
    $statement->execute(['id_historique' => $idHistorique]);
    $row = $statement->fetch();

    return $row ? mapHistoriqueRow($row) : null;
}

function assertRegimeExists(PDO $pdo, int $idRegime): void
{
    if (!findRegimeById($pdo, $idRegime)) {
        throw new InvalidArgumentException('Regime introuvable.');
    }
}

function assertSuiviExists(PDO $pdo, int $idSuivi): void
{
    if (!findSuiviById($pdo, $idSuivi)) {
        throw new InvalidArgumentException('Suivi introuvable.');
    }
}

function assertHistoriqueExists(PDO $pdo, int $idHistorique): void
{
    if (!findHistoriqueById($pdo, $idHistorique)) {
        throw new InvalidArgumentException('Historique introuvable.');
    }
}

function createRegime(PDO $pdo, Regime $regime): int
{
    $statement = $pdo->prepare(
        'INSERT INTO regime (type_regime, calories_cible, date_debut, poids_initial, duree)
         VALUES (:type_regime, :calories_cible, :date_debut, :poids_initial, :duree)'
    );

    $statement->execute([
        'type_regime' => $regime->getTypeRegime(),
        'calories_cible' => $regime->getCaloriesCible(),
        'date_debut' => $regime->getDateDebut(),
        'poids_initial' => $regime->getPoidsInitial(),
        'duree' => $regime->getDuree(),
    ]);

    $newId = (int) $pdo->lastInsertId();
    $regime->setIdRegime($newId);

    return $newId;
}

function updateRegime(PDO $pdo, Regime $regime): void
{
    $statement = $pdo->prepare(
        'UPDATE regime
         SET type_regime = :type_regime,
             calories_cible = :calories_cible,
             date_debut = :date_debut,
             poids_initial = :poids_initial,
             duree = :duree
         WHERE id_regime = :id_regime'
    );

    $statement->execute([
        'id_regime' => $regime->getIdRegime(),
        'type_regime' => $regime->getTypeRegime(),
        'calories_cible' => $regime->getCaloriesCible(),
        'date_debut' => $regime->getDateDebut(),
        'poids_initial' => $regime->getPoidsInitial(),
        'duree' => $regime->getDuree(),
    ]);
}

function deleteRegime(PDO $pdo, int $idRegime): void
{
    $statement = $pdo->prepare('DELETE FROM regime WHERE id_regime = :id_regime');
    $statement->execute(['id_regime' => $idRegime]);
}

function createSuivi(PDO $pdo, SuiviRegime $suivi): int
{
    $statement = $pdo->prepare(
        'INSERT INTO suivi_regime (id_regime, date, poids, calories_consommees)
         VALUES (:id_regime, :date, :poids, :calories_consommees)'
    );

    $statement->execute([
        'id_regime' => $suivi->getIdRegime(),
        'date' => $suivi->getDate(),
        'poids' => $suivi->getPoids(),
        'calories_consommees' => $suivi->getCaloriesConsommees(),
    ]);

    $newId = (int) $pdo->lastInsertId();
    $suivi->setIdSuivi($newId);

    return $newId;
}

function updateSuivi(PDO $pdo, SuiviRegime $suivi): void
{
    $statement = $pdo->prepare(
        'UPDATE suivi_regime
         SET id_regime = :id_regime,
             date = :date,
             poids = :poids,
             calories_consommees = :calories_consommees
         WHERE id_suivi = :id_suivi'
    );

    $statement->execute([
        'id_suivi' => $suivi->getIdSuivi(),
        'id_regime' => $suivi->getIdRegime(),
        'date' => $suivi->getDate(),
        'poids' => $suivi->getPoids(),
        'calories_consommees' => $suivi->getCaloriesConsommees(),
    ]);
}

function deleteSuivi(PDO $pdo, int $idSuivi): void
{
    $statement = $pdo->prepare('DELETE FROM suivi_regime WHERE id_suivi = :id_suivi');
    $statement->execute(['id_suivi' => $idSuivi]);
}

function createHistorique(PDO $pdo, HistoriqueRecommandation $historique): int
{
    $statement = $pdo->prepare(
        'INSERT INTO historique_recommandation (id_regime, recommandation, date)
         VALUES (:id_regime, :recommandation, :date)'
    );

    $statement->execute([
        'id_regime' => $historique->getIdRegime(),
        'recommandation' => $historique->getRecommandation(),
        'date' => $historique->getDate(),
    ]);

    $newId = (int) $pdo->lastInsertId();
    $historique->setIdHistorique($newId);

    return $newId;
}

function updateHistorique(PDO $pdo, HistoriqueRecommandation $historique): void
{
    $statement = $pdo->prepare(
        'UPDATE historique_recommandation
         SET recommandation = :recommandation
         WHERE id_historique = :id_historique'
    );

    $statement->execute([
        'id_historique' => $historique->getIdHistorique(),
        'recommandation' => $historique->getRecommandation(),
    ]);
}

function deleteHistorique(PDO $pdo, int $idHistorique): void
{
    $statement = $pdo->prepare('DELETE FROM historique_recommandation WHERE id_historique = :id_historique');
    $statement->execute(['id_historique' => $idHistorique]);
}

function getRegimeStats(PDO $pdo): array
{
    $regimeRow = $pdo->query(
        'SELECT COUNT(*) AS total_regimes, AVG(calories_cible) AS moyenne_calories FROM regime'
    )->fetch();

    $suiviRow = $pdo->query('SELECT COUNT(*) AS total_suivis FROM suivi_regime')->fetch();
    $historiqueRow = $pdo->query('SELECT COUNT(*) AS total_historiques FROM historique_recommandation')->fetch();

    return [
        'regimes' => (int) ($regimeRow['total_regimes'] ?? 0),
        'suivis' => (int) ($suiviRow['total_suivis'] ?? 0),
        'histos' => (int) ($historiqueRow['total_historiques'] ?? 0),
        'avg_calories' => isset($regimeRow['moyenne_calories']) && $regimeRow['moyenne_calories'] !== null
            ? (int) round((float) $regimeRow['moyenne_calories']) . ' kcal'
            : '-',
    ];
}

function getRegimeSelectOptions(PDO $pdo): array
{
    $statement = $pdo->query('SELECT id_regime, type_regime, calories_cible FROM regime ORDER BY id_regime ASC');
    return $statement->fetchAll();
}

function generateRecommendation(Regime $regime): string
{
    if ($regime->getTypeRegime() === 'cut') {
        return 'Maintenez un deficit modere et privilegiez les proteines maigres.';
    }

    if ($regime->getTypeRegime() === 'bulk') {
        return 'Visez un surplus progressif avec proteines et glucides complexes.';
    }

    return 'Equilibrez glucides, proteines et lipides avec une bonne hydratation.';
}

function handleDelete(PDO $pdo, array $input): void
{
    $type = isset($input['type']) ? (string) $input['type'] : '';
    $id = requirePositiveInt($input, 'id');

    if ($type === 'regime') {
        assertRegimeExists($pdo, $id);
        deleteRegime($pdo, $id);
        return;
    }

    if ($type === 'suivi') {
        assertSuiviExists($pdo, $id);
        deleteSuivi($pdo, $id);
        return;
    }

    if ($type === 'histo') {
        assertHistoriqueExists($pdo, $id);
        deleteHistorique($pdo, $id);
        return;
    }

    throw new InvalidArgumentException('Type de suppression invalide.');
}

function handleRegimeApi(): void
{
    try {
        $pdo = Database::getConnection();
        $action = isset($_GET['action']) ? (string) $_GET['action'] : '';

        switch ($action) {
            case 'regimes':
                jsonResponse(array_map('regimeToArray', findAllRegimes($pdo)));
                break;

            case 'suivis':
                jsonResponse(array_map('suiviToArray', findAllSuivis($pdo)));
                break;

            case 'histos':
                jsonResponse(array_map('historiqueToArray', findAllHistoriques($pdo)));
                break;

            case 'stats':
                jsonResponse(getRegimeStats($pdo));
                break;

            case 'regimeSelect':
                jsonResponse(getRegimeSelectOptions($pdo));
                break;

            case 'regime':
                $input = getRequestInput();
                $regime = buildRegimeFromInput($input);
                $pdo->beginTransaction();
                $newId = createRegime($pdo, $regime);
                createHistorique(
                    $pdo,
                    new HistoriqueRecommandation(null, $newId, generateRecommendation($regime), date('Y-m-d'))
                );
                $pdo->commit();
                jsonResponse(['success' => true, 'newId' => $newId], 201);
                break;

            case 'editRegime':
                $input = getRequestInput();
                $regime = buildRegimeFromInput($input);
                $regime->setIdRegime(requirePositiveInt($input, 'id_regime'));
                assertRegimeExists($pdo, (int) $regime->getIdRegime());
                updateRegime($pdo, $regime);
                jsonResponse(['success' => true]);
                break;

            case 'suivi':
                $input = getRequestInput();
                $suivi = buildSuiviFromInput($input);
                assertRegimeExists($pdo, $suivi->getIdRegime());
                $newId = createSuivi($pdo, $suivi);
                jsonResponse(['success' => true, 'newId' => $newId], 201);
                break;

            case 'getSuivi':
                $idSuivi = isset($_GET['id']) ? (int) $_GET['id'] : 0;
                if ($idSuivi <= 0) {
                    throw new InvalidArgumentException('ID de suivi invalide.');
                }

                $suivi = findSuiviById($pdo, $idSuivi);
                if (!$suivi) {
                    jsonResponse(['error' => 'Suivi introuvable.'], 404);
                }

                jsonResponse($suivi->toArray());
                break;

            case 'editSuivi':
                $input = getRequestInput();
                $suivi = buildSuiviFromInput($input);
                $suivi->setIdSuivi(requirePositiveInt($input, 'id_suivi'));
                assertSuiviExists($pdo, (int) $suivi->getIdSuivi());
                assertRegimeExists($pdo, $suivi->getIdRegime());
                updateSuivi($pdo, $suivi);
                jsonResponse(['success' => true]);
                break;

            case 'histo':
                $input = getRequestInput();
                $historique = buildHistoriqueFromInput($input);
                assertRegimeExists($pdo, $historique->getIdRegime());
                $newId = createHistorique($pdo, $historique);
                jsonResponse(['success' => true, 'newId' => $newId], 201);
                break;

            case 'editHisto':
                $input = getRequestInput();
                $historique = buildHistoriqueFromInput($input, false);
                $historique->setIdHistorique(requirePositiveInt($input, 'id_historique'));
                assertHistoriqueExists($pdo, (int) $historique->getIdHistorique());
                updateHistorique($pdo, $historique);
                jsonResponse(['success' => true]);
                break;

            case 'delete':
                handleDelete($pdo, getRequestInput());
                jsonResponse(['success' => true]);
                break;

            case 'reset':
                $pdo->exec('DELETE FROM historique_recommandation');
                $pdo->exec('DELETE FROM suivi_regime');
                $pdo->exec('DELETE FROM regime');
                jsonResponse(['success' => true]);
                break;

            default:
                jsonResponse(['error' => 'Action invalide.'], 400);
        }
    } catch (InvalidArgumentException $exception) {
        jsonResponse(['error' => $exception->getMessage()], 422);
    } catch (Throwable $exception) {
        jsonResponse(['error' => $exception->getMessage()], 500);
    }
}
