<?php
/** Route : Liste admin des recettes. */
require_once __DIR__ . '/../config/bootstrap.php';
(new RecetteAdminController())->index();
