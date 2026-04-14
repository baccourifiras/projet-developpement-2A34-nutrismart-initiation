<?php
/*
 * ============================================================
 * NutriSmart — Fichier PHP
 *
 * Pour l'instant ce fichier est une page HTML standard.
 * Quand le projet sera connecté à MySQL :
 *   - Remplacer les données JavaScript par des requêtes SQL
 *   - Exemple : $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
 *   - Utiliser echo pour injecter les données dans la page
 * ============================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Backoffice</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">NS</div>
      <div>
        <h1>NutriSmart</h1>
        <p class="brand-slogan">Eat Smart Live Smart</p>
        <p class="sidebar-text">Administration des événements nutrition, régimes et bien-être.</p>
      </div>
    </div>

    <nav class="menu">
      <a href="#dashboard">Tableau de bord</a>
      <a href="#categorySection">Catégories</a>
      <a href="#eventSection">Événements</a>
      <a href="#participantSection">Participants</a>
      <a href="../frontoffice/tache-categories.php">Tâche Catégories</a>
      <a href="../frontoffice/tache-evenements.php">Tâche Événements</a>
      <a href="../frontoffice/tache-participants.php">Tâche Participants</a>
      <a href="../frontoffice/tache-design.php">Tâche Design CSS</a>
      <a href="../frontoffice/tache-javascript.php">Tâche JavaScript</a>
      <a href="../frontoffice/index.php">Voir Front Office</a>
    </nav>

    <div class="sidebar-footer">
      <div class="sidebar-chip">Backoffice moderne</div>
      <button id="resetDataBtn" class="danger-btn">Réinitialiser les données</button>
    </div>
  </aside>

  <main class="main content">
    <section id="dashboard" class="panel stats-panel">
      <div class="stats-card">
        <span>Catégories</span>
        <strong id="categoryCount">0</strong>
      </div>
      <div class="stats-card">
        <span>Événements</span>
        <strong id="eventCount">0</strong>
      </div>
      <div class="stats-card">
        <span>Participants</span>
        <strong id="participantCount">0</strong>
      </div>
    </section>

    <section class="panel intro-panel">
      <p class="kicker">Projet</p>
      <h2>Backoffice NutriSmart</h2>
      <p class="note">Ce backoffice permet d’ajouter des catégories, des événements et des inscriptions avec des identifiants courts et propres. Chaque ajout reste synchronisé avec le front office.</p>
    </section>

    <section id="categorySection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Ajouter une nouvelle catégorie</h2>
        </div>
      </div>
      <form id="categoryForm" class="form-grid two-columns">
        <div>
          <label for="categoryName">Nom de la catégorie</label>
          <input id="categoryName" required type="text" placeholder="Ex : Nutrition sportive" />
        </div>
        <div>
          <label for="categoryDescription">Description</label>
          <input id="categoryDescription" type="text" placeholder="Courte description" />
        </div>
        <button class="primary-btn" type="submit">Ajouter la catégorie</button>
      </form>
      <div id="categoryTableContainer"></div>
    </section>

    <section id="eventSection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Gestion</p>
          <h2>Ajouter un nouvel événement</h2>
        </div>
      </div>
      <form id="eventForm" class="form-grid two-columns">
        <div>
          <label for="eventTitle">Titre</label>
          <input id="eventTitle" required type="text" placeholder="Nom de l'événement" />
        </div>
        <div>
          <label for="eventCategory">Catégorie</label>
          <select id="eventCategory" required></select>
        </div>
        <div>
          <label for="eventDate">Date</label>
          <input id="eventDate" required type="date" />
        </div>
        <div>
          <label for="eventTime">Heure</label>
          <input id="eventTime" required type="time" />
        </div>
        <div>
          <label for="eventLocation">Lieu</label>
          <input id="eventLocation" required type="text" placeholder="Lieu" />
        </div>
        <div>
          <label for="eventSeats">Nombre de places</label>
          <input id="eventSeats" required type="number" min="1" placeholder="50" />
        </div>
        <div class="full-width">
          <label for="eventDescription">Description</label>
          <textarea id="eventDescription" required rows="4" placeholder="Description de l'événement"></textarea>
        </div>
        <div class="full-width">
          <label for="eventImage">Image URL</label>
          <input id="eventImage" type="url" placeholder="https://..." />
        </div>
        <button class="primary-btn" type="submit">Ajouter l'événement</button>
      </form>
      <div id="eventTableContainer"></div>
    </section>

    <section id="participantSection" class="panel">
      <div class="panel-header">
        <div>
          <p class="kicker kicker-soft">Suivi</p>
          <h2>Liste des participants</h2>
        </div>
      </div>
      <div id="participantTableContainer"></div>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
