<?php
/**
 * View : /views/frontoffice/planning/index.php
 * Variables : $grid, $jours, $moments, $momentLabels,
 *             $lundi, $lundiPrec, $lundiSuiv, $lundiAuj
 */
$baseUrl = '/' . basename(BASE_PATH);
$lundiFr = date('d/m/Y', strtotime($lundi));
$dimFr   = date('d/m/Y', strtotime($lundi . ' +6 days'));
?>

<header class="page-hero" style="padding:120px 24px 30px;text-align:center;">
  <h1 style="font-family:'Outfit',sans-serif;font-size:clamp(2rem,5vw,3rem);font-weight:900;
             background:linear-gradient(135deg,var(--primary),var(--primary-dark));
             -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
             margin:0 0 10px;">
    📅 Menu de la semaine
  </h1>
  <p style="color:var(--muted);font-size:1.05rem;">
    Du <strong><?= e($lundiFr) ?></strong> au <strong><?= e($dimFr) ?></strong>
  </p>
</header>

<main style="max-width:1200px;margin:0 auto;padding:10px 24px 40px;">

  <!-- Navigation -->
  <nav style="display:flex;justify-content:center;gap:10px;margin-bottom:22px;flex-wrap:wrap;">
    <a class="btn-link btn-export" href="?semaine=<?= e($lundiPrec) ?>">← Semaine précédente</a>
    <?php if ($lundi !== $lundiAuj): ?>
      <a class="btn-link" href="?semaine=<?= e($lundiAuj) ?>">Cette semaine</a>
    <?php endif; ?>
    <a class="btn-link btn-export" href="?semaine=<?= e($lundiSuiv) ?>">Semaine suivante →</a>
  </nav>

  <?php
    // Compter le total de cases remplies
    $total = 0;
    foreach ($grid as $row) foreach ($row as $cell) if ($cell) $total++;
  ?>

  <?php if ($total === 0): ?>
    <div class="empty-state">
      <div class="icon">📅</div>
      <h3>Aucun menu prévu pour cette semaine</h3>
      <p>Le planning n'a pas encore été défini.</p>
    </div>
  <?php else: ?>

    <section class="planning-public-grid">
      <?php foreach ($jours as $j): ?>
        <article class="planning-public-day <?= $j['est_aujourdhui']?'planning-public-today':'' ?>">
          <header class="planning-public-day-header">
            <span class="planning-public-day-name"><?= e($j['jour_nom']) ?></span>
            <span class="planning-public-day-date"><?= e($j['jour_court']) ?></span>
            <?php if ($j['est_aujourdhui']): ?>
              <span class="planning-today-badge">Aujourd'hui</span>
            <?php endif; ?>
          </header>

          <div class="planning-public-meals">
            <?php foreach ($moments as $m): ?>
              <?php $entry = $grid[$m][$j['date']] ?? null; ?>
              <?php if ($entry): ?>
                <a class="planning-public-meal" href="<?= e($baseUrl) ?>/frontoffice/recette.php?id=<?= (int)$entry['id_recette'] ?>">
                  <?php if (!empty($entry['image'])): ?>
                    <span class="planning-public-meal-img" style="background-image:url('<?= e($entry['image']) ?>')"></span>
                  <?php else: ?>
                    <span class="planning-public-meal-img planning-public-meal-noimg">🍽️</span>
                  <?php endif; ?>
                  <span class="planning-public-meal-body">
                    <span class="planning-public-meal-moment"><?= e($momentLabels[$m]) ?></span>
                    <span class="planning-public-meal-name"><?= e($entry['recette_nom']) ?></span>
                    <span class="planning-public-meal-meta">⏱ <?= e($entry['duree']) ?>′</span>
                  </span>
                </a>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

  <?php endif; ?>
</main>
