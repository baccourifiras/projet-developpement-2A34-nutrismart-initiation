<?php
/*
 * ============================================================
 * NutriSmart — Module Régime
 * Page : Historique des Recommandations (Front Office)
 * Table : historique_recommandation (id_historique, id_regime,
 *                                    recommandation)
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Historique Recommandations</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css" />
  <style>
    :root {
      --primary:       #17995f;
      --primary-dark:  #0f6d42;
      --secondary:     #7c3aed;
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
      background: rgba(124,58,237,.1); color: #5b21b6;
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

    /* ── STATS ROW ───────────────────────────────────────── */
    .stats-row { display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 16px; margin-bottom: 36px; }
    .stat-card {
      background: rgba(255,255,255,.92); border: 1px solid rgba(255,255,255,.8);
      border-radius: 18px; padding: 20px; box-shadow: var(--shadow);
      transition: transform .3s;
    }
    .stat-card:hover { transform: translateY(-4px); }
    .stat-card .stat-icon { font-size: 24px; margin-bottom: 8px; }
    .stat-card .stat-label { color: var(--muted); font-size: 12px; font-weight: 600; margin-bottom: 6px; }
    .stat-card .stat-value { font-size: 28px; font-weight: 900; color: var(--primary-dark); }

    /* ── FORMULAIRE AJOUT RECO ───────────────────────────── */
    .form-card {
      background: rgba(255,255,255,.94); border: 1px solid rgba(255,255,255,.8);
      border-radius: var(--radius); padding: 32px;
      box-shadow: var(--shadow); margin-bottom: 36px;
    }
    .form-card h2 { font-size: 19px; font-weight: 800; margin-bottom: 22px; color: var(--primary-dark); }
    .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px; }
    .form-group label { font-size: 13px; font-weight: 700; }
    .form-group select, .form-group textarea {
      padding: 12px 14px; border: 1.5px solid var(--border);
      border-radius: 14px; background: #fbfffc;
      font-size: 15px; font-family: 'Outfit', sans-serif; color: var(--text);
      transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .form-group select:focus, .form-group textarea:focus {
      outline: none; border-color: rgba(23,153,95,.5);
      box-shadow: 0 0 0 4px rgba(23,153,95,.1); transform: translateY(-1px);
    }
    .form-group textarea { resize: vertical; min-height: 100px; }
    .form-actions { display: flex; gap: 12px; }
    .primary-btn {
      padding: 13px 28px; border: none; border-radius: 14px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: #fff; font-size: 15px; font-weight: 800; cursor: pointer;
      font-family: 'Outfit', sans-serif; box-shadow: 0 12px 28px rgba(23,153,95,.22);
      transition: transform .25s, box-shadow .25s;
    }
    .primary-btn:hover { transform: translateY(-3px); }
    .msg-box { padding: 13px 18px; border-radius: 12px; margin-top: 14px; font-weight: 700; font-size: 14px; display: none; }
    .msg-success { background: rgba(23,153,95,.1); color: var(--primary-dark); display: block; }

    /* ── FILTRE ──────────────────────────────────────────── */
    .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
    .filter-select {
      padding: 9px 16px; border: 1.5px solid var(--border); border-radius: 999px;
      background: rgba(255,255,255,.85); font-family: 'Outfit', sans-serif;
      font-size: 13px; font-weight: 700; color: var(--text); cursor: pointer;
      transition: border-color .2s;
    }
    .filter-select:focus { outline: none; border-color: var(--primary); }
    .search-input {
      padding: 9px 16px; border: 1.5px solid var(--border); border-radius: 999px;
      background: rgba(255,255,255,.85); font-family: 'Outfit', sans-serif;
      font-size: 13px; color: var(--text); flex: 1; min-width: 200px;
      transition: border-color .2s, box-shadow .2s;
    }
    .search-input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(23,153,95,.1); }

    /* ── RECO CARDS ──────────────────────────────────────── */
    .recos-grid { display: grid; grid-template-columns: repeat(auto-fill,minmax(340px,1fr)); gap: 22px; }
    .reco-card {
      background: rgba(255,255,255,.93); border: 1px solid rgba(255,255,255,.8);
      border-radius: var(--radius); padding: 28px; box-shadow: var(--shadow);
      position: relative; overflow: hidden;
      transition: transform .3s, box-shadow .3s;
    }
    .reco-card::before {
      content:""; position:absolute; top:0; left:0; right:0; height:4px;
      background: linear-gradient(90deg, var(--primary), #34d399);
    }
    .reco-card:hover { transform: translateY(-6px); box-shadow: 0 28px 70px rgba(12,29,21,.12); }
    .reco-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .reco-id { font-size: 11px; color: var(--muted); font-weight: 700; }
    .reco-regime-badge {
      display: inline-flex; padding: 5px 14px; border-radius: 999px;
      font-size: 12px; font-weight: 800;
    }
    .badge-cut   { background: rgba(239,68,68,.1); color: #b91c1c; }
    .badge-bulk  { background: rgba(37,99,235,.1); color: #1d4ed8; }
    .badge-equil { background: rgba(23,153,95,.1); color: var(--primary-dark); }
    .reco-text {
      font-size: 15px; line-height: 1.7; color: var(--text);
      padding: 16px; border-radius: 12px;
      background: rgba(23,153,95,.04); border-left: 3px solid var(--primary);
      margin-bottom: 16px;
    }
    .reco-footer { display: flex; justify-content: space-between; align-items: center; }
    .reco-regime-info { font-size: 12px; color: var(--muted); font-weight: 600; }
    .del-btn {
      padding: 6px 12px; border: none; border-radius: 8px; font-size: 12px;
      font-weight: 700; cursor: pointer; background: rgba(239,68,68,.1); color: #b91c1c;
      font-family: 'Outfit', sans-serif; transition: background .2s, transform .2s;
    }
    .del-btn:hover { background: rgba(239,68,68,.2); transform: translateY(-1px); }

    /* ── TIMELINE ────────────────────────────────────────── */
    .timeline { display: flex; flex-direction: column; gap: 0; }
    .timeline-item { display: flex; gap: 20px; padding-bottom: 28px; position: relative; }
    .timeline-item:not(:last-child)::before {
      content:""; position:absolute; left:19px; top:40px; bottom:0;
      width: 2px; background: linear-gradient(180deg, var(--primary), transparent);
    }
    .timeline-dot {
      width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 18px;
      box-shadow: 0 8px 20px rgba(23,153,95,.25);
    }
    .timeline-content {
      flex: 1; background: rgba(255,255,255,.93);
      border: 1px solid rgba(255,255,255,.8); border-radius: 18px; padding: 20px 24px;
      box-shadow: var(--shadow);
    }
    .timeline-content .reco-regime-badge { margin-bottom: 10px; }
    .timeline-content p { font-size: 14px; line-height: 1.7; color: var(--text); margin-top: 10px; }
    .timeline-content .reco-id { display: block; margin-top: 10px; }

    /* ── VUE TOGGLE ──────────────────────────────────────── */
    .view-toggle { display: flex; gap: 8px; margin-bottom: 22px; }
    .view-btn {
      padding: 9px 22px; border-radius: 999px; border: 1.5px solid var(--border);
      background: rgba(255,255,255,.8); color: var(--text); font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: 'Outfit', sans-serif;
      transition: background .2s, border-color .2s;
    }
    .view-btn.active {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      border-color: transparent; color: #fff;
    }

    /* ── EMPTY ───────────────────────────────────────────── */
    .empty-state {
      text-align: center; padding: 60px 20px;
      background: rgba(255,255,255,.7); border-radius: var(--radius);
      border: 2px dashed var(--border);
    }
    .empty-state .empty-icon { font-size: 48px; margin-bottom: 14px; }
    .empty-state h3 { font-size: 18px; font-weight: 800; margin-bottom: 8px; }
    .empty-state p { color: var(--muted); }

    /* ── REVEAL ──────────────────────────────────────────── */
    .reveal { opacity: 0; transform: translateY(22px); transition: opacity .5s ease, transform .5s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    @media (max-width: 768px) {
      .page-header { padding-top: calc(var(--navbar-h) + 36px); }
      .container { padding: 0 16px 60px; }
      .recos-grid { grid-template-columns: 1fr; }
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
      <a href="../backoffice/regime-admin.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- ═══════════════════════════════════════════════════════
       HEADER
       ═══════════════════════════════════════════════════════ -->
  <header class="page-header">
    <p class="badge">💡 Recommandations</p>
    <h1>Historique des Recommandations</h1>
    <p class="subtitle">Consultez toutes les recommandations nutritionnelles générées pour vos régimes.</p>
  </header>

  <!-- ═══════════════════════════════════════════════════════
       CONTENU
       ═══════════════════════════════════════════════════════ -->
  <main class="container">

    <!-- STATS -->
    <section class="section">
      <div class="stats-row">
        <div class="stat-card reveal">
          <div class="stat-icon">💡</div>
          <div class="stat-label">Total Recommandations</div>
          <div class="stat-value" id="statTotal">0</div>
        </div>
        <div class="stat-card reveal">
          <div class="stat-icon">🔥</div>
          <div class="stat-label">Pour régimes Cut</div>
          <div class="stat-value" id="statCut">0</div>
        </div>
        <div class="stat-card reveal">
          <div class="stat-icon">💪</div>
          <div class="stat-label">Pour régimes Bulk</div>
          <div class="stat-value" id="statBulk">0</div>
        </div>
        <div class="stat-card reveal">
          <div class="stat-icon">⚖️</div>
          <div class="stat-label">Pour régimes Équilibrés</div>
          <div class="stat-value" id="statEquil">0</div>
        </div>
      </div>
    </section>

    <!-- FORMULAIRE AJOUT RECO MANUELLE -->
    <section class="section">
      <h2 class="section-title">Ajouter une recommandation</h2>
      <div class="form-card reveal">
        <h2>Nouvelle recommandation</h2>
        <form id="recoForm">
          <div class="form-group">
            <label for="selectRegimeReco">📋 Choisir le régime associé</label>
            <select id="selectRegimeReco" required>
              <option value="">-- Sélectionner un régime --</option>
            </select>
          </div>
          <div class="form-group">
            <label for="texteReco">💬 Recommandation</label>
            <textarea id="texteReco" required placeholder="Ex : Augmentez votre apport en protéines, consommez davantage de légumes verts..."></textarea>
          </div>
          <div class="form-actions">
            <button type="submit" class="primary-btn">💾 Enregistrer</button>
          </div>
          <div id="recoMsg" class="msg-box"></div>
        </form>
      </div>
    </section>

    <!-- LISTE RECOMMANDATIONS -->
    <section class="section">
      <h2 class="section-title">Toutes les recommandations</h2>

      <!-- Filtres & Recherche -->
      <div class="filter-bar">
        <select class="filter-select" id="filtreType">
          <option value="tous">Tous les types</option>
          <option value="cut">🔥 Cut</option>
          <option value="bulk">💪 Bulk</option>
          <option value="équilibré">⚖️ Équilibré</option>
        </select>
        <input type="text" class="search-input" id="searchReco" placeholder="🔍 Rechercher dans les recommandations..." />
      </div>

      <!-- Toggle vue -->
      <div class="view-toggle">
        <button class="view-btn active" data-view="cards">🗂️ Cartes</button>
        <button class="view-btn" data-view="timeline">📜 Timeline</button>
      </div>

      <!-- Contenu -->
      <div id="recoContainer"></div>
    </section>

  </main>

  <script>
  /* ============================================================
     NutriSmart — historique.php (CORRIGÉ - API BACKEND)
     ============================================================ */

  const API_BASE = '../back/api-regime.php';
  var vueActuelle = 'cards';

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

  /* ── Récupère les recommandations depuis la base ────────– */
  async function getHistos() {
    try {
      return await apiCall('histos');
    } catch (err) {
      console.error('getHistos error:', err);
      return [];
    }
  }

  /* ── Récupérer un régime ────────────────────────────────– */
  async function getRegime(id, regimes) {
    if (!Array.isArray(regimes)) regimes = await getRegimes();
    return regimes.find(r => r.id_regime === id);
  }

  /* ── Badge classe selon type ────────────────────────────– */
  function badgeClass(type) {
    if (!type) return 'badge-equil';
    if (type === 'cut')  return 'badge-cut';
    if (type === 'bulk') return 'badge-bulk';
    return 'badge-equil';
  }
  
  function typeEmoji(type) {
    if (type === 'cut')  return '🔥';
    if (type === 'bulk') return '💪';
    return '⚖️';
  }

  /* ── Peupler le select régimes ──────────────────────────– */
  async function peuplerSelect() {
    try {
      const regimes = await getRegimes();
      const sel = document.getElementById('selectRegimeReco');
      sel.innerHTML = '<option value="">-- Sélectionner un régime --</option>';
      
      if (Array.isArray(regimes) && regimes.length > 0) {
        regimes.forEach(r => {
          const opt = document.createElement('option');
          opt.value = r.id_regime;
          opt.textContent = `${typeEmoji(r.type_regime)} Régime #R-${String(r.id_regime).padStart(3,'0')} — ${r.type_regime}`;
          sel.appendChild(opt);
        });
      }
    } catch (err) {
      console.error('peuplerSelect error:', err);
    }
  }

  /* ── Stats ──────────────────────────────────────────────– */
  async function majStats() {
    try {
      const histos = await getHistos();
      const regimes = await getRegimes();
      
      document.getElementById('statTotal').textContent = Array.isArray(histos) ? histos.length : 0;
      
      let cut = 0, bulk = 0, equil = 0;
      if (Array.isArray(regimes)) {
        regimes.forEach(r => {
          if (r.type_regime === 'cut') cut++;
          else if (r.type_regime === 'bulk') bulk++;
          else if (r.type_regime === 'equilibre') equil++;
        });
      }
      
      document.getElementById('statCut').textContent = cut;
      document.getElementById('statBulk').textContent = bulk;
      document.getElementById('statEquil').textContent = equil;
    } catch (err) {
      console.error('majStats error:', err);
    }
  }

  /* ── Filtrer les recommandations ────────────────────────– */
  async function filtrer() {
    try {
      const filtreType = document.getElementById('filtreType').value;
      const recherche = document.getElementById('searchReco').value.toLowerCase();
      const histos = await getHistos();
      const regimes = await getRegimes();

      if (!Array.isArray(histos)) return [];

      return histos.filter(h => {
        const r = Array.isArray(regimes) ? regimes.find(x => x.id_regime === h.id_regime) : null;
        if (filtreType !== 'tous' && (!r || r.type_regime !== filtreType)) return false;
        if (recherche && h.recommandation && h.recommandation.toLowerCase().indexOf(recherche) === -1) return false;
        return true;
      });
    } catch (err) {
      console.error('filtrer error:', err);
      return [];
    }
  }

  /* ── Affichage cartes ───────────────────────────────────– */
  async function afficherCartes(liste) {
    try {
      if (!Array.isArray(liste) || liste.length === 0) return emptyState();
      
      const regimes = await getRegimes();
      let html = '<div class="recos-grid">';
      
      for (let idx = 0; idx < liste.length; idx++) {
        const h = liste[idx];
        const r = Array.isArray(regimes) ? regimes.find(x => x.id_regime === h.id_regime) : null;
        const type = r ? r.type_regime : 'inconnu';
        const calories = r ? r.calories_cible + ' kcal' : '—';
        
        html += `<div class="reco-card reveal" style="transition-delay:${idx*70}ms">
          <div class="reco-card-header">
            <span class="reco-id">Recommandation #${String(h.id_historique).padStart(3,'0')}</span>
            <span class="reco-regime-badge ${badgeClass(type)}">${typeEmoji(type)} ${type}</span>
          </div>
          <div class="reco-text">${h.recommandation || 'N/A'}</div>
          <div class="reco-footer">
            <span class="reco-regime-info">📋 Régime #R-${String(h.id_regime).padStart(3,'0')} · ${calories}</span>
            <button class="del-btn" data-hid="${h.id_historique}">🗑️ Supprimer</button>
          </div>
        </div>`;
      }
      
      html += '</div>';
      return html;
    } catch (err) {
      console.error('afficherCartes error:', err);
      return emptyState();
    }
  }

  /* ── Affichage timeline ─────────────────────────────────– */
  async function afficherTimeline(liste) {
    try {
      if (!Array.isArray(liste) || liste.length === 0) return emptyState();
      
      const regimes = await getRegimes();
      let html = '<div class="timeline">';
      
      for (const h of liste) {
        const r = Array.isArray(regimes) ? regimes.find(x => x.id_regime === h.id_regime) : null;
        const type = r ? r.type_regime : 'inconnu';
        
        html += `<div class="timeline-item reveal">
          <div class="timeline-dot">${typeEmoji(type)}</div>
          <div class="timeline-content">
            <span class="reco-regime-badge ${badgeClass(type)}">${typeEmoji(type)} ${type}</span>
            <p>${h.recommandation || 'N/A'}</p>
            <span class="reco-id">Régime #R-${String(h.id_regime).padStart(3,'0')} · Reco #${String(h.id_historique).padStart(3,'0')}</span>
          </div>
        </div>`;
      }
      
      html += '</div>';
      return html;
    } catch (err) {
      console.error('afficherTimeline error:', err);
      return emptyState();
    }
  }

  /* ── Empty state ────────────────────────────────────────– */
  function emptyState() {
    return `<div class="empty-state reveal">
      <div class="empty-icon">💡</div>
      <h3>Aucune recommandation trouvée</h3>
      <p>Créez un régime pour recevoir des recommandations automatiques, ou ajoutez-en une manuellement.</p>
    </div>`;
  }

  /* ── Afficher selon la vue ──────────────────────────────– */
  async function afficher() {
    try {
      const liste = await filtrer();
      const container = document.getElementById('recoContainer');
      
      let html;
      if (vueActuelle === 'cards') {
        html = await afficherCartes(liste);
      } else {
        html = await afficherTimeline(liste);
      }
      
      container.innerHTML = html;

      container.querySelectorAll('.del-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
          if (!confirm('Supprimer cette recommandation ?')) return;
          try {
            await apiCall('delete', 'POST', { type: 'histo', id: Number(btn.dataset.hid) });
            await majStats();
            await afficher();
          } catch (err) {
            alert('❌ Erreur: ' + err.message);
          }
        });
      });

      activerAnimations();
      await majStats();
    } catch (err) {
      console.error('afficher error:', err);
    }
  }

  /* ── Formulaire ─────────────────────────────────────────– */
  document.getElementById('recoForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    try {
      const payload = {
        id_regime: Number(document.getElementById('selectRegimeReco').value),
        recommandation: document.getElementById('texteReco').value.trim()
      };
      
      await apiCall('histo', 'POST', payload);
      
      const msg = document.getElementById('recoMsg');
      msg.className = 'msg-box msg-success';
      msg.textContent = '✅ Recommandation enregistrée avec succès !';
      
      e.target.reset();
      await afficher();
      
      setTimeout(() => { msg.className = 'msg-box'; msg.textContent = ''; }, 3000);
    } catch (err) {
      const msg = document.getElementById('recoMsg');
      msg.className = 'msg-box msg-error';
      msg.textContent = '❌ ' + err.message;
    }
  });

  /* ── Filtres & recherche ────────────────────────────────– */
  document.getElementById('filtreType').addEventListener('change', afficher);
  document.getElementById('searchReco').addEventListener('input', afficher);

  /* ── Toggle vue ─────────────────────────────────────────– */
  document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      vueActuelle = btn.dataset.view;
      afficher();
    });
  });

  /* ── Animations ─────────────────────────────────────────– */
  function activerAnimations() {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(en => {
        if (en.isIntersecting) { 
          en.target.classList.add('visible'); 
          obs.unobserve(en.target); 
        }
      });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.reveal:not(.observer-ready)').forEach(el => {
      el.classList.add('observer-ready');
      obs.observe(el);
    });
  }

  /* ── Navbar ─────────────────────────────────────────────– */
  (function() {
    const nb = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
      nb.classList.toggle('scrolled', window.scrollY > 50);
    });
    const page = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a').forEach(a => {
      if (a.getAttribute('href') === page) a.classList.add('active');
    });
  })();

  /* ── Init ───────────────────────────────────────────────– */
  (async () => {
    await peuplerSelect();
    await afficher();
    activerAnimations();
  })();
  </script>
</body>
</html>
