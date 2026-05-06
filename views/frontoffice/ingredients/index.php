<?php
/**
 * View : /views/frontoffice/ingredients/index.php
 * Variables : $result, $opts, $categories
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<header class="page-hero" style="padding:120px 24px 40px;text-align:center;">
  <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(2rem,5vw,3.5rem);font-weight:900;
             background:linear-gradient(135deg,var(--primary),var(--primary-dark));
             -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
             margin:0 0 12px;">
    Nos ingrédients
  </h1>
  <p style="color:var(--muted);font-size:1.1rem;max-width:620px;margin:0 auto;">
    Découvrez tous les ingrédients utilisés dans nos recettes, classés par catégorie.
  </p>
</header>

<main style="max-width:1200px;margin:0 auto;padding:20px 24px 40px;">

  <form class="toolbar" method="get" action="ingredients.php"
        style="grid-template-columns:2fr 1fr auto;">
    <input type="text" name="q" placeholder="🔍 Rechercher un ingrédient..."
           value="<?= e($opts['q']) ?>" data-live-search>
    <select name="categorie" data-live-filter>
      <option value="">Toutes catégories</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= e($c) ?>" <?= $opts['categorie']===$c?'selected':'' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
    <a href="ingredients.php" class="btn-reset">Réinit.</a>
  </form>

  <?php if ($result['total'] === 0): ?>
    <div class="empty-state">
      <div class="icon">🥕</div>
      <h3>Aucun ingrédient trouvé</h3>
    </div>
  <?php else: ?>
    <p style="color:var(--muted);margin:0 0 18px;font-weight:600;">
      <?= e($result['total']) ?> ingrédient<?= $result['total']>1?'s':'' ?>
    </p>

    <section style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
      <?php foreach ($result['rows'] as $i): ?>
        <a href="ingredient.php?id=<?= (int)$i['id'] ?>" class="recipe-card"
           style="text-decoration:none;color:inherit;padding:18px;display:block;">
          <h3 style="margin:0 0 8px;font-family:'Outfit',sans-serif;font-size:1.1rem;color:var(--text);">
            <?= e($i['nom']) ?>
          </h3>
          <span class="chip chip-ingredients" style="margin-bottom:10px;display:inline-block;">
            <?= e($i['categorie']) ?>
          </span>
          <p style="margin:8px 0 0;font-size:13px;color:var(--muted);">
            Stock : <strong style="color:var(--primary-dark)">
              <?= e(rtrim(rtrim((string)$i['quantite_stock'],'0'),'.')) ?> <?= e($i['unite']) ?>
            </strong>
          </p>
        </a>
      <?php endforeach; ?>
    </section>

    <?php if ($result['pages'] > 1): ?>
      <nav class="pagination">
        <?php
          $base = $_GET; unset($base['page']);
          $qs = http_build_query($base);
          $cur = $result['page']; $tot = $result['pages'];
        ?>
        <?php if ($cur > 1): ?>
          <a href="?<?= e($qs) ?>&page=<?= $cur-1 ?>">‹</a>
        <?php else: ?><span class="disabled">‹</span><?php endif; ?>
        <?php for ($i = 1; $i <= $tot; $i++): ?>
          <?php if ($i == $cur): ?><span class="current"><?= $i ?></span>
          <?php else: ?><a href="?<?= e($qs) ?>&page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($cur < $tot): ?>
          <a href="?<?= e($qs) ?>&page=<?= $cur+1 ?>">›</a>
        <?php else: ?><span class="disabled">›</span><?php endif; ?>
      </nav>
    <?php endif; ?>
  <?php endif; ?>
</main>
