<?php
/**
 * View : /views/frontoffice/recettes/index.php
 * Variables : $result, $opts, $ingredients
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<header class="page-hero" style="padding:120px 24px 40px;text-align:center;">
  <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:900;
             background:linear-gradient(135deg,var(--primary),var(--primary-dark));
             -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
             margin:0 0 12px;">
    Toutes nos recettes
  </h1>
  <p style="color:var(--muted);font-size:1.1rem;max-width:620px;margin:0 auto;">
    Explorez notre collection de recettes saines, conçues pour bien manger sans complications.
  </p>
</header>

<main style="max-width:1200px;margin:0 auto;padding:20px 24px 40px;">

  <!-- Filtres -->
  <form class="toolbar" method="get" action="recettes.php">
    <input type="text" name="q" placeholder="🔍 Rechercher une recette..."
           value="<?= e($opts['q']) ?>" data-live-search>
    <select name="niveau" data-live-filter>
      <option value="">Tous niveaux</option>
      <?php foreach (Recette::NIVEAUX as $n): ?>
        <option value="<?= e($n) ?>" <?= $opts['niveau']===$n?'selected':'' ?>><?= e(ucfirst($n)) ?></option>
      <?php endforeach; ?>
    </select>
    <input type="number" name="duree_max" min="0" placeholder="Max minutes" value="<?= e($opts['duree_max']) ?>">
    <select name="ingredient_id" data-live-filter>
      <option value="">Tous ingrédients</option>
      <?php foreach ($ingredients as $i): ?>
        <option value="<?= (int)$i['id'] ?>" <?= (string)$opts['ingredient_id']===(string)$i['id']?'selected':'' ?>>
          <?= e($i['nom']) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <a href="recettes.php" class="btn-reset">Réinit.</a>
  </form>

  <?php if ($result['total'] === 0): ?>
    <div class="empty-state">
      <div class="icon">🍽️</div>
      <h3>Aucune recette trouvée</h3>
      <p>Essayez d'élargir vos critères de recherche.</p>
    </div>
  <?php else: ?>
    <p style="color:var(--muted);margin:0 0 18px;font-weight:600;">
      <?= e($result['total']) ?> recette<?= $result['total']>1?'s':'' ?> trouvée<?= $result['total']>1?'s':'' ?>
    </p>

    <section class="recipe-grid">
      <?php foreach ($result['rows'] as $r): ?>
        <article class="recipe-card">
          <div class="recipe-card-image"
               style="<?= $r['image']?'background-image:url(\''.e($r['image']).'\')':'' ?>">
            <?php if (empty($r['image'])): ?>
              <div style="display:grid;place-items:center;height:100%;font-size:50px;">🍽️</div>
            <?php endif; ?>
          </div>
          <div class="recipe-card-body">
            <h3><?= e($r['nom']) ?></h3>
            <p><?= e(mb_substr($r['description'], 0, 110)) ?>…</p>
            <div class="recipe-meta">
              <span class="chip chip-time">⏱ <?= e($r['duree']) ?> min</span>
              <?php $cls = $r['niveau']==='facile'?'chip-easy':($r['niveau']==='moyen'?'chip-medium':'chip-hard'); ?>
              <span class="chip <?= $cls ?>"><?= e(ucfirst($r['niveau'])) ?></span>
            </div>
          </div>
          <div class="recipe-card-footer">
            <span style="font-size:12px;color:var(--muted);">
              <?= e(date('d/m/Y', strtotime($r['date_creation']))) ?>
            </span>
            <a class="btn-link" href="recette.php?id=<?= (int)$r['id'] ?>">Voir →</a>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <?php if ($result['pages'] > 1): ?>
      <nav class="pagination">
        <?php
          $base = $_GET; unset($base['page']);
          $qs   = http_build_query($base);
          $cur  = $result['page']; $tot = $result['pages'];
        ?>
        <?php if ($cur > 1): ?>
          <a href="?<?= e($qs) ?>&page=<?= $cur-1 ?>">‹ Précédent</a>
        <?php else: ?><span class="disabled">‹ Précédent</span><?php endif; ?>

        <?php for ($i = 1; $i <= $tot; $i++): ?>
          <?php if ($i == $cur): ?>
            <span class="current"><?= $i ?></span>
          <?php else: ?>
            <a href="?<?= e($qs) ?>&page=<?= $i ?>"><?= $i ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($cur < $tot): ?>
          <a href="?<?= e($qs) ?>&page=<?= $cur+1 ?>">Suivant ›</a>
        <?php else: ?><span class="disabled">Suivant ›</span><?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</main>
