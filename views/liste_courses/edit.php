<?php
$pageTitle   = 'Modifier la Liste';
$currentPage = 'liste_courses';
require __DIR__ . '/../shared/front_header.php';
?>

<div class="page-header">
  <p class="ph-kicker">✏ Modification</p>
  <h1>Modifier la <span>Liste #<?= $liste['id'] ?></span></h1>
  <p>Modifiez les informations de votre liste de courses.</p>
</div>

<div class="container">
  <div class="form-card">
    <form method="POST" id="listeForm" novalidate>
      <div class="form-grid">

        <div class="form-group full">
          <label for="articles_a_acheter">Articles à acheter *</label>
          <input type="text" id="articles_a_acheter" name="articles_a_acheter"
                 value="<?= htmlspecialchars($_POST['articles_a_acheter'] ?? $liste['articles_a_acheter']) ?>"
                 class="<?= isset($errors['articles_a_acheter']) ? 'is-invalid':'' ?>"/>
          <span class="error-msg" id="err-articles_a_acheter"><?= $errors['articles_a_acheter'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label for="budget">Budget (TND) *</label>
          <input type="text" id="budget" name="budget"
                 value="<?= htmlspecialchars($_POST['budget'] ?? $liste['budget']) ?>"
                 class="<?= isset($errors['budget']) ? 'is-invalid':'' ?>"/>
          <span class="error-msg" id="err-budget"><?= $errors['budget'] ?? '' ?></span>
        </div>

        <div class="form-group">
          <label for="date_creation">Date de création *</label>
          <input type="date" id="date_creation" name="date_creation"
                 value="<?= htmlspecialchars($_POST['date_creation'] ?? $liste['date_creation']) ?>"
                 class="<?= isset($errors['date_creation']) ? 'is-invalid':'' ?>"/>
          <span class="error-msg" id="err-date_creation"><?= $errors['date_creation'] ?? '' ?></span>
        </div>

        <div class="form-group full">
          <label for="stock_id">Associer à un stock (optionnel)</label>
          <select id="stock_id" name="stock_id">
            <option value="">— Aucun stock —</option>
            <?php foreach ($stocks as $s):
              $cur = isset($_POST['stock_id']) ? $_POST['stock_id'] : $liste['stock_id'];
            ?>
              <option value="<?= $s['id'] ?>" <?= $cur == $s['id'] ? 'selected':'' ?>>
                <?= htmlspecialchars($s['produits']) ?> — <?= htmlspecialchars($s['type']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-actions">
          <a href="index.php?page=liste_courses" class="btn btn-secondary">← Annuler</a>
          <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
        </div>

      </div>
    </form>
  </div>
</div>

<footer class="site-footer">© <?= date('Y') ?> NutriSmart — Eat Smart Live Smart</footer>

<script>
(function () {
  'use strict';
  function showErr(id,msg){var f=document.getElementById(id),s=document.getElementById('err-'+id);if(f)f.classList.add('is-invalid');if(s)s.textContent=msg;}
  function clearErr(id){var f=document.getElementById(id),s=document.getElementById('err-'+id);if(f)f.classList.remove('is-invalid');if(s)s.textContent='';}
  function vArticles(){var v=document.getElementById('articles_a_acheter').value.trim();if(!v){showErr('articles_a_acheter','Obligatoire.');return false;}if(v.length<2||v.length>255){showErr('articles_a_acheter','2-255 car.');return false;}clearErr('articles_a_acheter');return true;}
  function vBudget(){var v=document.getElementById('budget').value.trim();if(!v){showErr('budget','Obligatoire.');return false;}var n=parseFloat(v);if(isNaN(n)||n<=0){showErr('budget','Nombre positif.');return false;}if(n>99999){showErr('budget','Max 99 999.');return false;}clearErr('budget');return true;}
  function vDate(){var v=document.getElementById('date_creation').value;if(!v){showErr('date_creation','Obligatoire.');return false;}if(isNaN(new Date(v).getTime())){showErr('date_creation','Date invalide.');return false;}clearErr('date_creation');return true;}
  document.getElementById('articles_a_acheter').addEventListener('blur',vArticles);
  document.getElementById('budget').addEventListener('blur',vBudget);
  document.getElementById('date_creation').addEventListener('blur',vDate);
  document.getElementById('listeForm').addEventListener('submit',function(e){if([vArticles(),vBudget(),vDate()].indexOf(false)!==-1)e.preventDefault();});
})();
</script>
</body></html>
