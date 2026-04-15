<?php
/**
 * NutriSmart — Front Office : Inscription uniquement
 * (Liste des utilisateurs supprimée)
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Inscription</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="../view/frontoffice/style.css" />
</head>
<body>

  <!-- NAVBAR FIXE -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="PageController.php?page=accueil">Accueil</a>
      <a href="PageController.php?page=inscription" class="active">Inscription</a>
      <a href="PageController.php?page=dashboard" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- EN-TÊTE -->
  <header class="page-header">
    <p class="badge">👤 Rejoignez NutriSmart</p>
    <h1>Créer un compte</h1>
    <p class="subtitle">Rejoignez la communauté NutriSmart et commencez à manger plus intelligent.</p>
  </header>

  <main class="container">

    <!-- SECTION : FORMULAIRE D'INSCRIPTION UNIQUEMENT -->
    <section class="section" id="registerSection">
      <div class="section-header">
        <h2>Créer mon compte</h2>
        <p class="section-sub">Remplissez le formulaire ci-dessous</p>
      </div>

      <div id="registerMsg" class="msg-box hidden"></div>

      <form id="registerForm" class="register-form" novalidate>
        <div class="form-row">
          <div class="form-group">
            <label for="r-nom">Nom <span class="star">*</span></label>
            <input id="r-nom" type="text" placeholder="Ex : Ben Ali" autocomplete="off" />
            <span class="ferr" id="r-err-nom"></span>
          </div>
          <div class="form-group">
            <label for="r-prenom">Prénom <span class="star">*</span></label>
            <input id="r-prenom" type="text" placeholder="Ex : Mohamed" autocomplete="off" />
            <span class="ferr" id="r-err-prenom"></span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="r-email">Email <span class="star">*</span></label>
            <input id="r-email" type="text" placeholder="exemple@mail.com" autocomplete="off" />
            <span class="ferr" id="r-err-email"></span>
          </div>
          <div class="form-group">
            <label for="r-mot_de_passe">Mot de passe <span class="star">*</span></label>
            <div class="pwd-wrap">
              <input id="r-mot_de_passe" type="password" placeholder="Min. 8 car., 1 maj., 1 chiffre" />
              <button type="button" class="eye-btn" onclick="togglePwd('r-mot_de_passe', this)">👁</button>
            </div>
            <span class="ferr" id="r-err-mot_de_passe"></span>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="r-role">Rôle <span class="star">*</span></label>
            <select id="r-role">
              <option value="">-- Choisir un rôle --</option>
              <option value="client">Client</option>
              <option value="nutritionniste">Nutritionniste</option>
              <option value="admin">Admin</option>
            </select>
            <span class="ferr" id="r-err-role"></span>
          </div>
          <div class="form-group">
            <label for="r-provider_login">Connexion via <span class="star">*</span></label>
            <select id="r-provider_login">
              <option value="">-- Choisir un provider --</option>
              <option value="local">Local</option>
              <option value="google">Google</option>
              <option value="facebook">Facebook</option>
            </select>
            <span class="ferr" id="r-err-provider_login"></span>
          </div>
        </div>

        <button type="submit" class="submit-btn">✅ Créer mon compte</button>
      </form>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="page-footer">
    <p>© 2026 <strong>NutriSmart</strong> — Eat Smart Live Smart</p>
  </footer>

  <script src="../view/frontoffice/script.js"></script>
</body>
</html>
