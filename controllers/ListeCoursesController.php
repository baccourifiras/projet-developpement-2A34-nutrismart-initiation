<?php
require_once __DIR__ . '/../models/ListeCourses.php';
require_once __DIR__ . '/../models/Stock.php';

class ListeCoursesController {
    private ListeCourses $model;
    private Stock        $stockModel;

    public function __construct() {
        $this->model      = new ListeCourses();
        $this->stockModel = new Stock();
    }

    public function index(): void {
        $listes = $this->model->getAll();
        require __DIR__ . '/../views/liste_courses/index.php';
    }

    public function create(): void {
        $errors = [];
        $data   = [];
        $stocks = $this->stockModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->create($data);
                header('Location: index.php?page=liste_courses&success=created');
                exit;
            }
        }
        require __DIR__ . '/../views/liste_courses/create.php';
    }

    public function edit(): void {
        $id     = (int)($_GET['id'] ?? 0);
        $liste  = $this->model->getById($id);
        $stocks = $this->stockModel->getAll();
        if (!$liste) { header('Location: index.php?page=liste_courses&error=notfound'); exit; }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->update($id, $data);
                header('Location: index.php?page=liste_courses&success=updated');
                exit;
            }
        }
        require __DIR__ . '/../views/liste_courses/edit.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) $this->model->delete($id);
        header('Location: index.php?page=liste_courses&success=deleted');
        exit;
    }

    private function sanitize(array $p): array {
        return [
            'articles_a_acheter' => trim($p['articles_a_acheter'] ?? ''),
            'budget'             => trim($p['budget'] ?? ''),
            'date_creation'      => trim($p['date_creation'] ?? ''),
            'stock_id'           => trim($p['stock_id'] ?? ''),
        ];
    }

    private function validate(array $d): array {
        $errors = [];
        if (empty($d['articles_a_acheter']))
            $errors['articles_a_acheter'] = 'Les articles sont obligatoires.';
        elseif (strlen($d['articles_a_acheter']) < 2 || strlen($d['articles_a_acheter']) > 255)
            $errors['articles_a_acheter'] = 'Entre 2 et 255 caractères requis.';

        if ($d['budget'] === '')
            $errors['budget'] = 'Le budget est obligatoire.';
        elseif (!is_numeric($d['budget']) || (float)$d['budget'] <= 0)
            $errors['budget'] = 'Le budget doit être un nombre positif.';
        elseif ((float)$d['budget'] > 99999)
            $errors['budget'] = 'Le budget ne peut pas dépasser 99 999.';

        if (empty($d['date_creation']))
            $errors['date_creation'] = 'La date de création est obligatoire.';
        else {
            $dt = DateTime::createFromFormat('Y-m-d', $d['date_creation']);
            if (!$dt) $errors['date_creation'] = 'Format de date invalide.';
        }
        return $errors;
    }
}
