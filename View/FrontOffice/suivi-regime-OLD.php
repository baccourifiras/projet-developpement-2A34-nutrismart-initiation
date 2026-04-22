<?php
/*
 * ============================================================
 * NutriSmart — Module Régime
 * Page : Suivi Régime par Jour (Front Office)
 * Table : suivi_regime (id_suivi, id_regime, date, poids,
 *                       calories_consommees)
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
    .nav-links a {
      color: rgba(255,255,255,.78); font-size: 14px; font-weight: 600;
      padding: 8px 14px; border-radius: 12px; text-decoration: none;
      transition: background .2s, color .2s;
    }
    .nav-links a:hover, .nav-links a.active { background: rgba(23,153,95,.22); color: #fff; }
    .nav-links a.nav-dashboard {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff; padding: 8px 18px;
    }

    /* ── PAGE HEADER ─────────────────────────────────────── */
    .page-header {
      padding: calc(var(--navbar-h) + 60px) 40px 52px;
      text-align: center;
    }
    .badge {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 7px 18px; border-radius: 999px;
      background: rgba(23,153,95,.12); color: var(--primary-dark);
      font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em;
      margin-bottom: 18px;
    }
    .page-header h1 { font-size: clamp(30px,5vw,52px); font-weight: 900; line-height: 1.1; }
    .page-header .subtitle { margin-top: 12px; color: var(--muted); font-size: 17px; }

    /* ── CONTAINER ───────────────────────────────────────── */
    .container { max-width: 1100px; margin: 0 auto; padding: 0 32px 80px; }
    .section { margin-bottom: 50px; }
    .section-title {
      font-size: 22px; font-weight: 800; margin-bottom: 22px;
      display: flex; align-items: center; gap: 12px;
    }
    .section-title::after {
      content:""; flex:1; height:2px;
      background: linear-gradient(90deg, var(--border), transparent);
    }

    /* ── SÉLECTEUR DE RÉGIME ─────────────────────────────── */
    .regime-selector {
      background: rgba(255,255,255,.93); border-radius: var(--radius);
      border: 1px solid rgba(255,255,255,.8); box-shadow: var(--shadow);
      padding: 28px 32px; margin-bottom: 36px;
      display: flex; flex-direction: column; gap: 16px;
    }
    .regime-selector label { font-weight: 700; font-size: 15px; }
    .search-regime {
      width: 100%; padding: 13px 16px;
      border: 1.5px solid var(--border); border-radius: 14px;
      background: #fbfffc; font-family: 'Outfit', sans-serif; font-size: 15px;
      color: var(--text); transition: border-color .2s, box-shadow .2s;
    }
    .search-regime:focus { 
      outline: none; border-color: rgba(23,153,95,.5); 
      box-shadow: 0 0 0 4px rgba(23,153,95,.1);
    }
    .search-regime::placeholder { color: #ccc; }
    .regime-selector select {
      width: 100%; padding: 12px 16px;
      border: 1.5px solid var(--border); border-radius: 14px;
      background: #fbfffc; font-family: 'Outfit', sans-serif; font-size: 15px;
      color: var(--text);
    }
    .regime-selector select:focus { outline: none; border-color: rgba(23,153,95,.5); box-shadow: 0 0 0 4px rgba(23,153,95,.1); }

    /* ── RÉGIME INFO BANNER ──────────────────────────────── */
    .regime-banner {
      background: linear-gradient(135deg, rgba(23,153,95,.9), rgba(15,109,66,.95));
      border-radius: var(--radius); padding: 26px 32px; color: #fff;
      margin-bottom: 36px; display: none;
      box-shadow: 0 16px 48px rgba(23,153,95,.22);
    }
    .regime-banner.visible { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; }
    .regime-banner-info { display: flex; gap: 28px; flex-wrap: wrap; flex: 1; }
    .banner-item { display: flex; flex-direction: column; gap: 4px; }
    .banner-item .b-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; opacity: .75; }
    .banner-item .b-value { font-size: 20px; font-weight: 900; }

    /* ── STATS ROW ───────────────────────────────────────── */
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap: 16px; margin-bottom: 36px; }
    .stat-card {
      background: rgba(255,255,255,.92); border: 1px solid rgba(255,255,255,.8);
      border-radius: 18px; padding: 20px 20px;
      box-shadow: var(--shadow);
      transition: transform .3s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-card .stat-icon { font-size: 24px; margin-bottom: 8px; }
    .stat-card .stat-label { color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px; }
    .stat-card .stat-value { font-size: 30px; font-weight: 900; color: var(--primary-dark); }

    /* ── FORMULAIRE SUIVI ────────────────────────────────── */
    .form-card {
      background: rgba(255,255,255,.94); border: 1px solid rgba(255,255,255,.8);
      border-radius: var(--radius); padding: 32px;
      box-shadow: var(--shadow); margin-bottom: 36px;
    }
    .form-card h2 { font-size: 19px; font-weight: 800; margin-bottom: 22px; color: var(--primary-dark); }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 18px; }
    .form-group { display: flex; flex-direction: column; gap: 7px; }
    .form-group label { font-size: 13px; font-weight: 700; }
    .form-group input {
      padding: 12px 14px; border: 1.5px solid var(--border);
      border-radius: 14px; background: #fbfffc;
      font-size: 15px; font-family: 'Outfit', sans-serif;
      transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .form-group input:focus { outline:none; border-color: rgba(23,153,95,.5); box-shadow:0 0 0 4px rgba(23,153,95,.1); transform:translateY(-1px); }
    .form-actions { margin-top: 20px; display: flex; gap: 12px; }
    .primary-btn {
      padding: 13px 28px; border: none; border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff; font-size: 15px; font-weight: 800; cursor: pointer;
      font-family: 'Outfit', sans-serif; box-shadow: 0 12px 28px rgba(23,153,95,.22);
      transition: transform .25s, box-shadow .25s;
    }
    .primary-btn:hover { transform: translateY(-3px); box-shadow: 0 18px 36px rgba(23,153,95,.3); }
    .msg-box { padding: 13px 18px; border-radius: 12px; margin-top: 14px; font-weight: 700; font-size: 14px; display: none; }
    .msg-success { background: rgba(23,153,95,.1); color: var(--primary-dark); display: block; }
    .msg-error   { background: rgba(239,68,68,.1); color: #b91c1c; display: block; }

    /* ── ALERTE CALORIES ─────────────────────────────────── */
    .calorie-alert {
      padding: 14px 20px; border-radius: 14px; margin-top: 14px;
      font-weight: 700; font-size: 14px; display: none;
      border-left: 4px solid;
    }
    .alert-ok      { background: rgba(23,153,95,.08); border-color: var(--primary); color: var(--primary-dark); display: block; }
    .alert-surplus { background: rgba(239,68,68,.08); border-color: #dc2626; color: #b91c1c; display: block; }
    .alert-deficit { background: rgba(245,158,11,.08); border-color: #d97706; color: #92400e; display: block; }

    /* ── TABLEAU SUIVI ───────────────────────────────────── */
    .table-wrapper {
      background: rgba(255,255,255,.93); border-radius: var(--radius);
      border: 1px solid rgba(255,255,255,.8); box-shadow: var(--shadow); overflow-x: auto;
    }
    .suivi-table { width: 100%; border-collapse: collapse; }
    .suivi-table th {
      padding: 15px 18px; text-align: left;
      background: linear-gradient(180deg, #f5fbf7, #eff8f3);
      font-size: 13px; font-weight: 800; color: var(--primary-dark);
      border-bottom: 1px solid var(--border);
    }
    .suivi-table td {
      padding: 14px 18px; border-bottom: 1px solid rgba(23,153,95,.07);
      font-size: 14px;
    }
    .suivi-table tbody tr { transition: background .2s; }
    .suivi-table tbody tr:hover { background: rgba(23,153,95,.035); }
    .suivi-table tbody tr:last-child td { border-bottom: none; }
    .calorie-badge {
      display: inline-flex; align-items: center; padding: 5px 12px;
      border-radius: 999px; font-size: 12px; font-weight: 800;
    }
    .cal-ok      { background: rgba(23,153,95,.12); color: var(--primary-dark); }
    .cal-surplus { background: rgba(239,68,68,.12); color: #b91c1c; }
    .cal-deficit { background: rgba(245,158,11,.12); color: #92400e; }
    .id-chip {
      display: inline-flex; padding: 5px 12px; border-radius: 999px;
      background: rgba(23,153,95,.08); color: var(--primary-dark);
      font-size: 12px; font-weight: 800;
    }
    .del-btn {
      padding: 6px 12px; border: none; border-radius: 8px; font-size: 12px;
      font-weight: 700; cursor: pointer; background: rgba(239,68,68,.1); color: #b91c1c;
      font-family: 'Outfit', sans-serif;
      transition: background .2s, transform .2s;
    }
    .del-btn:hover { background: rgba(239,68,68,.2); transform: translateY(-1px); }

    /* ── GRAPHIQUE POIDS ─────────────────────────────────── */
    .chart-card {
      background: rgba(255,255,255,.93); border-radius: var(--radius);
      border: 1px solid rgba(255,255,255,.8); box-shadow: var(--shadow);
      padding: 28px 32px;
    }
    .chart-card h3 { font-size: 17px; font-weight: 800; margin-bottom: 20px; }
    .chart-canvas { width: 100%; height: 200px; position: relative; }
    .chart-bars { display: flex; align-items: flex-end; gap: 8px; height: 160px; padding: 0 8px; }
    .chart-bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; }
    .chart-bar {
      width: 100%; border-radius: 8px 8px 0 0;
      background: linear-gradient(180deg, var(--primary), var(--primary-dark));
      transition: height .5s ease, opacity .3s;
      min-height: 4px;
    }
    .chart-bar:hover { opacity: .8; }
    .chart-label { font-size: 10px; color: var(--muted); font-weight: 600; text-align: center; }

    /* ── EMPTY ───────────────────────────────────────────── */
    .empty-state {
      text-align: center; padding: 50px 20px;
      background: rgba(255,255,255,.7); border-radius: var(--radius);
      border: 2px dashed var(--border);
    }
    .empty-state .empty-icon { font-size: 44px; margin-bottom: 14px; }
    .empty-state h3 { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
    .empty-state p { color: var(--muted); }

    /* ── REVEAL ──────────────────────────────────────────── */
    .reveal { opacity: 0; transform: translateY(22px); transition: opacity .5s ease, transform .5s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    @media (max-width: 768px) {
      .page-header { padding-top: calc(var(--navbar-h) + 36px); }
      .container { padding: 0 16px 60px; }
      .regime-selector { flex-direction: column; align-items: flex-start; }
      .nav-links a { padding: 7px 10px; font-size: 13px; }
    }
  </style>
</head>
<body>

  <!-- ═══════════════════════════════════════════════════════
       NAVBAR
       ═══════════════════════════════════════════════════════ -->
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
      <a href="../BackOffice/regime-admin.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- ═══════════════════════════════════════════════════════
       HEADER
       ═══════════════════════════════════════════════════════ -->
  <header class="page-header">
    <p class="badge">📊 Suivi Quotidien</p>
    <h1>Suivi de mon Régime</h1>
    <p class="subtitle">Enregistrez votre poids et vos calories consommées chaque jour.</p>
  </header>

  <!-- ═══════════════════════════════════════════════════════
       CONTENU
       ═══════════════════════════════════════════════════════ -->
  <main class="container">

    <!-- SÉLECTEUR RÉGIME -->
    <section class="section">
      <div class="regime-selector reveal">
        <label for="searchRegime">🔍 Rechercher un régime :</label>
        <input type="text" id="searchRegime" class="search-regime" placeholder="Tapez un ID (#R-001), type (cut, bulk...) ou filtrez par calories..." />
        <label for="selectRegime" style="margin-top: 14px;">📋 Ou sélectionner directement :</label>
        <select id="selectRegime">
          <option value="">-- Choisir un régime --</option>
        </select>
      </div>

      <!-- BANNER INFO RÉGIME -->
      <div class="regime-banner" id="regimeBanner">
        <div class="regime-banner-info" id="bannerInfo"></div>
      </div>
    </section>

    <!-- STATISTIQUES SUIVI -->
    <section class="section" id="statsSection" style="display:none;">
      <h2 class="section-title">Résumé du suivi</h2>
      <div class="stats-row">
        <div class="stat-card reveal">
          <div class="stat-icon">📅</div>
          <div class="stat-label">Jours suivis</div>
          <div class="stat-value" id="statJours">0</div>
        </div>
        <div class="stat-card reveal">
          <div class="stat-icon">⚖️</div>
          <div class="stat-label">Poids actuel (kg)</div>
          <div class="stat-value" id="statPoidsActuel">—</div>
        </div>
        <div class="stat-card reveal">
          <div class="stat-icon">📉</div>
          <div class="stat-label">Variation poids</div>
          <div class="stat-value" id="statVariation">—</div>
        </div>
        <div class="stat-card reveal">
          <div class="stat-icon">🔥</div>
          <div class="stat-label">Moy. calories/jour</div>
          <div class="stat-value" id="statMoyCalories">—</div>
        </div>
      </div>
    </section>

    <!-- FORMULAIRE SUIVI -->
    <section class="section" id="formSection" style="display:none;">
      <h2 class="section-title">Enregistrer une journée</h2>
      <div class="form-card reveal">
        <h2>Entrée quotidienne</h2>
        <form id="suiviForm">
          <div class="form-grid">
            <div class="form-group">
              <label for="dateSuivi">📅 Date</label>
              <input type="date" id="dateSuivi" required />
            </div>
            <div class="form-group">
              <label for="poidsSuivi">⚖️ Poids (kg)</label>
              <input type="number" id="poidsSuivi" required min="20" max="300" step="0.1" placeholder="Ex : 74.8" />
            </div>
            <div class="form-group">
              <label for="caloriesConsommees">🔥 Calories consommées</label>
              <input type="number" id="caloriesConsommees" required min="0" max="9999" placeholder="Ex : 1950" />
            </div>
          </div>
          <div class="calorie-alert" id="calorieAlert"></div>
          <div class="form-actions">
            <button type="submit" class="primary-btn">💾 Enregistrer</button>
          </div>
          <div id="suiviMsg" class="msg-box"></div>
        </form>
      </div>
    </section>

    <!-- GRAPHIQUE POIDS -->
    <section class="section" id="chartSection" style="display:none;">
      <h2 class="section-title">Évolution du poids</h2>
      <div class="chart-card reveal">
        <h3>📈 Courbe de poids (7 derniers jours)</h3>
        <div class="chart-bars" id="chartBars"></div>
      </div>
    </section>

    <!-- TABLEAU SUIVI -->
    <section class="section" id="tableSection" style="display:none;">
      <h2 class="section-title">Historique de suivi</h2>
      <div id="tableContainer"></div>
    </section>

  </main>

  <script>
  /* ============================================================
     NutriSmart — suivi-regime.php (CORRIGÉ - API BACKEND)
     ============================================================ */

  const API_BASE = '../../Controller/api-regime.php';
  var regimeActif = null;

  /* ── Appel API générique ────────────────────────────────– */
  async function apiCall(action, method = 'GET', data = null) {
    try {
      const url = API_BASE + '?action=' + action;
      const options = { method };
      
      if (method === 'POST' && data) {
        options.headers = { 'Content-Type': 'application/json' };
        options.body = JSON.stringify(data);
      }
      
      const response = await fetch(url, options);
      const json = await response.json();
      
      if (!response.ok) {
        throw new Error(json.error || 'Erreur serveur');
      }
      return json;
    } catch (err) {
      console.error('API Error:', err);
      throw err;
    }
  }

  /* ── Récupère les régimes depuis la base ────────────────– */
  async function getRegimes() {
    try {
      return await apiCall('regimes');
    } catch (err) {
      console.error('getRegimes error:', err);
      return [];
    }
  }

  /* ── Récupère les suivis depuis la base ─────────────────– */
  async function getSuivis(idRegime) {
    try {
      const suivis = await apiCall('suivis');
      if (!idRegime) return suivis;
      return Array.isArray(suivis) ? suivis.filter(s => s.id_regime === idRegime) : [];
    } catch (err) {
      console.error('getSuivis error:', err);
      return [];
    }
  }

  function fmtDate(d) {
    return new Intl.DateTimeFormat('fr-FR', {year:'numeric',month:'short',day:'numeric'}).format(new Date(d));
  }

  /* ── Charger la liste des régimes dans la dropdown ───────– */
  async function chargerRegimes() {
    try {
      const regimes = await getRegimes();
      const select = document.getElementById('selectRegime');
      select.innerHTML = '<option value="">-- Choisir un régime --</option>';
      
      if (Array.isArray(regimes) && regimes.length > 0) {
        regimes.forEach(r => {
          const opt = document.createElement('option');
          opt.value = r.id_regime;
          opt.textContent = `💪 Régime #R-${String(r.id_regime).padStart(3,'0')} — ${r.type_regime} (${r.calories_cible} kcal)`;
          select.appendChild(opt);
        });
      }
    } catch (err) {
      console.error('chargerRegimes error:', err);
    }
  }

  /* ── Recherche en temps réel ────────────────────────────– */
  let regimesCache = [];
  
  async function initSearchRegime() {
    try {
      regimesCache = await getRegimes();
      if (!Array.isArray(regimesCache)) regimesCache = [];
    } catch (err) {
      console.error('initSearchRegime error:', err);
    }
  }

  document.getElementById('searchRegime').addEventListener('input', async (e) => {
    const query = e.target.value.toLowerCase().trim();
    const select = document.getElementById('selectRegime');
    
    if (!query) {
      // Restaurer toutes les options si la recherche est vidée
      select.innerHTML = '<option value="">-- Choisir un régime --</option>';
      if (Array.isArray(regimesCache)) {
        regimesCache.forEach(r => {
          const opt = document.createElement('option');
          opt.value = r.id_regime;
          opt.textContent = `💪 Régime #R-${String(r.id_regime).padStart(3,'0')} — ${r.type_regime} (${r.calories_cible} kcal)`;
          select.appendChild(opt);
        });
      }
      return;
    }

    // Filtrer les régimes basé sur la recherche
    const filtered = regimesCache.filter(r => {
      const id = `#r-${String(r.id_regime).padStart(3,'0')}`.toLowerCase();
      const type = r.type_regime.toLowerCase();
      const cal = String(r.calories_cible).toLowerCase();
      
      return id.includes(query) || type.includes(query) || cal.includes(query);
    });

    // Mettre à jour la dropdown
    select.innerHTML = '<option value="">-- Choisir un régime --</option>';
    if (filtered.length > 0) {
      filtered.forEach(r => {
        const opt = document.createElement('option');
        opt.value = r.id_regime;
        opt.textContent = `💪 Régime #R-${String(r.id_regime).padStart(3,'0')} — ${r.type_regime} (${r.calories_cible} kcal)`;
        select.appendChild(opt);
      });
      // Sélectionner automatiquement si un seul résultat
      if (filtered.length === 1) {
        select.value = filtered[0].id_regime;
        afficherBannerRegime(filtered[0].id_regime);
      }
    } else {
      const opt = document.createElement('option');
      opt.textContent = '❌ Aucun régime trouvé';
      opt.disabled = true;
      select.appendChild(opt);
    }
  });

  /* ── Afficher banner du régime sélectionné ───────────────– */
  async function afficherBannerRegime(idRegime) {
    try {
      const regimes = await getRegimes();
      const r = Array.isArray(regimes) ? regimes.find(x => x.id_regime === Number(idRegime)) : null;
      
      if (!r) {
        document.getElementById('regimeBanner').classList.remove('visible');
        return;
      }

      regimeActif = r;
      const banner = document.getElementById('regimeBanner');
      const info = document.getElementById('bannerInfo');
      
      info.innerHTML = `
        <div class="banner-item">
          <div class="b-label">Type régime</div>
          <div class="b-value">${r.type_regime.toUpperCase()}</div>
        </div>
        <div class="banner-item">
          <div class="b-label">Calories cible</div>
          <div class="b-value">${r.calories_cible} kcal/j</div>
        </div>
        <div class="banner-item">
          <div class="b-label">Date de début</div>
          <div class="b-value">${fmtDate(r.date_debut)}</div>
        </div>
        <div class="banner-item">
          <div class="b-label">Poids initial</div>
          <div class="b-value">${r.poids_initial} kg</div>
        </div>
        <div class="banner-item">
          <div class="b-label">Durée</div>
          <div class="b-value">${r.duree} jours</div>
        </div>
      `;
      banner.classList.add('visible');
      
      document.getElementById('statsSection').style.display = 'block';
      document.getElementById('formSection').style.display = 'block';
      document.getElementById('chartSection').style.display = 'block';
      document.getElementById('tableSection').style.display = 'block';
      
      await afficherTableSuivi(idRegime);
      await majStats(idRegime);
      await afficherGraphique();
    } catch (err) {
      console.error('afficherBannerRegime error:', err);
    }
  }

  /* ── Afficher le tableau de suivi ───────────────────────– */
  async function afficherTableSuivi(idRegime) {
    try {
      const suivis = await getSuivis(Number(idRegime));
      const container = document.getElementById('tableContainer');
      
      if (!Array.isArray(suivis) || suivis.length === 0) {
        container.innerHTML = '<div class="empty-state"><div class="empty-icon">📋</div><h3>Aucun suivi</h3><p>Commencez à enregistrer vos données quotidiennes.</p></div>';
        return;
      }

      const sorted = suivis.slice().sort((a, b) => new Date(b.date) - new Date(a.date));
      const regimes = await getRegimes();
      const regime = Array.isArray(regimes) ? regimes.find(r => r.id_regime === Number(idRegime)) : null;
      const cibleCals = regime ? regime.calories_cible : 0;

      let html = `
        <div class="table-wrapper">
          <table class="suivi-table">
            <thead>
              <tr>
                <th>📅 Date</th>
                <th>⚖️ Poids</th>
                <th>🔥 Calories</th>
                <th>vs Cible</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
      `;

      sorted.forEach(s => {
        const diff = s.calories_consommees - cibleCals;
        let calClass = 'cal-ok';
        let calLabel = '✓ OK';
        if (Math.abs(diff) > 150) {
          calClass = diff > 0 ? 'cal-surplus' : 'cal-deficit';
          calLabel = diff > 0 ? `+${diff}` : `${diff}`;
        }

        html += `
          <tr>
            <td>${fmtDate(s.date)}</td>
            <td><strong>${s.poids} kg</strong></td>
            <td>${s.calories_consommees} kcal</td>
            <td><span class="calorie-badge ${calClass}">${calLabel}</span></td>
            <td><button class="del-btn" onclick="supprimerSuivi(${s.id_suivi})">Supprimer</button></td>
          </tr>
        `;
      });

      html += `
            </tbody>
          </table>
        </div>
      `;

      container.innerHTML = html;
    } catch (err) {
      console.error('afficherTableSuivi error:', err);
    }
  }

  /* ── Maj des stats ──────────────────────────────────────– */
  async function majStats(idRegime) {
    try {
      const suivis = await getSuivis(Number(idRegime));
      if (!Array.isArray(suivis) || suivis.length === 0) {
        document.getElementById('statJours').textContent = '0';
        document.getElementById('statPoidsActuel').textContent = '—';
        document.getElementById('statVariation').textContent = '—';
        document.getElementById('statMoyCalories').textContent = '—';
        return;
      }

      const poidsLatest = suivis[suivis.length - 1]?.poids || 0;
      const variation = (poidsLatest - regimeActif.poids_initial).toFixed(1);
      const moyCalories = Math.round(suivis.reduce((s, x) => s + (x.calories_consommees || 0), 0) / suivis.length);

      document.getElementById('statJours').textContent = suivis.length;
      document.getElementById('statPoidsActuel').textContent = poidsLatest;
      document.getElementById('statVariation').textContent = (variation >= 0 ? '+' : '') + variation;
      document.getElementById('statMoyCalories').textContent = moyCalories;
    } catch (err) {
      console.error('majStats error:', err);
    }
  }

  /* ── Supprimer un suivi ─────────────────────────────────– */
  async function supprimerSuivi(idSuivi) {
    if (!confirm('Supprimer cette entrée de suivi ?')) return;
    try {
      await apiCall('delete', 'POST', { type: 'suivi', id: idSuivi });
      const select = document.getElementById('selectRegime');
      if (select.value) {
        await afficherTableSuivi(select.value);
        await majStats(select.value);
        await afficherGraphique();
      }
    } catch (err) {
      alert('❌ Erreur: ' + err.message);
    }
  }

  /* ── Soumettre un suivi ─────────────────────────────────– */
  document.getElementById('suiviForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!regimeActif) {
      alert('Sélectionnez un régime!');
      return;
    }

    try {
      const payload = {
        id_regime: regimeActif.id_regime,
        date: document.getElementById('dateSuivi').value,
        poids: parseFloat(document.getElementById('poidsSuivi').value),
        calories_consommees: Number(document.getElementById('caloriesConsommees').value)
      };

      await apiCall('suivi', 'POST', payload);
      
      const msg = document.getElementById('suiviMsg');
      msg.className = 'msg-box msg-success';
      msg.textContent = '✅ Suivi enregistré !';
      
      e.target.reset();
      document.getElementById('dateSuivi').valueAsDate = new Date();
      await afficherTableSuivi(regimeActif.id_regime);
      await majStats(regimeActif.id_regime);
      await afficherGraphique();
      
      setTimeout(() => { msg.className = 'msg-box'; }, 3000);
    } catch (err) {
      const msg = document.getElementById('suiviMsg');
      msg.className = 'msg-box msg-error';
      msg.textContent = '❌ ' + err.message;
    }
  });

  /* ── Gestion dropdown ────────────────────────────────────– */
  document.getElementById('selectRegime').addEventListener('change', (e) => {
    const idRegime = e.target.value;
    if (idRegime) {
      afficherBannerRegime(idRegime);
    } else {
      document.getElementById('regimeBanner').classList.remove('visible');
      document.getElementById('statsSection').style.display = 'none';
      document.getElementById('formSection').style.display = 'none';
      document.getElementById('chartSection').style.display = 'none';
      document.getElementById('tableSection').style.display = 'none';
    }
  });

  /* ── INIT ───────────────────────────────────────────────– */
  document.getElementById('dateSuivi').valueAsDate = new Date();
  (async () => {
    await initSearchRegime();
    await chargerRegimes();
  })();
  /* ── Graphique poids (7 derniers jours) ─────────────────────── */
  async function afficherGraphique() {
    try {
      const suivis = await getSuivis(regimeActif.id_regime);
      const sorted = Array.isArray(suivis) ? suivis.slice().sort((a, b) => new Date(a.date) - new Date(b.date)) : [];
      const derniers = sorted.slice(-7);
      const container = document.getElementById('chartBars');
      
      if (!container) return;
      container.innerHTML = '';

      if (derniers.length === 0) { 
        container.innerHTML = '<p style="text-align:center;color:#999;">Pas de données</p>';
        return; 
      }

      const max = Math.max(...derniers.map(s => s.poids || 0));
      const min = Math.min(...derniers.map(s => s.poids || 0));
      const range = max - min || 1;

      derniers.forEach(s => {
        const hauteur = 30 + ((s.poids - min) / range) * 120;
        const d = new Date(s.date);
        const label = d.getDate() + '/' + (d.getMonth()+1);
        const wrap = document.createElement('div');
        wrap.className = 'chart-bar-wrap';
        wrap.innerHTML =
          '<div style="font-size:11px;color:var(--primary-dark);font-weight:800;">' + s.poids + '</div>' +
          '<div class="chart-bar" style="height:' + hauteur + 'px;"></div>' +
          '<div class="chart-label">' + label + '</div>';
        container.appendChild(wrap);
      });
    } catch (err) {
      console.error('afficherGraphique error:', err);
    }
  }

  /* ── Alerte calories en temps réel ─────────────────────── */
  document.getElementById('caloriesConsommees').addEventListener('input', function() {
    if (!regimeActif) return;
    var cal = Number(this.value);
    var cible = regimeActif?.calories_cible || 0;
    var alert = document.getElementById('calorieAlert');
    if (!cal) { alert.className = 'calorie-alert'; alert.textContent = ''; return; }
    var diff = cal - cible;
    if (Math.abs(diff) <= 150) {
      alert.className = 'calorie-alert alert-ok';
      alert.textContent = '✅ Excellent ! Vous êtes dedans votre objectif (' + cal + '/' + cible + ' kcal).';
    } else if (diff > 150) {
      alert.className = 'calorie-alert alert-surplus';
      alert.textContent = '⚠️ Surplus de ' + diff + ' kcal par rapport à votre objectif de ' + cible + ' kcal.';
    } else {
      alert.className = 'calorie-alert alert-deficit';
      alert.textContent = '⚡ Déficit de ' + Math.abs(diff) + ' kcal. Pensez à manger suffisamment.';
    }
  });

  /* ── Animations ─────────────────────────────────────────── */
  function activerAnimations() {
    var obs = new IntersectionObserver(function(entries) {
      entries.forEach(function(en) {
        if (en.isIntersecting) { en.target.classList.add('visible'); obs.unobserve(en.target); }
      });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal:not(.observer-ready)').forEach(function(el) {
      el.classList.add('observer-ready'); obs.observe(el);
    });
  }

  /* ── Navbar ─────────────────────────────────────────────── */
  (function() {
    var nb = document.getElementById('navbar');
    window.addEventListener('scroll', function() { nb.classList.toggle('scrolled', window.scrollY>50); });
    var page = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a').forEach(function(a) {
      if (a.getAttribute('href') === page) a.classList.add('active');
    });
  })();
  </script>
</body>
</html>
