<?php
/**
 * View : /views/backoffice/planning/besoins.php
 * Variables : $lundi, $besoins, $manquants
 */
$baseUrl = '/' . basename(BASE_PATH);
$lundiFr = date('d/m/Y', strtotime($lundi));
$dimFr   = date('d/m/Y', strtotime($lundi . ' +6 days'));
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Planning · Besoins ingrédients</p>
    <h2>🛒 Liste de courses estimée</h2>
    <p style="margin:6px 0 0;color:var(--muted);font-weight:600;">
      Semaine du <?= e($lundiFr) ?> au <?= e($dimFr) ?>
    </p>
  </div>
  <a href="planning.php?semaine=<?= e($lundi) ?>" class="btn-icon btn-view">← Retour au planning</a>
</div>

<?php if (empty($besoins)): ?>
  <div class="empty-state">
    <div class="icon">📋</div>
    <h3>Aucun ingrédient requis</h3>
    <p>Le planning de la semaine est vide ou les recettes assignées n'ont pas d'ingrédients liés.</p>
  </div>
<?php else: ?>
  <?php if (!empty($manquants)): ?>
    <div class="flash flash-error" style="margin-bottom:18px">
      ⚠️ <strong><?= count($manquants) ?> ingrédient(s) en stock insuffisant</strong> pour ce planning.
      Les lignes concernées sont marquées en rouge.
    </div>
  <?php endif; ?>

  <section class="panel" style="padding:0;">
    <div class="table-wrapper">
      <table class="table">
        <thead>
          <tr>
            <th>Ingrédient</th>
            <th>Quantité nécessaire</th>
            <th>Stock disponible</th>
            <th>Statut</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $idsManquants = array_column($manquants, 'id');
            foreach ($besoins as $b):
              $estManquant = in_array((int)$b['id'], array_map('intval', $idsManquants), true);
          ?>
            <tr style="<?= $estManquant?'background:rgba(239,68,68,.05)':'' ?>">
              <td><strong><?= e($b['nom']) ?></strong></td>
              <td><?= e(rtrim(rtrim((string)$b['quantite_necessaire'], '0'), '.')) ?> <?= e($b['unite_recette']) ?></td>
              <td><?= e(rtrim(rtrim((string)$b['quantite_stock'], '0'), '.')) ?> <?= e($b['unite_stock']) ?></td>
              <td>
                <?php if ($estManquant): ?>
                  <span class="chip chip-hard">⚠️ Manquant</span>
                <?php elseif ($b['unite_stock'] !== $b['unite_recette']): ?>
                  <span class="chip" style="background:rgba(124,58,237,.12);color:#5b21b6;">≈ Unités différentes</span>
                <?php else: ?>
                  <span class="chip chip-easy">✓ OK</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php endif; ?>
