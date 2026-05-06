<?php
/**
 * ============================================================
 *  NutriSmart - Controller Planning (Front office)
 *  /controllers/frontoffice/PlanningController.php
 *
 *  Affiche le menu de la semaine en lecture seule pour les visiteurs.
 * ============================================================
 */

class PlanningController extends Controller
{
    private PlanningMenu $model;

    public function __construct()
    {
        $this->model = new PlanningMenu();
    }

    public function index(): void
    {
        $semaineParam = $_GET['semaine'] ?? null;
        $lundi = PlanningMenu::lundiDeLaSemaine($semaineParam);

        $jours = PlanningMenu::joursDeLaSemaine($lundi);
        $grid  = $this->model->getSemaine($lundi);

        $lundiPrec = date('Y-m-d', strtotime($lundi . ' -7 days'));
        $lundiSuiv = date('Y-m-d', strtotime($lundi . ' +7 days'));
        $lundiAuj  = PlanningMenu::lundiDeLaSemaine();

        $this->render('frontoffice/planning/index', [
            'pageTitle'    => 'Menu de la semaine',
            'grid'         => $grid,
            'jours'        => $jours,
            'moments'      => PlanningMenu::MOMENTS,
            'momentLabels' => PlanningMenu::MOMENT_LABELS,
            'lundi'        => $lundi,
            'lundiPrec'    => $lundiPrec,
            'lundiSuiv'    => $lundiSuiv,
            'lundiAuj'     => $lundiAuj,
        ], 'front');
    }
}
