<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Backoffice</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
  <link rel="stylesheet" href="public/css/backoffice.css" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── TABS ── */
    .tabs-nav {
      display: flex; gap: 6px; margin-bottom: 24px;
      border-bottom: 2px solid rgba(23,153,95,.15); padding-bottom: 0;
    }
    .tab-btn {
      padding: 11px 22px; border: none; background: none; cursor: pointer;
      font-size: 14px; font-weight: 700; color: #688273;
      border-bottom: 3px solid transparent; margin-bottom: -2px;
      border-radius: 10px 10px 0 0; transition: all .2s; font-family: inherit;
    }
    .tab-btn.active { color: #17995f; border-bottom-color: #17995f; background: rgba(23,153,95,.07); }
    .tab-btn:hover:not(.active) { background: rgba(23,153,95,.04); color: #17995f; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* ── SEARCH BAR ── */
    .search-bar {
      display: flex; gap: 12px; align-items: flex-end;
      flex-wrap: wrap; margin-bottom: 20px;
      background: #f8fdfb; border: 1px solid rgba(23,153,95,.14);
      border-radius: 18px; padding: 18px 20px;
    }
    .search-field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 150px; }
    .search-field label { font-size: 12px; font-weight: 700; color: #10281b; letter-spacing: .02em; }
    .search-field input,
    .search-field select {
      padding: 10px 14px; border: 1.5px solid #ddeee5; border-radius: 12px;
      background: #fff; font-size: 13px; font-family: inherit; color: #10281b;
      transition: border-color .2s, box-shadow .2s;
    }
    .search-field input:focus,
    .search-field select:focus { outline: none; border-color: #17995f; box-shadow: 0 0 0 3px rgba(23,153,95,.1); }

    .search-actions { display: flex; gap: 8px; align-items: flex-end; padding-bottom: 1px; }
    .btn-search {
      padding: 10px 20px; background: linear-gradient(135deg, #17995f, #0f6d42);
      color: #fff; border: none; border-radius: 12px; font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: inherit; box-shadow: 0 6px 16px rgba(23,153,95,.2);
      transition: transform .2s, box-shadow .2s;
    }
    .btn-search:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(23,153,95,.3); }
    .btn-reset {
      padding: 10px 16px; background: #f1f5f1; color: #688273; border: 1.5px solid #ddeee5;
      border-radius: 12px; font-size: 13px; font-weight: 700; cursor: pointer;
      font-family: inherit; text-decoration: none; display: flex; align-items: center;
      transition: background .2s;
    }
    .btn-reset:hover { background: #e8f0ea; }

    /* ── RESULT CHIP ── */
    .result-chip {
      display: inline-flex; align-items: center; gap: 8px;
      background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;
      padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
      margin-bottom: 16px;
    }

    /* ── TRI ── */
    .tri-bar {
      display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
      margin-bottom: 14px;
    }
    .tri-bar span { font-size: 12px; font-weight: 700; color: #688273; text-transform: uppercase; letter-spacing: .08em; }
    .tri-btn {
      padding: 6px 14px; border: 1.5px solid #ddeee5; border-radius: 999px;
      background: #fff; font-size: 12px; font-weight: 700; color: #10281b;
      cursor: pointer; font-family: inherit; transition: all .2s; display: flex; align-items: center; gap: 4px;
    }
    .tri-btn:hover, .tri-btn.active { background: rgba(23,153,95,.1); border-color: #17995f; color: #0f6d42; }

    /* ── TABLE ── */
    .table-wrap { overflow-x: auto; border-radius: 18px; border: 1px solid rgba(23,153,95,.12); }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th {
      background: linear-gradient(180deg, #f5fbf7, #eff8f3);
      padding: 13px 16px; text-align: left; font-size: 12px; font-weight: 800;
      color: #10281b; text-transform: uppercase; letter-spacing: .06em;
      border-bottom: 1px solid rgba(23,153,95,.12);
    }
    .data-table td {
      padding: 13px 16px; font-size: 13px; color: #10281b;
      border-bottom: 1px solid rgba(23,153,95,.07);
    }
    .data-table tbody tr:hover { background: rgba(23,153,95,.03); }
    .data-table tbody tr:last-child td { border-bottom: none; }

    /* ── BADGES RÔLES & STATUTS ── */
    .badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 4px 11px; border-radius: 999px;
      font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase;
    }
    .badge-admin        { background: #fef3c7; color: #92400e; }
    .badge-nutritionniste { background: #ede9fe; color: #5b21b6; }
    .badge-client       { background: #d1fae5; color: #065f46; }
    .badge-succes       { background: #dcfce7; color: #16a34a; }
    .badge-echec        { background: #fee2e2; color: #dc2626; }
    .badge-action       { background: #e0f2fe; color: #0369a1; }

    /* ── STAT CARDS HISTORIQUE ── */
    .hist-stats { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 20px; }
    .hist-stat-card {
      flex: 1; min-width: 130px; background: #fff;
      border: 1px solid rgba(23,153,95,.12); border-radius: 16px;
      padding: 18px 20px; text-align: center;
      box-shadow: 0 4px 16px rgba(12,29,21,.05);
    }
    .hist-stat-card span { font-size: 12px; color: #688273; font-weight: 600; display: block; margin-bottom: 6px; }
    .hist-stat-card strong { font-size: 2rem; font-weight: 900; color: #10281b; }
    .hist-stat-card.ok strong { color: #16a34a; }
    .hist-stat-card.ko strong { color: #dc2626; }
    .hist-stat-card.taux strong { color: #7c3aed; }

    /* ── GRAPHIQUES ── */
    .charts-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
    @media (max-width: 900px) { .charts-row { grid-template-columns: 1fr; } }

    .chart-card {
      background: #fff; border: 1px solid rgba(23,153,95,.12);
      border-radius: 20px; padding: 22px 24px;
      box-shadow: 0 4px 20px rgba(12,29,21,.06);
    }
    .chart-card h3 { font-size: 14px; font-weight: 800; color: #10281b; margin: 0 0 4px; }
    .chart-card p  { font-size: 12px; color: #688273; margin: 0 0 18px; }
    .chart-wrap { position: relative; height: 200px; }

    /* ── STAT UTILISATEUR (après recherche) ── */
    .user-stat-box {
      background: linear-gradient(135deg, #f0fdf7, #e8faf0);
      border: 1.5px solid rgba(23,153,95,.2); border-radius: 18px;
      padding: 20px 24px; margin-bottom: 20px;
    }
    .user-stat-box h4 { font-size: 14px; font-weight: 800; color: #10281b; margin: 0 0 14px; display: flex; align-items: center; gap: 8px; }
    .user-stat-row { display: flex; gap: 14px; flex-wrap: wrap; }
    .user-mini-stat {
      flex: 1; min-width: 110px; background: #fff;
      border-radius: 12px; padding: 14px 16px; text-align: center;
      border: 1px solid rgba(23,153,95,.1);
    }
    .user-mini-stat .val { font-size: 1.6rem; font-weight: 900; color: #17995f; }
    .user-mini-stat .lbl { font-size: 11px; color: #688273; font-weight: 600; margin-top: 2px; }

    /* ── PDF BUTTON ── */
    .btn-pdf {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #5b21b6);
      color: #fff; border: none; border-radius: 12px; font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: inherit;
      box-shadow: 0 6px 16px rgba(124,58,237,.25); transition: transform .2s, box-shadow .2s;
      text-decoration: none;
    }
    .btn-pdf:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(124,58,237,.35); }

    /* ── SECTION HEADER ── */
    .sec-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px; }
    .sec-head h2 { font-size: 18px; font-weight: 800; color: #10281b; margin: 0; }

    /* ── FORM ADD USER ── */
    .add-user-form {
      background: #f8fdfb; border: 1px solid rgba(23,153,95,.14);
      border-radius: 18px; padding: 22px 24px; margin-bottom: 24px;
    }
    .add-user-form h3 { font-size: 15px; font-weight: 800; color: #10281b; margin: 0 0 16px; }
    .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; }
    .form-grid label { font-size: 12px; font-weight: 700; color: #10281b; display: block; margin-bottom: 5px; }
    .form-grid input,
    .form-grid select {
      width: 100%; padding: 10px 13px; border: 1.5px solid #ddeee5;
      border-radius: 12px; background: #fff; font-size: 13px;
      font-family: inherit; color: #10281b; transition: border-color .2s, box-shadow .2s;
      box-sizing: border-box;
    }
    .form-grid input:focus, .form-grid select:focus { outline: none; border-color: #17995f; box-shadow: 0 0 0 3px rgba(23,153,95,.1); }
    .form-submit-row { margin-top: 14px; }

    /* ── MODAL ── */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px); z-index: 999; justify-content: center; align-items: center; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: #fff; border-radius: 20px; padding: 28px; width: 100%; max-width: 520px; box-shadow: 0 30px 80px rgba(0,0,0,.2); animation: popIn .3s cubic-bezier(.23,1,.32,1); }
    @keyframes popIn { from { opacity:0; transform:scale(.9) translateY(20px); } to { opacity:1; transform:scale(1) translateY(0); } }
    .modal-box h3 { font-size: 16px; font-weight: 800; color: #10281b; margin: 0 0 20px; }
    .modal-form-grid { display: grid; gap: 14px; }
    .modal-form-grid label { font-size: 12px; font-weight: 700; color: #10281b; display: block; margin-bottom: 5px; }
    .modal-form-grid input,
    .modal-form-grid select {
      width: 100%; padding: 10px 13px; border: 1.5px solid #ddeee5;
      border-radius: 12px; background: #fff; font-size: 13px; font-family: inherit; color: #10281b;
      transition: border-color .2s, box-shadow .2s; box-sizing: border-box;
    }
    .modal-form-grid input:focus, .modal-form-grid select:focus { outline: none; border-color: #17995f; box-shadow: 0 0 0 3px rgba(23,153,95,.1); }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
    .modal-actions .cancel-btn { padding: 9px 20px; border: 1.5px solid #ddeee5; border-radius: 12px; background: #f8fafb; color: #688273; font-weight: 700; cursor: pointer; font-family: inherit; transition: background .2s; }
    .modal-actions .cancel-btn:hover { background: #edf2ee; }
    .msg-champ { font-size: 11px; font-weight: 600; margin-top: 3px; min-height: 15px; }
    .msg-champ.erreur { color: #dc2626; }
    .msg-champ.succes { color: #16a34a; }

    /* ── MESSAGES ── */
    .msg-ok, .msg-err {
      padding: 13px 18px; border-radius: 12px; font-weight: 700; font-size: 13px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;
    }
    .msg-ok  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .msg-err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    /* VIDER BTN */
    .btn-danger-outline {
      padding: 8px 16px; background: #fff; color: #dc2626; border: 2px solid #dc2626;
      border-radius: 10px; cursor: pointer; font-weight: 700; font-size: 13px;
      font-family: inherit; transition: background .2s, color .2s;
    }
    .btn-danger-outline:hover { background: #dc2626; color: #fff; }
  </style>
  <script src="public/js/lang.js"></script>
</head>
<body data-page-title="page_title_dashboard">

<!-- ═══════════════ SIDEBAR ═══════════════ -->
<aside class="sidebar">
  <div class="brand">
    <div class="brand-mark">🥗</div>
    <div>
      <h1 style="font-size:20px;margin:0 0 4px;">NutriSmart</h1>
      <p class="brand-slogan">Eat Smart · Live Smart</p>
    </div>
  </div>

  <nav class="menu">
    <a href="index.php?page=dashboard&onglet=utilisateurs"
       class="<?php echo ($onglet === 'utilisateurs') ? 'active' : ''; ?>">
      👥 Utilisateurs
    </a>
    <a href="index.php?page=dashboard&onglet=historique"
       class="<?php echo ($onglet === 'historique') ? 'active' : ''; ?>">
      📋 Historique
    </a>
    <a href="index.php?page=accueil">🌐 Voir le site</a>
  </nav>

  <div class="sidebar-footer">
    <div id="dashLangSwitcherContainer" style="margin-bottom:10px;"></div>
    <div style="background:rgba(255,255,255,.07);border-radius:14px;padding:14px;border:1px solid rgba(255,255,255,.08);">
      <div style="font-size:11px;color:rgba(255,255,255,.5);font-weight:700;letter-spacing:.08em;text-transform:uppercase;margin-bottom:6px;">Connecté en tant que</div>
      <div style="font-size:14px;font-weight:800;color:#d1fae5;">👤 <?php echo htmlspecialchars($adminPrenom); ?></div>
    </div>
    <form action="index.php" method="POST">
      <input type="hidden" name="action" value="deconnexion" />
      <button type="submit" class="danger-btn" style="border-radius:12px;padding:11px 16px;font-size:13px;">
        Se déconnecter
      </button>
    </form>
  </div>
</aside>

<!-- ═══════════════ MAIN ═══════════════ -->
<main class="main">

  <!-- STATS CARDS TOP -->
  <section class="panel stats-panel">
    <div class="stats-card">
      <span data-i18n="dash_total_users">Total utilisateurs</span>
      <strong><?php echo $total; ?></strong>
    </div>
    <div class="stats-card">
      <span data-i18n="dash_admins">Admins</span>
      <strong><?php echo $parRole['admin']; ?></strong>
    </div>
    <div class="stats-card">
      <span data-i18n="dash_nutritionists">Nutritionnistes</span>
      <strong><?php echo $parRole['nutritionniste']; ?></strong>
    </div>
    <div class="stats-card">
      <span data-i18n="dash_clients">Clients</span>
      <strong><?php echo $parRole['client']; ?></strong>
    </div>
  </section>

  <!-- MESSAGES -->
  <?php if ($succes === '1' || $succes === 'ajoute'): ?>
    <div class="msg-ok" data-i18n="msg_added">✅ Utilisateur ajouté avec succès.</div>
  <?php elseif ($succes === 'modifie'): ?>
    <div class="msg-ok" data-i18n="msg_updated">✅ Utilisateur modifié avec succès.</div>
  <?php elseif ($succes === 'supprime'): ?>
    <div class="msg-ok">🗑️ Utilisateur supprimé.</div>
  <?php elseif ($succes === 'histo_supprime'): ?>
    <div class="msg-ok">🗑️ Entrée supprimée.</div>
  <?php elseif ($succes === 'histo_vide'): ?>
    <div class="msg-ok" data-i18n="msg_cleared">🗑️ Historique vidé.</div>
  <?php endif; ?>
  <?php if ($erreur === 'champs_vides'): ?>
    <div class="msg-err" data-i18n="err_fields_d">⚠️ Veuillez remplir tous les champs obligatoires.</div>
  <?php elseif ($erreur === 'email_existe'): ?>
    <div class="msg-err">❌ Cet email est déjà utilisé.</div>
  <?php endif; ?>

  <!-- PANEL TABS -->
  <section class="panel">

    <div class="tabs-nav">
      <button class="tab-btn <?php echo ($onglet === 'utilisateurs') ? 'active' : ''; ?>"
              onclick="ouvrirOnglet('utilisateurs')" data-i18n="tab_users">👥 Gestion Utilisateurs</button>
      <button class="tab-btn <?php echo ($onglet === 'historique') ? 'active' : ''; ?>"
              onclick="ouvrirOnglet('historique')" data-i18n="tab_history">📋 Historique Connexions</button>
    </div>

    <!-- ══════════ ONGLET UTILISATEURS ══════════ -->
    <div class="tab-content <?php echo ($onglet === 'utilisateurs') ? 'active' : ''; ?>" id="tab-utilisateurs">

      <!-- GRAPHIQUE INSCRIPTIONS PAR JOUR -->
      <div class="charts-row" style="margin-bottom:24px;">
        <div class="chart-card">
          <h3>📈 Taux d'inscription par mois</h3>
          <p data-i18n="dash_new_users">Inscriptions par mois — 12 derniers mois</p>
          <div class="chart-wrap">
            <canvas id="chartInscriptions"></canvas>
          </div>
        </div>
        <div class="chart-card">
          <h3>🥧 Répartition par rôle</h3>
          <p data-i18n="dash_role_dist">Distribution des rôles dans le système</p>
          <div class="chart-wrap">
            <canvas id="chartRoles"></canvas>
          </div>
        </div>
      </div>

      <!-- RECHERCHE + TRI UTILISATEURS -->
      <form action="index.php" method="GET" id="formRechUser">
        <input type="hidden" name="page" value="dashboard" />
        <input type="hidden" name="onglet" value="utilisateurs" />
        <input type="hidden" name="tri" id="triHidden" value="<?php echo htmlspecialchars($_GET['tri'] ?? 'az'); ?>" />

        <div class="search-bar">
          <div class="search-field">
            <label><span data-i18n="search_nom">Nom</span></label>
            <input type="text" name="recherche_nom"
                   value="<?php echo htmlspecialchars($rechercheNom); ?>"
                   placeholder="Ex : Ben Ali" />
          </div>
          <div class="search-field">
            <label><span data-i18n="search_prenom">Prénom</span></label>
            <input type="text" name="recherche_prenom"
                   value="<?php echo htmlspecialchars($recherchePrenom); ?>"
                   placeholder="Ex : Mohamed" />
          </div>
          <div class="search-field">
            <label><span data-i18n="search_role">Rôle</span></label>
            <select name="recherche_role">
              <option value="">— Tous les rôles —</option>
              <option value="admin"          <?php echo ($rechercheRole === 'admin') ? 'selected' : ''; ?>>Admin</option>
              <option value="nutritionniste" <?php echo ($rechercheRole === 'nutritionniste') ? 'selected' : ''; ?>>Nutritionniste</option>
              <option value="client"         <?php echo ($rechercheRole === 'client') ? 'selected' : ''; ?>>Client</option>
            </select>
          </div>
          <div class="search-actions">
            <button type="submit" class="btn-search" data-i18n="btn_search" data-i18n="btn_search">🔍 Rechercher</button>
            <a href="index.php?page=dashboard&onglet=utilisateurs" class="btn-reset" data-i18n="btn_reset" data-i18n="btn_reset">✕ Reset</a>
          </div>
        </div>
      </form>

      <!-- STATS UTILISATEUR TROUVÉ (si recherche par nom unique) -->
      <?php if (!empty($rechercheNom) || !empty($recherchePrenom)): ?>
        <?php
          $totalConnexions = 0; $connexionsReussies = 0;
          foreach ($historique as $h) {
            if ($h['statut'] === 'succes') $connexionsReussies++;
            $totalConnexions++;
          }
          $tauxConnexion = $totalConnexions > 0 ? round(($connexionsReussies / $totalConnexions) * 100) : 0;
        ?>
        <div class="user-stat-box">
          <h4>📊 Statistiques de connexion — <?php echo htmlspecialchars($rechercheNom . ' ' . $recherchePrenom); ?></h4>
          <div class="user-stat-row">
            <div class="user-mini-stat">
              <div class="val"><?php echo $totalConnexions; ?></div>
              <div class="lbl">Tentatives totales</div>
            </div>
            <div class="user-mini-stat">
              <div class="val" style="color:#16a34a;"><?php echo $connexionsReussies; ?></div>
              <div class="lbl" data-i18n="dash_success_conn">Connexions réussies</div>
            </div>
            <div class="user-mini-stat">
              <div class="val" style="color:#dc2626;"><?php echo $totalConnexions - $connexionsReussies; ?></div>
              <div class="lbl">Échecs</div>
            </div>
            <div class="user-mini-stat">
              <div class="val" style="color:#7c3aed;"><?php echo $tauxConnexion; ?>%</div>
              <div class="lbl" data-i18n="dash_rate">Taux de succès</div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- RÉSULTAT + TRI + PDF -->
      <?php if (!empty($rechercheNom) || !empty($recherchePrenom) || !empty($rechercheRole)): ?>
        <div class="result-chip">
          🔎 <?php echo count($utilisateurs); ?> résultat(s) trouvé(s)
          <?php if (!empty($rechercheRole)): ?> · Rôle : <strong><?php echo htmlspecialchars($rechercheRole); ?></strong><?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- BARRE DE TRI -->
      <div class="tri-bar">
        <span data-i18n="sort_label">Trier :</span>
        <button type="button" class="tri-btn <?php echo (($_GET['tri'] ?? 'az') === 'az') ? 'active' : ''; ?>"
                onclick="setTri('az')">🔤 A → Z</button>
        <button type="button" class="tri-btn <?php echo (($_GET['tri'] ?? '') === 'za') ? 'active' : ''; ?>"
                onclick="setTri('za')">🔤 Z → A</button>
        <button type="button" class="tri-btn <?php echo (($_GET['tri'] ?? '') === 'recent') ? 'active' : ''; ?>"
                onclick="setTri('recent')">🕐 Plus récent</button>
        <button type="button" class="tri-btn <?php echo (($_GET['tri'] ?? '') === 'ancien') ? 'active' : ''; ?>"
                onclick="setTri('ancien')">🕐 Plus ancien</button>
        <button type="button" class="tri-btn <?php echo (($_GET['tri'] ?? '') === 'role') ? 'active' : ''; ?>"
                onclick="setTri('role')">👤 Par rôle</button>

        <button type="button" class="btn-pdf" onclick="exportPDFUtilisateurs()" style="margin-left:auto;">
          📄 Exporter PDF
        </button>
      </div>

      <!-- FORMULAIRE AJOUTER -->
      <div class="add-user-form">
        <h3 data-i18n="add_user_title">➕ Ajouter un utilisateur</h3>
        <form action="index.php" method="POST">
          <input type="hidden" name="action" value="ajouter" />
          <input type="hidden" name="source" value="dashboard" />
          <div class="form-grid">
            <div>
              <label><span data-i18n="label_nom_d">Nom</span></label>
              <input type="text" name="nom" placeholder="Ben Ali" />
            </div>
            <div>
              <label><span data-i18n="label_prenom_d">Prénom</span></label>
              <input type="text" name="prenom" placeholder="Mohamed" />
            </div>
            <div>
              <label><span data-i18n="label_email_d">Email</span></label>
              <input type="email" name="email" placeholder="exemple@mail.com" />
            </div>
            <div>
              <label><span data-i18n="label_password_d">Mot de passe</span></label>
              <input type="password" name="mot_de_passe" placeholder="••••••••" />
            </div>
            <div>
              <label><span data-i18n="label_role_d">Rôle</span></label>
              <select name="role">
                <option value="">— Choisir —</option>
                <option value="admin">Admin</option>
                <option value="nutritionniste">Nutritionniste</option>
                <option value="client">Client</option>
              </select>
            </div>
          </div>
          <div class="form-submit-row">
            <button type="submit" class="primary-btn" data-i18n="btn_add">✅ Ajouter l'utilisateur</button>
          </div>
        </form>
      </div>

      <!-- TABLEAU UTILISATEURS -->
      <div class="table-wrap" id="tableUtilisateurs">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="th_fullname">Nom complet</th>
              <th data-i18n="th_email">Email</th>
              <th data-i18n="th_role">Rôle</th>
              <th data-i18n="th_actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($utilisateurs)): ?>
              <tr><td colspan="5" style="text-align:center;color:#688273;padding:32px;">Aucun utilisateur trouvé.</td></tr>
            <?php else: ?>
              <?php foreach ($utilisateurs as $u): ?>
              <tr>
                <td style="font-weight:800;color:#17995f;">#<?php echo $u['id_user']; ?></td>
                <td style="font-weight:700;"><?php echo htmlspecialchars($u['nom'] . ' ' . $u['prenom']); ?></td>
                <td style="color:#688273;"><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                  <span class="badge badge-<?php echo $u['role']; ?>">
                    <?php
                      echo $u['role'] === 'admin' ? '⚙️ Admin'
                        : ($u['role'] === 'nutritionniste' ? '👩‍⚕️ Nutritionniste' : '🧑‍💼 Client');
                    ?>
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:6px;">
                    <button class="edit-btn" onclick="ouvrirModifier(
                      <?php echo $u['id_user']; ?>,
                      '<?php echo htmlspecialchars($u['nom'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($u['prenom'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>',
                      '<?php echo $u['role']; ?>'
                    )" data-i18n="btn_edit">✏️ Modifier</button>
                    <form action="index.php" method="POST" style="display:inline;"
                          onsubmit="return confirm(typeof t === 'function' ? t('confirm_delete_user') : 'Supprimer cet utilisateur ?');">
                      <input type="hidden" name="action" value="supprimer" />
                      <input type="hidden" name="id_user" value="<?php echo $u['id_user']; ?>" />
                      <button type="submit" class="delete-btn" data-i18n="btn_delete">🗑️ Supprimer</button>
                    </form>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div><!-- /tab-utilisateurs -->

    <!-- ══════════ ONGLET HISTORIQUE ══════════ -->
    <div class="tab-content <?php echo ($onglet === 'historique') ? 'active' : ''; ?>" id="tab-historique">

      <!-- STATS HISTORIQUE -->
      <div class="hist-stats">
        <div class="hist-stat-card">
          <span data-i18n="dash_total_conn">Total entrées</span>
          <strong><?php echo $stats['total']; ?></strong>
        </div>
        <div class="hist-stat-card ok">
          <span>✅ Réussies</span>
          <strong><?php echo $stats['succes']; ?></strong>
        </div>
        <div class="hist-stat-card ko">
          <span>❌ Échouées</span>
          <strong><?php echo $stats['echec']; ?></strong>
        </div>
        <div class="hist-stat-card taux">
          <span>🎯 Taux de succès</span>
          <strong><?php
            echo $stats['total'] > 0
              ? round(($stats['succes'] / $stats['total']) * 100) . '%'
              : '—';
          ?></strong>
        </div>
      </div>

      <!-- GRAPHIQUE ACTIVITÉ HISTORIQUE -->
      <div class="charts-row" style="margin-bottom:24px;">
        <div class="chart-card">
          <h3>📊 Activité globale par statut</h3>
          <p data-i18n="dash_conn_chart">Connexions réussies vs échouées</p>
          <div class="chart-wrap">
            <canvas id="chartHistoGlobal"></canvas>
          </div>
        </div>
        <div class="chart-card">
          <h3>👤 Taux d'activité par utilisateur</h3>
          <p data-i18n="dash_top_users">Top 5 utilisateurs les plus actifs</p>
          <div class="chart-wrap">
            <canvas id="chartActiviteUsers"></canvas>
          </div>
        </div>
      </div>

      <!-- RECHERCHE HISTORIQUE + FILTRE STATUT -->
      <div class="sec-head">
        <h2 data-i18n="dash_conn_log">Journal des connexions</h2>
        <div style="display:flex;gap:8px;">
          <button type="button" class="btn-pdf" onclick="exportPDFHistorique()" data-i18n="btn_export_pdf">📄 Exporter PDF</button>
          <form action="index.php" method="POST"
                onsubmit="return confirm('Vider tout l\'historique ?');">
            <input type="hidden" name="action" value="vider" />
            <button type="submit" class="btn-danger-outline" data-i18n="btn_clear">🗑️ Vider</button>
          </form>
        </div>
      </div>

      <form action="index.php" method="GET">
        <input type="hidden" name="page" value="dashboard" />
        <input type="hidden" name="onglet" value="historique" />
        <div class="search-bar">
          <div class="search-field">
            <label>Nom</label>
            <input type="text" name="recherche_hist_nom"
                   value="<?php echo htmlspecialchars($rechercheHistNom); ?>"
                   placeholder="Ex : Ben Ali" />
          </div>
          <div class="search-field">
            <label>Prénom</label>
            <input type="text" name="recherche_hist_prenom"
                   value="<?php echo htmlspecialchars($rechercheHistPrenom); ?>"
                   placeholder="Ex : Mohamed" />
          </div>
          <div class="search-field">
            <label><span data-i18n="search_status">Statut</span></label>
            <select name="recherche_hist_statut">
              <option value="">— Tous —</option>
              <option value="succes" <?php echo (($_GET['recherche_hist_statut'] ?? '') === 'succes') ? 'selected' : ''; ?>>✅ Succès</option>
              <option value="echec"  <?php echo (($_GET['recherche_hist_statut'] ?? '') === 'echec')  ? 'selected' : ''; ?>>❌ Échec</option>
            </select>
          </div>
          <div class="search-actions">
            <button type="submit" class="btn-search">🔍 Rechercher</button>
            <a href="index.php?page=dashboard&onglet=historique" class="btn-reset">✕ Reset</a>
          </div>
        </div>
      </form>

      <?php if (!empty($rechercheHistNom) || !empty($rechercheHistPrenom) || !empty($_GET['recherche_hist_statut'])): ?>
        <div class="result-chip">
          🔎 <?php echo count($historique); ?> résultat(s)
          <?php if (!empty($_GET['recherche_hist_statut'])): ?> · Statut : <strong><?php echo htmlspecialchars($_GET['recherche_hist_statut']); ?></strong><?php endif; ?>
        </div>
      <?php endif; ?>

      <!-- TABLEAU HISTORIQUE -->
      <div class="table-wrap" id="tableHistorique">
        <table class="data-table">
          <thead>
            <tr>
              <th>#</th>
              <th data-i18n="th_user">Utilisateur</th>
              <th data-i18n="th_action">Action</th>
              <th data-i18n="th_status">Statut</th>
              <th data-i18n="th_email_tried">Email tenté</th>
              <th data-i18n="th_date">Date</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($historique)): ?>
              <tr><td colspan="7" style="text-align:center;color:#688273;padding:32px;">Aucun historique trouvé.</td></tr>
            <?php else: ?>
              <?php foreach ($historique as $h): ?>
              <tr>
                <td style="font-weight:800;color:#688273;">#<?php echo $h['id_historique']; ?></td>
                <td style="font-weight:700;">
                  <?php echo $h['nom'] ? htmlspecialchars($h['nom'] . ' ' . $h['prenom']) : '<span style="color:#aaa;">Inconnu</span>'; ?>
                </td>
                <td><span class="badge badge-action"><?php echo htmlspecialchars($h['action']); ?></span></td>
                <td>
                  <?php if ($h['statut'] === 'succes'): ?>
                    <span class="badge badge-succes">✅ Succès</span>
                  <?php else: ?>
                    <span class="badge badge-echec">❌ Échec</span>
                  <?php endif; ?>
                </td>
                <td style="color:#688273;font-size:12px;"><?php echo htmlspecialchars($h['email_tente']); ?></td>
                <td style="color:#688273;font-size:12px;"><?php echo htmlspecialchars($h['date_action']); ?></td>
                <td>
                  <form action="index.php" method="POST" style="display:inline;"
                        onsubmit="return confirm(typeof t === 'function' ? t('confirm_delete_entry') : 'Supprimer cette entrée ?');">
                    <input type="hidden" name="action" value="supprimer_historique" />
                    <input type="hidden" name="id_historique" value="<?php echo $h['id_historique']; ?>" />
                    <button type="submit" class="delete-btn">🗑️</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div><!-- /tab-historique -->

  </section>
</main>

<!-- MODAL MODIFIER -->
<div class="modal-overlay" id="modalModifier">
  <div class="modal-box">
    <h3 data-i18n="modal_edit_title">✏️ Modifier l'utilisateur</h3>
    <form id="formModifier" action="index.php" method="POST">
      <input type="hidden" name="action" value="modifier" />
      <input type="hidden" name="id_user" id="edit-id" />
      <div class="modal-form-grid">
        <div>
          <label>Nom</label>
          <input type="text" name="nom" id="edit-nom" />
          <div id="msgEditNom" class="msg-champ"></div>
        </div>
        <div>
          <label>Prénom</label>
          <input type="text" name="prenom" id="edit-prenom" />
          <div id="msgEditPrenom" class="msg-champ"></div>
        </div>
        <div>
          <label><span data-i18n="label_email_d">Email</span></label>
          <input type="email" name="email" id="edit-email" />
          <div id="msgEditEmail" class="msg-champ"></div>
        </div>
        <div>
          <label><span data-i18n="label_role_d">Rôle</span></label>
          <select name="role" id="edit-role">
            <option value="admin">Admin</option>
            <option value="nutritionniste">Nutritionniste</option>
            <option value="client">Client</option>
          </select>
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="cancel-btn" onclick="fermerModal()">Annuler</button>
        <button type="submit" class="primary-btn" onclick="return validerModifier()">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════ SCRIPT CHARTS + PDF + LOGIQUE ══════════ -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
/* ── DONNÉES PHP → JS ── */
const inscriptionsLabels = <?php
  // Labels des 12 derniers mois (ex: Jan, Fév, Mar...)
  $moisFr = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Aoû','Sep','Oct','Nov','Déc'];
  $labels = [];
  foreach (array_keys($inscriptionsParMois) as $key) {
    $m = (int)substr($key, 5, 2) - 1;
    $labels[] = $moisFr[$m] . ' ' . substr($key, 2, 2);
  }
  echo json_encode($labels);
?>;
const inscriptionsData = <?php echo json_encode(array_values($inscriptionsParMois)); ?>;

const rolesLabels = ['Admin', 'Nutritionniste', 'Client'];
const rolesData   = [<?php echo (int)$parRole['admin']; ?>, <?php echo (int)$parRole['nutritionniste']; ?>, <?php echo (int)$parRole['client']; ?>];

const statsHistoSucces = <?php echo (int)$stats['succes']; ?>;
const statsHistoEchec  = <?php echo (int)$stats['echec']; ?>;

/* ── GRAPHIQUE INSCRIPTIONS (12 mois) ── */
(function() {
  const ctx = document.getElementById('chartInscriptions');
  if (!ctx) return;

  // Couleur des barres : vert intense pour le mois le plus haut, plus clair sinon
  const maxVal = Math.max(...inscriptionsData, 1);
  const bgColors = inscriptionsData.map(function(v) {
    const alpha = 0.35 + 0.65 * (v / maxVal);
    return 'rgba(23,153,95,' + alpha.toFixed(2) + ')';
  });

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: inscriptionsLabels,
      datasets: [{
        label: 'Inscriptions',
        data: inscriptionsData,
        backgroundColor: bgColors,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: function(ctx) {
              return ' ' + ctx.parsed.y + ' inscription' + (ctx.parsed.y > 1 ? 's' : '');
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, precision: 0 },
          grid: { color: 'rgba(0,0,0,.05)' }
        },
        x: {
          grid: { display: false },
          ticks: { font: { size: 11 }, maxRotation: 45 }
        }
      }
    }
  });
})();

/* ── GRAPHIQUE RÔLES ── */
(function() {
  const ctx = document.getElementById('chartRoles');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: rolesLabels,
      datasets: [{
        data: rolesData,
        backgroundColor: ['#f59e0b', '#7c3aed', '#17995f'],
        borderWidth: 2, borderColor: '#fff',
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 14, font: { size: 12, weight: '700' } } }
      },
      cutout: '62%'
    }
  });
})();

/* ── GRAPHIQUE HISTO GLOBAL ── */
(function() {
  const ctx = document.getElementById('chartHistoGlobal');
  if (!ctx) return;
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['Succès', 'Échecs'],
      datasets: [{
        data: [statsHistoSucces, statsHistoEchec],
        backgroundColor: ['#16a34a', '#dc2626'],
        borderWidth: 2, borderColor: '#fff',
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom', labels: { padding: 14, font: { size: 12, weight: '700' } } }
      },
      cutout: '60%'
    }
  });
})();

/* ── GRAPHIQUE ACTIVITÉ PAR USER ── */
(function() {
  const ctx = document.getElementById('chartActiviteUsers');
  if (!ctx) return;
  // Compter depuis le tableau HTML
  const rows = document.querySelectorAll('#tableHistorique tbody tr');
  const userCounts = {};
  rows.forEach(function(row) {
    const cells = row.querySelectorAll('td');
    if (cells.length < 2) return;
    const name = cells[1].textContent.trim();
    if (name && name !== 'Inconnu') {
      userCounts[name] = (userCounts[name] || 0) + 1;
    }
  });
  const sorted = Object.entries(userCounts).sort((a,b) => b[1]-a[1]).slice(0, 5);
  const labels = sorted.map(e => e[0]);
  const data   = sorted.map(e => e[1]);
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels.length ? labels : ['—'],
      datasets: [{
        label: 'Connexions',
        data: data.length ? data : [0],
        backgroundColor: 'rgba(124,58,237,.7)',
        borderRadius: 8, borderSkipped: false,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false, indexAxis: 'y',
      plugins: { legend: { display: false } },
      scales: {
        x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,.05)' } },
        y: { grid: { display: false } }
      }
    }
  });
})();

/* ── TRI ── */
function setTri(val) {
  document.getElementById('triHidden').value = val;
  document.getElementById('formRechUser').submit();
}

/* ── ONGLETS ── */
function ouvrirOnglet(nom) {
  document.querySelectorAll('.tab-content').forEach(function(t) { t.classList.remove('active'); });
  document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
  document.getElementById('tab-' + nom).classList.add('active');
  document.querySelectorAll('.tab-btn')[nom === 'utilisateurs' ? 0 : 1].classList.add('active');
}

/* ── MODAL MODIFIER ── */
function ouvrirModifier(id, nom, prenom, email, role) {
  document.getElementById('edit-id').value     = id;
  document.getElementById('edit-nom').value    = nom;
  document.getElementById('edit-prenom').value = prenom;
  document.getElementById('edit-email').value  = email;
  document.getElementById('edit-role').value   = role;
  ['msgEditNom','msgEditPrenom','msgEditEmail'].forEach(function(id) {
    document.getElementById(id).textContent = '';
    document.getElementById(id).className = 'msg-champ';
  });
  document.getElementById('modalModifier').classList.add('active');
}
function fermerModal() {
  document.getElementById('modalModifier').classList.remove('active');
}
document.getElementById('modalModifier').addEventListener('click', function(e) {
  if (e.target === this) fermerModal();
});
function validerModifier() {
  var RE_NAME  = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
  var RE_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var ok = true;
  function check(id, val, test, okMsg, errMsg) {
    var el = document.getElementById(id);
    if (test(val)) { el.textContent = '✓ ' + okMsg; el.className = 'msg-champ succes'; }
    else           { el.textContent = '✗ ' + errMsg; el.className = 'msg-champ erreur'; ok = false; }
  }
  check('msgEditNom',    document.getElementById('edit-nom').value,    function(v){ return RE_NAME.test(v); },  'Correct', 'Min 2 lettres');
  check('msgEditPrenom', document.getElementById('edit-prenom').value, function(v){ return RE_NAME.test(v); },  'Correct', 'Min 2 lettres');
  check('msgEditEmail',  document.getElementById('edit-email').value,  function(v){ return RE_EMAIL.test(v); }, 'Correct', 'Email invalide');
  return ok;
}
document.getElementById('formModifier').addEventListener('submit', function(e) {
  if (!validerModifier()) e.preventDefault();
});

/* ── EXPORT PDF UTILISATEURS ── */
function exportPDFUtilisateurs() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(18);
  doc.setTextColor(15, 109, 66);
  doc.text('NutriSmart — Liste des Utilisateurs', 14, 18);
  doc.setFontSize(10);
  doc.setTextColor(100);
  doc.setFont('helvetica', 'normal');
  doc.text('Exporté le : ' + new Date().toLocaleString('fr-FR'), 14, 26);

  const rows = [];
  document.querySelectorAll('#tableUtilisateurs tbody tr').forEach(function(tr) {
    const tds = tr.querySelectorAll('td');
    if (tds.length >= 4) {
      rows.push([
        tds[0].textContent.trim(),
        tds[1].textContent.trim(),
        tds[2].textContent.trim(),
        tds[3].textContent.trim().replace(/[✅👩‍⚕️🧑‍💼⚙️]/g, '').trim()
      ]);
    }
  });

  doc.autoTable({
    head: [['#', 'Nom complet', 'Email', 'Rôle']],
    body: rows,
    startY: 32,
    styles: { fontSize: 11, cellPadding: 6 },
    headStyles: { fillColor: [15, 109, 66], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [240, 253, 247] },
    tableLineColor: [220, 240, 228], tableLineWidth: 0.3,
  });

  doc.save('nutrismart_utilisateurs.pdf');
}

/* ── EXPORT PDF HISTORIQUE ── */
function exportPDFHistorique() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF({ orientation: 'landscape' });
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(18);
  doc.setTextColor(15, 109, 66);
  doc.text('NutriSmart — Historique des Connexions', 14, 18);
  doc.setFontSize(10);
  doc.setTextColor(100);
  doc.setFont('helvetica', 'normal');
  doc.text('Exporté le : ' + new Date().toLocaleString('fr-FR'), 14, 26);

  const rows = [];
  document.querySelectorAll('#tableHistorique tbody tr').forEach(function(tr) {
    const tds = tr.querySelectorAll('td');
    if (tds.length >= 6) {
      rows.push([
        tds[0].textContent.trim(),
        tds[1].textContent.trim(),
        tds[2].textContent.trim(),
        tds[3].textContent.trim().replace(/[✅❌]/g, '').trim(),
        tds[4].textContent.trim(),
        tds[5].textContent.trim()
      ]);
    }
  });

  doc.autoTable({
    head: [['#', 'Utilisateur', 'Action', 'Statut', 'Email tenté', 'Date']],
    body: rows,
    startY: 32,
    styles: { fontSize: 10, cellPadding: 5 },
    headStyles: { fillColor: [15, 109, 66], textColor: 255, fontStyle: 'bold' },
    alternateRowStyles: { fillColor: [240, 253, 247] },
    tableLineColor: [220, 240, 228], tableLineWidth: 0.3,
  });

  doc.save('nutrismart_historique.pdf');
}
</script>
<script>
// Inject lang switcher in dashboard sidebar
document.addEventListener('DOMContentLoaded', function() {
  var container = document.getElementById('dashLangSwitcherContainer');
  if (container) {
    container.innerHTML = renderLangSwitcher();
    // Override style for dark sidebar
    var trigger = container.querySelector('.lang-trigger');
    if (trigger) {
      trigger.style.background = 'rgba(255,255,255,.1)';
      trigger.style.borderColor = 'rgba(255,255,255,.2)';
      trigger.style.color = '#d1fae5';
    }
  }
});
</script>
</body>
</html>
