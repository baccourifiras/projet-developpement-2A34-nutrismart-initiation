<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Produit - NutriSmart BackOffice</title>
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
            <a href="index.php?page=commande&action=list">Commandes</a>
            <a href="index.php">Voir Front Office</a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-chip">Backoffice moderne</div>
        </div>
    </aside>

    <main class="main content">
        <section class="panel intro-panel">
            <p class="kicker">Modification</p>
            <h2>Modifier le Produit</h2>
            <p class="note">Mettez à jour les informations du produit sélectionné.</p>
        </section>

        <?php if ($produit): ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="kicker kicker-soft">Formulaire</p>
                    <h2><?= htmlspecialchars($produit->getNom()) ?></h2>
                </div>
                <a href="index.php?page=backoffice&action=list" class="secondary-btn">← Retour</a>
            </div>

            <form id="addProduitForm" method="POST" action="index.php?page=backoffice&action=update" class="form-grid two-columns">
                <input type="hidden" name="id_produit" value="<?= $produit->getIdProduit() ?>">
                
                <div>
                    <label for="nom">Nom du produit *</label>
                    <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($produit->getNom()) ?>" placeholder="Ex: Plan Diabète Premium" required>
                    <span class="validation-message" id="nom-message"></span>
                </div>

                <div>
                    <label for="prix">Prix (TND) *</label>
                    <input type="number" id="prix" name="prix" step="0.01" min="0" value="<?= $produit->getPrix() ?>" placeholder="Ex: 29.99" required>
                    <span class="validation-message" id="prix-message"></span>
                </div>

                <div>
                    <label for="categorie">Catégorie *</label>
                    <select id="categorie" name="categorie" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="plan" <?= $produit->getCategorie() === 'plan' ? 'selected' : '' ?>>Plan Nutritionnel</option>
                        <option value="premium" <?= $produit->getCategorie() === 'premium' ? 'selected' : '' ?>>Fonctionnalité Premium</option>
                        <option value="coaching" <?= $produit->getCategorie() === 'coaching' ? 'selected' : '' ?>>Coaching</option>
                        <option value="guide" <?= $produit->getCategorie() === 'guide' ? 'selected' : '' ?>>Guide</option>
                    </select>
                    <span class="validation-message" id="categorie-message"></span>
                </div>

                <div>
                    <label for="regime_cible">Régime Ciblé *</label>
                    <select id="regime_cible" name="regime_cible" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="diabete" <?= $produit->getRegimeCible() === 'diabete' ? 'selected' : '' ?>>Diabète</option>
                        <option value="vegan" <?= $produit->getRegimeCible() === 'vegan' ? 'selected' : '' ?>>Vegan</option>
                        <option value="sans_gluten" <?= $produit->getRegimeCible() === 'sans_gluten' ? 'selected' : '' ?>>Sans Gluten</option>
                        <option value="multi" <?= $produit->getRegimeCible() === 'multi' ? 'selected' : '' ?>>Multi-régimes</option>
                    </select>
                </div>

                <div>
                    <label for="type_vente">Type de Vente *</label>
                    <select id="type_vente" name="type_vente" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="abonnement" <?= $produit->getTypeVente() === 'abonnement' ? 'selected' : '' ?>>Abonnement</option>
                        <option value="achat_unique" <?= $produit->getTypeVente() === 'achat_unique' ? 'selected' : '' ?>>Achat Unique</option>
                    </select>
                    <span class="validation-message" id="type_vente-message"></span>
                </div>

                <div>
                    <label>
                        <input type="checkbox" id="disponible" name="disponible" <?= $produit->getDisponible() ? 'checked' : '' ?>>
                        Produit disponible à la vente
                    </label>
                    <span class="validation-message" id="disponible-message"></span>
                </div>

                <div class="full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" placeholder="Décrivez les caractéristiques du produit..."><?= htmlspecialchars($produit->getDescription()) ?></textarea>
                </div>

                <button type="submit" class="primary-btn" id="submitBtn">Mettre à Jour</button>
                <a href="index.php?page=backoffice&action=list" class="cancel-btn" style="display:inline-flex;align-items:center;justify-content:center;text-decoration:none;">Annuler</a>
            </form>
        </section>
        <?php else: ?>
        <section class="panel">
            <div class="no-data">Produit introuvable.</div>
        </section>
        <?php endif; ?>
    </main>

    <script src="public/js/addProduit.js"></script>
</body>
</html>
