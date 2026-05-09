<?php
require_once __DIR__ . '/../Model/Utilisateur.php';
require_once __DIR__ . '/../Model/Historique.php';

class PageController
{
    // Constructeur (Chapitre 4)
    public function __construct()
    {
    }

    // Destructeur (Chapitre 4)
    public function __destruct()
    {
    }

    // Page accueil - aucune verification necessaire
    public function accueil()
    {
        include __DIR__ . '/../View/FrontOffice/accueil.php';
    }

    // Page inscription
    public function inscription()
    {
        // Messages pour la vue (Chapitre 3 - isset)
        $succes = isset($_GET['succes']) ? $_GET['succes'] : '';
        $erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';
        include __DIR__ . '/../View/FrontOffice/inscription.php';
    }

    // Page login - redirige si deja connecte (Chapitre 3 - if/elseif)
    public function login()
    {
        if (!empty($_SESSION['user_role'])) {
            if      ($_SESSION['user_role'] === 'admin')          { header('Location: index.php?page=dashboard');             exit; }
            elseif  ($_SESSION['user_role'] === 'nutritionniste') { header('Location: index.php?page=espace_nutritionniste'); exit; }
            elseif  ($_SESSION['user_role'] === 'client')         { header('Location: index.php?page=espace_client');         exit; }
        }
        $erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';
        include __DIR__ . '/../View/FrontOffice/login.php';
    }

    // Dashboard admin - verifie acces + prepare donnees (MVC : Controller appelle Model)
    public function dashboard()
    {
        // Verification d'acces (Chapitre 3 - if)
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }

        // Controller appelle le Model et prepare les donnees pour la Vue (Chapitre 4 - MVC)
        $modelUser    = new Utilisateur();
        $modelHist    = new Historique();
        $utilisateurs = $modelUser->findAll();
        $historique   = $modelHist->findAll();
        $stats        = $modelHist->stats();
        $parRole      = $modelUser->countByRole();
        $total        = $modelUser->count();

        // Donnees d'affichage (Chapitre 3 - isset)
        $adminPrenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Admin';
        $adminEmail  = isset($_SESSION['user_email'])  ? $_SESSION['user_email']  : '';
        $onglet      = isset($_GET['onglet']) ? $_GET['onglet'] : 'utilisateurs';
        $succes      = isset($_GET['succes']) ? $_GET['succes'] : '';
        $erreur      = isset($_GET['erreur']) ? $_GET['erreur'] : '';

        // Controller inclut la Vue — la Vue n'appelle jamais le Model
        include __DIR__ . '/../View/BackOffice/dashboard.php';
    }

    // Espace client - verifie acces
    public function espace_client()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'client') {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }
        // Donnees passees a la vue
        $userPrenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Client';
        include __DIR__ . '/../View/FrontOffice/espace_client.php';
    }

    // Espace nutritionniste - verifie acces
    public function espace_nutritionniste()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'nutritionniste') {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }
        // Donnees passees a la vue
        $userPrenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Nutritionniste';
        include __DIR__ . '/../View/FrontOffice/espace_nutritionniste.php';
    }
}
?>
