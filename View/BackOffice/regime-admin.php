<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriSmart - BackOffice regime</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="page-admin">
    <main class="page-shell page-shell--admin">
        <header class="hero hero--admin">
            <p class="eyebrow">BackOffice regime</p>
            <h1>Gestion des regimes, suivis et recommandations.</h1>
            <p class="hero-copy">
                Cette page admin utilise seulement les vues pour l'interface, les modeles pour les classes et le controller pour les fonctions.
            </p>
        </header>

        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-card__label">Regimes</span>
                <strong id="adminStatRegimes">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Suivis</span>
                <strong id="adminStatSuivis">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Recommandations</span>
                <strong id="adminStatHistos">0</strong>
            </article>
            <article class="stat-card">
                <span class="stat-card__label">Moyenne kcal</span>
                <strong id="adminStatCalories">-</strong>
            </article>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Maintenance</p>
                    <h2>Actions globales</h2>
                </div>
                <div class="button-row">
                    <a class="button button--ghost" href="../FrontOffice/regime.php">Voir le front</a>
                    <button class="button button--danger" id="resetAllButton" type="button">Reset data</button>
                </div>
            </div>
            <p class="feedback" id="adminFeedback" aria-live="polite"></p>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Regimes</p>
                    <h2 id="adminRegimeTitle">Ajouter un regime</h2>
                </div>
            </div>
            <form id="adminRegimeForm" class="form-grid form-grid--two">
                <input id="adminRegimeId" type="hidden">
                <label class="field">
                    <span>Type</span>
                    <select id="adminRegimeType" required>
                        <option value="">Selectionner</option>
                        <option value="cut">Cut</option>
                        <option value="bulk">Bulk</option>
                        <option value="equilibre">Equilibre</option>
                    </select>
                </label>
                <label class="field">
                    <span>Calories</span>
                    <input id="adminRegimeCalories" type="number" min="500" max="6000" required>
                </label>
                <label class="field">
                    <span>Date de debut</span>
                    <input id="adminRegimeDate" type="date" required>
                </label>
                <label class="field">
                    <span>Poids initial</span>
                    <input id="adminRegimePoids" type="number" min="20" max="300" step="0.1" required>
                </label>
                <label class="field field--full">
                    <span>Duree</span>
                    <input id="adminRegimeDuree" type="number" min="1" max="365" required>
                </label>
                <div class="button-row field--full">
                    <button class="button button--primary" type="submit">Enregistrer</button>
                    <button class="button button--ghost" id="adminRegimeCancel" type="reset">Annuler</button>
                </div>
            </form>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Calories</th>
                            <th>Date</th>
                            <th>Poids initial</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminRegimeTableBody"></tbody>
                </table>
            </div>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Suivis</p>
                    <h2 id="adminSuiviTitle">Ajouter un suivi</h2>
                </div>
            </div>
            <form id="adminSuiviForm" class="form-grid form-grid--two">
                <input id="adminSuiviId" type="hidden">
                <label class="field">
                    <span>Regime</span>
                    <select id="adminSuiviRegime" required>
                        <option value="">Selectionner</option>
                    </select>
                </label>
                <label class="field">
                    <span>Date</span>
                    <input id="adminSuiviDate" type="date" required>
                </label>
                <label class="field">
                    <span>Poids</span>
                    <input id="adminSuiviPoids" type="number" min="20" max="300" step="0.1" required>
                </label>
                <label class="field">
                    <span>Calories</span>
                    <input id="adminSuiviCalories" type="number" min="0" max="10000" required>
                </label>
                <div class="button-row field--full">
                    <button class="button button--primary" type="submit">Enregistrer</button>
                    <button class="button button--ghost" id="adminSuiviCancel" type="reset">Annuler</button>
                </div>
            </form>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Regime</th>
                            <th>Date</th>
                            <th>Poids</th>
                            <th>Calories</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminSuiviTableBody"></tbody>
                </table>
            </div>
        </section>

        <section class="surface">
            <div class="section-heading">
                <div>
                    <p class="section-heading__kicker">Historique</p>
                    <h2 id="adminHistoTitle">Ajouter une recommandation</h2>
                </div>
            </div>
            <form id="adminHistoForm" class="form-grid">
                <input id="adminHistoId" type="hidden">
                <label class="field">
                    <span>Regime</span>
                    <select id="adminHistoRegime" required>
                        <option value="">Selectionner</option>
                    </select>
                </label>
                <label class="field">
                    <span>Texte</span>
                    <textarea id="adminHistoTexte" rows="4" required></textarea>
                </label>
                <div class="button-row">
                    <button class="button button--primary" type="submit">Enregistrer</button>
                    <button class="button button--ghost" id="adminHistoCancel" type="reset">Annuler</button>
                </div>
            </form>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Regime</th>
                            <th>Texte</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="adminHistoTableBody"></tbody>
                </table>
            </div>
        </section>
    </main>

    <script>window.API_URL = '../../Controller/api-regime.php';</script>
    <script src="js/admin.js"></script>
</body>
</html>
