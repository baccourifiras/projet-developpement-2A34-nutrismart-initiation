<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controller/PageController.php';
require_once __DIR__ . '/Controller/UtilisateurController.php';
require_once __DIR__ . '/Controller/HistoriqueController.php';

// --- Traitement POST : tout passe par index.php (MVC) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Actions utilisateur (Chapitre 3 - if/elseif)
    if ($action === 'ajouter' || $action === 'modifier' || $action === 'supprimer'
        || $action === 'login' || $action === 'deconnexion') {
        $ctrl = new UtilisateurController();
        $ctrl->handle();
        exit;
    }

    // Actions historique
    if ($action === 'supprimer_historique' || $action === 'vider') {
        $ctrl = new HistoriqueController();
        $ctrl->handle();
        exit;
    }
}

// --- Affichage GET : routing par page (Chapitre 3 - if/elseif) ---
$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
$ctrl = new PageController();

if      ($page === 'accueil')               { $ctrl->accueil();               }
elseif  ($page === 'inscription')           { $ctrl->inscription();           }
elseif  ($page === 'login')                 { $ctrl->login();                 }
elseif  ($page === 'dashboard')             { $ctrl->dashboard();             }
elseif  ($page === 'espace_client')         { $ctrl->espace_client();         }
elseif  ($page === 'espace_nutritionniste') { $ctrl->espace_nutritionniste(); }
else                                        { $ctrl->accueil();               }
?>
