<?php

function handleStockRequest($action, $id, $space)
{
    // --- AJOUTER ---
    if ($action === 'add') {
        $errors    = [];
        $stockData = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validation PHP (aucun attribut HTML5)
            if (empty(trim($_POST['produits'])))
                $errors['produits'] = 'Le nom du produit est obligatoire.';

            if (empty(trim($_POST['type'])))
                $errors['type'] = 'La categorie est obligatoire.';

            if (empty(trim($_POST['date_expiration']))) {
                $errors['date_expiration'] = "La date d'expiration est obligatoire.";
            } else {
                $d = DateTime::createFromFormat('d/m/Y', $_POST['date_expiration']);
                if (!$d || $d->format('d/m/Y') !== $_POST['date_expiration'])
                    $errors['date_expiration'] = "Format invalide. Utilisez JJ/MM/AAAA.";
            }

            if (empty(trim($_POST['seuil_minimum'] ?? ''))) {
                $errors['seuil_minimum'] = 'Le seuil minimum est obligatoire.';
            } elseif (!is_numeric($_POST['seuil_minimum']) || (float)$_POST['seuil_minimum'] < 0) {
                $errors['seuil_minimum'] = 'Le seuil doit etre un nombre positif.';
            }

            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_expiration']);
                $stock = new Stock(
                    trim($_POST['type']),
                    trim($_POST['produits']),
                    $dt->format('Y-m-d'),
                    (float)$_POST['seuil_minimum']
                );
                $stock->addStock();
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
        $row    = Stock::getStockById($id);

        if (!$row) { redirectToPage('stock', $space); }

        $dt        = DateTime::createFromFormat('Y-m-d', (string)$row['date_expiration']);
        $stockData = $row;
        $stockData['date_expiration_fmt'] = $dt ? $dt->format('d/m/Y') : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Validation PHP (aucun attribut HTML5)
            if (empty(trim($_POST['produits'])))
                $errors['produits'] = 'Le nom du produit est obligatoire.';

            if (empty(trim($_POST['type'])))
                $errors['type'] = 'La categorie est obligatoire.';

            if (empty(trim($_POST['date_expiration']))) {
                $errors['date_expiration'] = "La date d'expiration est obligatoire.";
            } else {
                $d = DateTime::createFromFormat('d/m/Y', $_POST['date_expiration']);
                if (!$d || $d->format('d/m/Y') !== $_POST['date_expiration'])
                    $errors['date_expiration'] = "Format invalide. Utilisez JJ/MM/AAAA.";
            }

            if (empty(trim($_POST['seuil_minimum'] ?? ''))) {
                $errors['seuil_minimum'] = 'Le seuil minimum est obligatoire.';
            } elseif (!is_numeric($_POST['seuil_minimum']) || (float)$_POST['seuil_minimum'] < 0) {
                $errors['seuil_minimum'] = 'Le seuil doit etre un nombre positif.';
            }

            if (empty($errors)) {
                $dt    = DateTime::createFromFormat('d/m/Y', $_POST['date_expiration']);
                $stock = new Stock(
                    trim($_POST['type']),
                    trim($_POST['produits']),
                    $dt->format('Y-m-d'),
                    (float)$_POST['seuil_minimum'],
                    $id
                );
                $stock->updateStock($id);
                redirectToPage('stock', $space, ['success' => 'update']);
            }
            $stockData                    = $_POST;
            $stockData['id']              = $id;
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
        Stock::deleteStock($id);
        redirectToPage('stock', $space, ['success' => 'delete']);
    }

    // --- LISTER ---
    $stocks    = Stock::getStocks();
    $retourAdd = 'index.php?page=stock&action=add' . ($space === 'back' ? '&space=back' : '');
    $urlUpdate = 'index.php?page=stock&action=update&id=';
    $urlDelete = 'index.php?page=stock&action=delete&id=';

    renderHeader($space, 'stock');
    if ($space === 'front') {
        echo '<div class="hero"><div class="hero-inner"><div class="hero-breadcrumb">Gestion</div><h1>Mes <span>Stocks</span></h1><p>Gerez vos produits alimentaires et suivez vos seuils.</p></div></div>';
        echo '<div class="container">';
    }
    require BASE . '/View/BackOffice/stock/list.php';
    if ($space === 'front') echo '</div>';
    renderFooter($space);
}
?>
