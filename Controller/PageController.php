<?php
require_once __DIR__ . '/../Model/Utilisateur.php';
require_once __DIR__ . '/../Model/Historique.php';

class PageController
{

    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public function accueil()
    {
        include __DIR__ . '/../View/FrontOffice/accueil.php';
    }

    public function inscription()
    {

        $succes = isset($_GET['succes']) ? $_GET['succes'] : '';
        $erreur = isset($_GET['erreur']) ? $_GET['erreur'] : '';
        include __DIR__ . '/../View/FrontOffice/inscription.php';
    }

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

    public function dashboard()
    {

        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'nutritionniste'])) {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }

        $modelUser = new Utilisateur();
        $modelHist = new Historique();

        $rechercheNom    = isset($_GET['recherche_nom'])    ? trim($_GET['recherche_nom'])    : '';
        $recherchePrenom = isset($_GET['recherche_prenom']) ? trim($_GET['recherche_prenom']) : '';
        $rechercheRole   = isset($_GET['recherche_role'])   ? trim($_GET['recherche_role'])   : '';
        $tri             = isset($_GET['tri'])              ? trim($_GET['tri'])              : 'az';

        if (!empty($rechercheNom) || !empty($recherchePrenom)) {
            $utilisateurs = $modelUser->rechercherParNomEtPrenom($rechercheNom, $recherchePrenom);
        } elseif (!empty($rechercheRole)) {
            $utilisateurs = $modelUser->rechercherParRole($rechercheRole);
        } else {
            $utilisateurs = $modelUser->findAll();
        }

        // Tri côté PHP
        usort($utilisateurs, function($a, $b) use ($tri) {
            if ($tri === 'za')     return strcmp($b['nom'], $a['nom']);
            if ($tri === 'recent') return ($b['id_user'] <=> $a['id_user']);
            if ($tri === 'ancien') return ($a['id_user'] <=> $b['id_user']);
            if ($tri === 'role')   return strcmp($a['role'], $b['role']);
            return strcmp($a['nom'], $b['nom']); // az par défaut
        });

        $rechercheHistNom    = isset($_GET['recherche_hist_nom'])    ? trim($_GET['recherche_hist_nom'])    : '';
        $rechercheHistPrenom = isset($_GET['recherche_hist_prenom']) ? trim($_GET['recherche_hist_prenom']) : '';
        $rechercheHistStatut = isset($_GET['recherche_hist_statut']) ? trim($_GET['recherche_hist_statut']) : '';
        $triHist             = isset($_GET['tri_hist'])              ? trim($_GET['tri_hist'])              : 'recent';

        if (!empty($rechercheHistNom) || !empty($rechercheHistPrenom)) {
            $historique = $modelHist->rechercherParNomEtPrenom($rechercheHistNom, $rechercheHistPrenom);
        } else {
            $historique = $modelHist->findAll();
        }

        // Filtre statut côté PHP
        if (!empty($rechercheHistStatut)) {
            $historique = array_filter($historique, function($h) use ($rechercheHistStatut) {
                return $h['statut'] === $rechercheHistStatut;
            });
            $historique = array_values($historique);
        }

        // Tri de l'historique
        usort($historique, function($a, $b) use ($triHist) {
            if ($triHist === 'az') {
                $nomA = trim(($a['nom'] ?? '') . ' ' . ($a['prenom'] ?? ''));
                $nomB = trim(($b['nom'] ?? '') . ' ' . ($b['prenom'] ?? ''));
                return strcmp($nomA, $nomB);
            }
            if ($triHist === 'za') {
                $nomA = trim(($a['nom'] ?? '') . ' ' . ($a['prenom'] ?? ''));
                $nomB = trim(($b['nom'] ?? '') . ' ' . ($b['prenom'] ?? ''));
                return strcmp($nomB, $nomA);
            }
            if ($triHist === 'ancien') {
                return strtotime($a['date_action']) <=> strtotime($b['date_action']);
            }
            // 'recent' par défaut
            return strtotime($b['date_action']) <=> strtotime($a['date_action']);
        });

        $stats   = $modelHist->stats();
        $parRole = $modelUser->countByRole();
        $total   = $modelUser->count();
        $inscriptionsParMois = $modelUser->inscriptionsParMois(12);

        $adminPrenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Admin';
        $adminEmail  = isset($_SESSION['user_email'])  ? $_SESSION['user_email']  : '';
        $onglet      = isset($_GET['onglet']) ? $_GET['onglet'] : 'utilisateurs';
        $succes      = isset($_GET['succes']) ? $_GET['succes'] : '';
        $erreur      = isset($_GET['erreur']) ? $_GET['erreur'] : '';

        include __DIR__ . '/../View/BackOffice/dashboard.php';
    }

    public function espace_client()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'client') {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }

        $userPrenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Client';
        include __DIR__ . '/../View/FrontOffice/espace_client.php';
    }

    public function espace_nutritionniste()
    {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'nutritionniste') {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }

        $userPrenom = isset($_SESSION['user_prenom']) ? $_SESSION['user_prenom'] : 'Nutritionniste';
        include __DIR__ . '/../View/FrontOffice/espace_nutritionniste.php';
    }

    public function evenements()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?page=login&erreur=acces');
            exit;
        }
        header('Location: nutrismart_evenement/View/FrontOffice/index.php');
        exit;
    }
}
?>
