<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Backoffice</title>
  <link rel="stylesheet" href="public/css/backoffice.css" />
  <style>
    .tabs-nav { display:flex; gap:8px; margin-bottom:24px; border-bottom:2px solid #e2e8f0; padding-bottom:0; }
    .tab-btn { padding:10px 24px; border:none; background:none; cursor:pointer; font-size:.95rem; font-weight:600; color:#64748b; border-bottom:3px solid transparent; margin-bottom:-2px; border-radius:8px 8px 0 0; }
    .tab-btn.active { color:#16a34a; border-bottom-color:#16a34a; background:#f0fdf4; }
    .tab-content { display:none; }
    .tab-content.active { display:block; }
    .hist-stats { display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap; }
    .hist-stat-card { background:#fff; border-radius:12px; padding:16px 24px; box-shadow:0 2px 12px rgba(0,0,0,.06); flex:1; min-width:140px; text-align:center; }
    .hist-stat-card span { font-size:.85rem; color:#64748b; display:block; }
    .hist-stat-card strong { font-size:1.8rem; font-weight:900; color:#0f172a; }
    .hist-stat-card.succes strong { color:#16a34a; }
    .hist-stat-card.echec  strong { color:#dc2626; }
    .badge-succes { background:#dcfce7; color:#16a34a; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:700; }
    .badge-echec  { background:#fee2e2; color:#dc2626; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:700; }
    .badge-action { background:#e0f2fe; color:#0369a1; padding:3px 10px; border-radius:20px; font-size:.78rem; font-weight:600; }
    .msg-ok  { background:#dcfce7; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-weight:600; }
    .msg-err { background:#fee2e2; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-weight:600; }
    .modal-overlay { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:999; justify-content:center; align-items:center; }
    .modal-overlay.active { display:flex; }
    .modal-box { background:#fff; border-radius:16px; padding:2rem; width:100%; max-width:500px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
    .modal-box h3 { margin-bottom:1.2rem; font-size:1.2rem; color:#0f172a; }
    .modal-actions { display:flex; gap:12px; justify-content:flex-end; margin-top:1.5rem; }
    .modal-actions .cancel-btn { padding:8px 20px; border:1px solid #e2e8f0; border-radius:8px; background:#fff; cursor:pointer; font-weight:600; }
    .msg-champ { font-size: 12px; margin-top: 3px; }
    .msg-champ.erreur { color: red; }
    .msg-champ.succes { color: green; }
    .form-grid input, .form-grid select { width:100%; padding:10px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.95rem; }
    .form-grid label { font-size:.88rem; font-weight:600; color:#374151; margin-bottom:4px; display:block; }
    .form-grid > div { margin-bottom:14px; }
  </style>
</head>
<body>

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
      </div>
    </div>

    <nav class="menu">
      <a href="index.php?page=dashboard&onglet=utilisateurs">Utilisateurs</a>
      <a href="index.php?page=dashboard&onglet=historique">Historique</a>
      <a href="index.php?page=accueil">Voir le site</a>
    </nav>

    <div class="sidebar-footer">
      <!-- $adminPrenom et $adminEmail sont prepares par PageController::dashboard() -->
      <div>
        Connecte en tant que : <?php echo htmlspecialchars($adminPrenom); ?>
      </div>
      <!-- Formulaire deconnexion - action vers index.php (MVC) -->
      <form action="index.php" method="POST" style="margin-top:10px;">
        <input type="hidden" name="action" value="deconnexion" />
        <button type="submit" style="width:100%;padding:8px;background:#dc2626;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">
          Se deconnecter
        </button>
      </form>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main content">

    <!-- STATISTIQUES - variables preparees par le Controller (Chapitre 5 - CRUD Read) -->
    <section class="panel stats-panel">
      <div class="stats-card">
        <span>Total utilisateurs</span>
        <!-- $total est prepare par PageController::dashboard() -->
        <strong><?php echo $total; ?></strong>
      </div>
      <div class="stats-card">
        <span>Admins</span>
        <!-- $parRole est prepare par PageController::dashboard() -->
        <strong><?php echo $parRole['admin']; ?></strong>
      </div>
      <div class="stats-card">
        <span>Nutritionnistes</span>
        <strong><?php echo $parRole['nutritionniste']; ?></strong>
      </div>
      <div class="stats-card">
        <span>Clients</span>
        <strong><?php echo $parRole['client']; ?></strong>
      </div>
    </section>

    <!-- MESSAGES - $succes et $erreur prepares par le Controller (Chapitre 3 - if/elseif) -->
    <?php if ($succes === '1' || $succes === 'ajoute'): ?>
      <div class="msg-ok">Utilisateur ajoute avec succes.</div>
    <?php elseif ($succes === 'modifie'): ?>
      <div class="msg-ok">Utilisateur modifie avec succes.</div>
    <?php elseif ($succes === 'supprime'): ?>
      <div class="msg-ok">Utilisateur supprime.</div>
    <?php elseif ($succes === 'histo_supprime'): ?>
      <div class="msg-ok">Entree supprimee.</div>
    <?php elseif ($succes === 'histo_vide'): ?>
      <div class="msg-ok">Historique vide.</div>
    <?php endif; ?>

    <?php if ($erreur === 'champs_vides'): ?>
      <div class="msg-err">Veuillez remplir tous les champs obligatoires.</div>
    <?php elseif ($erreur === 'email_existe'): ?>
      <div class="msg-err">Cet email est deja utilise.</div>
    <?php endif; ?>

    <!-- ONGLETS -->
    <section class="panel">
      <!-- $onglet est prepare par le Controller (Chapitre 3 - isset) -->
      <div class="tabs-nav">
        <button class="tab-btn <?php if ($onglet === 'utilisateurs') { echo 'active'; } ?>"
                onclick="ouvrirOnglet('utilisateurs')">Gestion Utilisateurs</button>
        <button class="tab-btn <?php if ($onglet === 'historique') { echo 'active'; } ?>"
                onclick="ouvrirOnglet('historique')">Historique Connexions</button>
      </div>

      <!-- ONGLET UTILISATEURS -->
      <div class="tab-content <?php if ($onglet === 'utilisateurs') { echo 'active'; } ?>" id="tab-utilisateurs">

        <div class="panel-header">
          <h2>Ajouter un nouvel utilisateur</h2>
        </div>

        <!-- Formulaire ajout - action vers index.php (MVC) -->
        <form action="index.php" method="POST" class="form-grid two-columns">
          <input type="hidden" name="action" value="ajouter" />
          <div>
            <label>Nom</label>
            <input type="text" name="nom" placeholder="Ex : Ben Ali" />
          </div>
          <div>
            <label>Prenom</label>
            <input type="text" name="prenom" placeholder="Ex : Mohamed" />
          </div>
          <div>
            <label>Email</label>
            <input type="text" name="email" placeholder="exemple@mail.com" />
          </div>
          <div>
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" />
          </div>
          <div>
            <label>Role</label>
            <select name="role">
              <option value="">-- Choisir un role --</option>
              <option value="admin">Admin</option>
              <option value="nutritionniste">Nutritionniste</option>
              <option value="client">Client</option>
            </select>
          </div>
          <div class="full-width">
            <button class="primary-btn" type="submit">Ajouter l'utilisateur</button>
          </div>
        </form>

        <!-- TABLEAU UTILISATEURS -->
        <!-- $utilisateurs est prepare par PageController::dashboard() -->
        <div style="margin-top:2rem;overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- if/foreach - Chapitre 3 -->
              <?php if (empty($utilisateurs)): ?>
              <tr><td colspan="6" style="text-align:center;color:#64748b;">Aucun utilisateur.</td></tr>
              <?php else: ?>
              <?php foreach ($utilisateurs as $u): ?>
              <tr>
                <td><?php echo htmlspecialchars($u['id_user']); ?></td>
                <td><?php echo htmlspecialchars($u['nom']); ?></td>
                <td><?php echo htmlspecialchars($u['prenom']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['role']); ?></td>
                <td>
                  <button class="edit-btn" onclick="ouvrirModifier(
                    <?php echo $u['id_user']; ?>,
                    '<?php echo htmlspecialchars($u['nom'], ENT_QUOTES); ?>',
                    '<?php echo htmlspecialchars($u['prenom'], ENT_QUOTES); ?>',
                    '<?php echo htmlspecialchars($u['email'], ENT_QUOTES); ?>',
                    '<?php echo $u['role']; ?>'
                  )">Modifier</button>

                  <form action="index.php" method="POST" style="display:inline;"
                        onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    <input type="hidden" name="action" value="supprimer" />
                    <input type="hidden" name="id_user" value="<?php echo $u['id_user']; ?>" />
                    <button type="submit" class="delete-btn">Supprimer</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ONGLET HISTORIQUE -->
      <div class="tab-content <?php if ($onglet === 'historique') { echo 'active'; } ?>" id="tab-historique">

        <div class="panel-header" style="display:flex;justify-content:space-between;align-items:center;">
          <h2>Historique des connexions</h2>
          <form action="index.php" method="POST"
                onsubmit="return confirm('Vider tout l\'historique ?');">
            <input type="hidden" name="action" value="vider" />
            <button type="submit" style="background:#fff;color:#dc2626;border:2px solid #dc2626;padding:8px 18px;border-radius:8px;cursor:pointer;font-weight:600;">
              Vider l'historique
            </button>
          </form>
        </div>

        <!-- Stats historique - $stats prepare par le Controller -->
        <div class="hist-stats">
          <div class="hist-stat-card">
            <span>Total entrees</span>
            <strong><?php echo $stats['total']; ?></strong>
          </div>
          <div class="hist-stat-card succes">
            <span>Connexions reussies</span>
            <strong><?php echo $stats['succes']; ?></strong>
          </div>
          <div class="hist-stat-card echec">
            <span>Tentatives echouees</span>
            <strong><?php echo $stats['echec']; ?></strong>
          </div>
        </div>

        <!-- Tableau historique - $historique prepare par le Controller -->
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Utilisateur</th>
                <th>Action</th>
                <th>Statut</th>
                <th>Email tente</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- foreach - Chapitre 3 -->
              <?php if (empty($historique)): ?>
              <tr><td colspan="7" style="text-align:center;color:#64748b;">Aucun historique.</td></tr>
              <?php else: ?>
              <?php foreach ($historique as $h): ?>
              <tr>
                <td><?php echo $h['id_historique']; ?></td>
                <td>
                  <!-- if/else - Chapitre 3 -->
                  <?php if ($h['nom']): ?>
                    <?php echo htmlspecialchars($h['nom'] . ' ' . $h['prenom']); ?>
                  <?php else: ?>
                    Inconnu
                  <?php endif; ?>
                </td>
                <td><span class="badge-action"><?php echo htmlspecialchars($h['action']); ?></span></td>
                <td>
                  <?php if ($h['statut'] === 'succes'): ?>
                    <span class="badge-succes">Succes</span>
                  <?php else: ?>
                    <span class="badge-echec">Echec</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($h['email_tente']); ?></td>
                <td><?php echo htmlspecialchars($h['date_action']); ?></td>
                <td>
                  <form action="index.php" method="POST" style="display:inline;"
                        onsubmit="return confirm('Supprimer cette entree ?');">
                    <input type="hidden" name="action" value="supprimer_historique" />
                    <input type="hidden" name="id_historique" value="<?php echo $h['id_historique']; ?>" />
                    <button type="submit" class="delete-btn">Supprimer</button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </section>
  </main>

  <!-- MODAL MODIFIER -->
  <div class="modal-overlay" id="modalModifier">
    <div class="modal-box">
      <h3>Modifier l'utilisateur</h3>
      <!-- Formulaire modifier - action vers index.php (MVC) -->
      <!-- Partie 1 : onClick appelle validerModifier() (Cours JS Partie 1) -->
      <form id="formModifier" action="index.php" method="POST" class="form-grid">
        <input type="hidden" name="action" value="modifier" />
        <input type="hidden" name="id_user" id="edit-id" value="" />
        <div>
          <label>Nom</label>
          <input type="text" name="nom" id="edit-nom" />
          <div id="msgEditNom" class="msg-champ"></div>
        </div>
        <div>
          <label>Prenom</label>
          <input type="text" name="prenom" id="edit-prenom" />
          <div id="msgEditPrenom" class="msg-champ"></div>
        </div>
        <div>
          <label>Email</label>
          <input type="text" name="email" id="edit-email" />
          <div id="msgEditEmail" class="msg-champ"></div>
        </div>
        <div>
          <label>Role</label>
          <select name="role" id="edit-role">
            <option value="admin">Admin</option>
            <option value="nutritionniste">Nutritionniste</option>
            <option value="client">Client</option>
          </select>
          <div id="msgEditRole" class="msg-champ"></div>
        </div>
        <div class="modal-actions">
          <button type="button" class="cancel-btn" onclick="fermerModal()">Annuler</button>
          <!-- Partie 1 : onClick (Cours JS Partie 1) -->
          <button type="submit" class="primary-btn" onclick="return validerModifier()">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function ouvrirOnglet(nom) {
        document.querySelectorAll('.tab-content').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
        document.getElementById('tab-' + nom).classList.add('active');
        document.querySelectorAll('.tab-btn')[nom === 'utilisateurs' ? 0 : 1].classList.add('active');
    }

    function ouvrirModifier(id, nom, prenom, email, role) {
        document.getElementById('edit-id').value     = id;
        document.getElementById('edit-nom').value    = nom;
        document.getElementById('edit-prenom').value = prenom;
        document.getElementById('edit-email').value  = email;
        document.getElementById('edit-role').value   = role;
        // Vider les messages quand on ouvre le modal
        document.getElementById('msgEditNom').textContent    = '';
        document.getElementById('msgEditPrenom').textContent = '';
        document.getElementById('msgEditEmail').textContent  = '';
        document.getElementById('msgEditRole').textContent   = '';
        document.getElementById('modalModifier').classList.add('active');
    }

    function fermerModal() {
        document.getElementById('modalModifier').classList.remove('active');
    }

    document.getElementById('modalModifier').addEventListener('click', function(e) {
        if (e.target === this) { fermerModal(); }
    });

    // ============================================================
    // Partie 1 : Controle de saisie avec l'evenement onClick
    // ============================================================
    function validerModifier() {
      var nom    = document.getElementById('edit-nom').value;
      var prenom = document.getElementById('edit-prenom').value;
      var email  = document.getElementById('edit-email').value;
      var role   = document.getElementById('edit-role').value;

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
      if (role === '') {
        alert("Veuillez choisir un role.");
        return false;
      }
      return true;
    }

    // ============================================================
    // Partie 2 : Controle de saisie avec addEventListener('submit')
    // ============================================================
    document.getElementById('formModifier').addEventListener('submit', function(e) {
      var estValide = true;

      var nom    = document.getElementById('edit-nom').value;
      var prenom = document.getElementById('edit-prenom').value;
      var email  = document.getElementById('edit-email').value;
      var role   = document.getElementById('edit-role').value;

      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      var regexEmail     = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      // --- Nom ---
      var msgEditNom = document.getElementById('msgEditNom');
      if (nom.length < 2 || !regexNomPrenom.test(nom)) {
        msgEditNom.textContent = "Le nom doit contenir au moins 2 lettres.";
        msgEditNom.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgEditNom.textContent = "Correct";
        msgEditNom.className   = "msg-champ succes";
      }

      // --- Prenom ---
      var msgEditPrenom = document.getElementById('msgEditPrenom');
      if (prenom.length < 2 || !regexNomPrenom.test(prenom)) {
        msgEditPrenom.textContent = "Le prenom doit contenir au moins 2 lettres.";
        msgEditPrenom.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgEditPrenom.textContent = "Correct";
        msgEditPrenom.className   = "msg-champ succes";
      }

      // --- Email ---
      var msgEditEmail = document.getElementById('msgEditEmail');
      if (!regexEmail.test(email)) {
        msgEditEmail.textContent = "Veuillez saisir un email valide.";
        msgEditEmail.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgEditEmail.textContent = "Correct";
        msgEditEmail.className   = "msg-champ succes";
      }

      // --- Role ---
      var msgEditRole = document.getElementById('msgEditRole');
      if (role === '') {
        msgEditRole.textContent = "Veuillez choisir un role.";
        msgEditRole.className   = "msg-champ erreur";
        estValide = false;
      } else {
        msgEditRole.textContent = "Correct";
        msgEditRole.className   = "msg-champ succes";
      }

      if (!estValide) {
        e.preventDefault();
      }
    });

    // ============================================================
    // Partie 3 : Controle de saisie avec plusieurs evenements JS
    // ============================================================

    // Champ Nom - evenement keyup (verification en temps reel)
    document.getElementById('edit-nom').addEventListener('keyup', function() {
      var msgEditNom     = document.getElementById('msgEditNom');
      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      if (this.value.length < 2 || !regexNomPrenom.test(this.value)) {
        msgEditNom.textContent = "Le nom doit contenir au moins 2 lettres.";
        msgEditNom.className   = "msg-champ erreur";
      } else {
        msgEditNom.textContent = "Correct";
        msgEditNom.className   = "msg-champ succes";
      }
    });

    // Champ Prenom - evenement keyup
    document.getElementById('edit-prenom').addEventListener('keyup', function() {
      var msgEditPrenom  = document.getElementById('msgEditPrenom');
      var regexNomPrenom = /^[a-zA-ZÀ-ÿ\s]{2,}$/;
      if (this.value.length < 2 || !regexNomPrenom.test(this.value)) {
        msgEditPrenom.textContent = "Le prenom doit contenir au moins 2 lettres.";
        msgEditPrenom.className   = "msg-champ erreur";
      } else {
        msgEditPrenom.textContent = "Correct";
        msgEditPrenom.className   = "msg-champ succes";
      }
    });

    // Champ Email - evenement blur (verification quand l'utilisateur quitte le champ)
    document.getElementById('edit-email').addEventListener('blur', function() {
      var msgEditEmail = document.getElementById('msgEditEmail');
      var regexEmail   = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!regexEmail.test(this.value)) {
        msgEditEmail.textContent = "Veuillez saisir un email valide.";
        msgEditEmail.className   = "msg-champ erreur";
      } else {
        msgEditEmail.textContent = "Correct";
        msgEditEmail.className   = "msg-champ succes";
      }
    });

    // Champ Role - evenement change
    document.getElementById('edit-role').addEventListener('change', function() {
      var msgEditRole = document.getElementById('msgEditRole');
      if (this.value === '') {
        msgEditRole.textContent = "Veuillez choisir un role.";
        msgEditRole.className   = "msg-champ erreur";
      } else {
        msgEditRole.textContent = "Correct";
        msgEditRole.className   = "msg-champ succes";
      }
    });
  </script>
</body>
</html>
