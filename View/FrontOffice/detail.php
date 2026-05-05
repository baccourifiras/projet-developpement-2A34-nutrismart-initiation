<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $produit ? htmlspecialchars($produit->getNom()) : 'Produit' ?> - NutriSmart</title>
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
            <a href="index.php?page=wishlist">Favoris</a>
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
        <p class="badge">Détail Produit</p>
        <h1><?= $produit ? htmlspecialchars($produit->getNom()) : 'Produit' ?></h1>
        <p class="subtitle">Découvrez toutes les informations sur ce produit NutriSmart.</p>
    </header>

    <div class="container">
        <?php if ($produit): ?>
        <section class="section reveal">
            <div class="product-detail-header">
                <div class="product-badges">
                    <?php
                    $categorieLabel = [
                        'plan' => 'Plan Nutritionnel',
                        'premium' => 'Premium',
                        'coaching' => 'Coaching',
                        'guide' => 'Guide'
                    ][$produit->getCategorie()] ?? $produit->getCategorie();
                    
                    $regimeLabel = [
                        'diabete' => 'Diabète',
                        'vegan' => 'Vegan',
                        'sans_gluten' => 'Sans Gluten',
                        'multi' => 'Multi-régimes'
                    ][$produit->getRegimeCible()] ?? $produit->getRegimeCible();
                    ?>
                    <span class="small-badge"><?= $categorieLabel ?></span>
                    <span class="small-badge"><?= $regimeLabel ?></span>
                    <?php if ($produit->getDisponible()): ?>
                        <span class="small-badge">Disponible</span>
                    <?php else: ?>
                        <span class="badge-danger">Indisponible</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-detail-body">
                <div class="product-description">
                    <h3>Description</h3>
                    <p><?= nl2br(htmlspecialchars($produit->getDescription())) ?></p>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <strong>Type de vente</strong>
                        <p><?= $produit->getTypeVente() === 'abonnement' ? 'Abonnement' : 'Achat Unique' ?></p>
                    </div>
                    <div class="info-card">
                        <strong>Prix</strong>
                        <p class="price-large"><?= $produit->getPrix() ?> TND</p>
                    </div>
                    <div class="info-card">
                        <strong>Ajouté le</strong>
                        <p><?= date('d/m/Y', strtotime($produit->getDateAjout())) ?></p>
                    </div>
                </div>

                <?php if ($produit->getDisponible()): ?>
                <div class="order-section">
                    <h3>Ajouter au panier</h3>
                    <div class="detail-actions">
                        <button onclick="ajouterAuPanier({id: <?= $produit->getIdProduit() ?>, nom: '<?= addslashes($produit->getNom()) ?>', prix: <?= $produit->getPrix() ?>, categorie: '<?= $categorieLabel ?>', regime: '<?= $regimeLabel ?>'})" class="primary-btn large-btn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                            Ajouter au panier
                        </button>
                        <a href="index.php" class="secondary-btn large-btn">← Retour aux produits</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="no-data">
                    Ce produit n'est actuellement pas disponible à la commande.
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php else: ?>
        <section class="section">
            <div class="no-data">Produit introuvable.</div>
        </section>
        <?php endif; ?>
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

            // Reveal animations
            var elements = document.querySelectorAll('.reveal');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            elements.forEach(function(el) {
                observer.observe(el);
            });
        })();
    </script>
</body>
</html>
