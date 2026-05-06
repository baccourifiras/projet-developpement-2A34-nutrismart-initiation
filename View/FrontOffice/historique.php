<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSmart - Historique</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/historique.css">
</head>
<body class="page-historique">
    <nav class="site-nav">
        <div class="site-nav__brand">
            <span class="site-nav__title">NutriSmart</span>
            <span class="site-nav__tag">Front office</span>
        </div>
        <div class="site-nav__links">
            <a class="site-nav__link" href="regime.php">Regimes</a>
            <a class="site-nav__link" href="suivi-regime.php">Suivi</a>
            <a class="site-nav__link is-active" href="historique.php">Historique</a>
            <a class="site-nav__button" href="../BackOffice/regime-admin.php">Admin</a>
        </div>
    </nav>

    <main class="page-shell">
        <header class="hero hero--historique">
            <p class="eyebrow">Historique</p>
            <h1>Consultez et ajoutez les recommandations depuis une vue simple.</h1>
            <p class="hero-copy">
                Les donnees sont gerees par le controller et les modeles, la page garde seulement le front.
            </p>
        </header>

        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-card__label">Total</span>
                <strong id="historyStatTotal">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Cut</span>
                <strong id="historyStatCut">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Bulk</span>
                <strong id="historyStatBulk">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Equilibre</span>
                <strong id="historyStatEquilibre">0</strong>
            </article>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Ajout</p>
                    <h2>Nouvelle recommandation</h2>
                </div>
            </div>
            <form id="historiqueForm" class="form-grid">
                <label class="field">
                    <span>Regime lie</span>
                    <select id="histoRegime" required>
                        <option value="">Selectionner</option>
                    </select>
                </label>
                <label class="field">
                    <span>Recommandation</span>
                    <textarea id="historiqueTexte" rows="5" required placeholder="Saisissez votre recommandation"></textarea>
                </label>
                <div class="button-row">
                    <button class="button button--primary" type="submit">Enregistrer</button>
                </div>
                <p class="feedback" id="historiqueFeedback" aria-live="polite"></p>
            </form>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Affichage</p>
                    <h2>Recommandations</h2>
                </div>
            </div>

            <div class="toolbar">
                <label class="field">
                    <span>Type</span>
                    <select id="historiqueFilter">
                        <option value="tous">Tous</option>
                        <option value="cut">Cut</option>
                        <option value="bulk">Bulk</option>
                        <option value="equilibre">Equilibre</option>
                    </select>
                </label>
                <label class="field">
                    <span>Recherche</span>
                    <input id="historiqueSearch" type="search" placeholder="Chercher dans le texte">
                </label>
            </div>

            <div class="filter-row" id="historyViewButtons">
                <button class="chip is-active" type="button" data-view="cards">Cartes</button>
                <button class="chip" type="button" data-view="timeline">Timeline</button>
            </div>

            <div id="historiqueList"></div>
        </section>
    </main>

    <script>window.API_URL = '../../Controller/api-regime.php';</script>
    <script src="js/historique.js"></script>
</body>
</html>
