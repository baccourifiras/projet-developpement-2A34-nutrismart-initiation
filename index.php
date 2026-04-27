<?php
/**
 * Point d'entrée et routeur de l'application NutriSmart
 */

// Chargement des contrôleurs
require_once __DIR__ . '/Controller/ProduitController.php';
require_once __DIR__ . '/Controller/CommandeController.php';

// Récupération des paramètres de routing
$page = $_GET['page'] ?? 'frontoffice';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Routage
switch ($page) {
    case 'backoffice':
        $controller = new ProduitController();
        
        switch ($action) {
            case 'list':
                $produits = $controller->listProduits();
                require_once __DIR__ . '/View/BackOffice/listProduits.php';
                break;
                
            case 'add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $produit = new Produit(
                        null,
                        $_POST['nom'],
                        $_POST['description'],
                        $_POST['categorie'],
                        $_POST['regime_cible'],
                        $_POST['type_vente'],
                        $_POST['prix'],
                        isset($_POST['disponible']) ? 1 : 0
                    );
                    $controller->addProduit($produit);
                } else {
                    require_once __DIR__ . '/View/BackOffice/addProduit.php';
                }
                break;
                
            case 'update':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $produit = new Produit(
                        $_POST['id_produit'],
                        $_POST['nom'],
                        $_POST['description'],
                        $_POST['categorie'],
                        $_POST['regime_cible'],
                        $_POST['type_vente'],
                        $_POST['prix'],
                        isset($_POST['disponible']) ? 1 : 0
                    );
                    $controller->updateProduit($produit);
                } else {
                    $produit = $controller->getProduit($id);
                    require_once __DIR__ . '/View/BackOffice/updateProduit.php';
                }
                break;
                
            case 'delete':
                $controller->deleteProduit($id);
                break;
                
            default:
                header('Location: index.php?page=backoffice&action=list');
                break;
        }
        break;
        
    case 'commande':
        $controller = new CommandeController();
        
        switch ($action) {
            case 'list':
                $commandes = $controller->listCommandes();
                require_once __DIR__ . '/View/BackOffice/listCommandes.php';
                break;
                
            case 'add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $commande = new Commande(
                        null,
                        $_POST['id_utilisateur'],
                        $_POST['id_produit'],
                        $_POST['quantite'],
                        $_POST['prix_total'],
                        'en_attente',
                        $_POST['mode_paiement']
                    );
                    $controller->addCommande($commande);
                    header('Location: index.php?page=frontoffice&action=index&success=1');
                    exit();
                }
                break;
                
            case 'updateStatus':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->updateStatut($_POST['id_commande'], $_POST['statut']);
                    header('Location: index.php?page=commande&action=list');
                    exit();
                }
                break;
                
            case 'delete':
                $controller->deleteCommande($id);
                break;
                
            default:
                header('Location: index.php?page=commande&action=list');
                break;
        }
        break;
        
    case 'detail':
        $controller = new ProduitController();
        $produit = $controller->getProduit($id);
        require_once __DIR__ . '/View/FrontOffice/detail.php';
        break;
        
    case 'frontoffice':
    default:
        $controller = new ProduitController();
        $produits = $controller->listProduits();
        require_once __DIR__ . '/View/FrontOffice/index.php';
        break;
}
