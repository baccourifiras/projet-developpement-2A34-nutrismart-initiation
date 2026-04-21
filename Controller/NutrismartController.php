<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            $action = isset($_GET['action']) ? $_GET['action'] : 'all';
            $data = $this->input();

            if ($method !== 'POST') {
                $this->redirectBack();
            }

            $success = $this->dispatch($action, $data);
            $this->redirectBack('', $success);
        } catch (InvalidArgumentException $e) {
            if ($this->pdo instanceof PDO && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->redirectBack($e->getMessage());
            }
            $this->redirectBack($e->getMessage());
        } catch (Throwable $e) {
            if ($this->pdo instanceof PDO && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->redirectBack($e->getMessage());
            }
            $this->redirectBack($e->getMessage());
        }
    }

    private function dispatch($action, $data)
    {
        switch ($action) {
            case 'addCategory':
                $this->categoryController->add($data);
                return 'add';
            case 'updateCategory':
                $this->categoryController->update($data);
                return 'edit';
            case 'deleteCategory':
                $this->categoryController->delete($data);
                return 'delete';
            case 'addEvent':
                $this->eventController->add($data);
                return 'add';
            case 'updateEvent':
                $this->eventController->update($data);
                return 'edit';
            case 'deleteEvent':
                $this->eventController->delete($data);
                return 'delete';
            case 'addParticipant':
                $this->participantController->add($data);
                return 'add';
            case 'updateParticipant':
                $this->participantController->update($data);
                return 'edit';
            case 'deleteParticipant':
                $this->participantController->delete($data);
                return 'delete';
            default:
                throw new InvalidArgumentException('Action inconnue.');
        }
    }

    private function redirectBack($error = '', $success = '')
    {
        $target = isset($_POST['redirect']) && $_POST['redirect'] !== ''
            ? $_POST['redirect']
            : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '../View/FrontOffice/index.php');

        $target = $this->cleanRedirectUrl($target);

        if ($error !== '') {
            $_SESSION['flash_error'] = $error;
            $separator = strpos($target, '?') === false ? '?' : '&';
            $target .= $separator . 'error=' . urlencode($error);
        } elseif ($success !== '') {
            $_SESSION['flash_success'] = $success;
            $separator = strpos($target, '?') === false ? '?' : '&';
            $target .= $separator . 'success=' . urlencode($success);
        }

        header('Location: ' . $target);
        exit;
    }

    private function cleanRedirectUrl($target)
    {
        $parts = parse_url($target);
        if ($parts === false) {
            return $target;
        }

        $query = array();
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            unset($query['success'], $query['error']);
        }

        $clean = '';
        if (isset($parts['scheme'])) {
            $clean .= $parts['scheme'] . '://';
        }
        if (isset($parts['host'])) {
            $clean .= $parts['host'];
        }
        if (isset($parts['port'])) {
            $clean .= ':' . $parts['port'];
        }
        $clean .= isset($parts['path']) ? $parts['path'] : '';

        $queryString = http_build_query($query);
        if ($queryString !== '') {
            $clean .= '?' . $queryString;
        }
        if (isset($parts['fragment'])) {
            $clean .= '#' . $parts['fragment'];
        }

        return $clean;
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
        return $_POST;
    }

}

$controller = new NutrismartController();
$controller->handleRequest();
