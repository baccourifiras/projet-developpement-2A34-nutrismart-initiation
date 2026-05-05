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
    
    case 'wishlist':
        require_once __DIR__ . '/View/FrontOffice/wishlist.php';
        break;
    
    case 'payment':
        require_once __DIR__ . '/Util/StripeService.php';
        $stripeService = new StripeService();
        
        switch ($action) {
            case 'create-session':
                // Créer une session Stripe
                header('Content-Type: application/json');
                
                try {
                    $input = json_decode(file_get_contents('php://input'), true);
                    $panier = $input['panier'] ?? [];
                    
                    if (empty($panier)) {
                        echo json_encode(['error' => 'Panier vide']);
                        exit();
                    }
                    
                    // Sauvegarder le panier dans la session PHP pour le récupérer après paiement
                    session_start();
                    $_SESSION['panier_stripe'] = $panier;
                    
                    // Log pour debug
                    error_log("Création session Stripe pour panier: " . json_encode($panier));
                    
                    $session = $stripeService->creerSessionPaiement(
                        $panier,
                        'http://localhost/NutriSmart/index.php?page=payment&action=success&session_id={CHECKOUT_SESSION_ID}',
                        'http://localhost/NutriSmart/index.php?page=payment&action=cancel'
                    );
                    
                    if ($session && isset($session['id'])) {
                        error_log("Session créée avec succès: " . $session['id']);
                        echo json_encode(['sessionId' => $session['id']]);
                    } else {
                        error_log("Échec création session Stripe");
                        echo json_encode(['error' => 'Erreur création session Stripe. Vérifiez les logs PHP.']);
                    }
                } catch (Exception $e) {
                    error_log("Exception Stripe: " . $e->getMessage());
                    echo json_encode(['error' => 'Exception: ' . $e->getMessage()]);
                }
                exit();
                
            case 'success':
                session_start();
                $sessionId = $_GET['session_id'] ?? '';
                
                if ($sessionId && $stripeService->verifierPaiement($sessionId)) {
                    // Paiement vérifié - créer les commandes dans la base de données
                    
                    // Récupérer le panier depuis la session PHP
                    $panier = $_SESSION['panier_stripe'] ?? [];
                    
                    if (!empty($panier)) {
                        require_once __DIR__ . '/Controller/CommandeController.php';
                        $commandeController = new CommandeController();
                        
                        // Récupérer les détails de la session Stripe
                        $sessionDetails = $stripeService->getSessionDetails($sessionId);
                        $montantTotal = ($sessionDetails['amount_total'] ?? 0) / 100; // Convertir centimes en euros
                        
                        $commandeIds = [];
                        
                        // Créer une commande pour chaque produit du panier
                        foreach ($panier as $item) {
                            $commande = new Commande(
                                null,
                                1, // ID utilisateur (à remplacer par l'utilisateur connecté)
                                $item['id'], // ID produit
                                $item['quantite'], // Quantité
                                $item['prix'] * $item['quantite'], // Prix total pour ce produit
                                'confirmee', // Statut confirmé car paiement réussi
                                'Carte bancaire (Stripe)'
                            );
                            
                            $commandeId = $commandeController->addCommande($commande);
                            $commandeIds[] = $commandeId;
                        }
                        
                        // Envoyer email de confirmation avec tous les produits
                        require_once __DIR__ . '/Util/EmailService.php';
                        $emailService = new EmailService();
                        
                        // Créer un résumé des produits
                        $produitsResume = [];
                        foreach ($panier as $item) {
                            $produitsResume[] = $item['nom'] . ' (x' . $item['quantite'] . ')';
                        }
                        
                        $emailService->envoyerConfirmationCommande(
                            'aniskontra123@gmail.com',
                            'Client NutriSmart',
                            [
                                'id' => implode(', ', $commandeIds),
                                'produit' => implode(', ', $produitsResume),
                                'quantite' => count($panier) . ' produit(s)',
                                'paiement' => 'Carte bancaire (Stripe)',
                                'total' => number_format($montantTotal, 2) . ' EUR'
                            ]
                        );
                        
                        // Nettoyer le panier de la session
                        unset($_SESSION['panier_stripe']);
                    }
                    
                    require_once __DIR__ . '/View/FrontOffice/payment-success.php';
                } else {
                    header('Location: index.php?page=payment&action=cancel');
                }
                break;
                
            case 'cancel':
                require_once __DIR__ . '/View/FrontOffice/payment-cancel.php';
                break;
                
            default:
                $stripePublicKey = $stripeService->getPublicKey();
                require_once __DIR__ . '/View/FrontOffice/checkout.php';
                break;
        }
        break;
        
    case 'frontoffice':
    default:
        $controller = new ProduitController();
        $produits = $controller->listProduits();
        require_once __DIR__ . '/View/FrontOffice/index.php';
        break;
}
