<?php
/** Route : POST création ingrédient. */
require_once __DIR__ . '/../config/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Méthode non autorisée'); }
(new IngredientAdminController())->store();
