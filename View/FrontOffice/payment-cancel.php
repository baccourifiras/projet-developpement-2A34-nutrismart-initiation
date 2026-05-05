<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Annulé - NutriSmart</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-brand">
            <div class="logo">NutriSmart</div>
            <div class="slogan">Eat Smart Live Smart</div>
        </div>
    </nav>

    <div class="payment-result-page cancel-page">
        <div class="result-container cancel-result">
            <div class="result-icon-wrapper">
                <div class="cancel-icon-circle">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                    </svg>
                </div>
            </div>
            
            <h1 class="result-title cancel-title">Paiement annulé</h1>
            <p class="result-subtitle">Votre transaction n'a pas été effectuée</p>
            
            <div class="result-details-card cancel-card">
                <div class="detail-item">
                    <div class="detail-icon neutral-bg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <h3>Aucun débit</h3>
                        <p>Aucun montant n'a été prélevé sur votre compte</p>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon cart-bg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"/>
                            <circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <h3>Panier conservé</h3>
                        <p>Vos articles sont toujours disponibles dans votre panier</p>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon retry-bg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="23 4 23 10 17 10"/>
                            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <h3>Réessayer</h3>
                        <p>Vous pouvez finaliser votre commande quand vous le souhaitez</p>
                    </div>
                </div>
            </div>
            
            <div class="result-actions">
                <button onclick="ouvrirPanier()" class="btn btn-primary">Voir mon panier</button>
                <a href="index.php" class="btn btn-secondary">Retour à l'accueil</a>
            </div>
        </div>
    </div>

    <!-- MODAL PANIER -->
    <div id="panierModal" class="modal hidden">
        <div class="modal-card panier-modal">
            <button onclick="fermerPanier()" class="close-btn">×</button>
            <h2>Mon Panier</h2>
            <div id="panierItems" class="panier-items-container"></div>
            <div class="panier-footer">
                <div class="panier-total">
                    <span>Total:</span>
                    <span id="panierTotal" class="total-price">0.00 EUR</span>
                </div>
                <div class="panier-actions">
                    <button onclick="viderPanier()" class="secondary-btn">Vider le panier</button>
                    <button onclick="validerCommande()" class="primary-btn">Valider la commande</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .cancel-page {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }
        
        .payment-result-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .result-container {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .result-icon-wrapper {
            margin-bottom: 32px;
        }
        
        .cancel-icon-circle {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: shake 0.5s ease-in-out;
        }
        
        .cancel-icon-circle svg {
            color: #dc2626;
            filter: drop-shadow(0 2px 4px rgba(220, 38, 38, 0.2));
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .result-title {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 12px;
            line-height: 1.3;
        }
        
        .cancel-title {
            color: #991b1b;
        }
        
        .result-subtitle {
            font-size: 18px;
            color: #638070;
            margin: 0 0 40px;
        }
        
        .result-details-card {
            background: #f9fafb;
            border-radius: 16px;
            padding: 32px;
            margin: 40px 0;
            text-align: left;
        }
        
        .detail-item {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            padding: 20px 0;
        }
        
        .detail-item:not(:last-child) {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .detail-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .neutral-bg {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
        }
        
        .neutral-bg svg {
            color: #d97706;
        }
        
        .cart-bg {
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        }
        
        .cart-bg svg {
            color: #4f46e5;
        }
        
        .retry-bg {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }
        
        .retry-bg svg {
            color: #059669;
        }
        
        .detail-content h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 6px;
        }
        
        .detail-content p {
            font-size: 15px;
            color: #6b7280;
            margin: 0;
            line-height: 1.5;
        }
        
        .result-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 40px;
        }
        
        .btn {
            padding: 14px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1fa463, #0f6c42);
            color: white;
            box-shadow: 0 4px 12px rgba(31, 164, 99, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 164, 99, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: #1fa463;
            border: 2px solid #1fa463;
        }
        
        .btn-secondary:hover {
            background: #f4fbf7;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .result-container {
                padding: 40px 24px;
            }
            
            .result-title {
                font-size: 24px;
            }
            
            .result-details-card {
                padding: 24px;
            }
            
            .detail-item {
                gap: 16px;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>

    <script src="public/js/panier.js"></script>
</body>
</html>
