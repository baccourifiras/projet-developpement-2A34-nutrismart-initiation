<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits - NutriSmart BackOffice</title>
    <link rel="stylesheet" href="public/css/backoffice.css">
</head>
<body>
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">NS</div>
            <div>
                <h1>NutriSmart</h1>
                <p class="brand-slogan">Eat Smart Live Smart</p>
                <p class="sidebar-text">Administration des produits et commandes nutrition.</p>
            </div>
        </div>
        <nav class="menu">
            <a href="index.php?page=backoffice&action=list" class="active">Produits</a>
            <a href="index.php?page=backoffice&action=add">Ajouter Produit</a>
            <a href="index.php?page=commande&action=list">Commandes</a>
            <a href="index.php">Voir Front Office</a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-chip">Backoffice moderne</div>
        </div>
    </aside>

    <main class="main content">
        <section class="panel intro-panel">
            <p class="kicker">Gestion</p>
            <h2>Liste des Produits</h2>
            <p class="note">Gérez tous vos produits NutriSmart : plans nutritionnels, coaching, guides et fonctionnalités premium.</p>
        </section>

        <section class="panel">
            <div class="panel-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <p class="kicker kicker-soft">Actions</p>
                    <h2>Tous les produits</h2>
                </div>
                <a href="index.php?page=backoffice&action=add" class="primary-btn">+ Ajouter un Produit</a>
            </div>

            <!-- Barre de recherche -->
            <div class="search-bar-bo">
                <svg class="search-icon-bo" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="searchProduit" placeholder="Rechercher par nom, catégorie, régime..." oninput="filterTable('produitTable', this.value)">
            </div>

            <div class="table-wrapper">
                <table class="table" id="produitTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('produitTable', 0)">ID <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 1)">Nom <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 2)">Catégorie <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 3)">Régime <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 4)">Type <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 5)">Prix <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 6)">Disponible <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('produitTable', 7)">Date <span class="sort-icon">↕</span></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (empty($produits)) {
                            echo "<tr><td colspan='9' style='text-align:center;color:var(--muted);'>Aucun produit trouvé</td></tr>";
                        } else {
                            foreach ($produits as $produit) {
                                $disponibleText = $produit->getDisponible() ? 'Oui' : 'Non';
                                $disponibleClass = $produit->getDisponible() ? 'small-badge' : 'badge-danger';
                                echo "<tr>";
                                echo "<td><span class='id-badge'>{$produit->getIdProduit()}</span></td>";
                                echo "<td><strong>{$produit->getNom()}</strong></td>";
                                echo "<td><span class='small-badge'>{$produit->getCategorie()}</span></td>";
                                echo "<td><span class='small-badge'>{$produit->getRegimeCible()}</span></td>";
                                echo "<td>{$produit->getTypeVente()}</td>";
                                echo "<td><strong>{$produit->getPrix()} TND</strong></td>";
                                echo "<td><span class='{$disponibleClass}'>{$disponibleText}</span></td>";
                                echo "<td>" . date('d/m/Y', strtotime($produit->getDateAjout())) . "</td>";
                                echo "<td class='action-cell'>";
                                echo "<a href='index.php?page=backoffice&action=update&id={$produit->getIdProduit()}' class='edit-btn'>Modifier</a>";
                                echo "<a href='index.php?page=backoffice&action=delete&id={$produit->getIdProduit()}' class='delete-btn' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce produit ?\")'>Supprimer</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <p class="table-count" id="produitCount"></p>
        </section>
    </main>

    <script src="public/js/tableUtils.js"></script>
    <script>
        initTableUtils('produitTable', 'produitCount', 'produit');
    </script>
</body>
</html>
