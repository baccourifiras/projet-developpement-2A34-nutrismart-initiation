<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Bienvenue</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/style.css" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* NAVBAR transparente sur accueil */
    .navbar {
      background: transparent !important;
      border-bottom: none !important;
      transition: background .3s, box-shadow .3s;
    }
    .navbar.scrolled {
      background: rgba(255,255,255,.92) !important;
      backdrop-filter: blur(20px);
      box-shadow: 0 4px 24px rgba(15,45,30,.1) !important;
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

    /* HERO */
    .hero {
      min-height: 100vh;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      text-align: center;
      padding: 100px 24px 72px;
      position: relative;
      overflow: hidden;
      background:
        radial-gradient(ellipse at 20% 25%, rgba(16,185,129,.18) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 75%, rgba(5,150,105,.12) 0%, transparent 50%),
        linear-gradient(160deg, #f0fdf8 0%, #ecfdf5 50%, #f7fffe 100%);
    }

    .hero-orb {
      position: absolute; border-radius: 50%; pointer-events: none;
    }
    .hero-orb-1 {
      width: 600px; height: 600px;
      background: radial-gradient(circle, rgba(16,185,129,.1), transparent 70%);
      top: -150px; left: -100px;
      animation: floatOrb 10s ease-in-out infinite;
    }
    .hero-orb-2 {
      width: 400px; height: 400px;
      background: radial-gradient(circle, rgba(5,150,105,.08), transparent 70%);
      bottom: -100px; right: -80px;
      animation: floatOrb 14s ease-in-out infinite reverse;
    }
    @keyframes floatOrb {
      0%, 100% { transform: translate(0,0) scale(1); }
      50%       { transform: translate(20px,-30px) scale(1.05); }
    }

    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3);
      color: #065f46; padding: 8px 20px; border-radius: 999px;
      font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
      margin-bottom: 28px; position: relative; z-index: 1;
      animation: popIn .7s cubic-bezier(.23,1,.32,1) .2s both;
    }

    @keyframes popIn {
      from { opacity: 0; transform: scale(.85) translateY(12px); }
      to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .hero-title {
      font-size: clamp(2.8rem, 7vw, 5.2rem);
      font-weight: 900; line-height: 1.05;
      color: #052e16; margin: 0 0 22px;
      position: relative; z-index: 1;
      animation: slideUp .8s cubic-bezier(.23,1,.32,1) .35s both;
    }
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .hero-title .accent {
      background: linear-gradient(135deg, #10b981, #059669);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .hero-sub {
      font-size: clamp(1rem, 2vw, 1.18rem);
      color: #374151; max-width: 680px;
      line-height: 1.85; margin: 0 0 40px;
      position: relative; z-index: 1;
      animation: slideUp .8s cubic-bezier(.23,1,.32,1) .5s both;
    }

    .hero-cta {
      display: flex; gap: 14px; flex-wrap: wrap;
      justify-content: center; position: relative; z-index: 1;
      animation: slideUp .8s cubic-bezier(.23,1,.32,1) .65s both;
    }

    .btn-hero-primary {
      padding: 16px 34px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: white; border-radius: 999px; font-weight: 800;
      font-size: 15px; text-decoration: none;
      box-shadow: 0 20px 40px rgba(16,185,129,.3);
      transition: transform .25s, box-shadow .25s;
    }
    .btn-hero-primary:hover { transform: translateY(-4px); box-shadow: 0 28px 50px rgba(16,185,129,.4); }

    .btn-hero-secondary {
      padding: 16px 34px;
      background: rgba(16,185,129,.1); border: 1.5px solid rgba(16,185,129,.3);
      color: #065f46; border-radius: 999px; font-weight: 700;
      font-size: 15px; text-decoration: none;
      transition: background .25s, transform .25s;
    }
    .btn-hero-secondary:hover { background: rgba(16,185,129,.18); transform: translateY(-4px); }

    .hero-scroll {
      margin-top: 56px; font-size: 11px; letter-spacing: .16em;
      text-transform: uppercase; color: rgba(5,46,22,.35);
      position: relative; z-index: 1;
      animation: slideUp 1s cubic-bezier(.23,1,.32,1) .9s both;
    }

    /* STATS BAR */
    .stats-bar {
      background: linear-gradient(135deg, #064e3b, #065f46);
      padding: 44px 24px;
    }
    .stats-inner {
      max-width: 1100px; margin: 0 auto;
      display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 24px; text-align: center;
    }
    .stat-num   { font-size: 2.2rem; font-weight: 900; color: #d1fae5; display: block; }
    .stat-label { font-size: 12px; color: rgba(255,255,255,.65); text-transform: uppercase; letter-spacing: .1em; margin-top: 4px; display: block; }

    /* FEATURES */
    .features-section {
      max-width: 1200px; margin: 0 auto; padding: 90px 24px;
    }
    .section-title {
      font-size: clamp(1.8rem, 4vw, 2.8rem); font-weight: 900;
      color: #052e16; margin: 0 0 52px; text-align: center;
    }
    .features-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }
    .feat-card {
      background: white; border: 1px solid rgba(16,185,129,.14);
      border-radius: 24px; padding: 34px 28px;
      box-shadow: 0 10px 40px rgba(5,46,22,.06);
      transition: transform .3s, box-shadow .3s;
    }
    .feat-card:hover { transform: translateY(-8px); box-shadow: 0 24px 60px rgba(5,46,22,.12); }
    .feat-icon { font-size: 2.2rem; margin-bottom: 16px; display: block; }
    .feat-card h3 { font-size: 1.1rem; font-weight: 800; color: #052e16; margin: 0 0 10px; }
    .feat-card p  { color: #6b7280; line-height: 1.75; font-size: 14px; margin: 0; }

    /* CTA BAS */
    .cta-section {
      background: linear-gradient(135deg, #0a3d22, #0f6c42);
      padding: 80px 24px; text-align: center;
    }
    .cta-section h2 { font-size: clamp(1.8rem, 4vw, 2.4rem); font-weight: 900; color: white; margin: 0 0 16px; }
    .cta-section p  { color: rgba(255,255,255,.72); font-size: 15px; max-width: 540px; margin: 0 auto 36px; line-height: 1.75; }
    .btn-cta {
      padding: 16px 36px; background: white;
      color: #0a3d22; border-radius: 999px; font-weight: 800;
      font-size: 15px; text-decoration: none;
      box-shadow: 0 16px 36px rgba(0,0,0,.15);
      transition: transform .25s, box-shadow .25s; display: inline-block;
    }
    .btn-cta:hover { transform: translateY(-4px); box-shadow: 0 24px 48px rgba(0,0,0,.22); }

    /* FOOTER */
    .main-footer {
      background: #052e16; color: rgba(255,255,255,.65);
      text-align: center; padding: 28px 24px; font-size: 13px;
    }
    .main-footer strong { color: #d1fae5; }
  </style>
  <script src="public/js/lang.js"></script>
</head>
<body data-page-title="page_title_accueil">

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">🥗 NutriSmart</div>
      <div class="slogan">Eat Smart · Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="index.php?page=accueil" class="active" data-i18n="nav_home">Accueil</a>

      <?php if (!empty($_SESSION['user_id'])): ?>
        <!-- Connecté -->
        <a href="nutrismart_evenement/View/FrontOffice/index.php" data-i18n="nav_events">Événements</a>

        <?php
          $espaceUrl = 'index.php?page=espace_client';
          if ($_SESSION['user_role'] === 'nutritionniste') $espaceUrl = 'index.php?page=espace_nutritionniste';
          elseif ($_SESSION['user_role'] === 'admin')      $espaceUrl = 'index.php?page=dashboard';
        ?>
        <a href="<?php echo $espaceUrl; ?>" class="nav-dashboard" data-i18n="nav_my_space">→ Mon espace</a>

        <?php if ($_SESSION['user_role'] === 'admin'): ?>
          <a href="index.php?page=dashboard" style="background:linear-gradient(135deg,#1d4ed8,#2563eb)!important;color:#fff!important;">⚙️ Dashboard</a>
        <?php endif; ?>

        <div class="greet-chip" style="display:flex;align-items:center;gap:6px;background:rgba(31,164,99,.1);border:1px solid rgba(31,164,99,.2);border-radius:999px;padding:7px 14px;font-size:13px;font-weight:600;color:var(--primary-dark);">
          👤 <?php echo htmlspecialchars($_SESSION['user_prenom']); ?>
        </div>

        <form action="index.php" method="POST" style="margin:0;">
          <input type="hidden" name="action" value="deconnexion" />
          <button type="submit" class="btn-deconnect" data-i18n="nav_logout">Se déconnecter</button>
        </form>

      <?php else: ?>
        <!-- Non connecté -->
        <a href="index.php?page=inscription" data-i18n="nav_signup">Inscription</a>
        <a href="index.php?page=login" data-i18n="nav_login">Connexion</a>
        <a href="index.php?page=login" class="nav-dashboard" data-i18n="nav_dashboard">→ Mon espace</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- HERO -->
  <section class="hero">
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>

    <div class="hero-badge" data-i18n="hero_badge">🌿 Plateforme Nutritionnelle #1</div>

    <h1 class="hero-title">
      <span data-i18n="hero_title_1">Mangez mieux,</span><br><span data-i18n="hero_title_2">vivez</span> <span class="accent">NutriSmart</span>
    </h1>

    <p class="hero-sub">
      <span data-i18n="hero_sub">NutriSmart connecte clients et nutritionnistes pour un suivi personnalisé, des plans alimentaires adaptés et une santé durable au quotidien.</span>
    </p>

    <div class="hero-cta">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="nutrismart_evenement/View/FrontOffice/index.php" class="btn-hero-primary">📅 Voir les événements</a>
        <?php
          $espaceHero = 'index.php?page=espace_client';
          if ($_SESSION['user_role'] === 'nutritionniste') $espaceHero = 'index.php?page=espace_nutritionniste';
          elseif ($_SESSION['user_role'] === 'admin')      $espaceHero = 'index.php?page=dashboard';
        ?>
        <a href="<?php echo $espaceHero; ?>" class="btn-hero-secondary" data-i18n="btn_my_space">→ Mon espace</a>
      <?php else: ?>
        <a href="index.php?page=inscription" class="btn-hero-primary" data-i18n="btn_signup_free">Créer mon compte gratuit</a>
        <a href="index.php?page=login" class="btn-hero-secondary" data-i18n="btn_login">Se connecter →</a>
      <?php endif; ?>
    </div>

    <div class="hero-scroll" data-i18n="hero_scroll">↓ Découvrir</div>
  </section>

  <!-- STATS -->
  <section class="stats-bar">
    <div class="stats-inner">
      <div>
        <span class="stat-num">500+</span>
        <span class="stat-label" data-i18n="stat_users">Utilisateurs actifs</span>
      </div>
      <div>
        <span class="stat-num">50+</span>
        <span class="stat-label" data-i18n="stat_nutritionists">Nutritionnistes</span>
      </div>
      <div>
        <span class="stat-num">98%</span>
        <span class="stat-label" data-i18n="stat_satisfaction">Satisfaction client</span>
      </div>
      <div>
        <span class="stat-num">MVC</span>
        <span class="stat-label" data-i18n="stat_arch">Architecture PHP</span>
      </div>
      <div>
        <span class="stat-num">PDO</span>
        <span class="stat-label" data-i18n="stat_db">Connexion sécurisée</span>
      </div>
    </div>
  </section>

  <!-- FEATURES -->
  <section class="features-section">
    <h2 class="section-title" data-i18n="features_title">Tout ce dont vous avez besoin</h2>
    <div class="features-grid">
      <div class="feat-card">
        <span class="feat-icon">🔐</span>
        <h3 data-i18n="feat1_title">Sécurité maximale</h3>
        <p data-i18n="feat1_desc">Mots de passe hashés avec bcrypt, requêtes PDO préparées contre les injections SQL, sessions sécurisées.</p>
      </div>
      <div class="feat-card">
        <span class="feat-icon">👩‍⚕️</span>
        <h3 data-i18n="feat2_title">Nutritionnistes certifiés</h3>
        <p data-i18n="feat2_desc">Accédez à un réseau de nutritionnistes qualifiés qui créent des plans alimentaires sur mesure pour vous.</p>
      </div>
      <div class="feat-card">
        <span class="feat-icon">📊</span>
        <h3 data-i18n="feat3_title">Suivi de progression</h3>
        <p data-i18n="feat3_desc">Visualisez vos progrès semaine après semaine, suivez vos objectifs et célébrez chaque étape atteinte.</p>
      </div>
      <div class="feat-card">
        <span class="feat-icon">📅</span>
        <h3 data-i18n="feat4_title">Gestion des rendez-vous</h3>
        <p data-i18n="feat4_desc">Planifiez facilement vos consultations avec votre nutritionniste directement depuis votre espace client.</p>
      </div>
      <div class="feat-card">
        <span class="feat-icon">🏗️</span>
        <h3 data-i18n="feat5_title">Architecture MVC</h3>
        <p data-i18n="feat5_desc">Séparation propre entre Modèle, Vue et Contrôleur pour un code maintenable et évolutif en PHP.</p>
      </div>
      <div class="feat-card">
        <span class="feat-icon">✅</span>
        <h3 data-i18n="feat6_title">Validation complète</h3>
        <p data-i18n="feat6_desc">Double validation côté client (JavaScript) et côté serveur (PHP) pour une saisie fiable et sécurisée.</p>
      </div>
    </div>
  </section>

  <!-- CTA FINAL -->
  <section class="cta-section">
    <h2 data-i18n="cta_title">Prêt à transformer votre alimentation ?</h2>
    <p data-i18n="cta_sub">Rejoignez des centaines d'utilisateurs qui ont déjà amélioré leur santé avec NutriSmart.</p>
    <a href="index.php?page=inscription" class="btn-cta" data-i18n="cta_btn">Commencer gratuitement →</a>
  </section>

  <!-- FOOTER -->
  <footer class="main-footer">
    <p>&copy; 2026 <strong>NutriSmart</strong> — Eat Smart · Live Smart</p>
  </footer>

  <script>
    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function () {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
  </script>
</body>
</html>
