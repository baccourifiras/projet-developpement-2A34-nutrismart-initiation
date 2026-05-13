<?php
ob_start();
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Controller/PageController.php';
require_once __DIR__ . '/Controller/UtilisateurController.php';
require_once __DIR__ . '/Controller/HistoriqueController.php';
require_once __DIR__ . '/Controller/BilanSanteController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'ajouter' || $action === 'modifier' || $action === 'supprimer'
        || $action === 'login' || $action === 'deconnexion'
        || $action === 'face_register' || $action === 'face_login') {
        $ctrl = new UtilisateurController();
        $ctrl->handle();
        exit;
    }

    if ($action === 'supprimer_historique' || $action === 'vider') {
        $ctrl = new HistoriqueController();
        $ctrl->handle();
        exit;
    }

    if ($action === 'sauvegarder_bilan' || $action === 'verifier_bilan') {
        $ctrl = new BilanSanteController();
        $ctrl->handle();
        exit;
    }
}

$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
$ctrl = new PageController();

if      ($page === 'accueil')               { $ctrl->accueil();               }
elseif  ($page === 'inscription')           { $ctrl->inscription();           }
elseif  ($page === 'login')                 { $ctrl->login();                 }
elseif  ($page === 'dashboard')             { $ctrl->dashboard();             }
elseif  ($page === 'espace_client')         { $ctrl->espace_client();         }
elseif  ($page === 'espace_nutritionniste') { $ctrl->espace_nutritionniste(); }
elseif  ($page === 'evenements')            { $ctrl->evenements();            }
else                                        { $ctrl->accueil();               }
?>
