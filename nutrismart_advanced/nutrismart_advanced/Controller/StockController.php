<?php
/**
 * CONTROLEUR — Stock
 * Contient : logique métier + TOUT le SQL lié aux stocks
 * Appelle : Model/Stock.php (entité) + View/BackOffice/stock/*.php
 */

/* ══════════════════════════════════════
   FONCTIONS SQL — accès base de données
══════════════════════════════════════ */

function addStock(Stock $stock)
{
    $db    = config::getConnexion();
    $query = $db->prepare(
        "INSERT INTO stock (type, produits, date_expiration, seuil_minimum)
         VALUES (:type, :produits, :date_expiration, :seuil_minimum)"
    );
    $query->execute([
        'type'            => $stock->getType(),
        'produits'        => $stock->getProduits(),
        'date_expiration' => $stock->getDateExpiration(),
        'seuil_minimum'   => $stock->getSeuilMinimum(),
    ]);
}

function updateStock(Stock $stock, $id)
{
    $db    = config::getConnexion();
    $query = $db->prepare(
        "UPDATE stock
         SET type            = :type,
             produits        = :produits,
             date_expiration = :date_expiration,
             seuil_minimum   = :seuil_minimum
         WHERE id = :id"
    );
    $query->execute([
        'id'              => $id,
        'type'            => $stock->getType(),
        'produits'        => $stock->getProduits(),
        'date_expiration' => $stock->getDateExpiration(),
        'seuil_minimum'   => $stock->getSeuilMinimum(),
    ]);
}

function deleteStock($id)
{
    $db    = config::getConnexion();
    $query = $db->prepare("DELETE FROM stock WHERE id = :id");
    $query->execute(['id' => $id]);
}

function getStockById($id)
{
    $db    = config::getConnexion();
    $query = $db->prepare("SELECT * FROM stock WHERE id = :id");
    $query->execute(['id' => $id]);
    return $query->fetch();
}

function getStocksPagines($search = '', $sort = 'id_desc', $page = 1, $perPage = 6)
{
    $db     = config::getConnexion();
    $offset = ($page - 1) * $perPage;
    $today  = date('Y-m-d');
    $week   = date('Y-m-d', strtotime('+7 days'));

    $order = match($sort) {
        'date_asc'  => 'date_expiration ASC',
        'date_desc' => 'date_expiration DESC',
        'produit'   => 'produits ASC',
        default     => 'id DESC',
    };

    $where = !empty(trim($search)) ? "WHERE produits LIKE :search" : "";

    $sql = "SELECT *,
              CASE
                WHEN date_expiration < '{$today}' THEN 'expired'
                WHEN date_expiration BETWEEN '{$today}' AND '{$week}' THEN 'warning'
                ELSE 'ok'
              END AS statut
            FROM stock {$where}
            ORDER BY {$order}
            LIMIT :limit OFFSET :offset";

    $query = $db->prepare($sql);
    $query->bindValue(':limit',  $perPage, PDO::PARAM_INT);
    $query->bindValue(':offset', $offset,  PDO::PARAM_INT);
    if (!empty(trim($search))) {
        $query->bindValue(':search', '%' . trim($search) . '%');
    }
    $query->execute();
    return $query->fetchAll();
}

function getStocksAvecStatut($search = '', $sort = 'id_desc')
{
    $db    = config::getConnexion();
    $today = date('Y-m-d');
    $week  = date('Y-m-d', strtotime('+7 days'));

    $order = match($sort) {
        'date_asc'  => 'date_expiration ASC',
        'date_desc' => 'date_expiration DESC',
        'produit'   => 'produits ASC',
        default     => 'id DESC',
    };

    $where = !empty(trim($search)) ? "WHERE produits LIKE :search" : "";
    $sql   = "SELECT *,
                CASE
                  WHEN date_expiration < '{$today}' THEN 'expired'
                  WHEN date_expiration BETWEEN '{$today}' AND '{$week}' THEN 'warning'
                  ELSE 'ok'
                END AS statut
              FROM stock {$where} ORDER BY {$order}";

    $query = $db->prepare($sql);
    if (!empty(trim($search))) {
        $query->execute(['search' => '%' . trim($search) . '%']);
    } else {
        $query->execute();
    }
    return $query->fetchAll();
}

function countStocks($search = '')
{
    $db    = config::getConnexion();
    $where = !empty(trim($search)) ? "WHERE produits LIKE :search" : "";
    $query = $db->prepare("SELECT COUNT(*) AS nb FROM stock {$where}");
    if (!empty(trim($search))) {
        $query->execute(['search' => '%' . trim($search) . '%']);
    } else {
        $query->execute();
    }
    return (int)$query->fetch()['nb'];
}

function getStatsStock()
{
    $db    = config::getConnexion();
    $today = date('Y-m-d');
    $week  = date('Y-m-d', strtotime('+7 days'));

    $q = $db->prepare("SELECT COUNT(*) AS total FROM stock");
    $q->execute();
    $total = $q->fetch()['total'];

    $q = $db->prepare("SELECT COUNT(*) AS nb FROM stock WHERE date_expiration < :today");
    $q->execute(['today' => $today]);
    $expires = $q->fetch()['nb'];

    $q = $db->prepare("SELECT COUNT(*) AS nb FROM stock WHERE date_expiration BETWEEN :today AND :week");
    $q->execute(['today' => $today, 'week' => $week]);
    $bientot = $q->fetch()['nb'];

    $q = $db->prepare("SELECT type, COUNT(*) AS nb FROM stock GROUP BY type ORDER BY nb DESC");
    $q->execute();
    $parCategorie = $q->fetchAll();

    return [
        'total'        => $total,
        'expires'      => $expires,
        'bientot'      => $bientot,
        'parCategorie' => $parCategorie,
    ];
}

function getAllStocks()
{
    $db    = config::getConnexion();
    $query = $db->prepare("SELECT * FROM stock ORDER BY id DESC");
    $query->execute();
    return $query->fetchAll();
}

/* ══════════════════════════════════════
   VALIDATION
══════════════════════════════════════ */

function validerStock($data)
{
    $errors = [];

    if (empty(trim($data['produits'] ?? '')))
        $errors['produits'] = 'Le nom du produit est obligatoire.';

    if (empty(trim($data['type'] ?? '')))
        $errors['type'] = 'La categorie est obligatoire.';

    $dateStr = trim($data['date_expiration'] ?? '');
    if ($dateStr === '') {
        $errors['date_expiration'] = "La date d'expiration est obligatoire.";
    } else {
        $d = DateTime::createFromFormat('d/m/Y', $dateStr);
        if (!$d || $d->format('d/m/Y') !== $dateStr)
            $errors['date_expiration'] = "Format invalide. Utilisez JJ/MM/AAAA.";
    }

    $seuil = trim($data['seuil_minimum'] ?? '');
    if ($seuil === '') {
        $errors['seuil_minimum'] = 'Le seuil minimum est obligatoire.';
    } elseif (!is_numeric($seuil) || (float)$seuil < 0) {
        $errors['seuil_minimum'] = 'Le seuil doit etre un nombre positif.';
    }

    return $errors;
}

/* ══════════════════════════════════════
   ROUTAGE — handleStockRequest()
══════════════════════════════════════ */

function handleStockRequest($action, $id, $space)
{
    // --- AJOUTER ---
    if ($action === 'add') {
        $errors    = [];
        $stockData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = validerStock($_POST);
            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_expiration']);
                $stock = new Stock(
                    trim($_POST['type']),
                    trim($_POST['produits']),
                    $dt->format('Y-m-d'),
                    (float)$_POST['seuil_minimum']
                );
                addStock($stock);
                redirectToPage('stock', $space, ['success' => 'add']);
            }
            $stockData = $_POST;
        }

        renderHeader($space, 'stock');
        if ($space === 'front') {
            echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Stocks</div><h1>Ajouter un <span>Stock</span></h1></div></div>';
            echo '<div class="container">';
        }
        $retourUrl = 'index.php?page=stock' . ($space === 'back' ? '&space=back' : '');
        require BASE . '/View/BackOffice/stock/form.php';
        if ($space === 'front') echo '</div>';
        renderFooter($space);
        return;
    }

    // --- MODIFIER ---
    if ($action === 'update' && $id > 0) {
        $errors = [];
        $row    = getStockById($id);
        if (!$row) { redirectToPage('stock', $space); }

        $dt        = DateTime::createFromFormat('Y-m-d', (string)$row['date_expiration']);
        $stockData = $row;
        $stockData['date_expiration_fmt'] = $dt ? $dt->format('d/m/Y') : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = validerStock($_POST);
            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_expiration']);
                $stock = new Stock(
                    trim($_POST['type']),
                    trim($_POST['produits']),
                    $dt->format('Y-m-d'),
                    (float)$_POST['seuil_minimum'],
                    $id
                );
                updateStock($stock, $id);
                redirectToPage('stock', $space, ['success' => 'update']);
            }
            $stockData                        = $_POST;
            $stockData['id']                  = $id;
            $stockData['date_expiration_fmt'] = $_POST['date_expiration'];
        }

        renderHeader($space, 'stock');
        if ($space === 'front') {
            echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Stocks</div><h1>Modifier le <span>Stock</span></h1></div></div>';
            echo '<div class="container">';
        }
        $retourUrl = 'index.php?page=stock' . ($space === 'back' ? '&space=back' : '');
        require BASE . '/View/BackOffice/stock/form.php';
        if ($space === 'front') echo '</div>';
        renderFooter($space);
        return;
    }

    // --- SUPPRIMER ---
    if ($action === 'delete' && $id > 0) {
        deleteStock($id);
        redirectToPage('stock', $space, ['success' => 'delete']);
    }

    // --- EXPORT PDF ---
    if ($action === 'export_pdf') {
        $search = trim($_GET['search'] ?? '');
        $sort   = $_GET['sort']   ?? 'id_desc';
        $stocks = getStocksAvecStatut($search, $sort);
        require BASE . '/View/BackOffice/stock/export_pdf.php';
        exit;
    }

    // --- LISTER (avec recherche + tri + pagination) ---
    $search  = trim($_GET['search'] ?? '');
    $sort    = $_GET['sort']  ?? 'id_desc';
    $curPage = max(1, (int)($_GET['p'] ?? 1));
    $perPage = 6;
    $total   = countStocks($search);
    $nbPages = (int)ceil($total / $perPage);
    $curPage = min($curPage, max(1, $nbPages));
    $stocks  = getStocksPagines($search, $sort, $curPage, $perPage);

    $retourAdd = 'index.php?page=stock&action=add' . ($space === 'back' ? '&space=back' : '');
    $urlUpdate = 'index.php?page=stock&action=update&id=';
    $urlDelete = 'index.php?page=stock&action=delete&id=';

    renderHeader($space, 'stock');
    if ($space === 'front') {
        echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Gestion</div><h1>Mes <span>Stocks</span></h1></div></div>';
        echo '<div class="container">';
    }
    require BASE . '/View/BackOffice/stock/list.php';
    if ($space === 'front') echo '</div>';
    renderFooter($space);
}
?>
