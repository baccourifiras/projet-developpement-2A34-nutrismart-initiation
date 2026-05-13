<?php
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username   = "root";
            $password   = "";
            $dbname     = "nutrismart";
            try {
                self::$pdo = new PDO(
                    "mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Erreur : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}


function redirectToPage($page, $space, $extra = [])
{
    $params = array_merge(['page' => $page], $space === 'back' ? ['space' => 'back'] : [], $extra);
    header('Location: index.php?' . http_build_query($params));
    exit;
}


function renderPagination($curPage, $nbPages, $baseUrl)
{
    if ($nbPages <= 1) return;
    echo '<div style="display:flex;justify-content:center;align-items:center;gap:6px;padding:18px 0;">';
    // Précédent
    if ($curPage > 1) {
        echo '<a href="' . $baseUrl . '&p=' . ($curPage - 1) . '" style="padding:7px 14px;border:1.5px solid var(--sand);border-radius:8px;font-size:13px;color:var(--green);font-weight:600;text-decoration:none;">← Préc.</a>';
    }
    // Pages
    for ($i = 1; $i <= $nbPages; $i++) {
        $active = $i === $curPage
            ? 'background:var(--green);color:#fff;border-color:var(--green);'
            : 'background:var(--ivory);color:var(--ink);';
        echo '<a href="' . $baseUrl . '&p=' . $i . '" style="padding:7px 13px;border:1.5px solid var(--sand);border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;' . $active . '">' . $i . '</a>';
    }
    // Suivant
    if ($curPage < $nbPages) {
        echo '<a href="' . $baseUrl . '&p=' . ($curPage + 1) . '" style="padding:7px 14px;border:1.5px solid var(--sand);border-radius:8px;font-size:13px;color:var(--green);font-weight:600;text-decoration:none;">Suiv. →</a>';
    }
    echo '</div>';
}

function renderHeader($space, $page)
{
    if ($space === 'back') {
        require BASE . '/View/shared/back_header.php';
    } else {
        require BASE . '/View/shared/front_header.php';
    }
}

function renderFooter($space)
{
    if ($space === 'back') {
        require BASE . '/View/shared/back_footer.php';
    } else {
        require BASE . '/View/shared/front_footer.php';
    }
}
?>
