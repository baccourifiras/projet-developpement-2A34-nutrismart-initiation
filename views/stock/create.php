<?php
$pageTitle   = 'Ajouter un Stock';
$currentPage = 'stock';
require __DIR__ . '/../shared/front_header.php';
?>

<div class="page-header">
  <p class="ph-kicker">📦 Nouveau produit</p>
  <h1>Ajouter un <span>Stock</span></h1>
  <p>Remplissez les informations de votre produit alimentaire.</p>
</div>

<div class="container">
  <div class="form-card">
    <form method="POST" id="stockForm" novalidate>
      <div class="form-grid">

        <div class="form-group">
          <label for="produits">Nom du produit *</label>
          <input type="text" id="produits" name="produits"
                 value="<?= htmlspecialchars($_POST['produits'] ?? '') ?>"
                 class="<?= isset($errors['produits']) ? 'is-invalid':'' ?>"
                 placeholder="Ex : Tomates cerises, Lait entier…"/>
          <span class="error-msg" id="err-produits"><?= $errors['produits'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label for="type">Catégorie *</label>
          <input type="text" id="type" name="type"
                 value="<?= htmlspecialchars($_POST['type'] ?? '') ?>"
                 class="<?= isset($errors['type']) ? 'is-invalid':'' ?>"
                 placeholder="Ex : Légumes, Laitier, Viande, Fruits…"/>
          <span class="error-msg" id="err-type"><?= $errors['type'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label for="date_expiration">Date d'expiration *</label>
          <input type="date" id="date_expiration" name="date_expiration"
                 value="<?= htmlspecialchars($_POST['date_expiration'] ?? '') ?>"
                 class="<?= isset($errors['date_expiration']) ? 'is-invalid':'' ?>"/>
          <span class="error-msg" id="err-date_expiration"><?= $errors['date_expiration'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label for="seuil_minimum">Seuil minimum *</label>
          <input type="text" id="seuil_minimum" name="seuil_minimum"
                 value="<?= htmlspecialchars($_POST['seuil_minimum'] ?? '') ?>"
                 class="<?= isset($errors['seuil_minimum']) ? 'is-invalid':'' ?>"
                 placeholder="Ex : 5"/>
          <span class="error-msg" id="err-seuil_minimum"><?= $errors['seuil_minimum'] ?? '' ?></span>
        </div>

        <div class="form-actions">
          <a href="index.php?page=stock" class="btn btn-secondary">← Annuler</a>
          <button type="submit" class="btn btn-primary">💾 Enregistrer le produit</button>
        </div>

      </div>
    </form>
  </div>
</div>

<footer class="site-footer">© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</footer>

<script>
(function () {
  'use strict';
  function showErr(id, msg) {
    var f = document.getElementById(id), s = document.getElementById('err-' + id);
    if (f) f.classList.add('is-invalid');
    if (s) s.textContent = msg;
  }
  function clearErr(id) {
    var f = document.getElementById(id), s = document.getElementById('err-' + id);
    if (f) f.classList.remove('is-invalid');
    if (s) s.textContent = '';
  }
  function vProduits() {
    var v = document.getElementById('produits').value.trim();
    if (!v) { showErr('produits', 'Le nom du produit est obligatoire.'); return false; }
    if (v.length > 255) { showErr('produits', 'Maximum 255 caractères.'); return false; }
    clearErr('produits'); return true;
  }
  function vType() {
    var v = document.getElementById('type').value.trim();
    if (!v) { showErr('type', 'La catégorie est obligatoire.'); return false; }
    if (v.length < 2 || v.length > 50) { showErr('type', 'Entre 2 et 50 caractères.'); return false; }
    clearErr('type'); return true;
  }
  function vDate() {
    var v = document.getElementById('date_expiration').value;
    if (!v) { showErr('date_expiration', "La date d'expiration est obligatoire."); return false; }
    var d = new Date(v), t = new Date(); t.setHours(0,0,0,0);
    if (isNaN(d.getTime())) { showErr('date_expiration', 'Date invalide.'); return false; }
    if (d < t) { showErr('date_expiration', 'La date doit être dans le futur.'); return false; }
    clearErr('date_expiration'); return true;
  }
  function vSeuil() {
    var v = document.getElementById('seuil_minimum').value.trim();
    if (v === '') { showErr('seuil_minimum', 'Le seuil minimum est obligatoire.'); return false; }
    if (isNaN(v) || parseFloat(v) < 0) { showErr('seuil_minimum', 'Nombre positif requis.'); return false; }
    clearErr('seuil_minimum'); return true;
  }
  document.getElementById('produits').addEventListener('blur', vProduits);
  document.getElementById('type').addEventListener('blur', vType);
  document.getElementById('date_expiration').addEventListener('blur', vDate);
  document.getElementById('seuil_minimum').addEventListener('blur', vSeuil);
  document.getElementById('stockForm').addEventListener('submit', function (e) {
    if ([vProduits(), vType(), vDate(), vSeuil()].indexOf(false) !== -1) e.preventDefault();
  });
})();
</script>
</body></html>
