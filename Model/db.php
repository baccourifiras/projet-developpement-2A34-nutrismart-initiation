<?php

declare(strict_types=1);

require_once __DIR__ . '/Database.php';

function getDb(): \PDO
{
    return Database::getConnection();
}
