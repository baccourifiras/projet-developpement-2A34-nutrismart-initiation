<?php
/*
 * ============================================================
 * NutriSmart — Module Régime
 * Page : Suivi Régime par Jour (Front Office)
 * Table détails: suivi_regime (id_suivi, id_regime, date, poids, calories_consommees)
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Suivi Régime</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css" />
  <style>
    :root {
      --primary:       #17995f;
      --primary-dark:  #0f6d42;
      --secondary:     #7c3aed;
      --bg:            #f4faf6;
      --text:          #10281b;
      --muted:         #688273;
      --border:        rgba(23,153,95,.18);
      --shadow:        0 18px 60px rgba(12,29,21,.08);
      --radius:        22px;
      --navbar-h:      72px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Outfit', Arial, sans-serif;
      background:
        radial-gradient(circle at top left,  rgba(23,153,95,.13), transparent 30%),
        radial-gradient(circle at right top, rgba(124,58,237,.08), transparent 26%),
        linear-gradient(180deg, #f8fcf9 0%, #f2f8f4 100%);
      color: var(--text); min-height: 100vh;
    }

    /* ── NAVBAR ─────────────────────────────────────────── */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 999;
      height: var(--navbar-h);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 36px;
      background: rgba(12,29,21,.92); backdrop-filter: blur(18px);
      border-bottom: 1px solid rgba(255,255,255,.07);
      transition: background .3s, box-shadow .3s;
    }
    .navbar.scrolled { background: rgba(10,24,17,.97); box-shadow: 0 8px 32px rgba(0,0,0,.22); }
    .nav-brand { display: flex; flex-direction: column; gap: 2px; }
    .nav-brand .logo { color: #fff; font-size: 22px; font-weight: 900; letter-spacing: -.5px; }
    .nav-brand .slogan { color: #6ee7b7; font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .nav-links { display: flex; align-items: center; gap: 6px; }
    .nav-links a { color: rgba(255,255,255,.78); font-size: 14px; font-weight: 600; padding: 8px 14px; border-radius: 12px; text-decoration: none; transition: background .2s, color .2s; }
    .nav-links a:hover, .nav-links a.active { background: rgba(23,153,95,.22); color: #fff; }
    .nav-links a.nav-dashboard { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; padding: 8px 18px; }

    /* ── PAGE HEADER ─────────────────────────────────────── */
    .page-header { padding: calc(var(--navbar-h) + 60px) 40px 52px; text-align: center; }
    .badge { display: inline-flex; align-items: center; gap: 6px; padding: 7px 18px; border-radius: 999px; background: rgba(23,153,95,.12); color: var(--primary-dark); font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; margin-bottom: 18px; }
    .page-header h1 { font-size: clamp(30px,5vw,52px); font-weight: 900; line-height: 1.1; }
    .page-header .subtitle { margin-top: 12px; color: var(--muted); font-size: 17px; }

    /* ── CONTAINER ───────────────────────────────────────── */
    .container { max-width: 1100px; margin: 0 auto; padding: 0 32px 80px; }
    .section { margin-bottom: 50px; }
    .section-title { font-size: 22px; font-weight: 800; margin-bottom: 22px; display: flex; align-items: center; gap: 12px; }
    .section-title::after { content:""; flex:1; height:2px; background: linear-gradient(90deg, var(--border), transparent); }

    /* ── RECHERCHE & SÉLECTEUR ─────────────────────────────── */
    .search-bar { background: rgba(255,255,255,.93); border-radius: var(--radius); border: 1px solid rgba(255,255,255,.8); box-shadow: var(--shadow); padding: 22px 28px; margin-bottom: 24px; }
    .search-input { width: 100%; padding: 13px 16px; border: 1.5px solid var(--border); border-radius: 14px; background: #fbfffc; font-family: 'Outfit', sans-serif; font-size: 15px; color: var(--text); }
    .search-input:focus { outline: none; border-color: rgba(23,153,95,.5); box-shadow: 0 0 0 4px rgba(23,153,95,.1); }
    .search-input::placeholder { color: #ccc; }

    /* ── RÉSULTATS RECHERCHE ─────────────────────────────── */
    .regime-results { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; }
    .regime-result-card { background: rgba(255,255,255,.9); border: 1px solid rgba(23,153,95,.2); border-radius: 16px; padding: 18px 20px; cursor: pointer; transition: all .2s; }
    .regime-result-card:hover { background: rgba(23,153,95,.08); transform: translateY(-2px); border-color: rgba(23,153,95,.4); }
    .regime-result-id { font-size: 11px; color: var(--muted); font-weight: 600; margin-bottom: 6px; }
    .regime-result-type { display: inline-block; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; margin-bottom: 8px; background: rgba(23,153,95,.1); color: var(--primary-dark); }
    .regime-result-cals { font-size: 13px; color: var(--text); margin-bottom: 8px; }
    .regime-result-select { padding: 8px 16px; background: rgba(23,153,95,.12); border: 1px solid var(--border); border-radius: 10px; font-size: 12px; font-weight: 700; color: var(--primary-dark); cursor: pointer; transition: background .2s; }
    .regime-result-select:hover { background: rgba(23,153,95,.2); }

    /* ── RÉGIME ACTIF BANNER ────────────────────────────── */
    .regime-active-banner { background: linear-gradient(135deg, rgba(23,153,95,.9), rgba(15,109,66,.95)); border-radius: var(--radius); padding: 26px 32px; color: #fff; margin-bottom: 36px; display: none; box-shadow: 0 16px 48px rgba(23,153,95,.22); }
    .regime-active-banner.visible { display: block; }
    .banner-info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; }
    .banner-item { }
    .banner-item .label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; opacity: .75; }
    .banner-item .value { font-size: 22px; font-weight: 900; margin-top: 4px; }

    /* ── STATS ROW ───────────────────────────────────────── */
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 30px; }
    .stat-card { background: rgba(255,255,255,.92); border: 1px solid rgba(255,255,255,.8); border-radius: 16px; padding: 18px; box-shadow: var(--shadow); }
    .stat-card .icon { font-size: 22px; margin-bottom: 6px; }
    .stat-card .label { color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 4px; }
    .stat-card .value { font-size: 28px; font-weight: 900; color: var(--primary-dark); }

    /* ── FORMULAIRE ─────────────────────────────────────── */
    .form-card { background: rgba(255,255,255,.94); border: 1px solid rgba(255,255,255,.8); border-radius: var(--radius); padding: 28px; box-shadow: var(--shadow); margin-bottom: 32px; }
    .form-card h2 { font-size: 18px; font-weight: 800; margin-bottom: 20px; color: var(--primary-dark); }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 13px; font-weight: 700; color: var(--text); }
    .form-group input { padding: 11px 13px; border: 1.5px solid var(--border); border-radius: 12px; background: #fbfffc; font-family: 'Outfit', sans-serif; font-size: 14px; color: var(--text); }
    .form-group input:focus { outline: none; border-color: rgba(23,153,95,.5); box-shadow: 0 0 0 4px rgba(23,153,95,.1); }
    .form-actions { margin-top: 18px; display: flex; gap: 10px; flex-wrap: wrap; }
    .primary-btn { padding: 11px 24px; border: none; border-radius: 12px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; font-size: 14px; font-weight: 800; cursor: pointer; font-family: 'Outfit', sans-serif; box-shadow: 0 8px 20px rgba(23,153,95,.2); transition: transform .2s, box-shadow .2s; }
    .primary-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(23,153,95,.3); }

    /* ── TABLE SUIVI ────────────────────────────────────── */
    .suivi-table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,.93); border-radius: 14px; overflow: hidden; box-shadow: var(--shadow); }
    .suivi-table th { background: rgba(23,153,95,.08); padding: 14px; text-align: left; font-size: 13px; font-weight: 800; color: var(--primary-dark); border-bottom: 1px solid var(--border); }
    .suivi-table td { padding: 13px 14px; border-bottom: 1px solid rgba(255,255,255,.5); font-size: 14px; }
    .suivi-table tr:hover { background: rgba(23,153,95,.04); }
    .action-btn { padding: 6px 12px; border: none; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; margin-right: 6px; }
    .edit-btn { background: rgba(59,130,246,.1); color: #1d4ed8; }
    .delete-btn { background: rgba(239,68,68,.1); color: #b91c1c; }

    /* ── MESSAGE ────────────────────────────────────────── */
    .msg-box { padding: 12px 16px; border-radius: 10px; margin-top: 12px; font-weight: 700; font-size: 13px; display: none; }
    .msg-success { background: rgba(23,153,95,.1); color: var(--primary-dark); display: block; }
    .msg-error { background: rgba(239,68,68,.1); color: #b91c1c; display: block; }

    /* ── EMPTY STATE ────────────────────────────────────── */
    .empty-state { text-align: center; padding: 50px 20px; background: rgba(255,255,255,.7); border-radius: var(--radius); border: 2px dashed var(--border); }
    .empty-state .icon { font-size: 48px; margin-bottom: 12px; }
    .empty-state h3 { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
    .empty-state p { color: var(--muted); }

    @media (max-width: 768px) {
      .page-header { padding-top: calc(var(--navbar-h) + 36px); }
      .container { padding: 0 16px 60px; }
      .regime-results { grid-template-columns: 1fr; }
      .form-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="accueil.php">Accueil</a>
      <a href="index.php">Événements</a>
      <a href="regime.php">Régimes</a>
      <a href="suivi-regime.php">Suivi</a>
      <a href="historique.php">Historique</a>
      <a href="../backoffice/regime-admin.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- HEADER -->
  <header class="page-header">
    <p class="badge">📊 Suivi Quotidien</p>
    <h1>Suivi de mon Régime</h1>
    <p class="subtitle">Enregistrez votre poids et vos calories consommées chaque jour.</p>
  </header>

  <!-- MAIN -->
  <main class="container">

    <!-- RECHERCHE RÉGIMES -->
    <section class="section">
      <h2 class="section-title">Chercher un régime</h2>
      <div class="search-bar">
        <input type="text" id="searchInput" class="search-input" placeholder="🔍 Tapez un ID (#R-001), type (cut, bulk, equilibre) ou calories (2000)..." />
      </div>
      <div id="resultsContainer"></div>
    </section>

    <!-- RÉGIME ACTIF -->
    <div class="regime-active-banner" id="activeBanner"></div>

    <!-- STATS SECTION -->
    <section class="section" id="statsSection" style="display:none;">
      <h2 class="section-title">Statistiques</h2>
      <div class="stats-row">
        <div class="stat-card">
          <div class="icon">📅</div>
          <div class="label">Jours suivis</div>
          <div class="value" id="statDays">0</div>
        </div>
        <div class="stat-card">
          <div class="icon">⚖️</div>
          <div class="label">Poids actuel</div>
          <div class="value" id="statWeight">—</div>
        </div>
        <div class="stat-card">
          <div class="icon">📉</div>
          <div class="label">Variation</div>
          <div class="value" id="statChange">—</div>
        </div>
        <div class="stat-card">
          <div class="icon">🔥</div>
          <div class="label">Moy. kcal/jour</div>
          <div class="value" id="statCalories">—</div>
        </div>
      </div>
    </section>

    <!-- FORMULAIRE AJOUT/ÉDITION SUIVI -->
    <section class="section" id="formSection" style="display:none;">
      <h2 class="section-title">Enregistrer un suivi</h2>
      <div class="form-card">
        <h2 id="formTitle">Nouvelle entrée</h2>
        <form id="suiviForm">
          <div class="form-grid">
            <div class="form-group">
              <label for="followDate">📅 Date</label>
              <input type="date" id="followDate" required />
            </div>
            <div class="form-group">
              <label for="followWeight">⚖️ Poids (kg)</label>
              <input type="number" id="followWeight" required min="20" max="300" step="0.1" />
            </div>
            <div class="form-group">
              <label for="followCalories">🔥 Calories</label>
              <input type="number" id="followCalories" required min="0" max="10000" />
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="primary-btn" id="submitBtn">💾 Enregistrer</button>
            <button type="reset" class="primary-btn" style="background: rgba(23,153,95,.1); color: var(--primary-dark);">🔄 Annuler</button>
          </div>
          <div id="formMsg" class="msg-box"></div>
        </form>
      </div>
    </section>

    <!-- TABLE SUIVIS -->
    <section class="section" id="tableSection" style="display:none;">
      <h2 class="section-title">Historique des suivis</h2>
      <div style="overflow-x: auto;">
        <table class="suivi-table" id="suiviTable">
          <thead>
            <tr>
              <th>📅 Date</th>
              <th>⚖️ Poids (kg)</th>
              <th>🔥 Calories</th>
              <th>Diff cal.</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="suiviBody"></tbody>
        </table>
      </div>
    </section>

  </main>

  <script>
  const API_URL = '../back/api-regime.php';
  let regimeActif = null;
  let suiviEnEdition = null;
  let allRegimes = [];

  /* ── API CALL ────────────────────────────────────────────── */
  async function apiCall(action, method = 'GET', data = null) {
    try {
      const url = API_URL + '?action=' + action;
      const options = { method };
      if (method === 'POST' && data) {
        options.headers = { 'Content-Type': 'application/json' };
        options.body = JSON.stringify(data);
      }
      const response = await fetch(url, options);
      const json = await response.json();
      if (!response.ok) throw new Error(json.error || 'Erreur serveur');
      return json;
    } catch (err) {
      console.error('API Error:', err);
      throw err;
    }
  }

  /* ── GET REGIMES ────────────────────────────────────────── */
  async function getRegimes() {
    try {
      return await apiCall('regimes');
    } catch (err) {
      return [];
    }
  }

  /* ── GET SUIVIS ────────────────────────────────────────── */
  async function getSuivis(idRegime) {
    try {
      const followUps = await apiCall('suivis');
      if (!idRegime) return followUps;
      return Array.isArray(followUps) ? followUps.filter(s => s.id_regime === parseInt(idRegime)) : [];
    } catch (err) {
      return [];
    }
  }

  /* ── AFFICHER RÉSULTATS RECHERCHE ───────────────────────– */
  async function displaySearchResults(query) {
    const container = document.getElementById('resultsContainer');
    
    if (!query.trim()) {
      container.innerHTML = '';
      return;
    }

    const q = query.toLowerCase();
    const filtered = allRegimes.filter(r => {
      const id = `#r-${String(r.id_regime).padStart(3,'0')}`.toLowerCase();
      const type = r.type_regime.toLowerCase();
      const cal = String(r.calories_cible).toLowerCase();
      return id.includes(q) || type.includes(q) || cal.includes(q);
    });

    if (filtered.length === 0) {
      container.innerHTML = '<div class="empty-state"><div class="icon">🔍</div><h3>Aucun régime trouvé</h3><p>Essayez une autre recherche...</p></div>';
      return;
    }

    container.innerHTML = '<div class="regime-results">' + filtered.map(r => `
      <div class="regime-result-card" onclick="selectRegime(${r.id_regime})">
        <div class="regime-result-id">#R-${String(r.id_regime).padStart(3,'0')}</div>
        <span class="regime-result-type">${r.type_regime}</span>
        <div class="regime-result-cals">📊 ${r.calories_cible} kcal/jour</div>
        <button type="button" class="regime-result-select">Sélectionner</button>
      </div>
    `).join('') + '</div>';
  }

  /* ── SELECT RÉGIME ──────────────────────────────────────– */
  async function selectRegime(idRegime) {
    try {
      regimeActif = allRegimes.find(r => r.id_regime === idRegime);
      if (!regimeActif) return;

      // Afficher banner
      const banner = document.getElementById('activeBanner');
      banner.innerHTML = `
        <div class="banner-info-grid">
          <div class="banner-item">
            <div class="label">Type régime</div>
            <div class="value">${regimeActif.type_regime.toUpperCase()}</div>
          </div>
          <div class="banner-item">
            <div class="label">Calories cible</div>
            <div class="value">${regimeActif.calories_cible} kcal</div>
          </div>
          <div class="banner-item">
            <div class="label">Poids initial</div>
            <div class="value">${regimeActif.poids_initial} kg</div>
          </div>
          <div class="banner-item">
            <div class="label">Durée</div>
            <div class="value">${regimeActif.duree} jours</div>
          </div>
          <div class="banner-item">
            <div class="label">Début</div>
            <div class="value">${new Date(regimeActif.date_debut).toLocaleDateString('fr-FR')}</div>
          </div>
        </div>
      `;
      banner.classList.add('visible');

      // Afficher sections
      document.getElementById('statsSection').style.display = 'block';
      document.getElementById('formSection').style.display = 'block';
      document.getElementById('tableSection').style.display = 'block';

      // Init form
      document.getElementById('followDate').valueAsDate = new Date();
      suiviEnEdition = null;
      document.getElementById('formTitle').textContent = 'Nouvelle entrée';
      document.getElementById('submitBtn').textContent = '💾 Enregistrer';
      document.getElementById('suiviForm').reset();

      // Charger données
      await afficherSuivis();
      await updateStats();
    } catch (err) {
      console.error('selectRegime error:', err);
    }
  }

  /* ── AFFICHER SUIVIS ────────────────────────────────────– */
  async function afficherSuivis() {
    const suivis = await getSuivis(regimeActif.id_regime);
    const tbody = document.getElementById('suiviBody');
    
    if (!Array.isArray(suivis) || suivis.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 40px;">Aucun suivi enregistré</td></tr>';
      return;
    }

    const sorted = suivis.slice().sort((a, b) => new Date(b.date) - new Date(a.date));
    tbody.innerHTML = sorted.map(s => {
      const diff = s.calories_consommees - regimeActif.calories_cible;
      const diffClass = Math.abs(diff) <= 150 ? '' : (diff > 0 ? 'style="color: #b91c1c;"' : 'style="color: #16a34a;"');
      return `
        <tr>
          <td>${new Date(s.date).toLocaleDateString('fr-FR')}</td>
          <td><strong>${s.poids} kg</strong></td>
          <td>${s.calories_consommees} kcal</td>
          <td ${diffClass}>${diff > 0 ? '+' : ''}${diff}</td>
          <td>
            <button type="button" class="action-btn edit-btn" onclick="editSuivi(${s.id_suivi}, ${s.id_regime}, '${s.date}', ${s.poids}, ${s.calories_consommees})">✏️ Éditer</button>
            <button type="button" class="action-btn delete-btn" onclick="deleteSuivi(${s.id_suivi})">🗑️ Suppr.</button>
          </td>
        </tr>
      `;
    }).join('');
  }

  /* ── ÉDITER SUIVI ───────────────────────────────────────– */
  async function editSuivi(idSuivi, idRegime, date, poids, calories) {
    suiviEnEdition = idSuivi;
    document.getElementById('formTitle').textContent = 'Éditer le suivi';
    document.getElementById('submitBtn').textContent = '✏️ Mettre à jour';
    document.getElementById('followDate').value = date;
    document.getElementById('followWeight').value = poids;
    document.getElementById('followCalories').value = calories;
    document.getElementById('followDate').focus();
  }

  /* ── SUPPRIMER SUIVI ────────────────────────────────────– */
  async function deleteSuivi(idSuivi) {
    if (!confirm('Supprimer ce suivi ?')) return;
    try {
      await apiCall('delete', 'POST', { type: 'suivi', id: idSuivi });
      await afficherSuivis();
      await updateStats();
    } catch (err) {
      alert('❌ Erreur: ' + err.message);
    }
  }

  /* ── UPDATE STATS ───────────────────────────────────────– */
  async function updateStats() {
    const suivis = await getSuivis(regimeActif.id_regime);
    
    if (!Array.isArray(suivis) || suivis.length === 0) {
      document.getElementById('statDays').textContent = '0';
      document.getElementById('statWeight').textContent = '—';
      document.getElementById('statChange').textContent = '—';
      document.getElementById('statCalories').textContent = '—';
      return;
    }

    const latestWeight = suivis[suivis.length - 1].poids;
    const variation = (latestWeight - regimeActif.poids_initial).toFixed(1);
    const avgCalories = Math.round(suivis.reduce((s, x) => s + x.calories_consommees, 0) / suivis.length);

    document.getElementById('statDays').textContent = suivis.length;
    document.getElementById('statWeight').textContent = latestWeight + ' kg';
    document.getElementById('statChange').textContent = (variation >= 0 ? '+' : '') + variation + ' kg';
    document.getElementById('statCalories').textContent = avgCalories;
  }

  /* ── FORM SUBMIT ────────────────────────────────────────– */
  document.getElementById('suiviForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!regimeActif) {
      alert('Sélectionnez un régime d\'abord!');
      return;
    }

    try {
      const data = {
        id_regime: regimeActif.id_regime,
        date: document.getElementById('followDate').value,
        poids: parseFloat(document.getElementById('followWeight').value),
        calories_consommees: parseInt(document.getElementById('followCalories').value)
      };

      if (suiviEnEdition) {
        // UPDATE
        data.id_suivi = suiviEnEdition;
        await apiCall('editSuivi', 'POST', data);
      } else {
        // INSERT
        await apiCall('suivi', 'POST', data);
      }

      const msg = document.getElementById('formMsg');
      msg.className = 'msg-box msg-success';
      msg.textContent = suiviEnEdition ? '✏️ Suivi mis à jour!' : '✅ Suivi enregistré!';

      document.getElementById('suiviForm').reset();
      document.getElementById('followDate').valueAsDate = new Date();
      suiviEnEdition = null;
      document.getElementById('formTitle').textContent = 'Nouvelle entrée';
      document.getElementById('submitBtn').textContent = '💾 Enregistrer';

      await afficherSuivis();
      await updateStats();

      setTimeout(() => { msg.className = 'msg-box'; }, 3000);
    } catch (err) {
      const msg = document.getElementById('formMsg');
      msg.className = 'msg-box msg-error';
      msg.textContent = '❌ ' + err.message;
    }
  });

  /* ── SEARCH INPUT ───────────────────────────────────────– */
  document.getElementById('searchInput').addEventListener('input', (e) => {
    displaySearchResults(e.target.value);
  });

  /* ── NAVBAR SCROLL ──────────────────────────────────────– */
  window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
  });
  document.querySelectorAll('.nav-links a').forEach(a => {
    if (a.getAttribute('href') === 'suivi-regime.php') a.classList.add('active');
  });

  /* ── INIT ───────────────────────────────────────────────– */
  (async () => {
    allRegimes = await getRegimes();
    document.getElementById('followDate').valueAsDate = new Date();
    if (!Array.isArray(allRegimes)) allRegimes = [];
  })();
  </script>
</body>
</html>
