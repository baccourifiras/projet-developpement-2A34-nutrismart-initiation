<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris - NutriSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
    <link rel="stylesheet" href="public/css/style.css">
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
            <a href="index.php?page=wishlist" class="active">Favoris</a>
            <a href="index.php?page=backoffice&action=list" class="nav-dashboard">BackOffice</a>
            <a href="index.php?page=commande&action=list">Commandes</a>
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
        <p class="badge">❤️ Mes Favoris</p>
        <h1>Produits que vous aimez</h1>
        <p class="subtitle">Retrouvez tous vos produits favoris en un seul endroit.</p>
    </header>

    <div class="container">
        <section class="section">
            <div id="wishlistContainer">
                <!-- Wishlist items will be inserted here by JavaScript -->
            </div>
        </section>
    </div>

    <!-- MODAL PANIER -->
    <div id="panierModal" class="modal hidden">
        <div class="modal-card panier-modal">
            <button onclick="fermerPanier()" class="close-btn">×</button>
            <h2>Mon Panier</h2>
            <div id="panierItems" class="panier-items-container">
                <!-- Items will be inserted here by JavaScript -->
            </div>
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
    </script>
</body>
</html>
