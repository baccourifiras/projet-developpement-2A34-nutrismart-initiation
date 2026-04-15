<?php
require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/CategoryController.php';
require_once __DIR__ . '/EventController.php';
require_once __DIR__ . '/ParticipantController.php';

class NutrismartController
{
    private $pdo;
    private $categoryController;
    private $eventController;
    private $participantController;

    public function __construct()
    {
        $this->pdo = config::getConnexion();
        $this->ensureSchema();
        $this->categoryController = new CategoryController($this->pdo);
        $this->eventController = new EventController($this->pdo);
        $this->participantController = new ParticipantController($this->pdo);
    }

    public function handleRequest()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $action = isset($_GET['action']) ? $_GET['action'] : 'all';
            $data = $this->input();

            if ($method === 'GET' && $action === 'all') {
                $this->respond($this->allData());
            }

            if ($method !== 'POST') {
                $this->respond(array('error' => 'Methode non autorisee.'), 405);
            }

            $this->dispatch($action, $data);
        } catch (InvalidArgumentException $e) {
            if ($this->pdo instanceof PDO && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->respond(array('error' => $e->getMessage()), 400);
        } catch (Throwable $e) {
            if ($this->pdo instanceof PDO && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->respond(array('error' => $e->getMessage()), 500);
        }
    }

    private function dispatch($action, $data)
    {
        switch ($action) {
            case 'addCategory':
                $this->categoryController->add($data);
                $this->respond($this->allData());
                break;
            case 'updateCategory':
                $this->categoryController->update($data);
                $this->respond($this->allData());
                break;
            case 'deleteCategory':
                $this->categoryController->delete($data);
                $this->respond($this->allData());
                break;
            case 'addEvent':
                $this->eventController->add($data);
                $this->respond($this->allData());
                break;
            case 'updateEvent':
                $this->eventController->update($data);
                $this->respond($this->allData());
                break;
            case 'deleteEvent':
                $this->eventController->delete($data);
                $this->respond($this->allData());
                break;
            case 'addParticipant':
                $this->participantController->add($data);
                $this->respond($this->allData());
                break;
            case 'updateParticipant':
                $this->participantController->update($data);
                $this->respond($this->allData());
                break;
            case 'deleteParticipant':
                $this->participantController->delete($data);
                $this->respond($this->allData());
                break;
            default:
                $this->respond(array('error' => 'Action inconnue.'), 404);
        }
    }

    private function columnExists($table, $column)
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*)
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?"
        );
        $stmt->execute(array($table, $column));
        return (int) $stmt->fetchColumn() > 0;
    }

    private function columnType($table, $column)
    {
        $stmt = $this->pdo->prepare(
            "SELECT DATA_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?"
        );
        $stmt->execute(array($table, $column));
        return $stmt->fetchColumn();
    }

    private function ensureSchema()
    {
        if (!$this->columnExists('evenement', 'heure_evenement')) {
            $this->pdo->exec("ALTER TABLE evenement ADD heure_evenement TIME NULL AFTER date_evenement");
        }
        if (!$this->columnExists('evenement', 'places')) {
            $this->pdo->exec("ALTER TABLE evenement ADD places INT NOT NULL DEFAULT 0 AFTER id_categorie");
        }
        if (!$this->columnExists('participant', 'date_inscription')) {
            $this->pdo->exec("ALTER TABLE participant ADD date_inscription DATE NULL AFTER id_evenement");
        }
        if ($this->columnType('evenement', 'image') !== 'text') {
            $this->pdo->exec("ALTER TABLE evenement MODIFY image TEXT NULL");
        }
    }

    private function input()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        return is_array($data) ? $data : $_POST;
    }

    private function respond($data, $status = 200)
    {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    private function allData()
    {
        return array(
            'categories' => $this->categoryController->getAll(),
            'events' => $this->eventController->getAll(),
            'participants' => $this->participantController->getAll()
        );
    }
}

$controller = new NutrismartController();
$controller->handleRequest();