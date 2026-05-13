<?php
$isUpdate = !empty($stockData['id']);
$retour   = $retourUrl ?? 'index.php?page=stock';
?>

<div class="form-page-header">
  <h1><?= $isUpdate ? 'Modifier le Stock' : 'Ajouter un Stock' ?></h1>
  <a href="<?= $retour ?>" class="btn btn-gris">← Retour</a>
</div>

<div class="form-box">
  <form method="POST" id="fStock" novalidate>

    <div class="form-group">
      <label for="produits">Nom du produit <span style="color:var(--danger)">*</span></label>
      <input type="text" id="produits" name="produits"
             value="<?= htmlspecialchars($stockData['produits'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['produits']) ? 'champ-err' : '' ?>"
             placeholder="Ex : Tomates cerises, Lait demi-ecreme" autocomplete="off"/>
      <span class="err-msg" id="e-produits"><?= $errors['produits'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="type">Categorie <span style="color:var(--danger)">*</span></label>
      <input type="text" id="type" name="type"
             value="<?= htmlspecialchars($stockData['type'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['type']) ? 'champ-err' : '' ?>"
             placeholder="Ex : Legumes, Laitier, Viande" autocomplete="off"/>
      <span class="err-msg" id="e-type"><?= $errors['type'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="date_expiration">Date d'expiration <span style="color:var(--danger)">*</span> <small>(JJ/MM/AAAA)</small></label>
      <input type="text" id="date_expiration" name="date_expiration"
             value="<?= htmlspecialchars($stockData['date_expiration_fmt'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['date_expiration']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 31/12/2026" autocomplete="off"/>
      <span class="err-msg" id="e-date_expiration"><?= $errors['date_expiration'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="seuil_minimum">Seuil minimum <span style="color:var(--danger)">*</span> <small>en unites</small></label>
      <input type="text" id="seuil_minimum" name="seuil_minimum"
             value="<?= htmlspecialchars((string)($stockData['seuil_minimum'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['seuil_minimum']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 5" autocomplete="off"/>
      <span class="err-msg" id="e-seuil_minimum"><?= $errors['seuil_minimum'] ?? '' ?></span>
    </div>

    <div class="form-actions">
      <a href="<?= $retour ?>" class="btn btn-gris">Annuler</a>
      <button type="submit" class="btn btn-vert">
        💾 <?= $isUpdate ? 'Enregistrer les modifications' : 'Ajouter le stock' ?>
      </button>
    </div>

  </form>
</div>

<!-- ══ VALIDATION JS — aucun HTML5 ══ -->
<script>
(function () {
  var form = document.getElementById('fStock');

  function showErr(id, msg) {
    var f = document.getElementById(id);
    var s = document.getElementById('e-' + id);
    if (f) { f.classList.add('champ-err'); }
    if (s) { s.textContent = msg; }
  }

  function clearErr(id) {
    var f = document.getElementById(id);
    var s = document.getElementById('e-' + id);
    if (f) { f.classList.remove('champ-err'); }
    if (s) { s.textContent = ''; }
  }

  function vProduits() {
    var v = document.getElementById('produits').value.trim();
    if (!v) { showErr('produits', 'Le nom du produit est obligatoire.'); return false; }
    if (v.length < 2) { showErr('produits', 'Minimum 2 caracteres.'); return false; }
    clearErr('produits'); return true;
  }

  function vType() {
    var v = document.getElementById('type').value.trim();
    if (!v) { showErr('type', 'La categorie est obligatoire.'); return false; }
    clearErr('type'); return true;
  }

  function vDate() {
    var v = document.getElementById('date_expiration').value.trim();
    if (!v) { showErr('date_expiration', "La date d'expiration est obligatoire."); return false; }
    if (!/^\d{2}\/\d{2}\/\d{4}$/.test(v)) { showErr('date_expiration', 'Format invalide. Utilisez JJ/MM/AAAA.'); return false; }
    var p = v.split('/');
    var d = new Date(p[2], p[1] - 1, p[0]);
    if (d.getFullYear() != p[2] || d.getMonth() != p[1] - 1 || d.getDate() != p[0]) {
      showErr('date_expiration', 'Date invalide.'); return false;
    }
    clearErr('date_expiration'); return true;
  }

  function vSeuil() {
    var v = document.getElementById('seuil_minimum').value.trim();
    if (v === '') { showErr('seuil_minimum', 'Le seuil minimum est obligatoire.'); return false; }
    if (isNaN(v) || parseFloat(v) < 0) { showErr('seuil_minimum', 'Nombre positif requis.'); return false; }
    clearErr('seuil_minimum'); return true;
  }

  // Validation en temps réel (blur)
  document.getElementById('produits').addEventListener('blur', vProduits);
  document.getElementById('type').addEventListener('blur', vType);
  document.getElementById('date_expiration').addEventListener('blur', vDate);
  document.getElementById('seuil_minimum').addEventListener('blur', vSeuil);

  // Validation à la soumission
  form.addEventListener('submit', function (e) {
    var ok = [vProduits(), vType(), vDate(), vSeuil()];
    if (ok.indexOf(false) !== -1) {
      e.preventDefault();
    }
  });
})();
</script>
