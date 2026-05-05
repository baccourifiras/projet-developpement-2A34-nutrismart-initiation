<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Espace Nutritionniste</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/style.css" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Navbar override pour pages internes */
    .navbar {
      background: rgba(255,255,255,.92) !important;
      backdrop-filter: blur(20px);
      box-shadow: 0 2px 20px rgba(15,45,30,.08);
    }

    /* Bouton déconnexion */
    .btn-deconnect {
      padding: 8px 18px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white !important;
      border: none;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
      cursor: pointer;
      font-family: inherit;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 6px 16px rgba(239,68,68,.2);
    }
    .btn-deconnect:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(239,68,68,.3); }

    /* Bonjour chip */
    .greet-chip {
      display: flex; align-items: center; gap: 6px;
      background: rgba(31,164,99,.1);
      border: 1px solid rgba(31,164,99,.2);
      border-radius: 999px;
      padding: 7px 14px;
      font-size: 13px;
      font-weight: 600;
      color: var(--primary-dark);
    }

    /* Hero dégradé pour espace nutritionniste */
    .page-header {
      padding: 100px 40px 48px;
      background: linear-gradient(135deg, #f0fdf7 0%, #e8faf0 100%);
      border-bottom: 1px solid rgba(31,164,99,.12);
      text-align: center;
    }

    .page-header .badge {
      background: rgba(139,92,246,.1);
      border: 1px solid rgba(139,92,246,.2);
      color: #5b21b6;
    }

    /* Grille des outils */
    .tools-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      padding: 12px 0;
    }

    .tool-card {
      background: linear-gradient(180deg, #fff 0%, #f8fefb 100%);
      border: 1.5px solid rgba(31,164,99,.12);
      border-radius: 20px;
      padding: 28px 24px;
      text-align: center;
      transition: transform .3s, box-shadow .3s, border-color .3s;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .tool-card::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(31,164,99,.06), transparent 60%);
      opacity: 0;
      transition: opacity .3s;
    }

    .tool-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 24px 56px rgba(15,45,30,.12);
      border-color: rgba(31,164,99,.3);
    }

    .tool-card:hover::before { opacity: 1; }

    .tool-icon {
      font-size: 2.8rem;
      margin-bottom: 14px;
      display: block;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,.1));
    }

    .tool-card h3 {
      font-size: 16px;
      font-weight: 800;
      color: var(--text);
      margin: 0 0 8px;
    }

    .tool-card p {
      color: var(--muted);
      font-size: 13px;
      line-height: 1.6;
      margin: 0;
    }

    .tool-card .tool-badge {
      display: inline-block;
      margin-top: 14px;
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      background: rgba(31,164,99,.1);
      color: var(--primary-dark);
    }

    /* Stats rapides */
    .quick-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 16px;
      margin-bottom: 28px;
    }

    .qs-card {
      background: white;
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .qs-icon {
      width: 44px; height: 44px; flex-shrink: 0;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px;
    }

    .qs-icon.green  { background: rgba(31,164,99,.12); }
    .qs-icon.purple { background: rgba(139,92,246,.12); }
    .qs-icon.amber  { background: rgba(245,158,11,.12); }

    .qs-val  { font-size: 22px; font-weight: 900; color: var(--text); line-height: 1; }
    .qs-lbl  { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-top: 2px; }
  </style>
  <script src="public/js/lang.js"></script>
</head>
<body data-page-title="page_title_nutri">

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">🥗 NutriSmart</div>
      <div class="slogan">Eat Smart · Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="index.php?page=accueil" data-i18n="nav_home">Accueil</a>
      <div class="greet-chip"><span data-i18n="greet_nutri">👩‍⚕️ Bonjour</span> <?php echo htmlspecialchars($userPrenom); ?></div>
      <form action="index.php" method="POST" style="margin:0;">
        <input type="hidden" name="action" value="deconnexion" />
        <button type="submit" class="btn-deconnect" data-i18n="nav_logout">Se déconnecter</button>
      </form>
    </div>
  </nav>

  <!-- HEADER -->
  <header class="page-header">
    <p class="badge" data-i18n="nutri_badge">👩‍⚕️ Espace Nutritionniste</p>
    <h1><span data-i18n="nutri_welcome">Bienvenue,</span> <?php echo htmlspecialchars($userPrenom); ?><span data-i18n="nutri_welcome_suffix"> ! 🌿</span></h1>
    <p class="subtitle" data-i18n="nutri_subtitle">Gérez vos consultations, suivez vos clients et construisez des plans nutritionnels personnalisés.</p>
  </header>

  <main class="container" style="margin-top: 32px;">

    <!-- Stats rapides -->
    <div class="quick-stats">
      <div class="qs-card">
        <div class="qs-icon green">👥</div>
        <div>
          <div class="qs-val">—</div>
          <div class="qs-lbl" data-i18n="qs_clients">Clients actifs</div>
        </div>
      </div>
      <div class="qs-card">
        <div class="qs-icon purple">📋</div>
        <div>
          <div class="qs-val">—</div>
          <div class="qs-lbl" data-i18n="qs_plans_created">Plans créés</div>
        </div>
      </div>
      <div class="qs-card">
        <div class="qs-icon amber">📅</div>
        <div>
          <div class="qs-val">—</div>
          <div class="qs-lbl" data-i18n="qs_upcoming">RDV à venir</div>
        </div>
      </div>
    </div>

    <!-- Outils -->
    <section class="section">
      <div class="section-header">
        <h2 data-i18n="pro_space">Mon espace professionnel</h2>
        <p class="section-sub" data-i18n="pro_space_sub">Vos outils de gestion au quotidien</p>
      </div>
      <div class="tools-grid">
        <div class="tool-card">
          <span class="tool-icon">👥</span>
          <h3 data-i18n="pro_tool1_title">Mes Clients</h3>
          <p data-i18n="pro_tool1_desc">Consultez et gérez vos clients, accédez à leurs profils et historiques nutritionnels.</p>
          <span class="tool-badge" data-i18n="pro_tool1_badge">Gestion</span>
        </div>
        <div class="tool-card">
          <span class="tool-icon">📋</span>
          <h3 data-i18n="pro_tool2_title">Plans Nutritionnels</h3>
          <p data-i18n="pro_tool2_desc">Créez et personnalisez des plans alimentaires adaptés aux objectifs de chaque client.</p>
          <span class="tool-badge" data-i18n="pro_tool2_badge">Création</span>
        </div>
        <div class="tool-card">
          <span class="tool-icon">📅</span>
          <h3 data-i18n="pro_tool3_title">Rendez-vous</h3>
          <p data-i18n="pro_tool3_desc">Planifiez et gérez vos consultations. Suivez votre agenda professionnel en un coup d'œil.</p>
          <span class="tool-badge" data-i18n="pro_tool3_badge">Agenda</span>
        </div>
        <div class="tool-card">
          <span class="tool-icon">📊</span>
          <h3 data-i18n="pro_tool4_title">Statistiques</h3>
          <p data-i18n="pro_tool4_desc">Visualisez les progrès de vos clients et l'évolution de votre activité professionnelle.</p>
          <span class="tool-badge" data-i18n="pro_tool4_badge">Analyses</span>
        </div>
      </div>
    </section>

  </main>

  <footer class="page-footer">
    <p>&copy; 2026 <strong>NutriSmart</strong> — Eat Smart · Live Smart</p>
  </footer>

  <script>
    window.addEventListener('scroll', function () {
      document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 10);
    });
  </script>

  <?php include __DIR__ . '/../chatbot_bilan.php'; ?>
  <script>
    verifierBilanDuJour(<?php echo json_encode($userPrenom); ?>);
  </script>
</body>
</html>
