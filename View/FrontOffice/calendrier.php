<?php
/*
 * ============================================================
 * NutriSmart — Calendrier des événements
 * Affiche les événements par date dans un calendrier simple.
 * ============================================================
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Config.php';
require_once __DIR__ . '/../../Controller/EventController.php';
require_once __DIR__ . '/../../Controller/CategoryController.php';

$events = array();
$categories = array();
$loadError = '';

try {
    $pdo = config::getConnexion();
    $eventController = new EventController($pdo);
    $categoryController = new CategoryController($pdo);
    $events = $eventController->getAll();
    $categories = $categoryController->getAll();
} catch (Throwable $error) {
    $loadError = $error->getMessage();
}

// Regrouper les événements par date (format Y-m-d)
$eventsByDate = array();
foreach ($events as $event) {
    if (!empty($event['date'])) {
        $dateKey = substr($event['date'], 0, 10); // format Y-m-d
        if (!isset($eventsByDate[$dateKey])) {
            $eventsByDate[$dateKey] = array();
        }
        $eventsByDate[$dateKey][] = $event;
    }
}

// Mois affiché (paramètre GET ou mois courant)
$annee = isset($_GET['annee']) ? (int) $_GET['annee'] : (int) date('Y');
$mois  = isset($_GET['mois'])  ? (int) $_GET['mois']  : (int) date('m');

// Sécurité : bornes raisonnables
if ($mois < 1)  { $mois = 12; $annee--; }
if ($mois > 12) { $mois = 1;  $annee++; }

// Calculs du calendrier
$premierJour    = mktime(0, 0, 0, $mois, 1, $annee);
$nbJours        = (int) date('t', $premierJour);
$jourDebutSemaine = (int) date('N', $premierJour); // 1=lundi, 7=dimanche

// Mois précédent / suivant
$moisPrev  = $mois - 1;
$anneePrev = $annee;
if ($moisPrev < 1) { $moisPrev = 12; $anneePrev--; }

$moisSuiv  = $mois + 1;
$anneeSuiv = $annee;
if ($moisSuiv > 12) { $moisSuiv = 1; $anneeSuiv++; }

// Noms des mois en français
$nomsMois = array(
    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart - Calendrier des événements</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css" />
  <!-- Leaflet CSS — carte dans les détails d'événement -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    /* ── Styles spécifiques au calendrier ── */

    .cal-wrapper {
      max-width: 900px;
      margin: 0 auto;
    }

    /* Navigation mois */
    .cal-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 20px;
    }

    .cal-nav h2 {
      margin: 0;
      font-size: clamp(1.4rem, 3vw, 1.9rem);
      font-weight: 900;
      color: var(--text);
    }

    .cal-nav-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: linear-gradient(135deg, #edfdf4, #ffffff);
      border: 1px solid var(--border);
      color: var(--primary-dark);
      font-size: 18px;
      font-weight: 900;
      text-decoration: none;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      box-shadow: 0 4px 12px rgba(31,164,99,0.1);
    }

    .cal-nav-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(31,164,99,0.2);
    }

    /* Grille du calendrier */
    .cal-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 6px;
    }

    /* En-têtes des jours */
    .cal-day-header {
      text-align: center;
      font-size: 12px;
      font-weight: 800;
      color: var(--primary-dark);
      text-transform: uppercase;
      letter-spacing: 0.08em;
      padding: 10px 4px;
      background: linear-gradient(180deg, rgba(31,164,99,0.08), rgba(255,255,255,0.9));
      border-radius: 10px;
    }

    /* Cellule d'un jour */
    .cal-cell {
      min-height: 80px;
      border-radius: 14px;
      border: 1px solid var(--border);
      background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(246,252,248,0.98));
      padding: 8px;
      cursor: default;
      transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
      position: relative;
    }

    /* Cellule vide (avant le 1er du mois) */
    .cal-cell.empty {
      background: rgba(244,250,246,0.5);
      border-color: rgba(31,164,99,0.06);
    }

    /* Cellule avec événements — cliquable */
    .cal-cell.has-events {
      cursor: pointer;
      border-color: rgba(31,164,99,0.35);
      background: linear-gradient(180deg, #f0fdf6, #ffffff);
    }

    .cal-cell.has-events:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(31,164,99,0.18);
      border-color: rgba(31,164,99,0.6);
    }

    /* Aujourd'hui */
    .cal-cell.today {
      border-color: var(--primary);
      box-shadow: 0 0 0 2px rgba(31,164,99,0.25);
    }

    /* Numéro du jour */
    .cal-day-num {
      font-size: 14px;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 4px;
    }

    .cal-cell.today .cal-day-num {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 26px;
      height: 26px;
      border-radius: 50%;
      background: var(--primary);
      color: #ffffff;
      font-size: 13px;
    }

    /* Pastille événement */
    .cal-event-dot {
      display: block;
      font-size: 11px;
      font-weight: 600;
      color: var(--primary-dark);
      background: rgba(31,164,99,0.12);
      border-radius: 6px;
      padding: 2px 6px;
      margin-top: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    /* Compteur si plus de 2 événements */
    .cal-more {
      font-size: 11px;
      color: var(--muted);
      font-weight: 600;
      margin-top: 2px;
    }

    /* ── Panneau de détail des événements d'une date ── */
    .cal-detail {
      margin-top: 24px;
      display: none; /* caché par défaut, affiché par JS */
    }

    .cal-detail.visible {
      display: block;
    }

    .cal-detail-title {
      font-size: 1.2rem;
      font-weight: 800;
      color: var(--text);
      margin-bottom: 16px;
    }

    .cal-detail-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 18px;
    }

    /* Carte événement dans le détail */
    .cal-event-card {
      border: 1px solid var(--border);
      border-radius: 18px;
      background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(246,252,248,0.98));
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .cal-event-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 40px rgba(31,164,99,0.14);
    }

    .cal-event-card img {
      width: 100%;
      height: 140px;
      object-fit: cover;
    }

    .cal-event-card-body {
      padding: 16px;
    }

    .cal-event-card-body .event-badge {
      margin-bottom: 8px;
    }

    .cal-event-card-body h3 {
      font-size: 1rem;
      font-weight: 800;
      margin: 0 0 8px;
    }

    .cal-event-card-body .meta {
      font-size: 13px;
      margin: 6px 0;
    }

    .cal-event-card-body .maps-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 10px;
      padding: 7px 12px;
      border-radius: 10px;
      background: rgba(31,164,99,0.1);
      color: var(--primary-dark);
      font-size: 12px;
      font-weight: 700;
      text-decoration: none;
      transition: background 0.2s ease;
    }

    .cal-event-card-body .maps-link:hover {
      background: rgba(31,164,99,0.2);
    }

    /* Bouton retour vers événements */
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
      color: var(--primary-dark);
      font-weight: 700;
      font-size: 14px;
      text-decoration: none;
      transition: transform 0.2s ease;
    }

    .back-link:hover {
      transform: translateX(-3px);
    }

    @media (max-width: 600px) {
      .cal-grid { gap: 3px; }
      .cal-cell { min-height: 54px; padding: 5px; }
      .cal-day-num { font-size: 12px; }
      .cal-event-dot { display: none; }
      .cal-cell.has-events::after {
        content: '●';
        display: block;
        color: var(--primary);
        font-size: 10px;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <!-- =====================================================
       NAVBAR FIXE — identique aux autres pages
       ===================================================== -->
  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="accueil.php">Accueil</a>
      <a href="index.php">Événements</a>
      <a href="tache-categories.php">Tâche 1</a>
      <a href="tache-evenements.php">Tâche 2</a>
      <a href="tache-participants.php">Tâche 3</a>
      <a href="tache-design.php">Tâche 4</a>
      <a href="tache-javascript.php">Tâche 5</a>
      <a href="../backoffice/index.php" class="nav-dashboard">Dashboard</a>
    </div>
  </nav>

  <!-- En-tête de la page -->
  <header class="page-header">
    <p class="badge">Calendrier</p>
    <h1>Calendrier des événements</h1>
    <p class="subtitle">Visualisez tous les événements NutriSmart par date. Cliquez sur un jour pour voir les détails.</p>
  </header>

  <main class="container">
    <section class="section">

      <?php if ($loadError): ?>
        <div class="no-data">Impossible de charger les événements : <?= htmlspecialchars((string) $loadError, ENT_QUOTES, 'UTF-8') ?></div>
      <?php else: ?>

        <div class="cal-wrapper">

          <!-- Lien retour -->
          <a href="index.php" class="back-link">← Retour aux événements</a>

          <!-- Navigation mois -->
          <div class="cal-nav">
            <a href="calendrier.php?mois=<?= $moisPrev ?>&annee=<?= $anneePrev ?>" class="cal-nav-btn" title="Mois précédent">‹</a>
            <h2><?= htmlspecialchars($nomsMois[$mois], ENT_QUOTES, 'UTF-8') ?> <?= $annee ?></h2>
            <a href="calendrier.php?mois=<?= $moisSuiv ?>&annee=<?= $anneeSuiv ?>" class="cal-nav-btn" title="Mois suivant">›</a>
          </div>

          <!-- Grille du calendrier -->
          <div class="cal-grid" id="calGrid">

            <!-- En-têtes des jours de la semaine -->
            <?php
            $joursNoms = array('Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
            foreach ($joursNoms as $nomJour):
            ?>
              <div class="cal-day-header"><?= $nomJour ?></div>
            <?php endforeach; ?>

            <!-- Cellules vides avant le 1er du mois -->
            <?php for ($vide = 1; $vide < $jourDebutSemaine; $vide++): ?>
              <div class="cal-cell empty"></div>
            <?php endfor; ?>

            <!-- Jours du mois -->
            <?php
            $aujourdHui = date('Y-m-d');
            for ($jour = 1; $jour <= $nbJours; $jour++):
              $dateStr = sprintf('%04d-%02d-%02d', $annee, $mois, $jour);
              $eventsJour = isset($eventsByDate[$dateStr]) ? $eventsByDate[$dateStr] : array();
              $nbEvents = count($eventsJour);
              $isToday = ($dateStr === $aujourdHui);
              $classes = 'cal-cell';
              if ($nbEvents > 0) $classes .= ' has-events';
              if ($isToday) $classes .= ' today';
            ?>
              <div class="<?= $classes ?>"
                   data-date="<?= $dateStr ?>"
                   <?php if ($nbEvents > 0): ?>onclick="afficherEvenementsDate('<?= $dateStr ?>')"<?php endif; ?>>
                <div class="cal-day-num"><?= $jour ?></div>
                <?php
                // Afficher jusqu'à 2 événements dans la cellule
                $affichés = 0;
                foreach ($eventsJour as $ev):
                  if ($affichés >= 2) break;
                ?>
                  <span class="cal-event-dot"><?= htmlspecialchars((string) $ev['title'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php
                  $affichés++;
                endforeach;
                // Si plus de 2 événements
                if ($nbEvents > 2):
                ?>
                  <span class="cal-more">+<?= $nbEvents - 2 ?> autre(s)</span>
                <?php endif; ?>
              </div>
            <?php endfor; ?>

          </div><!-- fin cal-grid -->

          <!-- Panneau de détail (affiché au clic sur un jour) -->
          <div class="cal-detail" id="calDetail">
            <h3 class="cal-detail-title" id="calDetailTitle"></h3>
            <div class="cal-detail-grid" id="calDetailGrid"></div>
          </div>

        </div><!-- fin cal-wrapper -->

      <?php endif; ?>

    </section>
  </main>

  <!-- Données JSON des événements pour JavaScript -->
  <script>
    // Tous les événements encodés en JSON pour le JS
    var EVENTS_DATA = <?= json_encode($events, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    // Correspondance id catégorie → nom
    var CATEGORIES_DATA = <?= json_encode(
        array_column($categories, 'name', 'id'),
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
    ) ?>;
  </script>

  <script src="script.js?v=20260428-structure"></script>
  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    /*
     * ============================================================
     * Calendrier — JavaScript simple
     * Affiche les événements d'une date au clic sur une cellule
     * ============================================================
     */

    /**
     * Formate une date Y-m-d en français (ex: "lundi 5 mai 2026")
     */
    function formaterDateFr(dateStr) {
      var d = new Date(dateStr + 'T00:00:00');
      return d.toLocaleDateString('fr-FR', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    }

    /**
     * Affiche les événements d'une date dans le panneau de détail.
     * Appelé au clic sur une cellule du calendrier.
     *
     * @param {string} dateStr - Date au format Y-m-d
     */
    function afficherEvenementsDate(dateStr) {
      var detail = document.getElementById('calDetail');
      var titre  = document.getElementById('calDetailTitle');
      var grille = document.getElementById('calDetailGrid');

      // Filtrer les événements de cette date
      var eventsJour = EVENTS_DATA.filter(function(ev) {
        return ev.date && ev.date.substring(0, 10) === dateStr;
      });

      if (eventsJour.length === 0) {
        detail.classList.remove('visible');
        return;
      }

      // Titre du panneau
      titre.textContent = 'Événements du ' + formaterDateFr(dateStr);

      // Construire les cartes HTML
      var html = '';
      eventsJour.forEach(function(ev, idx) {
        var catNom = CATEGORIES_DATA[ev.categoryId] || 'Catégorie';
        var heure  = ev.time ? ev.time.substring(0, 5) : '';
        var mapsBtn = ev.googleMapsLink
          ? '<a class="maps-link" href="' + ev.googleMapsLink + '" target="_blank" rel="noopener noreferrer">📍 Ouvrir dans Maps</a>'
          : '';
        // Mini-carte si coordonnées disponibles
        var miniMap = (ev.latitude && ev.longitude)
          ? '<div id="minimap-' + idx + '" style="width:100%;height:160px;border-radius:10px;overflow:hidden;margin-top:10px;border:1px solid rgba(31,164,99,.15)"></div>'
          : '';

        html += '<article class="cal-event-card">'
          + '<img src="' + ev.image + '" alt="' + ev.title + '" loading="lazy" />'
          + '<div class="cal-event-card-body">'
          +   '<span class="event-badge">' + catNom + '</span>'
          +   '<h3>' + ev.title + '</h3>'
          +   '<p class="meta"><strong>Heure :</strong> ' + (heure || '-') + '</p>'
          +   '<p class="meta"><strong>Lieu :</strong> ' + ev.location + '</p>'
          +   '<p class="meta"><strong>Places disponibles :</strong> ' + ev.seats + '</p>'
          +   '<p style="color:var(--muted);font-size:13px;line-height:1.6;margin:8px 0 0">' + ev.description + '</p>'
          +   mapsBtn
          +   miniMap
          + '</div>'
          + '</article>';
      });

      grille.innerHTML = html;
      detail.classList.add('visible');

      // Initialiser les mini-cartes Leaflet pour les événements géolocalisés
      eventsJour.forEach(function(ev, idx) {
        if (!ev.latitude || !ev.longitude) return;
        var el = document.getElementById('minimap-' + idx);
        if (!el || typeof L === 'undefined') return;

        var miniMap = L.map(el, { zoomControl: false, dragging: false, scrollWheelZoom: false }).setView([ev.latitude, ev.longitude], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '© OpenStreetMap',
          maxZoom: 19
        }).addTo(miniMap);

        // Icône verte
        var icon = L.divIcon({
          className: '',
          html: '<div style="width:24px;height:24px;background:linear-gradient(135deg,#1fa463,#0f6c42);border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:2px solid #fff;box-shadow:0 3px 8px rgba(31,164,99,.5)"></div>',
          iconSize: [24, 24],
          iconAnchor: [12, 24]
        });
        L.marker([ev.latitude, ev.longitude], { icon: icon }).addTo(miniMap);
      });

      // Scroll vers le panneau de détail
      detail.scrollIntoView({ behavior: 'smooth', block: 'start' });

      // Mettre en évidence la cellule sélectionnée
      document.querySelectorAll('.cal-cell').forEach(function(cell) {
        cell.classList.remove('selected');
      });
      var cellule = document.querySelector('.cal-cell[data-date="' + dateStr + '"]');
      if (cellule) cellule.classList.add('selected');
    }
  </script>
</body>
</html>
