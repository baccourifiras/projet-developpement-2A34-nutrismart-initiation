<?php
require_once __DIR__ . '/../Model/BilanSante.php';

class BilanSanteController
{
    private $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->model = new BilanSante();
    }

    public function handle()
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user_id'])) {
            echo json_encode(['ok' => false, 'msg' => 'Non connecté']);
            exit;
        }

        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if ($action === 'sauvegarder_bilan') {
            $this->sauvegarder();
        } elseif ($action === 'verifier_bilan') {
            $this->verifier();
        } else {
            echo json_encode(['ok' => false, 'msg' => 'Action inconnue']);
        }
        exit;
    }

    private function sauvegarder()
    {
        $id_user = (int)$_SESSION['user_id'];
        $data = [
            'fatigue'     => isset($_POST['fatigue'])     ? (int)$_POST['fatigue']     : null,
            'humeur'      => isset($_POST['humeur'])       ? (int)$_POST['humeur']      : null,
            'hydratation' => isset($_POST['hydratation'])  ? (int)$_POST['hydratation'] : null,
            'appetit'     => isset($_POST['appetit'])      ? (int)$_POST['appetit']     : null,
            'sommeil'     => isset($_POST['sommeil'])       ? (int)$_POST['sommeil']     : null,
        ];

        $conseil = $this->model->sauvegarder($id_user, $data);
        echo json_encode(['ok' => true, 'conseil' => $conseil]);
    }

    private function verifier()
    {
        $id_user = (int)$_SESSION['user_id'];
        $existe  = $this->model->bilanDuJourExiste($id_user);
        echo json_encode(['ok' => true, 'bilan_fait' => $existe]);
    }
}
?>
