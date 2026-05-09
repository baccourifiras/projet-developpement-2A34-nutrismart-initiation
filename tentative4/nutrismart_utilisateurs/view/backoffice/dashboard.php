<?php
/**
 * NutriSmart — Backoffice : Gestion des Utilisateurs
 * Ce fichier est le point d'entrée du backoffice.
 * Il affiche l'interface d'administration et délègue au contrôleur PHP via fetch().
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Backoffice Utilisateurs</title>
  <link rel="stylesheet" href="../view/backoffice/style.css" />
</head>
<body>

  <!-- ===================== SIDEBAR ===================== -->
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
        <p class="sidebar-text">Administration des utilisateurs (clients, nutritionnistes, admins).</p>
      </div>
    </div>

    <nav class="menu">
      <a href="#dashboard">Tableau de bord</a>
      <a href="#userSection">Utilisateurs</a>
      <a href="PageController.php?page=inscription">Voir Front Office</a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-chip">Backoffice moderne</div>
    </div>
  </aside>

  <!-- ===================== MAIN ===================== -->
  <main class="main content">

    <!-- STATS -->
    <section id="dashboard" class="panel stats-panel">
      <div class="stats-card">
        <span>Total utilisateurs</span>
        <strong id="totalCount">—</strong>
      </div>
      <div class="stats-card">
        <span>Admins</span>
        <strong id="adminCount">—</strong>
      </div>
      <div class="stats-card">
        <span>Nutritionnistes</span>
        <strong id="nutriCount">—</strong>
      </div>
      <div class="stats-card">
        <span>Clients</span>
        <strong id="clientCount">—</strong>
      </div>
    </section>

    <!-- INTRO -->
    <section class="panel intro-panel">
      <p class="kicker">Projet</p>
      <h2>Gestion des Utilisateurs</h2>
      <p class="note">Ce backoffice permet d'ajouter, modifier et supprimer des utilisateurs avec contrôle de saisie complet. Les données sont persistées en base MySQL via PDO.</p>
    </section>

    <!-- FORMULAIRE AJOUT -->
    <section id="userSection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Ajouter un nouvel utilisateur</h2>
        </div>
      </div>

      <!-- Les erreurs JS s'affichent ici -->
      <div id="formErrors" class="error-banner hidden"></div>

      <form id="userForm" class="form-grid two-columns" novalidate>
        <div>
          <label for="nom">Nom <span class="required-star">*</span></label>
          <input id="nom" name="nom" type="text" placeholder="Ex : Ben Ali" autocomplete="off" />
          <span class="field-error" id="err-nom"></span>
        </div>
        <div>
          <label for="prenom">Prénom <span class="required-star">*</span></label>
          <input id="prenom" name="prenom" type="text" placeholder="Ex : Mohamed" autocomplete="off" />
          <span class="field-error" id="err-prenom"></span>
        </div>
        <div>
          <label for="email">Email <span class="required-star">*</span></label>
          <input id="email" name="email" type="text" placeholder="exemple@mail.com" autocomplete="off" />
          <span class="field-error" id="err-email"></span>
        </div>
        <div>
          <label for="mot_de_passe">Mot de passe <span class="required-star">*</span></label>
          <div class="password-wrapper">
            <input id="mot_de_passe" name="mot_de_passe" type="password" placeholder="Min. 8 caractères, 1 majuscule, 1 chiffre" />
            <button type="button" class="toggle-pwd" onclick="togglePwd('mot_de_passe', this)">👁</button>
          </div>
          <span class="field-error" id="err-mot_de_passe"></span>
        </div>
        <div>
          <label for="role">Rôle <span class="required-star">*</span></label>
          <select id="role" name="role">
            <option value="">-- Choisir un rôle --</option>
            <option value="admin">Admin</option>
            <option value="nutritionniste">Nutritionniste</option>
            <option value="client">Client</option>
          </select>
          <span class="field-error" id="err-role"></span>
        </div>
        <div>
          <label for="provider_login">Provider de connexion <span class="required-star">*</span></label>
          <select id="provider_login" name="provider_login">
            <option value="">-- Choisir un provider --</option>
            <option value="local">Local</option>
            <option value="google">Google</option>
            <option value="facebook">Facebook</option>
          </select>
          <span class="field-error" id="err-provider_login"></span>
        </div>
        <div class="full-width" style="display:flex;gap:12px;align-items:center;">
          <button class="primary-btn" type="submit">➕ Ajouter l'utilisateur</button>
          <span id="loadingMsg" class="hidden note">Enregistrement…</span>
        </div>
      </form>

      <div id="userTableContainer"></div>
    </section>

  </main>

  <script src="../view/backoffice/script.js"></script>
</body>
</html>
