<?php

function handleListeCoursesRequest($action, $id, $space)
{
    // --- AJOUTER ---
    if ($action === 'add') {
        $errors    = [];
        $listeData = ['articles_a_acheter' => '', 'budget' => '', 'date_creation_fmt' => date('d/m/Y'), 'stock_id' => null];
        $stocks    = Stock::getStocks();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validation PHP (aucun attribut HTML5)
            if (empty(trim($_POST['articles_a_acheter'])))
                $errors['articles_a_acheter'] = 'Les articles sont obligatoires.';

            if (empty(trim($_POST['budget'] ?? ''))) {
                $errors['budget'] = 'Le budget est obligatoire.';
            } elseif (!is_numeric($_POST['budget']) || (float)$_POST['budget'] <= 0) {
                $errors['budget'] = 'Le budget doit etre un nombre positif.';
            }

            if (empty(trim($_POST['date_creation']))) {
                $errors['date_creation'] = 'La date de creation est obligatoire.';
            } else {
                $d = DateTime::createFromFormat('d/m/Y', $_POST['date_creation']);
                if (!$d || $d->format('d/m/Y') !== $_POST['date_creation'])
                    $errors['date_creation'] = 'Format invalide. Utilisez JJ/MM/AAAA.';
            }

            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_creation']);
                $liste = new ListeCourses(
                    trim($_POST['articles_a_acheter']),
                    (float)$_POST['budget'],
                    $dt->format('Y-m-d'),
                    !empty($_POST['stock_id']) ? (int)$_POST['stock_id'] : null
                );
                $liste->addListe();
                redirectToPage('liste_courses', $space, ['success' => 'add']);
            }
            $listeData = $_POST;
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
        $row    = ListeCourses::getListeById($id);
        $stocks = Stock::getStocks();

        if (!$row) { redirectToPage('liste_courses', $space); }

        $dt        = DateTime::createFromFormat('Y-m-d', (string)$row['date_creation']);
        $listeData = $row;
        $listeData['date_creation_fmt'] = $dt ? $dt->format('d/m/Y') : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validation PHP (aucun attribut HTML5)
            if (empty(trim($_POST['articles_a_acheter'])))
                $errors['articles_a_acheter'] = 'Les articles sont obligatoires.';

            if (empty(trim($_POST['budget'] ?? ''))) {
                $errors['budget'] = 'Le budget est obligatoire.';
            } elseif (!is_numeric($_POST['budget']) || (float)$_POST['budget'] <= 0) {
                $errors['budget'] = 'Le budget doit etre un nombre positif.';
            }

            if (empty(trim($_POST['date_creation']))) {
                $errors['date_creation'] = 'La date de creation est obligatoire.';
            } else {
                $d = DateTime::createFromFormat('d/m/Y', $_POST['date_creation']);
                if (!$d || $d->format('d/m/Y') !== $_POST['date_creation'])
                    $errors['date_creation'] = 'Format invalide. Utilisez JJ/MM/AAAA.';
            }

            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_creation']);
                $liste = new ListeCourses(
                    trim($_POST['articles_a_acheter']),
                    (float)$_POST['budget'],
                    $dt->format('Y-m-d'),
                    !empty($_POST['stock_id']) ? (int)$_POST['stock_id'] : null,
                    $id
                );
                $liste->updateListe($id);
                redirectToPage('liste_courses', $space, ['success' => 'update']);
            }
            $listeData = $_POST;
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
        ListeCourses::deleteListe($id);
        redirectToPage('liste_courses', $space, ['success' => 'delete']);
    }

    // --- LISTER ---
    $listes = ListeCourses::getListes();
    $retourAdd = 'index.php?page=liste_courses&action=add' . ($space === 'back' ? '&space=back' : '');
    $urlUpdate = 'index.php?page=liste_courses&action=update&id=';
    $urlDelete = 'index.php?page=liste_courses&action=delete&id=';

    renderHeader($space, 'liste_courses');
    if ($space === 'front') {
        echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Planification des achats</div><h1>Mes <span>Listes de Courses</span></h1><p>Creez et gerez vos listes d\'achats avec budget.</p></div></div>';
        echo '<div class="container">';
    }
    require BASE . '/View/BackOffice/liste_courses/list.php';
    if ($space === 'front') echo '</div>';
    renderFooter($space);
}
?>
