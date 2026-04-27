<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSmart - Plateforme de Nutrition pour Régimes Spéciaux</title>
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
            <a href="index.php" class="active">Accueil</a>
            <a href="index.php?page=backoffice&action=list" class="nav-dashboard">BackOffice</a>
            <a href="index.php?page=commande&action=list">Commandes</a>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <span class="badge">Plateforme Nutrition</span>
            <h1>Bienvenue sur NutriSmart</h1>
            <p class="subtitle">Votre plateforme de nutrition adaptée aux régimes spéciaux : diabète, vegan, sans gluten. Découvrez nos plans nutritionnels, coaching personnalisé et guides pratiques.</p>
        </div>
    </section>

    <div class="container">
        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <section class="section">
            <div class="alert-success">
                ✓ Votre commande a été enregistrée avec succès !
            </div>
        </section>
        <?php endif; ?>

        <section class="section">
            <div class="section-title-row">
                <div>
                    <p class="section-kicker">Recherche</p>
                    <h2>Trouvez votre produit</h2>
                </div>
            </div>
            
            <div class="filter-grid">
                <input type="text" id="searchInput" placeholder="Rechercher un produit..." onkeyup="filterProducts()">
                
                <select id="categorieFilter" onchange="filterProducts()">
                    <option value="">Toutes les catégories</option>
                    <option value="plan">Plan Nutritionnel</option>
                    <option value="premium">Fonctionnalité Premium</option>
                    <option value="coaching">Coaching</option>
                    <option value="guide">Guide</option>
                </select>

                <select id="regimeFilter" onchange="filterProducts()">
                    <option value="">Tous les régimes</option>
                    <option value="diabete">Diabète</option>
                    <option value="vegan">Vegan</option>
                    <option value="sans_gluten">Sans Gluten</option>
                    <option value="multi">Multi-régimes</option>
                </select>
            </div>
        </section>

        <section class="section">
            <div class="section-title-row">
                <div>
                    <p class="section-kicker">Nos Produits</p>
                    <h2>Catalogue NutriSmart</h2>
                </div>
                <div class="sort-controls">
                    <span class="sort-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M7 12h10M11 18h2"/></svg>
                        Trier par
                    </span>
                    <select id="sortSelect" onchange="sortProducts()">
                        <option value="">Par défaut</option>
                        <option value="prix-asc">Prix croissant</option>
                        <option value="prix-desc">Prix décroissant</option>
                        <option value="nom-asc">Nom A → Z</option>
                        <option value="nom-desc">Nom Z → A</option>
                    </select>
                </div>
            </div>
            <div class="products-grid" id="productsGrid">
            <?php
            if (empty($produits)) {
                echo "<div class='no-data'>Aucun produit disponible pour le moment.</div>";
            } else {
                foreach ($produits as $produit) {
                    if ($produit->getDisponible()) {
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
                        
                        echo "<article class='product-card reveal' data-categorie='{$produit->getCategorie()}' data-regime='{$produit->getRegimeCible()}' data-nom='" . strtolower($produit->getNom()) . "' data-prix='{$produit->getPrix()}'>";
                        echo "<div class='product-content'>";
                        echo "<div class='product-badges'>";
                        echo "<span class='small-badge'>{$categorieLabel}</span>";
                        echo "<span class='small-badge'>{$regimeLabel}</span>";
                        echo "</div>";
                        echo "<h3>{$produit->getNom()}</h3>";
                        echo "<p class='product-description'>" . substr($produit->getDescription(), 0, 120) . "...</p>";
                        echo "<div class='product-meta'>";
                        echo "<span class='product-type'>{$produit->getTypeVente()}</span>";
                        echo "<span class='product-price'>{$produit->getPrix()} TND</span>";
                        echo "</div>";
                        echo "<a href='index.php?page=detail&id={$produit->getIdProduit()}' class='primary-btn'>Voir détail</a>";
                        echo "</div>";
                        echo "</article>";
                    }
                }
            }
            ?>
            </div>
        </section>
    </div>

    <script>
        // Filtrage des produits
        function filterProducts() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const categorieValue = document.getElementById('categorieFilter').value;
            const regimeValue = document.getElementById('regimeFilter').value;
            const cards = document.querySelectorAll('.product-card');
            cards.forEach(card => {
                const nom = card.getAttribute('data-nom');
                const categorie = card.getAttribute('data-categorie');
                const regime = card.getAttribute('data-regime');
                const matchSearch = nom.includes(searchValue);
                const matchCategorie = !categorieValue || categorie === categorieValue;
                const matchRegime = !regimeValue || regime === regimeValue;
                card.style.display = (matchSearch && matchCategorie && matchRegime) ? 'block' : 'none';
            });
        }

        // Tri des produits
        function sortProducts() {
            const value = document.getElementById('sortSelect').value;
            const grid = document.getElementById('productsGrid');
            const cards = Array.from(grid.querySelectorAll('.product-card'));

            cards.sort(function(a, b) {
                if (value === 'prix-asc')  return parseFloat(a.dataset.prix) - parseFloat(b.dataset.prix);
                if (value === 'prix-desc') return parseFloat(b.dataset.prix) - parseFloat(a.dataset.prix);
                if (value === 'nom-asc')   return a.dataset.nom.localeCompare(b.dataset.nom, 'fr');
                if (value === 'nom-desc')  return b.dataset.nom.localeCompare(a.dataset.nom, 'fr');
                return 0;
            });

            cards.forEach(card => grid.appendChild(card));
        }

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

            // Mark active link
            var currentPage = window.location.pathname.split('/').pop() || 'index.php';
            document.querySelectorAll('.nav-links a').forEach(function (link) {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
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
