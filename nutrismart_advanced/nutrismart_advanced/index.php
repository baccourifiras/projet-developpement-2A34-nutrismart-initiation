<?php
define('BASE', __DIR__);

require_once BASE . '/config.php';
require_once BASE . '/Model/Stock.php';
require_once BASE . '/Model/ListeCourses.php';
require_once BASE . '/Controller/StockController.php';
require_once BASE . '/Controller/ListeCoursesController.php';

$space  = ($_GET['space'] ?? 'front') === 'back' ? 'back' : 'front';
$page   = $_GET['page'] ?? ($space === 'back' ? 'stock' : 'accueil');
$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($page === 'dashboard') {
    $stats          = getStatsStock();
    $totalBudget    = getTotalBudget();
    $budgetParMois  = getBudgetParMois();
    $stocksRecents  = getStocksAvecStatut('', 'date_asc');
    renderHeader($space, 'dashboard');
    require BASE . '/View/BackOffice/dashboard.php';
    renderFooter($space);
    exit;
}

if ($page === 'stock') {
    handleStockRequest($action, $id, $space);
    exit;
}

if ($page === 'liste_courses') {
    handleListeCoursesRequest($action, $id, $space);
    exit;
}

renderHeader($space, 'accueil');
?>
<div class="hero">
  <div class="hero-inner">
    <div class="hero-breadcrumb">Bienvenue</div>
    <h1>Gerez votre <span>alimentation</span><br>intelligemment</h1>
    <p>NutriSmart vous aide a suivre vos stocks alimentaires et planifier vos achats avec un budget maitrise.</p>
  </div>
</div>
<div class="container">
  <div class="accueil-grid">
    <div class="accueil-card">
      <div class="ico">Stocks</div>
      <h3>Mes Stocks</h3>
      <p>Gerez vos produits alimentaires, suivez les dates d'expiration et les seuils minimum.</p>
      <a href="index.php?page=stock<?= $space === 'back' ? '&space=back' : '' ?>">Gerer les stocks</a>
    </div>
    <div class="accueil-card">
      <div class="ico">Courses</div>
      <h3>Listes de Courses</h3>
      <p>Planifiez vos achats, definissez un budget et associez-les a vos stocks existants.</p>
      <a href="index.php?page=liste_courses<?= $space === 'back' ? '&space=back' : '' ?>">Gerer les listes</a>
    </div>
  </div>
</div>
<?php
renderFooter($space);
?>
