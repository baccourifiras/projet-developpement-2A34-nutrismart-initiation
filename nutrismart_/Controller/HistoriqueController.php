<?php
require_once __DIR__ . '/../Model/Historique.php';

class HistoriqueController
{

    private $model;

    public function __construct()
    {
        $this->model = new Historique();
    }

    public function __destruct()
    {
        unset($this->model);
    }

    public function handle()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if      ($action === 'supprimer_historique') { $this->supprimer(); }
        elseif  ($action === 'vider')                { $this->vider();     }
    }

    private function supprimer()
    {
        $id = isset($_POST['id_historique']) ? (int) $_POST['id_historique'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php?page=dashboard&onglet=historique&succes=histo_supprime');
        exit;
    }

    private function vider()
    {
        $this->model->viderTout();
        header('Location: index.php?page=dashboard&onglet=historique&succes=histo_vide');
        exit;
    }
}
?>
