<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/models/Stock.php';
require_once BASE_PATH . '/models/ListeCourses.php';
require_once BASE_PATH . '/controllers/StockController.php';
require_once BASE_PATH . '/controllers/ListeCoursesController.php';

$page   = $_GET['page']   ?? '';
$action = $_GET['action'] ?? 'index';

$allowedActions = ['index','create','edit','delete'];
if (!in_array($action, $allowedActions, true)) $action = 'index';

try {
    if ($page === 'stock') {
        $ctrl = new StockController();
        $ctrl->$action();

    } elseif ($page === 'liste_courses') {
        $ctrl = new ListeCoursesController();
        $ctrl->$action();

    } else {
        $pageTitle   = 'Accueil';
        $currentPage = '';
        require BASE_PATH . '/views/shared/front_header.php';
?>

<section class="hero-section">
  <div class="hero-content">
    <p class="hero-kicker">🌿 Bienvenue sur NutriSmart</p>
    <h1>Gérez votre alimentation <span>intelligemment</span></h1>
    <p class="hero-sub">Suivez vos stocks alimentaires, planifiez vos courses et réduisez le gaspillage alimentaire.</p>
    <div class="hero-btns">
      <a href="index.php?page=stock" class="btn-white">📦 Mes Stocks</a>
      <a href="index.php?page=liste_courses" class="btn-outline">🛒 Mes Courses</a>
    </div>
  </div>
</section>

<section class="features-section">
  <div class="features-grid">
    <div class="feature-card">
      <div class="feature-icon">📦</div>
      <h3>Gestion des Stocks</h3>
      <p>Ajoutez vos produits, suivez les dates d'expiration et recevez des alertes automatiques avant expiration.</p>
      <a href="index.php?page=stock" class="feature-link">Gérer mes stocks →</a>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🛒</div>
      <h3>Listes de Courses</h3>
      <p>Créez vos listes d'achats avec un budget défini et associez-les à vos stocks existants.</p>
      <a href="index.php?page=liste_courses" class="feature-link">Mes listes →</a>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📊</div>
      <h3>Tableau de bord Admin</h3>
      <p>L'administrateur consulte les statistiques globales, les alertes et surveille les données.</p>
      <a href="../backoffice/index.php" class="feature-link">Accès admin →</a>
    </div>
  </div>
</section>

<footer class="site-footer">© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</footer>
</body></html>
<?php
    }

} catch (PDOException $e) {
    echo '<div style="font-family:sans-serif;padding:40px;background:#fee2e2;color:#991b1b;border-radius:12px;margin:40px">';
    echo '<h2>⚠️ Erreur de connexion PDO</h2>';
    echo '<p>Vérifiez que MySQL est lancé et que la base <strong>nutrismart</strong> existe.</p>';
    echo '<p>Modifiez les identifiants dans <code>config/database.php</code></p>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '</div>';
}
