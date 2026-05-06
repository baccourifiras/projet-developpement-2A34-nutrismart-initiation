<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSmart - Regimes</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/regime.css">
</head>
<body class="page-regime">
    <nav class="site-nav">
        <div class="site-nav__brand">
            <span class="site-nav__title">NutriSmart</span>
            <span class="site-nav__tag">Front office</span>
        </div>
        <div class="site-nav__links">
            <a class="site-nav__link is-active" href="regime.php">Regimes</a>
            <a class="site-nav__link" href="suivi-regime.php">Suivi</a>
            <a class="site-nav__link" href="historique.php">Historique</a>
            <a class="site-nav__button" href="../BackOffice/regime-admin.php">Admin</a>
        </div>
    </nav>

    <main class="page-shell">
        <header class="hero hero--regime">
            <p class="eyebrow">Module regime</p>
            <h1>Construisez des programmes clairs et faciles a suivre.</h1>
            <p class="hero-copy">
                Cette page vous permet de creer, modifier et supprimer vos regimes alimentaires.
            </p>
        </header>

        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-card__label">Total</span>
                <strong id="statTotal">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Cut</span>
                <strong id="statCut">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Bulk</span>
                <strong id="statBulk">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Equilibre</span>
                <strong id="statEquilibre">0</strong>
            </article>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Edition</p>
                    <h2 id="regimeFormTitle">Nouveau regime</h2>
                </div>
            </div>

            <form id="regimeForm" class="form-grid form-grid--two">
                <label class="field">
                    <span>Type de regime</span>
                    <select id="typeRegime" required>
                        <option value="">Selectionner</option>
                        <option value="cut">Cut</option>
                        <option value="bulk">Bulk</option>
                        <option value="equilibre">Equilibre</option>
                    </select>
                </label>
                <label class="field">
                    <span>Calories cible</span>
                    <input id="caloriesCible" type="number" min="500" max="6000" required placeholder="2000">
                </label>
                <label class="field">
                    <span>Date de debut</span>
                    <input id="dateDebut" type="date" required>
                </label>
                <label class="field">
                    <span>Poids initial (kg)</span>
                    <input id="poidsInitial" type="number" min="20" max="300" step="0.1" required placeholder="75.5">
                </label>
                <label class="field field--full">
                    <span>Duree (jours)</span>
                    <input id="duree" type="number" min="1" max="365" required placeholder="30">
                </label>
                <div class="button-row field--full">
                    <button class="button button--primary" type="submit" id="regimeSubmitButton">Enregistrer</button>
                    <button class="button button--ghost" type="reset" id="regimeResetButton">Annuler</button>
                </div>
                <p class="feedback" id="regimeFeedback" aria-live="polite"></p>
            </form>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Catalogue</p>
                    <h2>Regimes enregistres</h2>
                </div>
            </div>

            <div class="filter-row" id="regimeFilters">
                <button class="chip is-active" type="button" data-filter="tous">Tous</button>
                <button class="chip" type="button" data-filter="cut">Cut</button>
                <button class="chip" type="button" data-filter="bulk">Bulk</button>
                <button class="chip" type="button" data-filter="equilibre">Equilibre</button>
            </div>

            <div class="card-grid" id="regimeList"></div>
        </section>
    </main>

    <div class="modal-backdrop" id="deleteModal" hidden>
        <div class="modal-card">
            <p class="section-heading__kicker">Suppression</p>
            <h3>Supprimer ce regime ?</h3>
            <p class="modal-copy">Les suivis et recommandations relies seront aussi supprimes.</p>
            <div class="button-row">
                <button class="button button--ghost" id="deleteCancelButton" type="button">Annuler</button>
                <button class="button button--danger" id="deleteConfirmButton" type="button">Supprimer</button>
            </div>
        </div>
    </div>

    <script>window.API_URL = '../../Controller/api-regime.php';</script>
    <script src="js/regime.js"></script>
</body>
</html>
