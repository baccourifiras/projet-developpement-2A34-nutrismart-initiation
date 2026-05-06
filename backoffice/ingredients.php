<?php
/** Route : Liste admin des ingrédients. */
require_once __DIR__ . '/../config/bootstrap.php';
(new IngredientAdminController())->index();
