<?php
/**
 * View : /views/backoffice/dashboard.php
 * Variables : $statsRecettes, $statsIngredients
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Administration</p>
    <h2>Tableau de bord</h2>
  </div>
</div>

<section class="dashboard-grid">
  <div class="widget">
    <span class="icon">🍽️</span>
    <p class="label">Recettes</p>
    <p class="value"><?= e($statsRecettes['total']) ?></p>
    <p class="sub">Durée moyenne : <?= e($statsRecettes['duree_moy']) ?> min</p>
  </div>
  <div class="widget success">
    <span class="icon">✅</span>
    <p class="label">Recettes faciles</p>
    <p class="value"><?= e($statsRecettes['facile']) ?></p>
  </div>
  <div class="widget">
    <span class="icon">🥕</span>
    <p class="label">Ingrédients</p>
    <p class="value"><?= e($statsIngredients['total']) ?></p>
  </div>
  <div class="widget danger">
    <span class="icon">⚠️</span>
    <p class="label">Ingrédients en rupture</p>
    <p class="value"><?= e($statsIngredients['en_rupture']) ?></p>
  </div>
</section>

<section style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
  <div class="panel">
    <h3 style="margin:0 0 14px;font-family:'Outfit',sans-serif;">Répartition des niveaux</h3>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <?php
        $total = max(1, $statsRecettes['total']);
        $bars = [
          'Faciles'    => [$statsRecettes['facile'],    'linear-gradient(90deg,#10b981,#059669)'],
          'Moyens'     => [$statsRecettes['moyen'],     'linear-gradient(90deg,#f59e0b,#b45309)'],
          'Difficiles' => [$statsRecettes['difficile'], 'linear-gradient(90deg,#ef4444,#b91c1c)'],
        ];
        foreach ($bars as $label => $data):
          $val = $data[0]; $bg = $data[1];
          $pct = round(($val / $total) * 100);
      ?>
        <div>
          <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-weight:700;font-size:13px;">
            <span><?= e($label) ?></span><span><?= e($val) ?> (<?= $pct ?>%)</span>
          </div>
          <div style="background:#f0f4f1;border-radius:8px;height:10px;overflow:hidden;">
            <div style="height:100%;width:<?= $pct ?>%;background:<?= $bg ?>;
                        transition:width .8s ease;border-radius:8px;"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="panel">
    <h3 style="margin:0 0 14px;font-family:'Outfit',sans-serif;">Catégories d'ingrédients</h3>
    <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:8px;">
      <?php foreach ($statsIngredients['par_categorie'] as $c): ?>
        <li style="display:flex;justify-content:space-between;
                   padding:9px 12px;background:#fbfffc;border:1px solid var(--border);
                   border-radius:10px;">
          <span style="font-weight:700;"><?= e($c['categorie']) ?></span>
          <span class="chip chip-ingredients"><?= e($c['n']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section style="margin-top:24px;display:flex;gap:14px;flex-wrap:wrap;">
  <a class="primary-btn" href="<?= e($baseUrl) ?>/backoffice/recettes.php">→ Gérer les recettes</a>
  <a class="primary-btn" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);"
     href="<?= e($baseUrl) ?>/backoffice/ingredients.php">→ Gérer les ingrédients</a>
  <a class="primary-btn" style="background:linear-gradient(135deg,#ef4444,#b91c1c);"
     href="<?= e($baseUrl) ?>/backoffice/notifications.php">🔔 Voir les notifications</a>
  <a class="primary-btn" style="background:linear-gradient(135deg,#f59e0b,#d97706);"
     href="<?= e($baseUrl) ?>/backoffice/planning.php">📅 Planning des menus</a>
</section>
