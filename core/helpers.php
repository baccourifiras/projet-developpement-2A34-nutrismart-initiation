<?php
/**
 * ============================================================
 *  NutriSmart - Helpers globaux
 *  /core/helpers.php
 *
 *  Petites fonctions utilitaires utilisées partout :
 *   - e()         : échappement XSS
 *   - redirect()  : redirection HTTP propre
 *   - flash()     : messages flash (succès / erreur) via session
 *   - csrf_*      : token anti-CSRF
 *   - old()       : repopulation des formulaires après erreur
 * ============================================================
 */

/**
 * Échappe une chaîne pour affichage HTML (anti-XSS).
 */
function e($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Redirige vers une URL et arrête le script.
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Stocke un message flash (affiché à la prochaine requête).
 *  flash('success', 'Recette ajoutée !');
 *  $msg = flash('success'); // récupère et consomme
 */
function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }
    if (isset($_SESSION['_flash'][$key])) {
        $msg = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }
    return null;
}

/**
 * Génère (et mémorise) un token CSRF pour la session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

/**
 * Champ caché à insérer dans les formulaires.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

/**
 * Vérifie le token CSRF d'une requête POST. Stoppe la requête si invalide.
 */
function csrf_check(): void
{
    $sent = $_POST['_csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['_csrf'] ?? '', $sent)) {
        http_response_code(419);
        die('Jeton CSRF invalide. Veuillez recharger la page.');
    }
}

/**
 * Mémorise les anciennes valeurs d'un formulaire (en cas d'erreur de validation).
 */
function old_set(array $data): void
{
    $_SESSION['_old'] = $data;
}

/**
 * Récupère une ancienne valeur de formulaire.
 */
function old(string $key, $default = '')
{
    $val = $_SESSION['_old'][$key] ?? $default;
    return $val;
}

/**
 * Vide les anciennes valeurs (à appeler après affichage du formulaire).
 */
function old_clear(): void
{
    unset($_SESSION['_old']);
}

/**
 * Mémorise les erreurs de validation pour la prochaine requête.
 */
function errors_set(array $errors): void
{
    $_SESSION['_errors'] = $errors;
}

/**
 * Récupère et consomme les erreurs.
 */
function errors_get(): array
{
    $errs = $_SESSION['_errors'] ?? [];
    unset($_SESSION['_errors']);
    return $errs;
}
