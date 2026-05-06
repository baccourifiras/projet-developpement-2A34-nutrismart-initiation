<?php
/**
 * ============================================================
 *  NutriSmart - Controller Notifications (Back office)
 *  /controllers/backoffice/NotificationController.php
 * ============================================================
 */

class NotificationController extends Controller
{
    private Notification $model;

    public function __construct()
    {
        $this->model = new Notification();
    }

    /** Page principale : liste complète des notifications. */
    public function index(): void
    {
        // Génération automatique à chaque visite (rafraîchit les alertes)
        $this->model->genererAlertesStock();

        $filtre = $_GET['filtre'] ?? 'toutes'; // 'toutes' | 'non_lues'
        $notifications = $this->model->lister($filtre === 'non_lues', 100);

        $this->render('backoffice/notifications/index', [
            'pageTitle'     => 'Notifications',
            'notifications' => $notifications,
            'filtre'        => $filtre,
            'seuil'         => $this->model->getSeuilCritique(),
            'totalNonLues'  => $this->model->countNonLues(),
        ], 'back');
    }

    /** POST : marque une notification comme lue. */
    public function marquerLue(int $id): void
    {
        csrf_check();
        $this->model->marquerLue($id);
        flash('success', 'Notification marquée comme lue.');
        redirect('notifications.php');
    }

    /** POST : marque toutes les notifications comme lues. */
    public function marquerToutesLues(): void
    {
        csrf_check();
        $n = $this->model->marquerToutesLues();
        flash('success', "$n notification(s) marquée(s) comme lue(s).");
        redirect('notifications.php');
    }

    /** POST : supprime une notification. */
    public function supprimer(int $id): void
    {
        csrf_check();
        $this->model->supprimer($id);
        flash('success', 'Notification supprimée.');
        redirect('notifications.php');
    }
}
