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
            <a href="index.php?page=backoffice&action=list" class="nav-dashboard">BackOffice</a>
            <a href="index.php?page=commande&action=list">Commandes</a>
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
                    <h3>Commander ce produit</h3>
                    <form method="POST" action="index.php?page=commande&action=add" class="form-grid two-columns">
                        <input type="hidden" name="id_produit" value="<?= $produit->getIdProduit() ?>">
                        <input type="hidden" name="prix_total" value="<?= $produit->getPrix() ?>">
                        
                        <div>
                            <label for="id_utilisateur">ID Utilisateur *</label>
                            <input type="number" id="id_utilisateur" name="id_utilisateur" value="1" required>
                        </div>

                        <div>
                            <label for="quantite">Quantité *</label>
                            <input type="number" id="quantite" name="quantite" value="1" min="1" required>
                        </div>

                        <div class="full-width">
                            <label for="mode_paiement">Mode de paiement *</label>
                            <select id="mode_paiement" name="mode_paiement" required>
                                <option value="">-- Sélectionnez --</option>
                                <option value="carte_bancaire">Carte Bancaire</option>
                                <option value="paypal">PayPal</option>
                                <option value="virement">Virement Bancaire</option>
                            </select>
                        </div>

                        <button type="submit" class="primary-btn">Commander maintenant</button>
                        <a href="index.php" class="secondary-btn">← Retour aux produits</a>
                    </form>
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
