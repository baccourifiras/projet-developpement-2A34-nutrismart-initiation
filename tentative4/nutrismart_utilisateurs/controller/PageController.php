<?php

/**
 * Controleur des pages.
 * Role MVC : choisir la vue a afficher sans mettre la logique dans la vue.
 */
class PageController {

    public function accueil() {
        $this->render(__DIR__ . '/../view/frontoffice/accueil.php');
    }

    public function inscription() {
        $this->render(__DIR__ . '/../view/frontoffice/inscription.php');
    }

    public function dashboard() {
        $this->render(__DIR__ . '/../view/backoffice/dashboard.php');
    }

    private function render($viewPath) {
        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo 'Vue introuvable.';
            return;
        }

        require $viewPath;
    }
}

if (isset($_SERVER['SCRIPT_FILENAME']) && basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $controller = new PageController();
    $page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

    if ($page === 'inscription') {
        $controller->inscription();
    } elseif ($page === 'dashboard') {
        $controller->dashboard();
    } else {
        $controller->accueil();
    }
}
