<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Commandes - NutriSmart BackOffice</title>
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
            <a href="index.php?page=backoffice&action=list">Produits</a>
            <a href="index.php?page=backoffice&action=add">Ajouter Produit</a>
            <a href="index.php?page=commande&action=list" class="active">Commandes</a>
            <a href="index.php">Voir Front Office</a>
        </nav>
        <div class="sidebar-footer">
            <div class="sidebar-chip">Backoffice moderne</div>
        </div>
    </aside>

    <main class="main content">
        <section class="panel intro-panel">
            <p class="kicker">Suivi</p>
            <h2>Gestion des Commandes</h2>
            <p class="note">Consultez et gérez toutes les commandes passées par vos clients.</p>
        </section>

        <section class="panel">
            <div class="panel-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <p class="kicker kicker-soft">Liste</p>
                    <h2>Toutes les commandes</h2>
                </div>
            </div>

            <!-- Barre de recherche -->
            <div class="search-bar-bo">
                <svg class="search-icon-bo" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="searchCommande" placeholder="Rechercher par produit, utilisateur, statut, paiement..." oninput="filterTable('commandeTable', this.value)">
            </div>

            <div class="table-wrapper">
                <table class="table" id="commandeTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable('commandeTable', 0)">ID <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 1)">Utilisateur <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 2)">Produit <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 3)">Quantité <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 4)">Prix Total <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 5)">Statut <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 6)">Paiement <span class="sort-icon">↕</span></th>
                            <th class="sortable" onclick="sortTable('commandeTable', 7)">Date <span class="sort-icon">↕</span></th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (empty($commandes)) {
                            echo "<tr><td colspan='9' style='text-align:center;color:var(--muted);'>Aucune commande trouvée</td></tr>";
                        } else {
                            foreach ($commandes as $commande) {
                                echo "<tr>";
                                echo "<td><span class='id-badge'>{$commande['id_commande']}</span></td>";
                                echo "<td>{$commande['id_utilisateur']}</td>";
                                echo "<td><strong>{$commande['nom_produit']}</strong></td>";
                                echo "<td>{$commande['quantite']}</td>";
                                echo "<td><strong>{$commande['prix_total']} TND</strong></td>";
                                echo "<td>";
                                echo "<form method='POST' action='index.php?page=commande&action=updateStatus' style='margin:0;'>";
                                echo "<input type='hidden' name='id_commande' value='{$commande['id_commande']}'>";
                                echo "<select name='statut' onchange='this.form.submit()' class='status-select status-{$commande['statut']}'>";
                                echo "<option value='en_attente'" . ($commande['statut'] === 'en_attente' ? ' selected' : '') . ">En attente</option>";
                                echo "<option value='confirmee'"  . ($commande['statut'] === 'confirmee'  ? ' selected' : '') . ">Confirmée</option>";
                                echo "<option value='annulee'"    . ($commande['statut'] === 'annulee'    ? ' selected' : '') . ">Annulée</option>";
                                echo "</select></form>";
                                echo "</td>";
                                echo "<td>{$commande['mode_paiement']}</td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($commande['date_commande'])) . "</td>";
                                echo "<td class='action-cell'>";
                                echo "<a href='index.php?page=commande&action=delete&id={$commande['id_commande']}' class='delete-btn' onclick='return confirm(\"Supprimer cette commande ?\")'>Supprimer</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <p class="table-count" id="commandeCount"></p>
        </section>
    </main>

    <script src="public/js/tableUtils.js"></script>
    <script>
        initTableUtils('commandeTable', 'commandeCount', 'commande');
    </script>
</body>
</html>
