<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - NutriSmart</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="nav-brand">
            <div class="logo">NutriSmart</div>
            <div class="slogan">Eat Smart Live Smart</div>
        </div>
    </nav>

    <div class="checkout-page">
        <div class="checkout-container">
            <div class="checkout-header">
                <div class="secure-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    <span>Paiement Sécurisé</span>
                </div>
                <h1>Finaliser votre commande</h1>
                <p>Paiement sécurisé par Stripe</p>
            </div>

            <div class="checkout-loading">
                <div class="loading-spinner">
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                    <div class="spinner-ring"></div>
                </div>
                <h2>Préparation du paiement sécurisé</h2>
                <p>Veuillez patienter, vous allez être redirigé vers notre plateforme de paiement...</p>
            </div>
        </div>
    </div>

    <style>
        .checkout-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f4fbf7 0%, #e8f5e9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        
        .checkout-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }
        
        .checkout-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #16a34a;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .secure-badge svg {
            color: #16a34a;
        }
        
        .checkout-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #0f6c42;
            margin: 0 0 12px;
        }
        
        .checkout-header p {
            font-size: 16px;
            color: #638070;
            margin: 0;
        }
        
        .checkout-loading {
            text-align: center;
            padding: 40px 20px;
        }
        
        .loading-spinner {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            position: relative;
        }
        
        .spinner-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 4px solid transparent;
            border-top-color: #1fa463;
            border-radius: 50%;
            animation: spin 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }
        
        .spinner-ring:nth-child(2) {
            border-top-color: #0f6c42;
            animation-delay: 0.3s;
        }
        
        .spinner-ring:nth-child(3) {
            border-top-color: #bbf7d0;
            animation-delay: 0.6s;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .checkout-loading h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 12px;
        }
        
        .checkout-loading p {
            font-size: 16px;
            color: #6b7280;
            margin: 0;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                padding: 40px 24px;
            }
            
            .checkout-header h1 {
                font-size: 24px;
            }
        }
    </style>

    <script src="public/js/panier.js"></script>
    <script>
        const panier = getPanier();
        
        console.log('Panier:', panier);
        
        if (panier.length === 0) {
            alert('Votre panier est vide');
            window.location.href = 'index.php';
        } else {
            fetch('index.php?page=payment&action=create-session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ panier: panier })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.error) {
                    alert('Erreur: ' + data.error);
                    window.location.href = 'index.php';
                } else if (data.sessionId) {
                    const stripe = Stripe('<?php echo $stripePublicKey ?? ""; ?>');
                    stripe.redirectToCheckout({ sessionId: data.sessionId })
                        .then(function(result) {
                            if (result.error) {
                                alert(result.error.message);
                                window.location.href = 'index.php';
                            }
                        });
                } else {
                    alert('Erreur lors de la création de la session de paiement');
                    window.location.href = 'index.php';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la préparation du paiement: ' + error.message);
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>
