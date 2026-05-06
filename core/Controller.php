<?php
/**
 * ============================================================
 *  NutriSmart - Controller (classe parente)
 *  /core/Controller.php
 *
 *  Toutes les classes Controller héritent de celle-ci.
 *  Fournit principalement render() pour afficher une vue
 *  avec un layout (header / footer).
 * ============================================================
 */

abstract class Controller
{
    /**
     * Rend une vue PHP en injectant des variables dans le scope local.
     *
     * @param string $view   Chemin relatif depuis /views (ex: 'frontoffice/recettes/index')
     * @param array  $data   Variables disponibles dans la vue (ex: ['recettes' => [...]])
     * @param string|null $layout  'front' | 'back' | null (vue brute)
     */
    protected function render(string $view, array $data = [], ?string $layout = null): void
    {
        $viewFile = BASE_PATH . '/views/' . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            die('Vue introuvable : ' . e($view));
        }

        // Extraire les variables pour la vue
        extract($data, EXTR_SKIP);

        // Capturer le contenu de la vue
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        // Injecter dans le layout
        $headerFile = BASE_PATH . '/views/layouts/' . $layout . '_header.php';
        $footerFile = BASE_PATH . '/views/layouts/' . $layout . '_footer.php';

        // $pageTitle peut être passé via $data
        $pageTitle = $data['pageTitle'] ?? 'NutriSmart';

        if (is_file($headerFile)) require $headerFile;
        echo $content;
        if (is_file($footerFile)) require $footerFile;
    }

    /**
     * Réponse JSON (utile pour les endpoints AJAX, ex: recherche live).
     */
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Vérifie si la requête est en POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
