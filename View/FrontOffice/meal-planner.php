<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planificateur de Repas - NutriSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .meal-plan-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .regime-card {
            background: linear-gradient(135deg, rgba(255,255,255,.98), rgba(246,252,248,.95));
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            text-align: center;
        }
        
        .regime-card:hover {
            transform: translateY(-8px);
            border-color: var(--primary);
            box-shadow: 0 12px 32px rgba(31, 164, 99, 0.2);
        }
        
        .regime-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        
        .regime-card h3 {
            margin: 0 0 10px;
            color: var(--primary-dark);
            font-size: 24px;
        }
        
        .regime-card p {
            color: var(--muted);
            margin: 0;
            line-height: 1.6;
        }
        
        .meal-plan-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .meal-plan-header h2 {
            font-size: 36px;
            color: var(--primary-dark);
            margin: 0 0 12px;
        }
        
        .meal-plan-description {
            color: var(--muted);
            font-size: 18px;
            margin: 0 0 24px;
        }
        
        .meal-plan-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .meal-plan-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
        }
        
        .meal-day-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: 18px;
            padding: 24px;
            transition: all 0.3s ease;
        }
        
        .meal-day-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 24px rgba(31, 164, 99, 0.12);
        }
        
        .meal-day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--border);
        }
        
        .meal-day-header h3 {
            margin: 0;
            color: var(--primary-dark);
            font-size: 22px;
        }
        
        .day-badge {
            background: linear-gradient(135deg, var(--primary), #34d399);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 800;
        }
        
        .meal-section {
            margin-bottom: 20px;
        }
        
        .meal-section h4 {
            color: var(--primary-dark);
            font-size: 16px;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .meal-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .meal-section li {
            padding: 8px 0 8px 24px;
            color: var(--text);
            position: relative;
            line-height: 1.6;
        }
        
        .meal-section li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary);
            font-weight: 800;
        }
        
        .meal-section.collations {
            background: rgba(31, 164, 99, 0.05);
            padding: 12px;
            border-radius: 12px;
        }
        
        @media print {
            .navbar, .meal-plan-actions, .page-header {
                display: none;
            }
            
            .meal-plan-grid {
                grid-template-columns: 1fr;
            }
            
            .meal-day-card {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR FIXE -->
    <nav class="navbar" id="navbar">
        <div class="nav-brand">
            <div class="logo">NutriSmart</div>
            <div class="slogan">Eat Smart Live Smart</div>
        </div>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="index.php?page=wishlist">Favoris</a>
            <a href="index.php?page=meal-planner" class="active">Plan de Repas</a>
            <a href="index.php?page=backoffice&action=list" class="nav-dashboard">BackOffice</a>
            <button onclick="ouvrirPanier()" class="panier-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <span id="panierBadge" class="panier-badge">0</span>
            </button>
            <button onclick="window.location.href='index.php?page=wishlist'" class="wishlist-nav-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <span id="wishlistBadge" class="wishlist-badge">0</span>
            </button>
        </div>
    </nav>

    <!-- PAGE HEADER -->
    <header class="page-header">
        <p class="badge">🍽️ Planificateur de Repas</p>
        <h1>Votre Plan Nutritionnel Personnalisé</h1>
        <p class="subtitle">Générez un plan de repas sur 7 jours adapté à votre régime alimentaire.</p>
    </header>

    <div class="container">
        <section class="section">
            <div class="section-title-row">
                <div>
                    <p class="section-kicker">Choisissez votre régime</p>
                    <h2>Sélectionnez un plan</h2>
                </div>
            </div>
            
            <div class="meal-plan-selector">
                <div class="regime-card" onclick="mealPlanner.afficherPlan('diabete')">
                    <div class="regime-icon">🩺</div>
                    <h3>Diabète</h3>
                    <p>Plan adapté pour le contrôle glycémique avec index glycémique bas</p>
                </div>
                
                <div class="regime-card" onclick="mealPlanner.afficherPlan('vegan')">
                    <div class="regime-icon">🌱</div>
                    <h3>Vegan</h3>
                    <p>100% végétal, riche en protéines végétales et nutriments essentiels</p>
                </div>
                
                <div class="regime-card" onclick="mealPlanner.afficherPlan('sans_gluten')">
                    <div class="regime-icon">🌾</div>
                    <h3>Sans Gluten</h3>
                    <p>Certifié sans gluten, équilibré et adapté aux intolérances</p>
                </div>
            </div>
        </section>
        
        <section class="section" id="plan-section" style="display: none;">
            <div id="meal-plan-container">
                <!-- Le plan sera inséré ici par JavaScript -->
            </div>
        </section>
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
                    <span id="panierTotal" class="total-price">0.00 TND</span>
                </div>
                <div class="panier-actions">
                    <button onclick="viderPanier()" class="secondary-btn">Vider le panier</button>
                    <button onclick="validerCommande()" class="primary-btn">Valider la commande</button>
                </div>
            </div>
        </div>
    </div>

    <script src="public/js/panier.js"></script>
    <script src="public/js/wishlist.js"></script>
    <script src="public/js/meal-planner.js"></script>
    <script>
        // Navbar scroll effect
        (function () {
            var navbar = document.getElementById('navbar');
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        })();
        
        // Override afficherPlan to show the section
        const originalAfficherPlan = mealPlanner.afficherPlan.bind(mealPlanner);
        mealPlanner.afficherPlan = function(regime, containerId) {
            document.getElementById('plan-section').style.display = 'block';
            document.getElementById('plan-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
            originalAfficherPlan(regime, containerId);
        };
    </script>
</body>
</html>
