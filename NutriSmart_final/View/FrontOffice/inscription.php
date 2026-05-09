<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Inscription</title>
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
      <a href="index.php?page=inscription" class="active">Inscription</a>
      <a href="index.php?page=login">Connexion</a>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Rejoignez NutriSmart</p>
    <h1>Creer un compte</h1>
    <p class="subtitle">Rejoignez la communaute NutriSmart.</p>
  </header>

  <main class="container">

    <!-- Messages PHP (Chapitre 3 - if/elseif) -->
    <?php if ($succes === '1'): ?>
    <div class="msg-box succes">Compte cree avec succes ! <a href="index.php?page=login">Se connecter</a></div>
    <?php elseif ($erreur === 'champs_vides'): ?>
    <div class="msg-box erreur">Veuillez remplir tous les champs obligatoires.</div>
    <?php elseif ($erreur === 'email_existe'): ?>
    <div class="msg-box erreur">Cet email est deja utilise.</div>
    <?php endif; ?>

    <section class="section">
      <div class="section-header">
        <h2>Creer mon compte</h2>
        <p class="section-sub">Remplissez le formulaire ci-dessous</p>
      </div>

      <!-- Formulaire - action pointe vers index.php (MVC) -->
      <!-- Partie 1 : onClick sur le bouton (Cours JS Partie 1) -->
      <form id="formInscription" action="index.php" method="POST" class="register-form">
        <input type="hidden" name="action" value="ajouter" />

        <div class="form-row">
          <div class="form-group">
            <label for="nom">Nom</label>
            <input id="nom" type="text" name="nom" placeholder="Ex : Ben Ali" />
            <div id="msgNom" class="msg-champ"></div>
          </div>
          <div class="form-group">
            <label for="prenom">Prenom</label>
            <input id="prenom" type="text" name="prenom" placeholder="Ex : Mohamed" />
            <div id="msgPrenom" class="msg-champ"></div>
          </div>
        </div>

        <div class="form-row">
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
        </div>

        <div class="form-group">
          <label for="role">Role</label>
          <select id="role" name="role">
            <option value="">-- Choisir un role --</option>
            <option value="client">Client</option>
            <option value="nutritionniste">Nutritionniste</option>
          </select>
          <div id="msgRole" class="msg-champ"></div>
        </div>

        <!-- Partie 1 : onClick appelle validerFormulaire() (Cours JS Partie 1) -->
        <button type="submit" class="submit-btn" onclick="return validerFormulaire()">Creer mon compte</button>
      </form>
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
      var nom          = document.getElementById('nom').value;
      var prenom       = document.getElementById('prenom').value;
      var email        = document.getElementById('email').value;
      var mot_de_passe = document.getElementById('mot_de_passe').value;
      var role         = document.getElementById('role').value;

      // Chapitre 3 - operateurs et conditions
      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      var regexEmail     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (nom.length < 2 || !regexNomPrenom.test(nom)) {
        alert("Le nom doit contenir au moins 2 lettres (lettres et espaces uniquement).");
        return false;
      }
      if (prenom.length < 2 || !regexNomPrenom.test(prenom)) {
        alert("Le prenom doit contenir au moins 2 lettres (lettres et espaces uniquement).");
        return false;
      }
      if (!regexEmail.test(email)) {
        alert("Veuillez saisir un email valide (exemple@mail.com).");
        return false;
      }
      if (mot_de_passe.length < 4) {
        alert("Le mot de passe doit contenir au moins 4 caracteres.");
        return false;
      }
      if (role === '') {
        alert("Veuillez choisir un role.");
        return false;
      }
      return true;
    }

    // ============================================================
    // Partie 2 : Controle de saisie avec addEventListener('submit')
    // ============================================================
    document.getElementById('formInscription').addEventListener('submit', function(e) {
      var estValide = true;

      var nom          = document.getElementById('nom').value;
      var prenom       = document.getElementById('prenom').value;
      var email        = document.getElementById('email').value;
      var mot_de_passe = document.getElementById('mot_de_passe').value;
      var role         = document.getElementById('role').value;

      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      var regexEmail     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      // --- Nom ---
      var msgNom = document.getElementById('msgNom');
      if (nom.length < 2 || !regexNomPrenom.test(nom)) {
        msgNom.textContent = "Le nom doit contenir au moins 2 lettres (lettres et espaces uniquement).";
        msgNom.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgNom.textContent = "Correct";
        msgNom.className   = "msg-champ succes";
      }

      // --- Prenom ---
      var msgPrenom = document.getElementById('msgPrenom');
      if (prenom.length < 2 || !regexNomPrenom.test(prenom)) {
        msgPrenom.textContent = "Le prenom doit contenir au moins 2 lettres (lettres et espaces uniquement).";
        msgPrenom.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgPrenom.textContent = "Correct";
        msgPrenom.className   = "msg-champ succes";
      }

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

      // --- Role ---
      var msgRole = document.getElementById('msgRole');
      if (role === '') {
        msgRole.textContent = "Veuillez choisir un role.";
        msgRole.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgRole.textContent = "Correct";
        msgRole.className   = "msg-champ succes";
      }

      if (!estValide) {
        e.preventDefault();
      }
    });

    // ============================================================
    // Partie 3 : Controle de saisie avec plusieurs evenements JS
    // ============================================================

    // Champ Nom - evenement keyup (verification en temps reel)
    document.getElementById('nom').addEventListener('keyup', function() {
      var msgNom         = document.getElementById('msgNom');
      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      if (this.value.length < 2 || !regexNomPrenom.test(this.value)) {
        msgNom.textContent = "Le nom doit contenir au moins 2 lettres (lettres et espaces uniquement).";
        msgNom.className   = "msg-champ erreur";
      } else {
        msgNom.textContent = "Correct";
        msgNom.className   = "msg-champ succes";
      }
    });

    // Champ Prenom - evenement keyup
    document.getElementById('prenom').addEventListener('keyup', function() {
      var msgPrenom      = document.getElementById('msgPrenom');
      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      if (this.value.length < 2 || !regexNomPrenom.test(this.value)) {
        msgPrenom.textContent = "Le prenom doit contenir au moins 2 lettres (lettres et espaces uniquement).";
        msgPrenom.className   = "msg-champ erreur";
      } else {
        msgPrenom.textContent = "Correct";
        msgPrenom.className   = "msg-champ succes";
      }
    });

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

    // Champ Mot de passe - evenement blur
    document.getElementById('mot_de_passe').addEventListener('blur', function() {
      var msgMdp = document.getElementById('msgMdp');
      if (this.value.length < 4) {
        msgMdp.textContent = "Le mot de passe doit contenir au moins 4 caracteres.";
        msgMdp.className   = "msg-champ erreur";
      } else {
        msgMdp.textContent = "Correct";
        msgMdp.className   = "msg-champ succes";
      }
    });

    // Champ Role - evenement change
    document.getElementById('role').addEventListener('change', function() {
      var msgRole = document.getElementById('msgRole');
      if (this.value === '') {
        msgRole.textContent = "Veuillez choisir un role.";
        msgRole.className   = "msg-champ erreur";
      } else {
        msgRole.textContent = "Correct";
        msgRole.className   = "msg-champ succes";
      }
    });
  </script>

</body>
</html>
