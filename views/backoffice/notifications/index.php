<?php
/**
 * View : /views/backoffice/notifications/index.php
 * Variables : $notifications, $filtre, $seuil, $totalNonLues
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Administration · Alertes</p>
    <h2>🔔 Notifications</h2>
  </div>
  <?php if ($totalNonLues > 0): ?>
    <form method="post" action="notification_mark_all.php" style="display:inline;"
          data-confirm="Marquer toutes les notifications comme lues ?">
      <?= csrf_field() ?>
      <button type="submit" class="primary-btn">✓ Tout marquer comme lu</button>
    </form>
  <?php endif; ?>
</div>

<!-- Widget récap -->
<section class="dashboard-grid" style="grid-template-columns:repeat(3, 1fr);">
  <div class="widget danger">
    <span class="icon">🔔</span>
    <p class="label">Non lues</p>
    <p class="value"><?= e($totalNonLues) ?></p>
  </div>
  <div class="widget">
    <span class="icon">📊</span>
    <p class="label">Seuil critique</p>
    <p class="value"><?= e($seuil) ?></p>
    <p class="sub">Stock ≤ <?= e($seuil) ?> = alerte</p>
  </div>
  <div class="widget success">
    <span class="icon">📋</span>
    <p class="label">Total affiché</p>
    <p class="value"><?= count($notifications) ?></p>
  </div>
</section>

<!-- Filtres -->
<div class="admin-toolbar" style="grid-template-columns:auto auto 1fr;">
  <a href="?filtre=toutes" class="btn-link <?= $filtre==='toutes'?'':'btn-export' ?>">
    Toutes
  </a>
  <a href="?filtre=non_lues" class="btn-link <?= $filtre==='non_lues'?'':'btn-export' ?>">
    Non lues uniquement
  </a>
</div>

<?php if (empty($notifications)): ?>
  <div class="empty-state">
    <div class="icon">🎉</div>
    <h3>Tout va bien !</h3>
    <p>Aucune notification <?= $filtre==='non_lues'?'non lue ':'' ?>pour le moment.</p>
  </div>
<?php else: ?>
  <section class="panel" style="padding:0;">
    <ul style="list-style:none;margin:0;padding:0;">
      <?php foreach ($notifications as $n): ?>
        <?php
          // Icône selon le type
          $icons = [
            'stock_bas'                       => '📦',
            'planning_jour_vide'              => '📅',
            'planning_semaine_vide'           => '🗓️',
            'planning_repetition'             => '🔁',
            'planning_ingredients_manquants'  => '🛒',
          ];
          $type = $n['type'] ?? 'stock_bas';
          $iconSymbol = $n['est_lue'] ? '✅' : ($icons[$type] ?? '🔔');
        ?>
        <li class="notif-item <?= $n['est_lue']?'notif-lue':'notif-non-lue' ?>">
          <div class="notif-icon">
            <?= $iconSymbol ?>
          </div>
          <div class="notif-body">
            <p class="notif-message"><?= e($n['message']) ?></p>
            <p class="notif-meta">
              <?php if (!empty($n['ingredient_nom'])): ?>
                <a href="ingredient_show.php?id=<?= (int)$n['id_ingredient'] ?>">
                  Voir l'ingrédient →
                </a>
                ·
              <?php elseif (!empty($n['lien'])): ?>
                <a href="<?= e($n['lien']) ?>">Ouvrir →</a> ·
              <?php endif; ?>
              <?= e(date('d/m/Y à H:i', strtotime($n['date_creation']))) ?>
            </p>
          </div>
          <div class="notif-actions">
            <?php if (!$n['est_lue']): ?>
              <form method="post" action="notification_mark.php?id=<?= (int)$n['id'] ?>" style="display:inline;">
                <?= csrf_field() ?>
                <button class="btn-icon btn-edit" type="submit" title="Marquer comme lue">✓</button>
              </form>
            <?php endif; ?>
            <form method="post" action="notification_delete.php?id=<?= (int)$n['id'] ?>" style="display:inline;"
                  data-confirm="Supprimer cette notification ?">
              <?= csrf_field() ?>
              <button class="btn-icon btn-delete" type="submit" title="Supprimer">🗑</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>
