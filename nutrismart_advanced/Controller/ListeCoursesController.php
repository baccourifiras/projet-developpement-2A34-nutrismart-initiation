<?php
/**
 * CONTROLEUR — ListeCourses
 * Contient : logique métier + TOUT le SQL lié aux listes de courses
 * Appelle : Model/ListeCourses.php (entité) + View/BackOffice/liste_courses/*.php
 */

/* ══════════════════════════════════════
   FONCTIONS SQL — accès base de données
══════════════════════════════════════ */

function addListe(ListeCourses $liste)
{
    $db    = config::getConnexion();
    $query = $db->prepare(
        "INSERT INTO liste_courses (articles_a_acheter, budget, date_creation, stock_id)
         VALUES (:articles_a_acheter, :budget, :date_creation, :stock_id)"
    );
    $query->execute([
        'articles_a_acheter' => $liste->getArticlesAcheter(),
        'budget'             => $liste->getBudget(),
        'date_creation'      => $liste->getDateCreation(),
        'stock_id'           => $liste->getStockId(),
    ]);
}

function updateListe(ListeCourses $liste, $id)
{
    $db    = config::getConnexion();
    $query = $db->prepare(
        "UPDATE liste_courses
         SET articles_a_acheter = :articles_a_acheter,
             budget             = :budget,
             date_creation      = :date_creation,
             stock_id           = :stock_id
         WHERE id = :id"
    );
    $query->execute([
        'id'                 => $id,
        'articles_a_acheter' => $liste->getArticlesAcheter(),
        'budget'             => $liste->getBudget(),
        'date_creation'      => $liste->getDateCreation(),
        'stock_id'           => $liste->getStockId(),
    ]);
}

function deleteListe($id)
{
    $db    = config::getConnexion();
    $query = $db->prepare("DELETE FROM liste_courses WHERE id = :id");
    $query->execute(['id' => $id]);
}

function getListeById($id)
{
    $db    = config::getConnexion();
    $query = $db->prepare("SELECT * FROM liste_courses WHERE id = :id");
    $query->execute(['id' => $id]);
    return $query->fetch();
}

function getListes()
{
    $db    = config::getConnexion();
    $query = $db->prepare(
        "SELECT lc.*, s.type AS stock_type
         FROM liste_courses lc
         LEFT JOIN stock s ON lc.stock_id = s.id
         ORDER BY lc.id DESC"
    );
    $query->execute();
    return $query->fetchAll();
}

function searchListes($search = '', $sort = 'id_desc', $page = 1, $perPage = 6)
{
    $db     = config::getConnexion();
    $offset = ($page - 1) * $perPage;

    $order = match($sort) {
        'budget_asc'  => 'budget ASC',
        'budget_desc' => 'budget DESC',
        'date_asc'    => 'lc.date_creation ASC',
        'date_desc'   => 'lc.date_creation DESC',
        default       => 'lc.id DESC',
    };

    $where = !empty(trim($search)) ? "WHERE lc.articles_a_acheter LIKE :search" : "";

    $sql = "SELECT lc.*, s.type AS stock_type
            FROM liste_courses lc
            LEFT JOIN stock s ON lc.stock_id = s.id
            {$where}
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

function countListes($search = '')
{
    $db    = config::getConnexion();
    $where = !empty(trim($search)) ? "WHERE articles_a_acheter LIKE :search" : "";
    $query = $db->prepare("SELECT COUNT(*) AS nb FROM liste_courses {$where}");
    if (!empty(trim($search))) {
        $query->execute(['search' => '%' . trim($search) . '%']);
    } else {
        $query->execute();
    }
    return (int)$query->fetch()['nb'];
}

function searchListesAll($search = '', $sort = 'id_desc')
{
    $db    = config::getConnexion();
    $order = match($sort) {
        'budget_asc'  => 'budget ASC',
        'budget_desc' => 'budget DESC',
        'date_asc'    => 'lc.date_creation ASC',
        'date_desc'   => 'lc.date_creation DESC',
        default       => 'lc.id DESC',
    };
    $where = !empty(trim($search)) ? "WHERE lc.articles_a_acheter LIKE :search" : "";
    $sql   = "SELECT lc.*, s.type AS stock_type
              FROM liste_courses lc
              LEFT JOIN stock s ON lc.stock_id = s.id
              {$where} ORDER BY {$order}";
    $query = $db->prepare($sql);
    if (!empty(trim($search))) {
        $query->execute(['search' => '%' . trim($search) . '%']);
    } else {
        $query->execute();
    }
    return $query->fetchAll();
}

function getTotalBudget()
{
    $db    = config::getConnexion();
    $query = $db->prepare("SELECT COALESCE(SUM(budget), 0) AS total FROM liste_courses");
    $query->execute();
    return (float)$query->fetch()['total'];
}

function getBudgetParMois()
{
    $db    = config::getConnexion();
    $query = $db->prepare(
        "SELECT DATE_FORMAT(date_creation, '%Y-%m') AS mois,
                DATE_FORMAT(date_creation, '%b %Y') AS mois_label,
                SUM(budget) AS total
         FROM liste_courses
         GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
         ORDER BY mois ASC
         LIMIT 12"
    );
    $query->execute();
    return $query->fetchAll();
}

function dupliquerListe($id)
{
    $row = getListeById($id);
    if (!$row) return false;

    $db    = config::getConnexion();
    $query = $db->prepare(
        "INSERT INTO liste_courses (articles_a_acheter, budget, date_creation, stock_id)
         VALUES (:articles_a_acheter, :budget, :date_creation, :stock_id)"
    );
    $query->execute([
        'articles_a_acheter' => $row['articles_a_acheter'] . ' (copie)',
        'budget'             => $row['budget'],
        'date_creation'      => date('Y-m-d'),
        'stock_id'           => $row['stock_id'],
    ]);
    return true;
}

/* ══════════════════════════════════════
   VALIDATION
══════════════════════════════════════ */

function validerListe($data)
{
    $errors = [];

    if (empty(trim($data['articles_a_acheter'] ?? '')))
        $errors['articles_a_acheter'] = 'Les articles sont obligatoires.';

    $budget = trim($data['budget'] ?? '');
    if ($budget === '') {
        $errors['budget'] = 'Le budget est obligatoire.';
    } elseif (!is_numeric($budget) || (float)$budget <= 0) {
        $errors['budget'] = 'Le budget doit etre un nombre positif.';
    }

    $dateStr = trim($data['date_creation'] ?? '');
    if ($dateStr === '') {
        $errors['date_creation'] = 'La date de creation est obligatoire.';
    } else {
        $d = DateTime::createFromFormat('d/m/Y', $dateStr);
        if (!$d || $d->format('d/m/Y') !== $dateStr)
            $errors['date_creation'] = 'Format invalide. Utilisez JJ/MM/AAAA.';
    }

    return $errors;
}

/* ══════════════════════════════════════
   ROUTAGE — handleListeCoursesRequest()
══════════════════════════════════════ */

function handleListeCoursesRequest($action, $id, $space)
{
    // --- AJOUTER ---
    if ($action === 'add') {
        $errors    = [];
        $listeData = ['articles_a_acheter' => '', 'budget' => '', 'date_creation_fmt' => date('d/m/Y'), 'stock_id' => null];
        $stocks    = getAllStocks();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = validerListe($_POST);
            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_creation']);
                $liste = new ListeCourses(
                    trim($_POST['articles_a_acheter']),
                    (float)$_POST['budget'],
                    $dt->format('Y-m-d'),
                    !empty($_POST['stock_id']) ? (int)$_POST['stock_id'] : null
                );
                addListe($liste);
                redirectToPage('liste_courses', $space, ['success' => 'add']);
            }
            $listeData                      = $_POST;
            $listeData['date_creation_fmt'] = $_POST['date_creation'];
        }

        renderHeader($space, 'liste_courses');
        if ($space === 'front') {
            echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Courses</div><h1>Nouvelle <span>Liste de Courses</span></h1></div></div>';
            echo '<div class="container">';
        }
        $retourUrl = 'index.php?page=liste_courses' . ($space === 'back' ? '&space=back' : '');
        require BASE . '/View/BackOffice/liste_courses/form.php';
        if ($space === 'front') echo '</div>';
        renderFooter($space);
        return;
    }

    // --- MODIFIER ---
    if ($action === 'update' && $id > 0) {
        $errors = [];
        $row    = getListeById($id);
        $stocks = getAllStocks();
        if (!$row) { redirectToPage('liste_courses', $space); }

        $dt        = DateTime::createFromFormat('Y-m-d', (string)$row['date_creation']);
        $listeData = $row;
        $listeData['date_creation_fmt'] = $dt ? $dt->format('d/m/Y') : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = validerListe($_POST);
            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_creation']);
                $liste = new ListeCourses(
                    trim($_POST['articles_a_acheter']),
                    (float)$_POST['budget'],
                    $dt->format('Y-m-d'),
                    !empty($_POST['stock_id']) ? (int)$_POST['stock_id'] : null,
                    $id
                );
                updateListe($liste, $id);
                redirectToPage('liste_courses', $space, ['success' => 'update']);
            }
            $listeData                      = $_POST;
            $listeData['id']                = $id;
            $listeData['date_creation_fmt'] = $_POST['date_creation'];
        }

        renderHeader($space, 'liste_courses');
        if ($space === 'front') {
            echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Courses</div><h1>Modifier la <span>Liste de Courses</span></h1></div></div>';
            echo '<div class="container">';
        }
        $retourUrl = 'index.php?page=liste_courses' . ($space === 'back' ? '&space=back' : '');
        require BASE . '/View/BackOffice/liste_courses/form.php';
        if ($space === 'front') echo '</div>';
        renderFooter($space);
        return;
    }

    // --- SUPPRIMER ---
    if ($action === 'delete' && $id > 0) {
        deleteListe($id);
        redirectToPage('liste_courses', $space, ['success' => 'delete']);
    }

    // --- DUPLIQUER ---
    if ($action === 'duplicate' && $id > 0) {
        dupliquerListe($id);
        redirectToPage('liste_courses', $space, ['success' => 'duplicate']);
    }

    // --- EXPORT PDF ---
    if ($action === 'export_pdf') {
        $search = trim($_GET['search'] ?? '');
        $sort   = $_GET['sort'] ?? 'id_desc';
        $listes = searchListesAll($search, $sort);
        require BASE . '/View/BackOffice/liste_courses/export_pdf.php';
        exit;
    }

    // --- LISTER (avec recherche + tri + pagination) ---
    $search  = trim($_GET['search'] ?? '');
    $sort    = $_GET['sort']  ?? 'id_desc';
    $curPage = max(1, (int)($_GET['p'] ?? 1));
    $perPage = 6;
    $total   = countListes($search);
    $nbPages = (int)ceil($total / $perPage);
    $curPage = min($curPage, max(1, $nbPages));
    $listes  = searchListes($search, $sort, $curPage, $perPage);

    $retourAdd = 'index.php?page=liste_courses&action=add' . ($space === 'back' ? '&space=back' : '');
    $urlUpdate = 'index.php?page=liste_courses&action=update&id=';
    $urlDelete = 'index.php?page=liste_courses&action=delete&id=';

    renderHeader($space, 'liste_courses');
    if ($space === 'front') {
        echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Planification</div><h1>Mes <span>Listes de Courses</span></h1></div></div>';
        echo '<div class="container">';
    }
    require BASE . '/View/BackOffice/liste_courses/list.php';
    if ($space === 'front') echo '</div>';
    renderFooter($space);
}
?>
