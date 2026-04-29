<?php
require_once __DIR__ . '/../Model/Historique.php';

class HistoriqueController
{
    // Propriete privee (Chapitre 4)
    private $model;

    // Constructeur (Chapitre 4)
    public function __construct()
    {
        $this->model = new Historique();
    }

    // Destructeur (Chapitre 4)
    public function __destruct()
    {
        unset($this->model);
    }

    // Dispatch des actions POST (Chapitre 3 - if/elseif)
    public function handle()
    {
        $action = isset($_POST['action']) ? $_POST['action'] : '';

        if      ($action === 'supprimer_historique') { $this->supprimer(); }
        elseif  ($action === 'vider')                { $this->vider();     }
    }

    // Supprimer une entree (Chapitre 5 - CRUD Delete)
    private function supprimer()
    {
        $id = isset($_POST['id_historique']) ? (int) $_POST['id_historique'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php?page=dashboard&onglet=historique&succes=histo_supprime');
        exit;
    }

    // Vider tout (Chapitre 5 - exec)
    private function vider()
    {
        $this->model->viderTout();
        header('Location: index.php?page=dashboard&onglet=historique&succes=histo_vide');
        exit;
    }
}
?>
