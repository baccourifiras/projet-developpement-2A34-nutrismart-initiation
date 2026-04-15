<?php
require_once __DIR__ . '/../models/Stock.php';

class StockController {
    private Stock $model;

    public function __construct() {
        $this->model = new Stock();
    }

    public function index(): void {
        $stocks         = $this->model->getAll();
        $expirent       = $this->model->getExpiresSoon(7);
        $tauxGaspillage = $this->model->getTauxGaspillage();
        require __DIR__ . '/../views/stock/index.php';
    }

    public function create(): void {
        $errors = [];
        $data   = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->create($data);
                header('Location: index.php?page=stock&success=created');
                exit;
            }
        }
        require __DIR__ . '/../views/stock/create.php';
    }

    public function edit(): void {
        $id    = (int)($_GET['id'] ?? 0);
        $stock = $this->model->getById($id);
        if (!$stock) { header('Location: index.php?page=stock&error=notfound'); exit; }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data   = $this->sanitize($_POST);
            $errors = $this->validate($data);
            if (empty($errors)) {
                $this->model->update($id, $data);
                header('Location: index.php?page=stock&success=updated');
                exit;
            }
        }
        require __DIR__ . '/../views/stock/edit.php';
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) $this->model->delete($id);
        header('Location: index.php?page=stock&success=deleted');
        exit;
    }

    private function sanitize(array $p): array {
        return [
            'type'            => trim($p['type'] ?? ''),
            'produits'        => trim($p['produits'] ?? ''),
            'date_expiration' => trim($p['date_expiration'] ?? ''),
            'seuil_minimum'   => trim($p['seuil_minimum'] ?? ''),
        ];
    }

    private function validate(array $d): array {
        $errors = [];

        if (empty($d['produits']))
            $errors['produits'] = 'Le nom du produit est obligatoire.';
        elseif (strlen($d['produits']) > 255)
            $errors['produits'] = 'Maximum 255 caractères.';

        if (empty($d['type']))
            $errors['type'] = 'La catégorie est obligatoire.';
        elseif (strlen($d['type']) < 2 || strlen($d['type']) > 50)
            $errors['type'] = 'Entre 2 et 50 caractères.';

        if (empty($d['date_expiration'])) {
            $errors['date_expiration'] = 'La date d\'expiration est obligatoire.';
        } else {
            $dt = DateTime::createFromFormat('Y-m-d', $d['date_expiration']);
            if (!$dt) $errors['date_expiration'] = 'Format de date invalide.';
            elseif ($dt < new DateTime('today')) $errors['date_expiration'] = 'La date doit être dans le futur.';
        }

        if ($d['seuil_minimum'] === '')
            $errors['seuil_minimum'] = 'Le seuil minimum est obligatoire.';
        elseif (!is_numeric($d['seuil_minimum']) || (float)$d['seuil_minimum'] < 0)
            $errors['seuil_minimum'] = 'Nombre positif requis.';

        return $errors;
    }
}
