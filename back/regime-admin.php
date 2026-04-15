<?php
/*
 * ============================================================
 * NutriSmart — Module Régime
 * Page : Backoffice Administration (regime-admin.php)
 * Gère : regime / suivi_regime / historique_recommandation
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Backoffice Régime</title>
  <style>
    :root {
      color-scheme: light;
      font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: #f1f5f9;
      color: #0f172a;
    }
    * { box-sizing: border-box; }
    html, body { min-height: 100%; margin: 0; }
    body {
      margin: 0;
      background: linear-gradient(180deg, #f8fafc 0%, #e2e8f0 100%);
      color: #0f172a;
      line-height: 1.6;
    }
    button, input, select, textarea { font: inherit; }
    a { color: inherit; text-decoration: none; }

    .sidebar {
      position: fixed;
      inset: 0 auto 0 0;
      width: 300px;
      padding: 32px 28px;
      background: linear-gradient(180deg, rgba(15,23,42,.96) 0%, rgba(5,77,64,.92) 100%);
      color: #f8fafc;
      display: flex;
      flex-direction: column;
      gap: 24px;
      border-right: 1px solid rgba(255,255,255,.08);
      overflow-y: auto;
    }
    .brand {
      display: flex;
      align-items: flex-start;
      gap: 16px;
    }
    .brand-mark {
      width: 58px;
      height: 58px;
      border-radius: 20px;
      display: grid;
      place-items: center;
      font-weight: 800;
      color: #f8fafc;
      background: linear-gradient(135deg, #22c55e, #06b6d4);
      box-shadow: 0 22px 70px rgba(0,0,0,.24);
    }
    .brand h1 {
      margin: 0;
      font-size: 1.45rem;
      letter-spacing: -.02em;
    }
    .brand-slogan,
    .sidebar-text {
      margin: 6px 0 0;
      color: #cbd5e1;
      font-size: .95rem;
      line-height: 1.5;
    }

    .menu {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .menu a {
      position: relative;
      display: block;
      padding: 14px 20px 14px 34px;
      border-radius: 18px;
      color: #f8fafc;
      background: rgba(255,255,255,.05);
      border: 1px solid rgba(255,255,255,.08);
      transition: background .2s, transform .2s, border-color .2s;
    }
    .menu a::before {
      content: '';
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255,255,255,.7);
    }
    .menu a:hover,
    .menu a.active {
      background: rgba(255,255,255,.16);
      border-color: rgba(255,255,255,.16);
      transform: translateY(-1px);
    }

    .sidebar-footer {
      margin-top: auto;
      display: flex;
      flex-direction: column;
      gap: 16px;
    }
    .sidebar-chip {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      padding: 10px 16px;
      border-radius: 999px;
      background: rgba(255,255,255,.08);
      color: #cbd5e1;
      width: fit-content;
    }

    .main.content {
      margin-left: 320px;
      padding: 36px 42px 48px;
      min-height: 100vh;
    }
    .dashboard-head {
      display: grid;
      gap: 24px;
      margin-bottom: 24px;
    }
    .panel {
      background: #fff;
      border: 1px solid rgba(15,23,42,.08);
      border-radius: 30px;
      padding: 28px 32px;
      box-shadow: 0 25px 80px rgba(15,23,42,.06);
      margin-bottom: 28px;
    }
    .hero-panel {
      background: linear-gradient(135deg, #0f766e 0%, #16a34a 100%);
      color: #f8fafc;
      border: none;
      box-shadow: 0 30px 90px rgba(15,23,42,.18);
    }
    .hero-panel .kicker {
      color: #d9fae3;
      background: rgba(255,255,255,.12);
      padding: 10px 16px;
      border-radius: 999px;
      display: inline-flex;
      margin-bottom: 16px;
    }
    .hero-panel h2 {
      font-size: clamp(2rem, 3vw, 3rem);
      line-height: 1.05;
      margin-bottom: 18px;
    }
    .hero-panel .note {
      color: rgba(248,250,252,.92);
      font-size: 1rem;
      max-width: 760px;
      margin: 0;
    }

    .panel-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 22px;
    }
    .kicker {
      margin: 0 0 8px;
      display: inline-flex;
      font-size: 12px;
      letter-spacing: .18em;
      text-transform: uppercase;
      color: #16a34a;
      font-weight: 800;
    }
    .kicker-soft {
      color: #475569;
    }
    h2 {
      margin: 0;
      font-size: clamp(1.5rem, 2vw, 2rem);
      line-height: 1.15;
      color: #0f172a;
    }
    .note {
      margin: 0;
      color: #475569;
      font-size: 1rem;
      line-height: 1.8;
    }

    .stats-panel {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 18px;
      border: 1px solid rgba(15,23,42,.06);
      padding: 24px;
      border-radius: 32px;
      background: rgba(255,255,255,.96);
    }
    .stats-card {
      background: #fff;
      border: 1px solid rgba(15,23,42,.08);
      border-radius: 24px;
      padding: 22px 24px;
      min-height: 120px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 18px 50px rgba(15,23,42,.06);
    }
    .stats-card span {
      color: #64748b;
      font-size: .85rem;
      text-transform: uppercase;
      letter-spacing: .08em;
    }
    .stats-card strong {
      color: #111827;
      font-size: 2.05rem;
      display: block;
    }

    .form-grid {
      display: grid;
      gap: 18px;
      margin-top: 8px;
    }
    .form-grid.two-columns {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .form-grid .full-width {
      grid-column: 1 / -1;
    }
    label {
      display: block;
      margin-bottom: 10px;
      font-size: .95rem;
      font-weight: 700;
      color: #334155;
    }
    input, select, textarea {
      width: 100%;
      border: 1.5px solid rgba(148,163,184,.35);
      border-radius: 18px;
      padding: 14px 16px;
      background: #f8fafc;
      color: #0f172a;
      transition: border-color .2s, box-shadow .2s;
    }
    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: #22c55e;
      box-shadow: 0 0 0 4px rgba(34,197,94,.12);
    }
    textarea { min-height: 120px; resize: vertical; }

    .primary-btn,
    .danger-btn,
    .tab-btn,
    .edit-btn,
    .delete-btn {
      font-family: inherit;
      border: none;
      cursor: pointer;
      transition: transform .2s, box-shadow .2s, background .2s, border-color .2s;
    }
    .primary-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 14px 24px;
      border-radius: 18px;
      background: linear-gradient(135deg, #22c55e, #0f766e);
      color: #fff;
      font-weight: 700;
      box-shadow: 0 18px 40px rgba(34,197,94,.18);
    }
    .primary-btn:hover { transform: translateY(-1px); }
    .danger-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 12px 18px;
      border-radius: 18px;
      background: #fff1f2;
      color: #b91c1c;
      border: 1px solid rgba(239,68,68,.18);
    }
    .danger-btn:hover { background: #fee2e2; }

    .table-wrapper {
      width: 100%;
      overflow-x: auto;
      margin-top: 20px;
      border-radius: 24px;
      border: 1px solid rgba(148,163,184,.22);
      background: #fff;
    }
    .table {
      width: 100%;
      border-collapse: collapse;
      min-width: 820px;
    }
    .table th,
    .table td {
      padding: 16px 18px;
      vertical-align: middle;
      text-align: left;
      border-bottom: 1px solid rgba(148,163,184,.15);
    }
    .table thead { background: #f8fafc; }
    .table th {
      color: #475569;
      font-size: .85rem;
      letter-spacing: .04em;
      text-transform: uppercase;
    }
    .table tbody tr:hover { background: #f8fafc; }
    .action-cell {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .edit-btn,
    .delete-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 14px;
      border-radius: 14px;
      border: 1px solid rgba(15,23,42,.08);
      background: #f8fafc;
      color: #0f172a;
    }
    .edit-btn:hover { background: #dcfce7; }
    .delete-btn:hover { background: #fee2e2; }

    .id-badge,
    .small-badge {
      display: inline-flex;
      align-items: center;
      padding: 7px 12px;
      border-radius: 999px;
      background: #f8fafc;
      color: #475569;
      font-size: .85rem;
      font-weight: 700;
    }

    .confirm-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15,23,42,.45);
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      z-index: 50;
    }
    .confirm-box {
      background: #fff;
      border-radius: 30px;
      box-shadow: 0 36px 90px rgba(15,23,42,.18);
      max-width: 640px;
      width: 100%;
      padding: 28px;
    }
    .confirm-icon {
      width: 56px;
      height: 56px;
      border-radius: 18px;
      display: grid;
      place-items: center;
      background: rgba(16,185,129,.12);
      font-size: 1.5rem;
      margin-bottom: 18px;
    }
    .confirm-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 14px;
      margin-top: 22px;
    }
    .edit-box {
      max-width: 640px;
    }
    .edit-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin: 18px 0;
    }
    .edit-form-grid .full { grid-column: 1 / -1; }
    .reco-preview {
      max-width: 320px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      color: #334155;
      font-size: 13px;
    }

    .tab-bar { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 28px; }
    .tab-btn {
      padding: 12px 22px;
      border-radius: 999px;
      border: 1.5px solid rgba(23,153,95,.16);
      background: rgba(255,255,255,.9);
      color: #334155;
      font-size: .95rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 10px 30px rgba(15,23,42,.06);
    }
    .tab-btn.active {
      background: linear-gradient(135deg, #22c55e, #0f766e);
      border-color: transparent;
      color: #fff;
      box-shadow: 0 16px 45px rgba(34,197,94,.18);
    }
    .tab-btn:hover:not(.active) { background: rgba(34,197,94,.12); }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .msg-flash {
      padding: 14px 18px;
      border-radius: 16px;
      margin-bottom: 18px;
      font-weight: 700;
      font-size: .95rem;
      display: none;
    }
    .msg-flash.show-success { display: block; background: rgba(22,163,74,.12); color: #0f5132; }
    .msg-flash.show-error   { display: block; background: rgba(239,68,68,.12); color: #7f1d1d; }

    @media (max-width: 1120px) {
      .main.content { margin-left: 0; padding: 24px; }
      .sidebar { position: relative; width: 100%; border-right: none; border-bottom: 1px solid rgba(255,255,255,.08); }
      .stats-panel { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 800px) {
      .form-grid.two-columns,
      .edit-form-grid { grid-template-columns: 1fr; }
      .stats-panel { grid-template-columns: 1fr; }
      .sidebar { padding: 18px; }
      .panel { padding: 22px; }
      .main.content { padding: 20px; }
      .table th, .table td { padding: 14px 12px; }
    }

    .regime-type-chip {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 13px; border-radius: 999px; font-size: 12px; font-weight: 800;
    }
    .chip-cut   { background: rgba(239,68,68,.12); color: #b91c1c; }
    .chip-bulk  { background: rgba(37,99,235,.12);  color: #1d4ed8; }
    .chip-equil { background: rgba(23,153,95,.12);  color: #0f6d42; }

    .progress-mini { height: 8px; background: rgba(23,153,95,.12); border-radius: 99px; overflow:hidden; min-width: 90px; }
    .progress-mini-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #22c55e, #38bdf8); }

    .calorie-diff {
      display: inline-flex; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 800;
    }
    .diff-ok      { background: rgba(23,153,95,.12);  color: #0f6d42; }
    .diff-surplus { background: rgba(239,68,68,.12);  color: #b91c1c; }
    .diff-deficit { background: rgba(245,158,11,.12); color: #92400e; }
  </style>
</head>
<body>

  <!-- ═══════════════════════════════════════════════════════
       SIDEBAR
       ═══════════════════════════════════════════════════════ -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
        <p class="sidebar-text">Administration du module Régime alimentaire.</p>
      </div>
    </div>

    <nav class="menu">
      <a href="#dashboard" class="active">Tableau de bord</a>
      <a href="#tabRegimes">Régimes</a>
      <a href="#tabSuivis">Suivi régime</a>
      <a href="#tabHistorique">Recommandations</a>
      <a href="../front/regime.php">👀 Voir Front</a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-chip">Module Régime</div>
      <button id="resetBtn" class="danger-btn">🗑️ Réinitialiser les données</button>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════════════════════
       MAIN
       ═══════════════════════════════════════════════════════ -->
  <main class="main content">

    <div class="dashboard-head">
      <section id="dashboard" class="panel stats-panel">
        <div class="stats-card">
          <span>Régimes</span>
          <strong id="cntRegimes">0</strong>
        </div>
        <div class="stats-card">
          <span>Entrées de suivi</span>
          <strong id="cntSuivis">0</strong>
        </div>
        <div class="stats-card">
          <span>Recommandations</span>
          <strong id="cntHistos">0</strong>
        </div>
        <div class="stats-card">
          <span>Moy. calories/régime</span>
          <strong id="cntMoyCalories">—</strong>
        </div>
      </section>

      <section class="panel hero-panel">
        <p class="kicker">PROJET</p>
        <h2>Backoffice NutriSmart</h2>
        <p class="note">Ce backoffice permet de gérer les régimes, les suivis et les recommandations. Chaque mise à jour reste synchronisée avec le front office et offre une expérience propre et moderne.</p>
      </section>
    </div>

    <!-- FLASH MESSAGE -->
    <div id="flashMsg" class="msg-flash"></div>

    <!-- TABS -->
    <div class="tab-bar">
      <button class="tab-btn active" data-tab="tabRegimes">🥗 Régimes</button>
      <button class="tab-btn" data-tab="tabSuivis">📊 Suivi journalier</button>
      <button class="tab-btn" data-tab="tabHistorique">💡 Recommandations</button>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         TAB 1 : RÉGIMES
         ═══════════════════════════════════════════════════════ -->
    <div id="tabRegimes" class="tab-panel active">
      <section class="panel">
        <div class="panel-header">
          <div>
            <p class="kicker kicker-soft">Gestion</p>
            <h2>Ajouter un nouveau régime</h2>
          </div>
        </div>
        <form id="regimeForm" class="form-grid two-columns">
          <div>
            <label for="typeRegime">Type de régime</label>
            <select id="typeRegime" required>
              <option value="">-- Choisir --</option>
              <option value="cut">🔥 Cut (perte de poids)</option>
              <option value="bulk">💪 Bulk (prise de masse)</option>
              <option value="equilibre">⚖️ Équilibré</option>
            </select>
          </div>
          <div>
            <label for="caloriesCible">Calories cible / jour (kcal)</label>
            <input id="caloriesCible" required type="number" min="500" max="6000" placeholder="Ex : 2000" />
          </div>
          <div>
            <label for="dateDebut">Date de début</label>
            <input id="dateDebut" required type="date" />
          </div>
          <div>
            <label for="poids">Poids de départ (kg)</label>
            <input id="poids" required type="number" min="20" max="300" step="0.1" placeholder="Ex : 75.5" />
          </div>
          <div>
            <label for="duree">Durée (jours)</label>
            <input id="duree" required type="number" min="1" max="365" placeholder="Ex : 30" />
          </div>
          <div style="display:flex;align-items:flex-end;">
            <button class="primary-btn" type="submit">✅ Ajouter le régime</button>
          </div>
        </form>
        <div id="regimeTableContainer"></div>
      </section>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         TAB 2 : SUIVI
         ═══════════════════════════════════════════════════════ -->
    <div id="tabSuivis" class="tab-panel">
      <section class="panel">
        <div class="panel-header">
          <div>
            <p class="kicker kicker-soft">Gestion</p>
            <h2>Ajouter une entrée de suivi</h2>
          </div>
        </div>
        <form id="suiviForm" class="form-grid two-columns">
          <div>
            <label for="suiviRegime">Régime associé</label>
            <select id="suiviRegime" required>
              <option value="">-- Choisir un régime --</option>
            </select>
          </div>
          <div>
            <label for="suiviDate">Date</label>
            <input id="suiviDate" required type="date" />
          </div>
          <div>
            <label for="suiviPoids">Poids (kg)</label>
            <input id="suiviPoids" required type="number" min="20" max="300" step="0.1" placeholder="Ex : 74.8" />
          </div>
          <div>
            <label for="suiviCalories">Calories consommées</label>
            <input id="suiviCalories" required type="number" min="0" max="9999" placeholder="Ex : 1950" />
          </div>
          <div style="grid-column:1/-1;">
            <button class="primary-btn" type="submit">✅ Ajouter l'entrée</button>
          </div>
        </form>
        <div id="suiviTableContainer"></div>
      </section>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         TAB 3 : HISTORIQUE RECOMMANDATIONS
         ═══════════════════════════════════════════════════════ -->
    <div id="tabHistorique" class="tab-panel">
      <section class="panel">
        <div class="panel-header">
          <div>
            <p class="kicker kicker-soft">Gestion</p>
            <h2>Ajouter une recommandation</h2>
          </div>
        </div>
        <form id="histoForm" class="form-grid two-columns">
          <div>
            <label for="histoRegime">Régime associé</label>
            <select id="histoRegime" required>
              <option value="">-- Choisir un régime --</option>
            </select>
          </div>
          <div class="full-width">
            <label for="histoReco">Recommandation</label>
            <textarea id="histoReco" required rows="3" placeholder="Ex : Augmentez votre apport en protéines..."></textarea>
          </div>
          <button class="primary-btn" type="submit">✅ Ajouter la recommandation</button>
        </form>
        <div id="histoTableContainer"></div>
      </section>
    </div>

  </main>

  <!-- ═══════════════════════════════════════════════════════
       MODAL SUPPRESSION
       ═══════════════════════════════════════════════════════ -->
  <div id="confirmModal" class="confirm-overlay" style="display:none;">
    <div class="confirm-box">
      <div class="confirm-icon">🗑️</div>
      <h3 id="confirmTitle">Supprimer cet élément ?</h3>
      <p id="confirmDesc">Cette action est irréversible.</p>
      <div class="confirm-actions">
        <button class="cancel-btn" id="cancelConfirm">Annuler</button>
        <button class="danger-btn" id="okConfirm" style="width:auto;padding:10px 24px;border-radius:999px;">Supprimer</button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       MODAL ÉDITION RÉGIME
       ═══════════════════════════════════════════════════════ -->
  <div id="editRegimeModal" class="confirm-overlay" style="display:none;">
    <div class="confirm-box edit-box">
      <div class="confirm-icon">✏️</div>
      <h3>Modifier le régime</h3>
      <div class="edit-form-grid">
        <input type="hidden" id="editRegimeId" />
        <div>
          <label>Type de régime</label>
          <select id="editTypeRegime">
            <option value="cut">🔥 Cut</option>
            <option value="bulk">💪 Bulk</option>
            <option value="equilibre">⚖️ Équilibré</option>
          </select>
        </div>
        <div>
          <label>Calories cible (kcal)</label>
          <input type="number" id="editCalories" min="500" max="6000" />
        </div>
        <div>
          <label>Date de début</label>
          <input type="date" id="editDateDebut" />
        </div>
        <div>
          <label>Poids (kg)</label>
          <input type="number" id="editPoids" min="20" max="300" step="0.1" />
        </div>
        <div>
          <label>Durée (jours)</label>
          <input type="number" id="editDuree" min="1" max="365" />
        </div>
      </div>
      <div class="confirm-actions">
        <button class="cancel-btn" id="cancelEditRegime">Annuler</button>
        <button class="primary-btn" id="saveEditRegime" style="border-radius:999px;padding:10px 24px;">💾 Enregistrer</button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════
       MODAL ÉDITION RECOMMANDATION
       ═══════════════════════════════════════════════════════ -->
  <div id="editHistoModal" class="confirm-overlay" style="display:none;">
    <div class="confirm-box edit-box">
      <div class="confirm-icon">✏️</div>
      <h3>Modifier la recommandation</h3>
      <input type="hidden" id="editHistoId" />
      <div style="margin: 18px 0;">
        <label>Recommandation</label>
        <textarea id="editHistoReco" rows="4" style="width:100%;margin-top:8px;padding:12px 14px;border:1.5px solid rgba(23,153,95,.16);border-radius:14px;font-family:Inter,Arial,sans-serif;font-size:15px;resize:vertical;"></textarea>
      </div>
      <div class="confirm-actions">
        <button class="cancel-btn" id="cancelEditHisto">Annuler</button>
        <button class="primary-btn" id="saveEditHisto" style="border-radius:999px;padding:10px 24px;">💾 Enregistrer</button>
      </div>
    </div>
  </div>

  <script>
  /* ============================================================
     NutriSmart — regime-admin.php (Backoffice)
     ============================================================ */

  const apiUrl = 'api-regime.php';

  async function apiFetch(action, body, method = 'GET') {
    const url = apiUrl + '?action=' + encodeURIComponent(action);
    const options = { method };
    if (method !== 'GET') {
      options.headers = { 'Content-Type': 'application/json; charset=utf-8' };
      options.body = JSON.stringify(body || {});
    }

    const response = await fetch(url, options);
    if (!response.ok) {
      const text = await response.text();
      throw new Error(text || response.statusText);
    }
    return response.json();
  }

  async function getStats() { return apiFetch('stats'); }
  async function getRegimes() { return apiFetch('regimes'); }
  async function getSuivis() { return apiFetch('suivis'); }
  async function getHistos() { return apiFetch('histos'); }
  async function getRegimeSelect() { return apiFetch('regimeSelect'); }

  function typeChip(t) {
    const cls = t === 'cut' ? 'chip-cut' : t === 'bulk' ? 'chip-bulk' : 'chip-equil';
    const emoji = t === 'cut' ? '🔥' : t === 'bulk' ? '💪' : '⚖️';
    return '<span class="regime-type-chip ' + cls + '">' + emoji + ' ' + t + '</span>';
  }

  function fmtDate(d) {
    return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(d));
  }

  async function getRegimeLabel(id) {
    const regimes = await getRegimes();
    const r = regimes.find((x) => x.id_regime === id);
    return r ? ('#R-' + String(r.id_regime).padStart(3, '0') + ' · ' + r.type_regime) : '—';
  }

  function calcProg(dateDebut, duree) {
    const j = Math.floor((new Date() - new Date(dateDebut)) / 86400000);
    return Math.min(100, Math.max(0, Math.round((j / duree) * 100)));
  }

  function flash(msg, type) {
    const el = document.getElementById('flashMsg');
    el.textContent = msg;
    el.className = 'msg-flash ' + (type === 'success' ? 'show-success' : 'show-error');
    setTimeout(() => { el.className = 'msg-flash'; }, 3500);
  }

  async function majStats() {
    const stats = await getStats();
    document.getElementById('cntRegimes').textContent = stats.regimes;
    document.getElementById('cntSuivis').textContent = stats.suivis;
    document.getElementById('cntHistos').textContent = stats.histos;
    document.getElementById('cntMoyCalories').textContent = stats.avg_calories;
  }

  async function peuplerSelects() {
    const regimes = await getRegimeSelect();
    ['suiviRegime', 'histoRegime'].forEach((id) => {
      const sel = document.getElementById(id);
      const val = sel.value;
      sel.innerHTML = '<option value="">-- Choisir un régime --</option>';
      regimes.forEach((r) => {
        const o = document.createElement('option');
        o.value = r.id_regime;
        o.textContent = (r.type_regime === 'cut' ? '🔥' : r.type_regime === 'bulk' ? '💪' : '⚖️') + ' #R-' + String(r.id_regime).padStart(3, '0') + ' — ' + r.type_regime + ' (' + r.calories_cible + ' kcal)';
        sel.appendChild(o);
      });
      if (val) sel.value = val;
    });
  }

  async function afficherTableauRegimes() {
    const regimes = await getRegimes();
    const c = document.getElementById('regimeTableContainer');
    if (regimes.length === 0) {
      c.innerHTML = '<p class="note" style="margin-top:18px;">Aucun régime enregistré.</p>';
      return;
    }

    const rows = regimes.map((r) => {
      const prog = calcProg(r.date_debut, r.duree);
      return '<tr>' +
        '<td><span class="id-badge">#R-' + String(r.id_regime).padStart(3, '0') + '</span></td>' +
        '<td>' + typeChip(r.type_regime) + '</td>' +
        '<td><strong>' + r.calories_cible + '</strong> kcal</td>' +
        '<td>' + fmtDate(r.date_debut) + '</td>' +
        '<td><strong>' + r.poids + '</strong> kg</td>' +
        '<td>' + r.duree + ' j</td>' +
        '<td><div class="progress-mini"><div class="progress-mini-fill" style="width:' + prog + '%"></div></div>' +
          '<span style="font-size:11px;color:#688273;margin-left:6px;">' + prog + '%</span></td>' +
        '<td><div class="action-cell">' +
          '<button class="edit-btn" data-rid="' + r.id_regime + '">✏️ Modifier</button>' +
          '<button class="delete-btn" data-rid="' + r.id_regime + '" data-type="regime">🗑️ Supprimer</button>' +
        '</div></td>' +
        '</tr>';
    }).join('');

    c.innerHTML =
      '<div class="table-wrapper" style="margin-top:20px;">' +
      '<table class="table"><thead><tr>' +
      '<th>ID</th><th>Type</th><th>Calories cible</th><th>Début</th><th>Poids départ</th><th>Durée</th><th>Progression</th><th>Actions</th>' +
      '</tr></thead><tbody>' + rows + '</tbody></table></div>';

    c.querySelectorAll('.edit-btn').forEach((btn) => {
      btn.addEventListener('click', () => ouvrirEditRegime(Number(btn.dataset.rid)));
    });
    c.querySelectorAll('.delete-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        ouvrirConfirm('Supprimer ce régime ?', 'Le suivi et les recommandations associés seront aussi supprimés.', async () => {
          const id = Number(btn.dataset.rid);
          await apiFetch('delete', { type: 'regime', id }, 'POST');
          await rafraichirTout();
          flash('✅ Régime supprimé avec succès.', 'success');
        });
      });
    });
  }

  document.getElementById('regimeForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const payload = {
      type_regime: document.getElementById('typeRegime').value,
      calories_cible: Number(document.getElementById('caloriesCible').value),
      date_debut: document.getElementById('dateDebut').value,
      poids_initial: parseFloat(document.getElementById('poids').value),
      duree: Number(document.getElementById('duree').value)
    };
    await apiFetch('regime', payload, 'POST');
    e.target.reset();
    await rafraichirTout();
    flash('✅ Régime ajouté ! Recommandation générée automatiquement.', 'success');
  });

  async function ouvrirEditRegime(id) {
    const regimes = await getRegimes();
    const r = regimes.find((x) => x.id_regime === id);
    if (!r) return;
    document.getElementById('editRegimeId').value = id;
    document.getElementById('editTypeRegime').value = r.type_regime;
    document.getElementById('editCalories').value = r.calories_cible;
    document.getElementById('editDateDebut').value = r.date_debut;
    document.getElementById('editPoids').value = r.poids;
    document.getElementById('editDuree').value = r.duree;
    document.getElementById('editRegimeModal').style.display = 'flex';
  }

  document.getElementById('cancelEditRegime').addEventListener('click', function () {
    document.getElementById('editRegimeModal').style.display = 'none';
  });

  document.getElementById('saveEditRegime').addEventListener('click', async function () {
    const payload = {
      id_regime: Number(document.getElementById('editRegimeId').value),
      type_regime: document.getElementById('editTypeRegime').value,
      calories_cible: Number(document.getElementById('editCalories').value),
      date_debut: document.getElementById('editDateDebut').value,
      poids_initial: parseFloat(document.getElementById('editPoids').value),
      duree: Number(document.getElementById('editDuree').value)
    };
    await apiFetch('editRegime', payload, 'POST');
    document.getElementById('editRegimeModal').style.display = 'none';
    await rafraichirTout();
    flash('✅ Régime modifié avec succès.', 'success');
  });

  async function afficherTableauSuivi() {
    const suivis = await getSuivis();
    const c = document.getElementById('suiviTableContainer');
    if (suivis.length === 0) {
      c.innerHTML = '<p class="note" style="margin-top:18px;">Aucune entrée de suivi enregistrée.</p>';
      return;
    }

    const regimes = await getRegimes();
    const rows = suivis.slice().sort((a, b) => new Date(b.date) - new Date(a.date)).map((s) => {
      const r = regimes.find((x) => x.id_regime === s.id_regime);
      const cible = r ? r.calories_cible : 0;
      const diff = s.calories_consommees - cible;
      const cls = Math.abs(diff) <= 150 ? 'diff-ok' : diff > 0 ? 'diff-surplus' : 'diff-deficit';
      const lbl = Math.abs(diff) <= 150 ? '✅ OK' : diff > 0 ? '⬆️ +' + diff + ' kcal' : '⬇️ ' + diff + ' kcal';
      return '<tr>' +
        '<td><span class="id-badge">#S-' + String(s.id_suivi).padStart(3, '0') + '</span></td>' +
        '<td><span class="small-badge">' + (r ? ('#R-' + String(r.id_regime).padStart(3, '0') + ' · ' + r.type_regime) : '—') + '</span></td>' +
        '<td>' + fmtDate(s.date) + '</td>' +
        '<td><strong>' + s.poids + '</strong> kg</td>' +
        '<td><strong>' + s.calories_consommees + '</strong> kcal</td>' +
        '<td><span class="calorie-diff ' + cls + '">' + lbl + '</span></td>' +
        '<td><div class="action-cell"><button class="delete-btn" data-sid="' + s.id_suivi + '">🗑️ Supprimer</button></div></td>' +
        '</tr>';
    }).join('');

    c.innerHTML =
      '<div class="table-wrapper" style="margin-top:20px;">' +
      '<table class="table"><thead><tr>' +
      '<th>ID</th><th>Régime</th><th>Date</th><th>Poids</th><th>Calories consommées</th><th>vs Cible</th><th>Actions</th>' +
      '</tr></thead><tbody>' + rows + '</tbody></table></div>';

    c.querySelectorAll('.delete-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        ouvrirConfirm('Supprimer cette entrée ?', 'Cette entrée de suivi sera supprimée définitivement.', async () => {
          const sid = Number(btn.dataset.sid);
          await apiFetch('delete', { type: 'suivi', id: sid }, 'POST');
          await rafraichirTout();
          flash('✅ Entrée de suivi supprimée.', 'success');
        });
      });
    });
  }

  document.getElementById('suiviForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const payload = {
      id_regime: Number(document.getElementById('suiviRegime').value),
      date: document.getElementById('suiviDate').value,
      poids: parseFloat(document.getElementById('suiviPoids').value),
      calories_consommees: Number(document.getElementById('suiviCalories').value)
    };
    await apiFetch('suivi', payload, 'POST');
    e.target.reset();
    await rafraichirTout();
    flash('✅ Entrée de suivi ajoutée avec succès.', 'success');
  });

  async function afficherTableauHistorique() {
    const histos = await getHistos();
    const c = document.getElementById('histoTableContainer');
    if (histos.length === 0) {
      c.innerHTML = '<p class="note" style="margin-top:18px;">Aucune recommandation enregistrée.</p>';
      return;
    }

    const regimes = await getRegimes();
    const rows = histos.map((h) => {
      const r = regimes.find((x) => x.id_regime === h.id_regime);
      const type = r ? r.type_regime : 'inconnu';
      return '<tr>' +
        '<td><span class="id-badge">#H-' + String(h.id_historique).padStart(3, '0') + '</span></td>' +
        '<td>' + typeChip(type) + '</td>' +
        '<td><span class="small-badge">' + (r ? ('#R-' + String(r.id_regime).padStart(3, '0') + ' · ' + r.type_regime) : '—') + '</span></td>' +
        '<td><div class="reco-preview">' + h.recommandation + '</div></td>' +
        '<td><div class="action-cell">' +
          '<button class="edit-btn" data-hid="' + h.id_historique + '">✏️ Modifier</button>' +
          '<button class="delete-btn" data-hid="' + h.id_historique + '">🗑️ Supprimer</button>' +
        '</div></td>' +
        '</tr>';
    }).join('');

    c.innerHTML =
      '<div class="table-wrapper" style="margin-top:20px;">' +
      '<table class="table"><thead><tr>' +
      '<th>ID</th><th>Type régime</th><th>Régime</th><th>Recommandation</th><th>Actions</th>' +
      '</tr></thead><tbody>' + rows + '</tbody></table></div>';

    c.querySelectorAll('.edit-btn').forEach((btn) => {
      btn.addEventListener('click', () => ouvrirEditHisto(Number(btn.dataset.hid)));
    });
    c.querySelectorAll('.delete-btn').forEach((btn) => {
      btn.addEventListener('click', () => {
        ouvrirConfirm('Supprimer cette recommandation ?', 'Cette recommandation sera supprimée définitivement.', async () => {
          const hid = Number(btn.dataset.hid);
          await apiFetch('delete', { type: 'histo', id: hid }, 'POST');
          await rafraichirTout();
          flash('✅ Recommandation supprimée.', 'success');
        });
      });
    });
  }

  document.getElementById('histoForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const payload = {
      id_regime: Number(document.getElementById('histoRegime').value),
      recommandation: document.getElementById('histoReco').value.trim()
    };
    await apiFetch('histo', payload, 'POST');
    e.target.reset();
    await rafraichirTout();
    flash('✅ Recommandation ajoutée avec succès.', 'success');
  });

  async function ouvrirEditHisto(id) {
    const histos = await getHistos();
    const h = histos.find((x) => x.id_historique === id);
    if (!h) return;
    document.getElementById('editHistoId').value = id;
    document.getElementById('editHistoReco').value = h.recommandation;
    document.getElementById('editHistoModal').style.display = 'flex';
  }

  document.getElementById('cancelEditHisto').addEventListener('click', function () {
    document.getElementById('editHistoModal').style.display = 'none';
  });

  document.getElementById('saveEditHisto').addEventListener('click', async function () {
    const payload = {
      id_historique: Number(document.getElementById('editHistoId').value),
      recommandation: document.getElementById('editHistoReco').value.trim()
    };
    await apiFetch('editHisto', payload, 'POST');
    document.getElementById('editHistoModal').style.display = 'none';
    await rafraichirTout();
    flash('✅ Recommandation modifiée.', 'success');
  });

  var confirmCallback = null;
  function ouvrirConfirm(titre, desc, cb) {
    document.getElementById('confirmTitle').textContent = titre;
    document.getElementById('confirmDesc').textContent = desc;
    confirmCallback = cb;
    document.getElementById('confirmModal').style.display = 'flex';
  }
  document.getElementById('cancelConfirm').addEventListener('click', function () {
    document.getElementById('confirmModal').style.display = 'none';
    confirmCallback = null;
  });
  document.getElementById('okConfirm').addEventListener('click', async function () {
    document.getElementById('confirmModal').style.display = 'none';
    if (confirmCallback) { await confirmCallback(); confirmCallback = null; }
  });

  document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
      document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
      btn.classList.add('active');
      document.getElementById(btn.dataset.tab).classList.add('active');
    });
  });

  document.querySelectorAll('.menu a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      var target = a.getAttribute('href').substring(1);
      var tabMap = { tabRegimes: 'tabRegimes', tabSuivis: 'tabSuivis', tabHistorique: 'tabHistorique' };
      if (tabMap[target]) {
        document.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
        document.querySelector('[data-tab="' + target + '"]').classList.add('active');
        document.getElementById(target).classList.add('active');
      }
      var el = document.getElementById(target);
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    });
  });

  document.getElementById('resetBtn').addEventListener('click', function () {
    ouvrirConfirm('Réinitialiser toutes les données ?', 'Tous les régimes, suivis et recommandations seront effacés.', async () => {
      await apiFetch('reset', {}, 'POST');
      await rafraichirTout();
      flash('🔄 Données réinitialisées.', 'success');
    });
  });

  async function rafraichirTout() {
    await majStats();
    await peuplerSelects();
    await afficherTableauRegimes();
    await afficherTableauSuivi();
    await afficherTableauHistorique();
  }

  document.getElementById('dateDebut').valueAsDate = new Date();
  document.getElementById('suiviDate').valueAsDate = new Date();
  rafraichirTout();
  </script>
</body>
</html>
