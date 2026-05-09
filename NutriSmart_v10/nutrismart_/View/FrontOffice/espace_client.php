<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Espace Client</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/style.css" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    .navbar {
      background: rgba(255,255,255,.92) !important;
      backdrop-filter: blur(20px);
      box-shadow: 0 2px 20px rgba(15,45,30,.08);
    }

    .btn-deconnect {
      padding: 8px 18px;
      background: linear-gradient(135deg, #ef4444, #dc2626);
      color: white !important;
      border: none; border-radius: 999px;
      font-size: 13px; font-weight: 700;
      cursor: pointer; font-family: inherit;
      transition: transform .2s, box-shadow .2s;
      box-shadow: 0 6px 16px rgba(239,68,68,.2);
    }
    .btn-deconnect:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(239,68,68,.3); }

    .greet-chip {
      display: flex; align-items: center; gap: 6px;
      background: rgba(31,164,99,.1);
      border: 1px solid rgba(31,164,99,.2);
      border-radius: 999px; padding: 7px 14px;
      font-size: 13px; font-weight: 600; color: var(--primary-dark);
    }

    .page-header {
      padding: 100px 40px 48px;
      background: linear-gradient(135deg, #f0fdf7 0%, #e8faf0 100%);
      border-bottom: 1px solid rgba(31,164,99,.12);
      text-align: center;
    }

    /* Cards outils */
    .tools-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 20px;
      padding: 12px 0;
    }

    .tool-card {
      background: linear-gradient(180deg, #fff 0%, #f8fefb 100%);
      border: 1.5px solid rgba(31,164,99,.12);
      border-radius: 20px; padding: 28px 24px;
      text-align: center;
      transition: transform .3s, box-shadow .3s, border-color .3s;
      cursor: pointer; position: relative; overflow: hidden;
    }
    .tool-card::before {
      content: ''; position: absolute; inset: 0;
      background: linear-gradient(135deg, rgba(31,164,99,.06), transparent 60%);
      opacity: 0; transition: opacity .3s;
    }
    .tool-card:hover { transform: translateY(-8px); box-shadow: 0 24px 56px rgba(15,45,30,.12); border-color: rgba(31,164,99,.3); }
    .tool-card:hover::before { opacity: 1; }

    .tool-icon { font-size: 2.8rem; margin-bottom: 14px; display: block; filter: drop-shadow(0 4px 8px rgba(0,0,0,.1)); }
    .tool-card h3 { font-size: 16px; font-weight: 800; color: var(--text); margin: 0 0 8px; }
    .tool-card p  { color: var(--muted); font-size: 13px; line-height: 1.6; margin: 0; }
    .tool-card .tool-badge {
      display: inline-block; margin-top: 14px;
      padding: 4px 12px; border-radius: 999px;
      font-size: 11px; font-weight: 700;
      background: rgba(31,164,99,.1); color: var(--primary-dark);
    }

    /* Progress */
    .progress-bar { height: 8px; background: #e3ede8; border-radius: 999px; margin-top: 8px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 999px; background: linear-gradient(90deg, #1fa463, #0f6c42); transition: width 1s ease; }

    .obj-item { display: flex; flex-direction: column; gap: 4px; }
    .obj-label { font-size: 13px; font-weight: 600; color: var(--text); display: flex; justify-content: space-between; }
    .obj-val   { font-size: 12px; color: var(--muted); }

    /* Quick stats */
    .quick-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 28px; }
    .qs-card { background: white; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 14px; }
    .qs-icon { width: 44px; height: 44px; flex-shrink: 0; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .qs-icon.green  { background: rgba(31,164,99,.12); }
    .qs-icon.blue   { background: rgba(59,130,246,.12); }
    .qs-icon.amber  { background: rgba(245,158,11,.12); }
    .qs-val  { font-size: 22px; font-weight: 900; color: var(--text); line-height: 1; }
    .qs-lbl  { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-top: 2px; }
  </style>
  <script src="public/js/lang.js"></script>
</head>
<body data-page-title="page_title_client">

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">🥗 NutriSmart</div>
      <div class="slogan">Eat Smart · Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="index.php?page=accueil" data-i18n="nav_home">Accueil</a>
      <div class="greet-chip"><span data-i18n="greet_client">👤 Bonjour</span> <?php echo htmlspecialchars($userPrenom); ?></div>
      <form action="index.php" method="POST" style="margin:0;">
        <input type="hidden" name="action" value="deconnexion" />
        <button type="submit" class="btn-deconnect" data-i18n="nav_logout">Se déconnecter</button>
      </form>
    </div>
  </nav>

  <!-- HEADER -->
  <header class="page-header">
    <p class="badge" data-i18n="client_badge">🧑‍💼 Espace Client</p>
    <h1><span data-i18n="client_welcome">Bienvenue,</span> <?php echo htmlspecialchars($userPrenom); ?><span data-i18n="client_welcome_suffix"> ! 👋</span></h1>
    <p class="subtitle" data-i18n="client_subtitle">Suivez votre programme nutritionnel, visualisez vos progrès et gérez vos rendez-vous.</p>
  </header>

  <main class="container" style="margin-top: 32px;">

    <!-- Stats rapides -->
    <div class="quick-stats">
      <div class="qs-card">
        <div class="qs-icon green">🥗</div>
        <div>
          <div class="qs-val">—</div>
          <div class="qs-lbl" data-i18n="qs_plans">Plans actifs</div>
        </div>
      </div>
      <div class="qs-card">
        <div class="qs-icon blue">📈</div>
        <div>
          <div class="qs-val">—</div>
          <div class="qs-lbl" data-i18n="qs_days">Jours de suivi</div>
        </div>
      </div>
      <div class="qs-card">
        <div class="qs-icon amber">📅</div>
        <div>
          <div class="qs-val">—</div>
          <div class="qs-lbl" data-i18n="qs_rdv">Prochain RDV</div>
        </div>
      </div>
    </div>

    <!-- Outils -->
    <section class="section">
      <div class="section-header">
        <h2 data-i18n="my_space">Mon espace</h2>
        <p class="section-sub" data-i18n="my_space_sub">Toutes vos informations et votre programme en un seul endroit</p>
      </div>
      <div class="tools-grid">
        <div class="tool-card">
          <span class="tool-icon">🍎</span>
          <h3 data-i18n="tool1_title">Mon Plan Alimentaire</h3>
          <p data-i18n="tool1_desc">Consultez votre programme nutritionnel personnalisé, vos repas et vos objectifs caloriques.</p>
          <span class="tool-badge" data-i18n="tool1_badge">Programme</span>
        </div>
        <div class="tool-card">
          <span class="tool-icon">📈</span>
          <h3 data-i18n="tool2_title">Ma Progression</h3>
          <p data-i18n="tool2_desc">Suivez l'évolution de vos objectifs, vos mensurations et vos résultats semaine par semaine.</p>
          <span class="tool-badge" data-i18n="tool2_badge">Suivi</span>
        </div>
        <div class="tool-card">
          <span class="tool-icon">📅</span>
          <h3 data-i18n="tool3_title">Mes Rendez-vous</h3>
          <p data-i18n="tool3_desc">Gérez vos prochaines consultations avec votre nutritionniste et consultez l'historique.</p>
          <span class="tool-badge" data-i18n="tool3_badge">Agenda</span>
        </div>
        <div class="tool-card">
          <span class="tool-icon">💬</span>
          <h3 data-i18n="tool4_title">Messages</h3>
          <p data-i18n="tool4_desc">Échangez directement avec votre nutritionniste pour poser vos questions et recevoir des conseils.</p>
          <span class="tool-badge" data-i18n="tool4_badge">Communication</span>
        </div>
      </div>
    </section>

    <!-- Objectifs -->
    <section class="section">
      <div class="section-header">
        <h2 data-i18n="my_goals">Mes objectifs</h2>
        <p class="section-sub" data-i18n="my_goals_sub">Votre progression vers une meilleure santé</p>
      </div>
      <div style="display:flex; flex-direction:column; gap:18px;">
        <div class="obj-item">
          <span class="obj-label"><span data-i18n="goal_hydration">Hydratation</span> <span class="obj-val">— / 2.5L</span></span>
          <div class="progress-bar"><div class="progress-fill" style="width:0%"></div></div>
        </div>
        <div class="obj-item">
          <span class="obj-label"><span data-i18n="goal_calories">Calories journalières</span> <span class="obj-val">— / 2000 kcal</span></span>
          <div class="progress-bar"><div class="progress-fill" style="width:0%"></div></div>
        </div>
        <div class="obj-item">
          <span class="obj-label"><span data-i18n="goal_weight">Objectif poids</span> <span class="obj-val" data-i18n="goal_weight_status">En cours</span></span>
          <div class="progress-bar"><div class="progress-fill" style="width:0%"></div></div>
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
