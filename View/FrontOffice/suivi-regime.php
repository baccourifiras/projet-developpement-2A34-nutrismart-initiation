<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSmart - Suivi regime</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/suivi.css">
</head>
<body class="page-suivi">
    <nav class="site-nav">
        <div class="site-nav__brand">
            <span class="site-nav__title">NutriSmart</span>
            <span class="site-nav__tag">Front office</span>
        </div>
        <div class="site-nav__links">
            <a class="site-nav__link" href="regime.php">Regimes</a>
            <a class="site-nav__link is-active" href="suivi-regime.php">Suivi</a>
            <a class="site-nav__link" href="historique.php">Historique</a>
            <a class="site-nav__button" href="../BackOffice/regime-admin.php">Admin</a>
        </div>
    </nav>

    <main class="page-shell">
        <header class="hero hero--suivi">
            <p class="eyebrow">Suivi quotidien</p>
            <h1>Suivez votre progression sans melanger la logique et l'interface.</h1>
            <p class="hero-copy">
                Recherchez un regime, selectionnez-le, puis enregistrez chaque entree de poids et de calories.
            </p>
        </header>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Recherche</p>
                    <h2>Trouver un regime</h2>
                </div>
            </div>
            <label class="field">
                <span>Recherche par ID, type ou calories</span>
                <input id="searchInput" type="text" placeholder="Ex: R-001, cut, 2200">
            </label>
            <div class="results-grid" id="searchResults"></div>
        </section>

        <section class="surface surface--highlight is-hidden" id="selectedRegimeSection">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Regime actif</p>
                    <h2 id="selectedRegimeTitle">Aucun regime selectionne</h2>
                </div>
            </div>
            <div class="stats-grid stats-grid--compact" id="selectedRegimeStats"></div>
        </section>

        <section class="stats-grid is-hidden" id="suiviStatsSection">
            <article class="stat-card">
                <span class="stat-card__label">Jours suivis</span>
                <strong id="statDays">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Poids actuel</span>
                <strong id="statWeight">-</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Variation</span>
                <strong id="statChange">-</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Calories moyennes</span>
                <strong id="statCalories">-</strong>
            </article>
        </section>

        <section class="surface is-hidden" id="suiviFormSection">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Edition</p>
                    <h2 id="suiviFormTitle">Nouvelle entree</h2>
                </div>
            </div>
            <form id="suiviForm" class="form-grid form-grid--three">
                <label class="field">
                    <span>Date</span>
                    <input id="followDate" type="date" required>
                </label>
                <label class="field">
                    <span>Poids (kg)</span>
                    <input id="followWeight" type="number" min="20" max="300" step="0.1" required>
                </label>
                <label class="field">
                    <span>Calories</span>
                    <input id="followCalories" type="number" min="0" max="10000" required>
                </label>
                <div class="button-row field--full">
                    <button class="button button--primary" id="suiviSubmitButton" type="submit">Enregistrer</button>
                    <button class="button button--ghost" id="suiviResetButton" type="reset">Annuler</button>
                </div>
                <p class="feedback" id="suiviFeedback" aria-live="polite"></p>
            </form>
        </section>

        <section class="surface is-hidden" id="suiviTableSection">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Historique</p>
                    <h2>Entrees de suivi</h2>
                </div>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Poids</th>
                            <th>Calories</th>
                            <th>Ecart</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="suiviTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <script>window.API_URL = '../../Controller/api-regime.php';</script>
    <script src="js/suivi.js"></script>
</body>
</html>
