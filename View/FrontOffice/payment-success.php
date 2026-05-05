<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Réussi - NutriSmart</title>
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

    <div class="payment-result-page">
        <div class="result-container success-result">
            <div class="result-icon-wrapper">
                <div class="success-icon-circle">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
            
            <h1 class="result-title">Votre paiement a été traité avec succès</h1>
            <p class="result-subtitle">Merci pour votre confiance</p>
            
            <div class="result-details-card">
                <div class="detail-item">
                    <div class="detail-icon success-bg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <h3>Paiement confirmé</h3>
                        <p>Votre transaction a été validée avec succès</p>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon info-bg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <h3>Email de confirmation</h3>
                        <p>Un récapitulatif vous a été envoyé par email</p>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-icon primary-bg">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                            <line x1="12" y1="22.08" x2="12" y2="12"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <h3>Commande en cours</h3>
                        <p>Votre commande sera traitée dans les plus brefs délais</p>
                    </div>
                </div>
            </div>
            
            <div class="result-actions">
                <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                <a href="index.php?page=commande&action=list" class="btn btn-secondary">Voir mes commandes</a>
            </div>
        </div>
    </div>

    <style>
        .payment-result-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f4fbf7 0%, #e8f5e9 100%);
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
        
        .success-icon-circle {
            width: 120px;
            height: 120px;
            margin: 0 auto;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: successPulse 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            position: relative;
        }
        
        .success-icon-circle::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            animation: ripple 1.5s ease-out infinite;
            opacity: 0;
        }
        
        .success-icon-circle svg {
            color: #16a34a;
            filter: drop-shadow(0 2px 4px rgba(22, 163, 74, 0.2));
        }
        
        @keyframes successPulse {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        @keyframes ripple {
            0% { transform: scale(1); opacity: 0.5; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        
        .result-title {
            font-size: 32px;
            font-weight: 700;
            color: #0f6c42;
            margin: 0 0 12px;
            line-height: 1.3;
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
        
        .success-bg {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        }
        
        .success-bg svg {
            color: #16a34a;
        }
        
        .info-bg {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        }
        
        .info-bg svg {
            color: #2563eb;
        }
        
        .primary-bg {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        }
        
        .primary-bg svg {
            color: #1fa463;
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
    <script>
        localStorage.removeItem('nutrismart_panier');
        updatePanierBadge();
    </script>
</body>
</html>
