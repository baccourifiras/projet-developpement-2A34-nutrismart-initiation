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
