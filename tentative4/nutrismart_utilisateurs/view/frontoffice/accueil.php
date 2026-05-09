<?php
// ── Page d'accueil du front office ──
// Affiche une présentation de l'application et des liens vers les différentes sections
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Bienvenue</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap">
  <link rel="stylesheet" href="../view/frontoffice/style.css" />
  <style>
    /* ── Page d'accueil : styles propres à cette page ── */

    .accueil-body {
      min-height: 100vh;
      background:
        radial-gradient(ellipse at 20% 20%, rgba(16, 185, 129, 0.18) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 80%, rgba(5, 150, 105, 0.12) 0%, transparent 50%),
        linear-gradient(160deg, #f0fdf8 0%, #ecfdf5 50%, #f7fffe 100%);
      overflow-x: hidden;
    }

    .accueil-hero {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 80px 24px 60px;
      position: relative;
      overflow: hidden;
    }

    .accueil-hero::before {
      content: '';
      position: absolute;
      width: 600px; height: 600px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(16,185,129,0.10), transparent 70%);
      top: -150px; left: -100px;
      animation: floatOrb 10s ease-in-out infinite;
    }
    .accueil-hero::after {
      content: '';
      position: absolute;
      width: 400px; height: 400px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(5,150,105,0.08), transparent 70%);
      bottom: -100px; right: -80px;
      animation: floatOrb 14s ease-in-out infinite reverse;
    }

    @keyframes floatOrb {
      0%, 100% { transform: translate(0, 0) scale(1); }
      50%       { transform: translate(20px, -30px) scale(1.05); }
    }

    .accueil-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: rgba(16, 185, 129, 0.12);
      border: 1px solid rgba(16, 185, 129, 0.3);
      color: #065f46;
      padding: 8px 18px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.14em;
      text-transform: uppercase;
      margin-bottom: 28px;
      animation: badgePop 0.6s cubic-bezier(0.23,1,0.32,1) 0.2s both;
    }

    @keyframes badgePop {
      from { opacity: 0; transform: scale(0.8) translateY(10px); }
      to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .accueil-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(2.8rem, 7vw, 5.5rem);
      font-weight: 900;
      line-height: 1.05;
      color: #052e16;
      margin: 0 0 24px;
      animation: titleReveal 0.8s cubic-bezier(0.23,1,0.32,1) 0.35s both;
    }

    @keyframes titleReveal {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .accueil-title .accent {
      background: linear-gradient(135deg, #10b981, #059669);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .accueil-subtitle {
      font-size: clamp(1rem, 2vw, 1.2rem);
      color: #374151;
      max-width: 700px;
      line-height: 1.8;
      margin: 0 0 36px;
      animation: titleReveal 0.8s cubic-bezier(0.23,1,0.32,1) 0.5s both;
    }

    .accueil-cta-row {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      justify-content: center;
      animation: titleReveal 0.8s cubic-bezier(0.23,1,0.32,1) 0.65s both;
    }

    .btn-primary-accueil {
      padding: 16px 32px;
      background: linear-gradient(135deg, #10b981, #059669);
      color: white;
      border-radius: 999px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      box-shadow: 0 20px 40px rgba(16,185,129,.3);
      transition: transform .25s ease, box-shadow .25s ease;
    }

    .btn-primary-accueil:hover {
      transform: translateY(-4px);
      box-shadow: 0 28px 50px rgba(16,185,129,.4);
    }

    .btn-secondary-accueil {
      padding: 16px 32px;
      background: rgba(16,185,129,.1);
      border: 1.5px solid rgba(16,185,129,.3);
      color: #065f46;
      border-radius: 999px;
      font-weight: 700;
      font-size: 15px;
      text-decoration: none;
      transition: background .25s ease, transform .25s ease;
    }

    .btn-secondary-accueil:hover {
      background: rgba(16,185,129,.16);
      transform: translateY(-4px);
    }

    .scroll-hint {
      margin-top: 48px;
      font-size: 12px;
      letter-spacing: .14em;
      text-transform: uppercase;
      color: rgba(5,46,22,.4);
      animation: titleReveal 1s cubic-bezier(0.23,1,0.32,1) 0.9s both;
    }

    /* STATS */
    .accueil-stats {
      background: linear-gradient(135deg, #064e3b, #065f46);
      padding: 40px 24px;
    }

    .stats-grid {
      max-width: 1100px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 24px;
      text-align: center;
    }

    .stat-item {}

    .stat-number {
      display: block;
      font-family: 'Outfit', sans-serif;
      font-size: 2rem;
      font-weight: 900;
      color: #d1fae5;
      margin-bottom: 6px;
    }

    .stat-label {
      color: rgba(255,255,255,.7);
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: .1em;
    }

    /* SECTIONS INFO */
    .accueil-info {
      max-width: 1200px;
      margin: 0 auto;
      padding: 80px 24px;
    }

    .accueil-info-title {
      font-family: 'Outfit', sans-serif;
      font-size: clamp(1.8rem, 4vw, 2.8rem);
      font-weight: 900;
      color: #052e16;
      margin: 0 0 48px;
      text-align: center;
    }

    .info-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
    }

    .info-feature-card {
      background: white;
      border: 1px solid rgba(16,185,129,.14);
      border-radius: 24px;
      padding: 32px 28px;
      box-shadow: 0 10px 40px rgba(5,46,22,.06);
      transition: transform .3s ease, box-shadow .3s ease;
    }

    .info-feature-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 24px 60px rgba(5,46,22,.12);
    }

    .feature-icon {
      display: block;
      font-size: 2.2rem;
      margin-bottom: 16px;
    }

    .info-feature-card h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #052e16;
      margin: 0 0 12px;
    }

    .info-feature-card h3 a {
      color: #059669;
      text-decoration: none;
    }
    .info-feature-card h3 a:hover { text-decoration: underline; }

    .info-feature-card p {
      color: #6b7280;
      line-height: 1.7;
      font-size: 14px;
      margin: 0;
    }

    /* FOOTER */
    .accueil-footer {
      background: #052e16;
      color: rgba(255,255,255,.7);
      text-align: center;
      padding: 30px 24px;
      font-size: 14px;
    }

    .accueil-footer strong { color: #d1fae5; }

    /* NAVBAR */
    .navbar {
      position: fixed;
      top: 0; left: 0; right: 0;
      z-index: 100;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 16px 40px;
      background: transparent;
      transition: background .3s ease, box-shadow .3s ease;
    }

    .navbar.scrolled {
      background: rgba(255,255,255,.92);
      backdrop-filter: blur(20px);
      box-shadow: 0 4px 24px rgba(5,46,22,.1);
    }

    .nav-brand { display: flex; flex-direction: column; }
    .logo { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 900; color: #052e16; }
    .slogan { font-size: 10px; color: #059669; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }

    .nav-links { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

    .nav-links a {
      padding: 8px 14px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 600;
      color: #052e16;
      text-decoration: none;
      transition: background .2s, color .2s, transform .2s;
    }

    .nav-links a:hover, .nav-links a.active {
      background: rgba(16,185,129,.12);
      color: #065f46;
    }

    .nav-dashboard {
      background: linear-gradient(135deg, #10b981, #059669) !important;
      color: white !important;
      box-shadow: 0 8px 20px rgba(16,185,129,.25);
    }
  </style>
</head>
<body class="accueil-body">

  <!-- NAVBAR -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="PageController.php?page=accueil">Accueil</a>
      <a href="PageController.php?page=inscription">Utilisateurs</a>
      <a href="PageController.php?page=dashboard" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- HERO -->
  <section class="accueil-hero">
    <div class="accueil-badge">👤 Gestion des utilisateurs — NutriSmart</div>

    <h1 class="accueil-title">
      Gérez vos<br>
      utilisateurs <span class="accent">NutriSmart</span>
    </h1>

    <p class="accueil-subtitle">
      NutriSmart propose une gestion complète des utilisateurs : clients, nutritionnistes et admins.
      Créez, modifiez et supprimez des comptes avec contrôle de saisie et connexion sécurisée via PDO.
    </p>

    <div class="accueil-cta-row">
      <a href="PageController.php?page=inscription" class="btn-primary-accueil">Voir les utilisateurs</a>
      <a href="PageController.php?page=dashboard" class="btn-secondary-accueil">Accéder au backoffice</a>
    </div>

    <div class="scroll-hint">Défiler</div>
  </section>

  <!-- STATS -->
  <section class="accueil-stats">
    <div class="stats-grid">
      <div class="stat-item">
        <span class="stat-number">3</span>
        <span class="stat-label">Rôles disponibles</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">PDO</span>
        <span class="stat-label">Connexion sécurisée</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">MVC</span>
        <span class="stat-label">Architecture</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">CRUD</span>
        <span class="stat-label">Complet</span>
      </div>
      <div class="stat-item">
        <span class="stat-number">JS</span>
        <span class="stat-label">Validation personnalisée</span>
      </div>
    </div>
  </section>

  <!-- ACCÈS AUX PAGES -->
  <section class="accueil-info" id="pages">
    <h2 class="accueil-info-title">Accéder aux pages</h2>
    <div class="info-cards-grid">
      <div class="info-feature-card">
        <span class="feature-icon">👥</span>
        <h3><a href="PageController.php?page=inscription">Liste des utilisateurs</a></h3>
        <p>Consultez tous les utilisateurs inscrits sur NutriSmart avec leurs rôles et providers de connexion.</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">➕</span>
        <h3><a href="PageController.php?page=inscription#registerSection">S'inscrire</a></h3>
        <p>Créez un nouveau compte utilisateur avec validation complète des données saisies.</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">⚙️</span>
        <h3><a href="PageController.php?page=dashboard">Administration</a></h3>
        <p>Accédez au backoffice pour gérer, modifier et supprimer des utilisateurs via le dashboard.</p>
      </div>
    </div>
  </section>

  <!-- INFO -->
  <section class="accueil-info" id="info">
    <h2 class="accueil-info-title">Fonctionnalités de la gestion utilisateur</h2>
    <div class="info-cards-grid">
      <div class="info-feature-card">
        <span class="feature-icon">🔐</span>
        <h3>Mot de passe sécurisé</h3>
        <p>Les mots de passe sont hashés avec bcrypt (password_hash). Aucun mot de passe n'est stocké en clair.</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">✅</span>
        <h3>Contrôle de saisie JS</h3>
        <p>Validation complète côté client (JavaScript pur, sans HTML5) et côté serveur (PHP) pour une sécurité maximale.</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">🗄️</span>
        <h3>Connexion PDO</h3>
        <p>Toutes les requêtes utilisent PDO avec requêtes préparées pour éviter les injections SQL.</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">🏗️</span>
        <h3>Architecture MVC</h3>
        <p>Séparation claire entre Modèle (Utilisateur.php), Vue (pages PHP) et Contrôleur (UtilisateurController.php).</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">👤</span>
        <h3>3 rôles d'accès</h3>
        <p>Admin, Nutritionniste et Client — chaque rôle peut être attribué et modifié depuis le backoffice.</p>
      </div>
      <div class="info-feature-card">
        <span class="feature-icon">🔗</span>
        <h3>Multi-provider</h3>
        <p>Connexion via compte local, Google ou Facebook — géré dans le champ provider_login.</p>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="accueil-footer">
    <p>© 2026 <strong>NutriSmart</strong> — Eat Smart Live Smart —</p>
  </footer>

  <script>
    (function () {
      var navbar = document.getElementById('navbar');
      window.addEventListener('scroll', function () {
        navbar.classList.toggle('scrolled', window.scrollY > 50);
      });
      var currentPage = window.location.pathname.split('/').pop() || 'accueil.php';
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        if (link.getAttribute('href') === currentPage) link.classList.add('active');
      });
      document.querySelectorAll('.nav-links a').forEach(function (link) {
        link.addEventListener('click', function () {
          this.style.transform = 'scale(0.93)';
          var self = this;
          setTimeout(function () { self.style.transform = ''; }, 150);
        });
      });
    })();
  </script>
</body>
</html>
