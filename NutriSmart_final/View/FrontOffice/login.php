<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Connexion</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="public/css/style.css" />
  <style>
    .msg-champ { font-size: 13px; margin-top: 4px; }
    .msg-champ.erreur  { color: red; }
    .msg-champ.succes  { color: green; }
  </style>
</head>
<body>

  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="index.php?page=accueil">Accueil</a>
      <a href="index.php?page=inscription">Inscription</a>
      <a href="index.php?page=login" class="active">Connexion</a>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Espace membre</p>
    <h1>Se connecter</h1>
    <p class="subtitle">Accedez a votre espace NutriSmart.</p>
  </header>

  <main class="container">

    <!-- Messages PHP (Chapitre 3 - if/elseif) -->
    <?php if ($erreur === 'acces'): ?>
    <div class="msg-box erreur">Acces refuse. Veuillez vous connecter.</div>
    <?php elseif ($erreur === 'identifiants'): ?>
    <div class="msg-box erreur">Email ou mot de passe incorrect.</div>
    <?php elseif ($erreur === 'champs_vides'): ?>
    <div class="msg-box erreur">Veuillez remplir tous les champs.</div>
    <?php endif; ?>

    <section class="section" style="max-width:480px;margin:0 auto;">
      <div class="section-header">
        <h2>Connexion</h2>
        <p class="section-sub">Entrez vos identifiants ci-dessous</p>
      </div>

      <!-- Formulaire - action pointe vers index.php (MVC) -->
      <!-- Partie 1 : onClick appelle validerFormulaire() (Cours JS Partie 1) -->
      <form id="formLogin" action="index.php" method="POST" class="register-form">
        <input type="hidden" name="action" value="login" />

        <div class="form-group">
          <label for="email">Email</label>
          <input id="email" type="text" name="email" placeholder="exemple@mail.com" />
          <div id="msgEmail" class="msg-champ"></div>
        </div>

        <div class="form-group">
          <label for="mot_de_passe">Mot de passe</label>
          <input id="mot_de_passe" type="password" name="mot_de_passe" placeholder="Votre mot de passe" />
          <div id="msgMdp" class="msg-champ"></div>
        </div>

        <!-- Partie 1 : onClick sur le bouton (Cours JS Partie 1) -->
        <button type="submit" class="submit-btn" onclick="return validerFormulaire()">Se connecter</button>
      </form>

      <p style="margin-top:1.2rem;text-align:center;font-size:.9rem;">
        Pas encore de compte ?
        <a href="index.php?page=inscription">Creer un compte</a>
      </p>
    </section>

  </main>

  <footer class="page-footer">
    <p>&copy; 2026 NutriSmart</p>
  </footer>

  <script>
    // ============================================================
    // Partie 1 : Controle de saisie avec l'evenement onClick
    // ============================================================
    function validerFormulaire() {
      var email        = document.getElementById('email').value;
      var mot_de_passe = document.getElementById('mot_de_passe').value;

      // Chapitre 3 - operateurs et conditions
      var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!regexEmail.test(email)) {
        alert("Veuillez saisir un email valide (exemple@mail.com).");
        return false;
      }
      if (mot_de_passe.length < 4) {
        alert("Le mot de passe doit contenir au moins 4 caracteres.");
        return false;
      }
      return true;
    }

    // ============================================================
    // Partie 2 : Controle de saisie avec addEventListener('submit')
    // ============================================================
    document.getElementById('formLogin').addEventListener('submit', function(e) {
      var estValide = true;

      var email        = document.getElementById('email').value;
      var mot_de_passe = document.getElementById('mot_de_passe').value;
      var regexEmail   = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      // --- Email ---
      var msgEmail = document.getElementById('msgEmail');
      if (!regexEmail.test(email)) {
        msgEmail.textContent = "Veuillez saisir un email valide (exemple@mail.com).";
        msgEmail.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgEmail.textContent = "Correct";
        msgEmail.className   = "msg-champ succes";
      }

      // --- Mot de passe ---
      var msgMdp = document.getElementById('msgMdp');
      if (mot_de_passe.length < 4) {
        msgMdp.textContent = "Le mot de passe doit contenir au moins 4 caracteres.";
        msgMdp.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgMdp.textContent = "Correct";
        msgMdp.className   = "msg-champ succes";
      }

      if (!estValide) {
        e.preventDefault();
      }
    });

    // ============================================================
    // Partie 3 : Controle de saisie avec plusieurs evenements JS
    // ============================================================

    // Champ Email - evenement blur (verification quand l'utilisateur quitte le champ)
    document.getElementById('email').addEventListener('blur', function() {
      var msgEmail   = document.getElementById('msgEmail');
      var regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!regexEmail.test(this.value)) {
        msgEmail.textContent = "Veuillez saisir un email valide (exemple@mail.com).";
        msgEmail.className   = "msg-champ erreur";
      } else {
        msgEmail.textContent = "Correct";
        msgEmail.className   = "msg-champ succes";
      }
    });

    // Champ Mot de passe - evenement keyup (verification en temps reel)
    document.getElementById('mot_de_passe').addEventListener('keyup', function() {
      var msgMdp = document.getElementById('msgMdp');
      if (this.value.length < 4) {
        msgMdp.textContent = "Le mot de passe doit contenir au moins 4 caracteres.";
        msgMdp.className   = "msg-champ erreur";
      } else {
        msgMdp.textContent = "Correct";
        msgMdp.className   = "msg-champ succes";
      }
    });
  </script>

</body>
</html>
