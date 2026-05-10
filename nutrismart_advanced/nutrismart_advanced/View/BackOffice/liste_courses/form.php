<?php
$isUpdate = !empty($listeData['id']);
$retour   = $retourUrl ?? 'index.php?page=liste_courses';
?>

<div class="form-page-header">
  <h1><?= $isUpdate ? 'Modifier la Liste' : 'Nouvelle Liste de Courses' ?></h1>
  <a href="<?= $retour ?>" class="btn btn-gris">← Retour</a>
</div>

<div class="form-box">
  <form method="POST" id="fListe" novalidate>

    <div class="form-group">
      <label for="articles_a_acheter">Articles a acheter <span style="color:var(--danger)">*</span></label>
      <input type="text" id="articles_a_acheter" name="articles_a_acheter"
             value="<?= htmlspecialchars($listeData['articles_a_acheter'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['articles_a_acheter']) ? 'champ-err' : '' ?>"
             placeholder="Ex : Tomates, lait, pain, fromage" autocomplete="off"/>
      <span class="err-msg" id="e-articles_a_acheter"><?= $errors['articles_a_acheter'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="budget">Budget <span style="color:var(--danger)">*</span> <small>en TND</small></label>
      <input type="text" id="budget" name="budget"
             value="<?= htmlspecialchars((string)($listeData['budget'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['budget']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 120" autocomplete="off"/>
      <span class="err-msg" id="e-budget"><?= $errors['budget'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="date_creation">Date de creation <span style="color:var(--danger)">*</span> <small>(JJ/MM/AAAA)</small></label>
      <input type="text" id="date_creation" name="date_creation"
             value="<?= htmlspecialchars($listeData['date_creation_fmt'] ?? date('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>"
             class="<?= isset($errors['date_creation']) ? 'champ-err' : '' ?>"
             placeholder="Ex : 15/04/2026" autocomplete="off"/>
      <span class="err-msg" id="e-date_creation"><?= $errors['date_creation'] ?? '' ?></span>
    </div>

    <div class="form-group">
      <label for="stock_id">Stock associe <small>(optionnel)</small></label>
      <select id="stock_id" name="stock_id">
        <option value="">— Aucun stock associe —</option>
        <?php foreach ($stocks as $s): ?>
          <option value="<?= $s['id'] ?>" <?= ($listeData['stock_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['produits'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($s['type'], ENT_QUOTES, 'UTF-8') ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-actions">
      <a href="<?= $retour ?>" class="btn btn-gris">Annuler</a>
      <button type="submit" class="btn btn-vert">
        💾 <?= $isUpdate ? 'Enregistrer les modifications' : 'Creer la liste' ?>
      </button>
    </div>

  </form>
</div>

<!-- ══ VALIDATION JS — aucun HTML5 ══ -->
<script>
(function () {
  var form = document.getElementById('fListe');

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

  function vArticles() {
    var v = document.getElementById('articles_a_acheter').value.trim();
    if (!v) { showErr('articles_a_acheter', 'Les articles sont obligatoires.'); return false; }
    if (v.length < 2) { showErr('articles_a_acheter', 'Minimum 2 caracteres.'); return false; }
    clearErr('articles_a_acheter'); return true;
  }

  function vBudget() {
    var v = document.getElementById('budget').value.trim();
    if (!v) { showErr('budget', 'Le budget est obligatoire.'); return false; }
    if (isNaN(v) || parseFloat(v) <= 0) { showErr('budget', 'Nombre positif requis (> 0).'); return false; }
    clearErr('budget'); return true;
  }

  function vDate() {
    var v = document.getElementById('date_creation').value.trim();
    if (!v) { showErr('date_creation', 'La date de creation est obligatoire.'); return false; }
    if (!/^\d{2}\/\d{2}\/\d{4}$/.test(v)) { showErr('date_creation', 'Format invalide. Utilisez JJ/MM/AAAA.'); return false; }
    var p = v.split('/');
    var d = new Date(p[2], p[1] - 1, p[0]);
    if (d.getFullYear() != p[2] || d.getMonth() != p[1] - 1 || d.getDate() != p[0]) {
      showErr('date_creation', 'Date invalide.'); return false;
    }
    clearErr('date_creation'); return true;
  }

  document.getElementById('articles_a_acheter').addEventListener('blur', vArticles);
  document.getElementById('budget').addEventListener('blur', vBudget);
  document.getElementById('date_creation').addEventListener('blur', vDate);

  form.addEventListener('submit', function (e) {
    var ok = [vArticles(), vBudget(), vDate()];
    if (ok.indexOf(false) !== -1) {
      e.preventDefault();
    }
  });
})();
</script>
