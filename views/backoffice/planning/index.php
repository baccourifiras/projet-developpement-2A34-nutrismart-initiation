<?php
/**
 * View : /views/backoffice/planning/index.php
 *
 * Variables : $grid, $jours, $moments, $momentLabels,
 *             $lundi, $lundiPrec, $lundiSuiv, $lundiAuj,
 *             $recettes, $stats, $errors
 */
$baseUrl = '/' . basename(BASE_PATH);
$lundiFr = date('d/m/Y', strtotime($lundi));
$dimFr   = date('d/m/Y', strtotime($lundi . ' +6 days'));
?>

<div class="page-head">
  <div>
    <p class="breadcrumb">Administration · Planning</p>
    <h2>📅 Planning de la semaine</h2>
    <p style="margin:6px 0 0;color:var(--muted);font-weight:600;">
      Du <?= e($lundiFr) ?> au <?= e($dimFr) ?>
    </p>
  </div>
  <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="?semaine=<?= e($lundiPrec) ?>" class="btn-icon btn-view">← Sem. préc.</a>
    <?php if ($lundi !== $lundiAuj): ?>
      <a href="?semaine=<?= e($lundiAuj) ?>" class="btn-icon btn-edit">Aujourd'hui</a>
    <?php endif; ?>
    <a href="?semaine=<?= e($lundiSuiv) ?>" class="btn-icon btn-view">Sem. suiv. →</a>
    <a href="planning_besoins.php?semaine=<?= e($lundi) ?>" class="primary-btn">🛒 Besoins ingrédients</a>
  </div>
</div>

<!-- Stats de la semaine -->
<section class="dashboard-grid" style="grid-template-columns:repeat(3,1fr);">
  <div class="widget">
    <span class="icon">🍽️</span>
    <p class="label">Repas planifiés</p>
    <p class="value"><?= e($stats['total_repas']) ?> / 28</p>
    <p class="sub">Sur 7 jours × 4 moments</p>
  </div>
  <div class="widget <?= $stats['manquants']>0?'danger':'success' ?>">
    <span class="icon"><?= $stats['manquants']>0?'⚠️':'✅' ?></span>
    <p class="label">Ingrédients manquants</p>
    <p class="value"><?= e($stats['manquants']) ?></p>
  </div>
  <div class="widget <?= $stats['repetitions']>0?'warn':'success' ?>">
    <span class="icon">🔁</span>
    <p class="label">Recettes répétées</p>
    <p class="value"><?= e($stats['repetitions']) ?></p>
  </div>
</section>

<!-- Action duplication -->
<form method="post" action="planning_dupliquer.php" style="margin-bottom:16px;"
      data-confirm="Dupliquer le planning de cette semaine vers la semaine suivante ? Le contenu existant de la cible sera remplacé.">
  <?= csrf_field() ?>
  <input type="hidden" name="source" value="<?= e($lundi) ?>">
  <input type="hidden" name="cible"  value="<?= e($lundiSuiv) ?>">
  <button type="submit" class="btn-icon btn-edit">📋 Dupliquer vers semaine suivante</button>
</form>

<!-- Erreurs de validation -->
<?php if (!empty($errors)): ?>
  <div class="flash flash-error">
    Erreurs : <?= implode(' · ', array_map('e', $errors)) ?>
  </div>
<?php endif; ?>

<!-- Grille calendrier -->
<section class="planning-grid-wrap">
  <table class="planning-grid">
    <thead>
      <tr>
        <th class="planning-corner"></th>
        <?php foreach ($jours as $j): ?>
          <th class="<?= $j['est_aujourdhui']?'planning-today':'' ?>">
            <span class="planning-day-name"><?= e($j['jour_nom']) ?></span>
            <span class="planning-day-date"><?= e($j['jour_court']) ?></span>
            <?php if ($j['est_aujourdhui']): ?>
              <span class="planning-today-badge">Aujourd'hui</span>
            <?php endif; ?>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($moments as $m): ?>
        <tr>
          <th class="planning-moment-label"><?= e($momentLabels[$m]) ?></th>
          <?php foreach ($jours as $j): ?>
            <?php $entry = $grid[$m][$j['date']] ?? null; ?>
            <td class="planning-cell <?= $entry?'planning-cell-filled':'planning-cell-empty' ?> <?= $j['est_aujourdhui']?'planning-cell-today':'' ?>"
                data-date="<?= e($j['date']) ?>"
                data-moment="<?= e($m) ?>"
                data-moment-label="<?= e($momentLabels[$m]) ?>"
                data-jour-label="<?= e($j['jour_nom']) ?> <?= e($j['jour_court']) ?>">
              <?php if ($entry): ?>
                <div class="planning-card">
                  <?php if (!empty($entry['image'])): ?>
                    <div class="planning-card-img" style="background-image:url('<?= e($entry['image']) ?>')"></div>
                  <?php endif; ?>
                  <div class="planning-card-body">
                    <h4>
                      <a href="recette_show.php?id=<?= (int)$entry['id_recette'] ?>" target="_blank">
                        <?= e($entry['recette_nom']) ?>
                      </a>
                    </h4>
                    <p class="planning-card-meta">
                      <span>⏱ <?= e($entry['duree']) ?>′</span>
                      <span>👥 <?= e($entry['nb_personnes']) ?></span>
                    </p>
                    <?php if (!empty($entry['notes'])): ?>
                      <p class="planning-card-notes"><?= e($entry['notes']) ?></p>
                    <?php endif; ?>
                  </div>
                  <div class="planning-card-actions">
                    <button type="button" class="btn-icon btn-edit planning-edit-btn" title="Remplacer">✏</button>
                    <form method="post" action="planning_supprimer.php" style="display:inline"
                          data-confirm="Retirer cette recette du planning ?">
                      <?= csrf_field() ?>
                      <input type="hidden" name="id"      value="<?= (int)$entry['id'] ?>">
                      <input type="hidden" name="semaine" value="<?= e($lundi) ?>">
                      <button class="btn-icon btn-delete" type="submit" title="Retirer">🗑</button>
                    </form>
                  </div>
                </div>
              <?php else: ?>
                <button type="button" class="planning-cell-add">
                  <span class="planning-add-icon">＋</span>
                  <span class="planning-add-text">Assigner</span>
                </button>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<!-- Modal d'assignation -->
<div id="planning-modal" class="planning-modal" style="display:none;">
  <div class="planning-modal-backdrop"></div>
  <div class="planning-modal-box">
    <div class="planning-modal-head">
      <h3 id="planning-modal-title">Assigner une recette</h3>
      <button type="button" class="planning-modal-close" aria-label="Fermer">×</button>
    </div>
    <form method="post" action="planning_assigner.php">
      <?= csrf_field() ?>
      <input type="hidden" name="date_jour"  id="planning-date">
      <input type="hidden" name="moment"     id="planning-moment">
      <input type="hidden" name="semaine"    value="<?= e($lundi) ?>">

      <div class="form-grid two-columns">
        <div class="full-width">
          <label for="planning-recette">Recette *</label>
          <select id="planning-recette" name="id_recette" required>
            <option value="">— Sélectionner une recette —</option>
            <?php foreach ($recettes as $r): ?>
              <option value="<?= (int)$r['id'] ?>"><?= e($r['nom']) ?> (<?= e($r['duree']) ?>′ — <?= e(ucfirst($r['niveau'])) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="planning-nb">Nombre de personnes</label>
          <input id="planning-nb" name="nb_personnes" type="number" min="1" max="50" value="2" required>
        </div>
        <div>
          <label for="planning-notes">Notes (facultatif)</label>
          <input id="planning-notes" name="notes" type="text" maxlength="255" placeholder="Plat principal, léger, etc.">
        </div>
      </div>

      <button type="submit" class="primary-btn" style="margin-top:14px;">Assigner</button>
    </form>
  </div>
</div>
