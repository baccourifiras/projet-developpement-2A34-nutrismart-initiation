<?php
/**
 * ============================================================
 *  NutriSmart - Controller Planning (Back office)
 *  /controllers/backoffice/PlanningController.php
 * ============================================================
 */

class PlanningController extends Controller
{
    private PlanningMenu $model;
    private Recette $recetteModel;

    public function __construct()
    {
        $this->model        = new PlanningMenu();
        $this->recetteModel = new Recette();
    }

    /** Vue hebdomadaire principale. */
    public function index(): void
    {
        $semaineParam = $_GET['semaine'] ?? null;
        $lundi = PlanningMenu::lundiDeLaSemaine($semaineParam);
        $jours = PlanningMenu::joursDeLaSemaine($lundi);
        $grid  = $this->model->getSemaine($lundi);

        // Navigation
        $lundiPrec = date('Y-m-d', strtotime($lundi . ' -7 days'));
        $lundiSuiv = date('Y-m-d', strtotime($lundi . ' +7 days'));
        $lundiAuj  = PlanningMenu::lundiDeLaSemaine();

        // Liste des recettes pour les sélecteurs
        $recettes = $this->recetteModel->all([]);

        // Métriques pour le bandeau
        $stats = [
            'total_repas'  => $this->model->countSemaine($lundi),
            'manquants'    => count($this->model->ingredientsManquants($lundi)),
            'repetitions'  => count($this->model->recettesRepetees($lundi)),
        ];

        $this->render('backoffice/planning/index', [
            'pageTitle'  => 'Planning de menus',
            'grid'       => $grid,
            'jours'      => $jours,
            'moments'    => PlanningMenu::MOMENTS,
            'momentLabels' => PlanningMenu::MOMENT_LABELS,
            'lundi'      => $lundi,
            'lundiPrec'  => $lundiPrec,
            'lundiSuiv'  => $lundiSuiv,
            'lundiAuj'   => $lundiAuj,
            'recettes'   => $recettes,
            'stats'      => $stats,
            'errors'     => errors_get(),
        ], 'back');
        old_clear();
    }

    /** Page liste agrégée des besoins ingrédients pour une semaine. */
    public function besoins(): void
    {
        $lundi = PlanningMenu::lundiDeLaSemaine($_GET['semaine'] ?? null);
        $besoins = $this->model->besoinsIngredients($lundi);
        $manquants = $this->model->ingredientsManquants($lundi);

        $this->render('backoffice/planning/besoins', [
            'pageTitle' => 'Besoins ingrédients - Semaine',
            'lundi'     => $lundi,
            'besoins'   => $besoins,
            'manquants' => $manquants,
        ], 'back');
    }

    /** POST : assigner une recette à une case (création OU remplacement). */
    public function assigner(): void
    {
        csrf_check();

        $date    = $_POST['date_jour']    ?? '';
        $moment  = $_POST['moment']       ?? '';
        $idRec   = (int)($_POST['id_recette'] ?? 0);
        $nbPers  = max(1, (int)($_POST['nb_personnes'] ?? 2));
        $notes   = trim($_POST['notes']   ?? '');
        $semaine = $_POST['semaine']      ?? '';

        // Validation
        $errors = [];
        if (!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $errors['date_jour'] = 'Date invalide.';
        }
        if (!in_array($moment, PlanningMenu::MOMENTS, true)) {
            $errors['moment'] = 'Moment invalide.';
        }
        if ($idRec <= 0 || !$this->recetteModel->find($idRec)) {
            $errors['id_recette'] = 'Recette introuvable.';
        }
        if ($nbPers < 1 || $nbPers > 50) {
            $errors['nb_personnes'] = 'Nombre de personnes entre 1 et 50.';
        }

        if (!empty($errors)) {
            errors_set($errors);
            old_set($_POST);
            redirect('planning.php?semaine=' . urlencode($semaine));
        }

        $this->model->assigner($date, $moment, $idRec, $nbPers, $notes ?: null);
        flash('success', 'Recette assignée au planning.');
        redirect('planning.php?semaine=' . urlencode($semaine));
    }

    /** POST : retirer une assignation. */
    public function supprimer(): void
    {
        csrf_check();
        $id      = (int)($_POST['id'] ?? 0);
        $semaine = $_POST['semaine']    ?? '';

        if ($this->model->supprimer($id)) {
            flash('success', 'Assignation supprimée.');
        } else {
            flash('error', 'Suppression impossible.');
        }
        redirect('planning.php?semaine=' . urlencode($semaine));
    }

    /** POST : dupliquer la semaine courante vers la suivante. */
    public function dupliquer(): void
    {
        csrf_check();
        $source = $_POST['source'] ?? PlanningMenu::lundiDeLaSemaine();
        $cible  = $_POST['cible']  ?? date('Y-m-d', strtotime($source . ' +7 days'));

        $n = $this->model->dupliquerSemaine($source, $cible);
        flash('success', "$n assignation(s) dupliquée(s) vers la semaine du " . date('d/m/Y', strtotime($cible)) . '.');
        redirect('planning.php?semaine=' . urlencode($cible));
    }
}
