<?php
/*
 * ============================================================
 * NutriSmart — Module Régime
 * Page : Gestion des Régimes (Front Office)
 * Tables : regime (id_regime, type_regime, calories_cible,
 *                  date_debut, poids, duree)
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Mes Régimes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css" />
  <style>
    /* ── Variables & Reset ─────────────────────────────── */
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
      color: var(--text);
      min-height: 100vh;
    }

    /* ── NAVBAR ────────────────────────────────────────── */
    .navbar {
      position: fixed; top: 0; left: 0; right: 0; z-index: 999;
      height: var(--navbar-h);
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 36px;
      background: rgba(12,29,21,.92);
      backdrop-filter: blur(18px);
      border-bottom: 1px solid rgba(255,255,255,.07);
      transition: background .3s, box-shadow .3s;
    }
    .navbar.scrolled {
      background: rgba(10,24,17,.97);
      box-shadow: 0 8px 32px rgba(0,0,0,.22);
    }
    .nav-brand { display: flex; flex-direction: column; gap: 2px; }
    .nav-brand .logo { color: #fff; font-size: 22px; font-weight: 900; letter-spacing: -.5px; }
    .nav-brand .slogan { color: #6ee7b7; font-size: 10px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
    .nav-links { display: flex; align-items: center; gap: 6px; }
    .nav-links a {
      color: rgba(255,255,255,.78); font-size: 14px; font-weight: 600;
      padding: 8px 14px; border-radius: 12px; text-decoration: none;
      transition: background .2s, color .2s, transform .15s;
    }
    .nav-links a:hover, .nav-links a.active {
      background: rgba(23,153,95,.22); color: #fff;
    }
    .nav-links a.nav-dashboard {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff; padding: 8px 18px;
    }
    .nav-links a.nav-dashboard:hover { opacity: .88; }

    /* ── PAGE HEADER ───────────────────────────────────── */
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
    .page-header h1 { font-size: clamp(34px,6vw,58px); font-weight: 900; line-height: 1.1; }
    .page-header .subtitle { margin-top: 14px; color: var(--muted); font-size: 17px; }

    /* ── CONTAINER ─────────────────────────────────────── */
    .container { max-width: 1240px; margin: 0 auto; padding: 0 32px 80px; }
    .section { margin-bottom: 54px; }
    .section-title {
      font-size: 24px; font-weight: 800; margin-bottom: 24px;
      display: flex; align-items: center; gap: 12px;
    }
    .section-title::after {
      content: ""; flex: 1; height: 2px;
      background: linear-gradient(90deg, var(--border), transparent);
    }

    /* ── STATS CARDS ───────────────────────────────────── */
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap: 18px; margin-bottom: 42px; }
    .stat-card {
      background: rgba(255,255,255,.92); border: 1px solid rgba(255,255,255,.8);
      border-radius: var(--radius); padding: 24px 22px;
      box-shadow: var(--shadow);
      transition: transform .3s, box-shadow .3s;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 24px 60px rgba(12,29,21,.12); }
    .stat-card .stat-label { color: var(--muted); font-size: 13px; font-weight: 600; margin-bottom: 8px; }
    .stat-card .stat-value { font-size: 38px; font-weight: 900; color: var(--primary-dark); }
    .stat-card .stat-icon { font-size: 28px; margin-bottom: 10px; }

    /* ── FORM CARD ─────────────────────────────────────── */
    .form-card {
      background: rgba(255,255,255,.94); border: 1px solid rgba(255,255,255,.8);
      border-radius: var(--radius); padding: 32px;
      box-shadow: var(--shadow); margin-bottom: 36px;
    }
    .form-card h2 { font-size: 20px; font-weight: 800; margin-bottom: 24px; color: var(--primary-dark); }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 18px; }
    .form-group { display: flex; flex-direction: column; gap: 7px; }
    .form-group.full { grid-column: 1/-1; }
    .form-group label { font-size: 13px; font-weight: 700; color: var(--text); }
    .form-group input, .form-group select {
      padding: 12px 14px; border: 1.5px solid var(--border);
      border-radius: 14px; background: #fbfffc; font-size: 15px;
      font-family: 'Outfit', sans-serif; color: var(--text);
      transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .form-group input:focus, .form-group select:focus {
      outline: none; border-color: rgba(23,153,95,.5);
      box-shadow: 0 0 0 4px rgba(23,153,95,.1); transform: translateY(-1px);
    }
    .form-actions { margin-top: 22px; display: flex; gap: 12px; flex-wrap: wrap; }
    .primary-btn {
      padding: 13px 28px; border: none; border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff; font-size: 15px; font-weight: 800; cursor: pointer;
      font-family: 'Outfit', sans-serif;
      box-shadow: 0 12px 28px rgba(23,153,95,.22);
      transition: transform .25s, box-shadow .25s, opacity .25s;
    }
    .primary-btn:hover { transform: translateY(-3px); box-shadow: 0 18px 36px rgba(23,153,95,.3); }
    .secondary-btn {
      padding: 13px 28px; border: 1.5px solid var(--border); border-radius: 14px;
      background: transparent; color: var(--primary-dark);
      font-size: 15px; font-weight: 700; cursor: pointer;
      font-family: 'Outfit', sans-serif;
      transition: background .2s, transform .2s;
    }
    .secondary-btn:hover { background: rgba(23,153,95,.07); transform: translateY(-2px); }

    /* ── RÉGIME CARDS ──────────────────────────────────── */
    .regimes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px,1fr)); gap: 24px; }
    .regime-card {
      background: rgba(255,255,255,.93); border: 1px solid rgba(255,255,255,.8);
      border-radius: var(--radius); overflow: hidden;
      box-shadow: var(--shadow);
      transition: transform .35s cubic-bezier(.23,1,.32,1), box-shadow .35s;
      position: relative;
    }
    .regime-card:hover { transform: translateY(-8px); box-shadow: 0 28px 70px rgba(12,29,21,.13); }
    .regime-card-top {
      padding: 24px 24px 16px;
      background: linear-gradient(135deg, rgba(23,153,95,.08), rgba(124,58,237,.04));
      border-bottom: 1px solid var(--border);
      display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    }
    .regime-type-badge {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 6px 16px; border-radius: 999px; font-size: 13px; font-weight: 800;
    }
    .badge-cut   { background: rgba(239,68,68,.12); color: #b91c1c; }
    .badge-bulk  { background: rgba(37,99,235,.12); color: #1d4ed8; }
    .badge-equil { background: rgba(23,153,95,.12); color: var(--primary-dark); }
    .regime-id { font-size: 11px; color: var(--muted); font-weight: 600; }
    .regime-card-body { padding: 20px 24px; }
    .regime-info-row { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px; color: var(--muted); }
    .regime-info-row strong { color: var(--text); font-weight: 700; }
    .regime-info-icon { font-size: 16px; }
    .regime-progress { margin-top: 14px; }
    .regime-progress-label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
    .progress-bar { height: 8px; background: rgba(23,153,95,.12); border-radius: 99px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--primary), #34d399); transition: width .6s ease; }
    .regime-card-footer { padding: 14px 24px; border-top: 1px solid var(--border); display: flex; gap: 8px; flex-wrap: wrap; }
    .regime-action-btn {
      flex: 1; min-width: 110px; padding: 9px 14px; border: none; border-radius: 10px;
      font-size: 13px; font-weight: 700; cursor: pointer; font-family: 'Outfit', sans-serif;
      transition: transform .2s, box-shadow .2s;
    }
    .btn-suivi { background: rgba(23,153,95,.1); color: var(--primary-dark); }
    .btn-suivi:hover { background: rgba(23,153,95,.18); transform: translateY(-2px); }
    .btn-delete { background: rgba(239,68,68,.1); color: #b91c1c; }
    .btn-delete:hover { background: rgba(239,68,68,.18); transform: translateY(-2px); }

    /* ── FILTER BAR ────────────────────────────────────── */
    .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 28px; align-items: center; }
    .filter-btn {
      padding: 8px 20px; border-radius: 999px; border: 1.5px solid var(--border);
      background: rgba(255,255,255,.8); color: var(--text); font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: 'Outfit', sans-serif;
      transition: background .2s, border-color .2s, transform .2s;
    }
    .filter-btn:hover, .filter-btn.active {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-color: transparent; color: #fff; transform: translateY(-2px);
    }

    /* ── EMPTY STATE ───────────────────────────────────── */
    .empty-state {
      text-align: center; padding: 60px 20px;
      background: rgba(255,255,255,.7); border-radius: var(--radius);
      border: 2px dashed var(--border);
    }
    .empty-state .empty-icon { font-size: 52px; margin-bottom: 16px; }
    .empty-state h3 { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
    .empty-state p { color: var(--muted); }

    /* ── MESSAGE ───────────────────────────────────────── */
    .msg-box {
      padding: 14px 20px; border-radius: 12px; margin-top: 16px;
      font-weight: 700; font-size: 14px; display: none;
    }
    .msg-success { background: rgba(23,153,95,.1); color: var(--primary-dark); display: block; }
    .msg-error   { background: rgba(239,68,68,.1); color: #b91c1c; display: block; }

    /* ── MODAL ─────────────────────────────────────────── */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,.5);
      backdrop-filter: blur(5px); z-index: 9000;
      display: flex; align-items: center; justify-content: center;
      animation: fadeIn .2s ease;
    }
    .modal-overlay.hidden { display: none; }
    .modal-box {
      background: #fff; border-radius: 22px; padding: 36px;
      max-width: 440px; width: 92%; text-align: center;
      box-shadow: 0 40px 100px rgba(0,0,0,.22);
      animation: popIn .3s cubic-bezier(.23,1,.32,1);
    }
    .modal-icon { font-size: 44px; margin-bottom: 12px; }
    .modal-box h3 { font-size: 20px; font-weight: 800; margin-bottom: 10px; }
    .modal-box p { color: var(--muted); line-height: 1.6; margin-bottom: 24px; }
    .modal-actions { display: flex; gap: 12px; justify-content: center; }
    .btn-cancel {
      padding: 11px 24px; border: 1.5px solid #e5e7eb; border-radius: 999px;
      background: #f9fafb; color: #374151; font-weight: 600; font-size: 14px;
      cursor: pointer; font-family: 'Outfit', sans-serif;
    }
    .btn-confirm-del {
      padding: 11px 24px; border: none; border-radius: 999px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: #fff; font-weight: 700; font-size: 14px; cursor: pointer;
      font-family: 'Outfit', sans-serif;
      box-shadow: 0 8px 20px rgba(220,38,38,.25);
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes popIn  { from { opacity: 0; transform: scale(.88) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

    /* ── REVEAL ANIMATION ──────────────────────────────── */
    .reveal { opacity: 0; transform: translateY(24px); transition: opacity .55s ease, transform .55s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    @media (max-width: 768px) {
      .page-header { padding-top: calc(var(--navbar-h) + 36px); }
      .container { padding: 0 16px 60px; }
      .regimes-grid { grid-template-columns: 1fr; }
      .nav-links { gap: 3px; }
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
      <a href="../back/regime-admin.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- ═══════════════════════════════════════════════════════
       HEADER
       ═══════════════════════════════════════════════════════ -->
  <header class="page-header">
    <p class="badge">🥗 Module Régime</p>
    <h1>Mes Programmes</h1>
    <p class="subtitle">Créez et gérez vos régimes alimentaires personnalisés.</p>
  </header>

  <!-- ═══════════════════════════════════════════════════════
       CONTENU PRINCIPAL
       ═══════════════════════════════════════════════════════ -->
  <main class="container">

    <!-- STATISTIQUES -->
    <div class="stats-row" id="statsRow">
      <div class="stat-card reveal">
        <div class="stat-icon">🥗</div>
        <div class="stat-label">Total Régimes</div>
        <div class="stat-value" id="statTotal">0</div>
      </div>
      <div class="stat-card reveal">
        <div class="stat-icon">🔥</div>
        <div class="stat-label">Régimes Cut</div>
        <div class="stat-value" id="statCut">0</div>
      </div>
      <div class="stat-card reveal">
        <div class="stat-icon">💪</div>
        <div class="stat-label">Régimes Bulk</div>
        <div class="stat-value" id="statBulk">0</div>
      </div>
      <div class="stat-card reveal">
        <div class="stat-icon">⚖️</div>
        <div class="stat-label">Équilibrés</div>
        <div class="stat-value" id="statEquil">0</div>
      </div>
    </div>

    <!-- FORMULAIRE AJOUT RÉGIME -->
    <section class="section">
      <h2 class="section-title">Ajouter un régime</h2>
      <div class="form-card reveal">
        <h2>Nouveau programme alimentaire</h2>
        <form id="regimeForm">
          <div class="form-grid">
            <div class="form-group">
              <label for="typeRegime">Type de régime</label>
              <select id="typeRegime" required>
                <option value="">-- Choisir --</option>
                <option value="cut">🔥 Cut (perte de poids)</option>
                <option value="bulk">💪 Bulk (prise de masse)</option>
                <option value="equilibre">⚖️ Équilibré</option>
              </select>
            </div>
            <div class="form-group">
              <label for="caloriesCible">Calories cible / jour</label>
              <input type="number" id="caloriesCible" required min="500" max="6000" placeholder="Ex : 2000" />
            </div>
            <div class="form-group">
              <label for="dateDebut">Date de début</label>
              <input type="date" id="dateDebut" required />
            </div>
            <div class="form-group">
              <label for="poidsBis">Poids actuel (kg)</label>
              <input type="number" id="poidsBis" required min="20" max="300" step="0.1" placeholder="Ex : 75.5" />
            </div>
            <div class="form-group">
              <label for="duree">Durée (jours)</label>
              <input type="number" id="duree" required min="1" max="365" placeholder="Ex : 30" />
            </div>
          </div>
          <div class="form-actions">
            <button type="submit" class="primary-btn">✅ Créer le régime</button>
            <button type="reset"  class="secondary-btn">🔄 Réinitialiser</button>
          </div>
          <div id="formMsg" class="msg-box"></div>
        </form>
      </div>
    </section>

    <!-- LISTE DES RÉGIMES -->
    <section class="section">
      <h2 class="section-title">Mes programmes</h2>
      <!-- Filtres -->
      <div class="filter-bar">
        <button class="filter-btn active" data-filter="tous">Tous</button>
        <button class="filter-btn" data-filter="cut">🔥 Cut</button>
        <button class="filter-btn" data-filter="bulk">💪 Bulk</button>
        <button class="filter-btn" data-filter="equilibre">⚖️ Équilibré</button>
      </div>
      <!-- Cards -->
      <div class="regimes-grid" id="regimeList"></div>
    </section>

  </main>

  <!-- ═══════════════════════════════════════════════════════
       MODAL SUPPRESSION
       ═══════════════════════════════════════════════════════ -->
  <div class="modal-overlay hidden" id="deleteModal">
    <div class="modal-box">
      <div class="modal-icon">🗑️</div>
      <h3>Supprimer ce régime ?</h3>
      <p>Cette action supprimera aussi les suivis et recommandations associés. Elle est irréversible.</p>
      <div class="modal-actions">
        <button class="btn-cancel" id="cancelDelete">Annuler</button>
        <button class="btn-confirm-del" id="confirmDelete">Supprimer</button>
      </div>
    </div>
  </div>

  <script>
  /* ============================================================
     NutriSmart — Module Régime — regime.php (CORRIGÉ)
     Appelle maintenant l'API backend au lieu de localStorage
     ============================================================ */

  const API_URL = '../back/api-regime.php';
  var idASupprimer = null;
  var idEnEdition = null;  // Variable pour savoir si on édite

  /* ── Appel API ──────────────────────────────────────────── */
  async function apiCall(action, method = 'GET', data = null) {
    try {
      let url = API_URL + '?action=' + action;
      let opts = { method };
      
      if (method === 'POST' && data) {
        opts.headers = { 'Content-Type': 'application/json' };
        opts.body = JSON.stringify(data);
      }
      
      let res = await fetch(url, opts);
      let json = await res.json();
      if (!res.ok) throw new Error(json.error || 'Erreur API');
      return json;
    } catch (err) {
      console.error('API Error:', err);
      throw err;
    }
  }
  
  /* ── Fonctions helpers (pas de localStorage, utilisez l'API) ──── */
  async function getRegimes() {
    try {
      return await apiCall('regimes');
    } catch (err) {
      console.error('Get regimes error:', err);
      return [];
    }
  }
  
  async function getSuivis() {
    try {
      return await apiCall('suivis');
    } catch (err) {
      return [];
    }
  }
  
  async function getHistos() {
    try {
      return await apiCall('histos');
    } catch (err) {
      return [];
    }
  }

  /* ── Format date ────────────────────────────────────────── */
  function fmtDate(d) {
    return new Intl.DateTimeFormat('fr-FR',{year:'numeric',month:'long',day:'numeric'}).format(new Date(d));
  }

  /* ── Progression (jours écoulés / durée) ───────────────── */
  function calcProgression(dateDebut, duree) {
    var debut = new Date(dateDebut);
    var aujourdhui = new Date();
    var jours = Math.floor((aujourdhui - debut) / 86400000);
    return Math.min(100, Math.max(0, Math.round((jours / duree) * 100)));
  }

  /* ── Type badge ─────────────────────────────────────────── */
  function typeBadgeClass(t) {
    if (t === 'cut') return 'badge-cut';
    if (t === 'bulk') return 'badge-bulk';
    return 'badge-equil';
  }
  function typeEmoji(t) {
    if (t === 'cut') return '🔥';
    if (t === 'bulk') return '💪';
    return '⚖️';
  }

  /* ── Stats ──────────────────────────────────────────────── */
  async function majStats() {
    try {
      let regimes = await getRegimes();
      if (!Array.isArray(regimes)) regimes = [];
      
      let cut = 0, bulk = 0, equil = 0;
      regimes.forEach(r => {
        if (r.type_regime === 'cut') cut++;
        else if (r.type_regime === 'bulk') bulk++;
        else if (r.type_regime === 'equilibre') equil++;
      });
      
      document.getElementById('statTotal').textContent = regimes.length;
      document.getElementById('statCut').textContent = cut;
      document.getElementById('statBulk').textContent = bulk;
      document.getElementById('statEquil').textContent = equil;
    } catch (err) {
      console.error('Stats error:', err);
    }
  }

  /* ── Affichage des cards ────────────────────────────────── */
  var filtreActif = 'tous';

  async function afficherRegimes() {
    try {
      let regimes = await getRegimes();
      if (!Array.isArray(regimes)) regimes = [];
      
      let liste = filtreActif === 'tous' ? regimes : regimes.filter(function(r){ return r.type_regime === filtreActif; });
      let conteneur = document.getElementById('regimeList');
      conteneur.innerHTML = '';

      if (liste.length === 0) {
        conteneur.innerHTML =
          '<div class="empty-state reveal">' +
          '<div class="empty-icon">🍽️</div>' +
          '<h3>Aucun régime trouvé</h3>' +
          '<p>Créez votre premier programme alimentaire avec le formulaire ci-dessus.</p></div>';
        activerAnimations();
        return;
      }

      liste.forEach(function(r, idx) {
        var prog = calcProgression(r.date_debut, r.duree);
        var card = document.createElement('article');
        card.className = 'regime-card reveal';
        card.style.transitionDelay = (idx * 80) + 'ms';
        card.innerHTML =
          '<div class="regime-card-top">' +
            '<div>' +
              '<span class="regime-type-badge ' + typeBadgeClass(r.type_regime) + '">' +
                typeEmoji(r.type_regime) + ' ' + r.type_regime.charAt(0).toUpperCase() + r.type_regime.slice(1) +
              '</span>' +
            '</div>' +
            '<span class="regime-id">#R-' + String(r.id_regime).padStart(3,'0') + '</span>' +
          '</div>' +
          '<div class="regime-card-body">' +
            '<div class="regime-info-row"><span class="regime-info-icon">🔥</span> Calories cible : <strong>' + r.calories_cible + ' kcal</strong></div>' +
            '<div class="regime-info-row"><span class="regime-info-icon">⚖️</span> Poids de départ : <strong>' + r.poids_initial + ' kg</strong></div>' +
            '<div class="regime-info-row"><span class="regime-info-icon">📅</span> Début : <strong>' + fmtDate(r.date_debut) + '</strong></div>' +
            '<div class="regime-info-row"><span class="regime-info-icon">⏱️</span> Durée : <strong>' + r.duree + ' jours</strong></div>' +
            '<div class="regime-progress">' +
              '<div class="regime-progress-label">Progression : ' + prog + '%</div>' +
              '<div class="progress-bar"><div class="progress-fill" style="width:' + prog + '%"></div></div>' +
            '</div>' +
          '</div>' +
          '<div class="regime-card-footer">' +
            '<button class="regime-action-btn btn-suivi" onclick="chargerForEdition(' + r.id_regime + ')">✏️ Éditer</button>' +
            '<button class="regime-action-btn btn-delete" data-id="' + r.id_regime + '">🗑️ Supprimer</button>' +
          '</div>';
        conteneur.appendChild(card);
      });

      /* Boutons supprimer */
      conteneur.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function() { ouvrirModalSuppression(Number(btn.dataset.id)); });
      });

      activerAnimations();
      majStats();
    } catch (err) {
      console.error('Affiche regimes error:', err);
    }
  }

  /* ── Charger pour édition ───────────────────────────────– */
  async function chargerForEdition(idRegime) {
    try {
      let regimes = await getRegimes();
      let regime = regimes.find(r => r.id_regime === idRegime);
      if (!regime) return;
      
      idEnEdition = idRegime;
      
      // Remplir le formulaire
      document.getElementById('typeRegime').value = regime.type_regime;
      document.getElementById('caloriesCible').value = regime.calories_cible;
      document.getElementById('dateDebut').value = regime.date_debut;
      document.getElementById('poidsBis').value = regime.poids_initial;
      document.getElementById('duree').value = regime.duree;
      
      // Changer le bouton et le titre
      document.querySelector('.form-card h2').textContent = '⚙️ Modifier le régime #R-' + String(idRegime).padStart(3,'0');
      document.querySelector('.primary-btn').textContent = '📝 Mettre à jour';
      
      // Scroll vers le formulaire
      document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (err) {
      console.error('chargerForEdition error:', err);
    }
  }

  /* ── Annuler édition ────────────────────────────────────– */
  function annulerEdition() {
    idEnEdition = null;
    document.getElementById('regimeForm').reset();
    document.querySelector('.form-card h2').textContent = 'Nouveau programme alimentaire';
    document.querySelector('.primary-btn').textContent = '✅ Créer le régime';
  }

  /* ── Formulaire ajout ───────────────────────────────────── */
  document.getElementById('regimeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    try {
      let data = {
        type_regime: document.getElementById('typeRegime').value,
        calories_cible: Number(document.getElementById('caloriesCible').value),
        date_debut: document.getElementById('dateDebut').value,
        poids_initial: parseFloat(document.getElementById('poidsBis').value),
        duree: Number(document.getElementById('duree').value)
      };
      
      let res;
      let msg = document.getElementById('formMsg');
      
      if (idEnEdition) {
        // MISE À JOUR
        data.id_regime = idEnEdition;
        res = await apiCall('editRegime', 'POST', data);
        msg.className = 'msg-box msg-success';
        msg.textContent = '📝 Régime mis à jour avec succès !';
      } else {
        // CRÉATION
        res = await apiCall('regime', 'POST', data);
        msg.className = 'msg-box msg-success';
        msg.textContent = '✅ Régime créé ! ID: ' + res.newId;
      }
      
      e.target.reset();
      annulerEdition();
      await afficherRegimes();
      await majStats();
      
      setTimeout(function() { msg.className = 'msg-box'; msg.textContent = ''; }, 4000);
    } catch (err) {
      let msg = document.getElementById('formMsg');
      msg.className = 'msg-box msg-error';
      msg.textContent = '❌ ' + err.message;
    }
  });

  /* ── Filtres ────────────────────────────────────────────── */
  document.querySelectorAll('.filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
      btn.classList.add('active');
      filtreActif = btn.dataset.filter;
      afficherRegimes();
    });
  });

  /* ── Bouton Réinitialiser ───────────────────────────────– */
  document.querySelector('button[type="reset"]').addEventListener('click', function() {
    annulerEdition();
  });

  /* ── Modal suppression ──────────────────────────────────── */
  function ouvrirModalSuppression(id) {
    idASupprimer = id;
    document.getElementById('deleteModal').classList.remove('hidden');
  }
  document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').classList.add('hidden');
    idASupprimer = null;
  });
  document.getElementById('confirmDelete').addEventListener('click', async function() {
    if (!idASupprimer) return;
    try {
      await apiCall('delete', 'POST', { type: 'regime', id: idASupprimer });
      document.getElementById('deleteModal').classList.add('hidden');
      idASupprimer = null;
      await afficherRegimes();
      await majStats();
    } catch (err) {
      alert('❌ Erreur suppression: ' + err.message);
    }
  });
  document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) { this.classList.add('hidden'); idASupprimer = null; }
  });

  /* ── Animations scroll ──────────────────────────────────── */
  function activerAnimations() {
    var obs = new IntersectionObserver(function(entries) {
      entries.forEach(function(en) {
        if (en.isIntersecting) { en.target.classList.add('visible'); obs.unobserve(en.target); }
      });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal:not(.observer-ready)').forEach(function(el) {
      el.classList.add('observer-ready'); obs.observe(el);
    });
  }

  /* ── Navbar scroll ──────────────────────────────────────── */
  (function() {
    var nb = document.getElementById('navbar');
    window.addEventListener('scroll', function() {
      nb.classList.toggle('scrolled', window.scrollY > 50);
    });
    var page = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a').forEach(function(a) {
      if (a.getAttribute('href') === page) a.classList.add('active');
    });
  })();

  /* ── Date min = aujourd'hui ─────────────────────────────── */
  document.getElementById('dateDebut').valueAsDate = new Date();

  /* ── INIT ───────────────────────────────────────────────── */
  majStats();
  afficherRegimes();
  activerAnimations();
  </script>
</body>
</html>
