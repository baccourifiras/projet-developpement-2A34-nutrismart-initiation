<?php
/** Route : Planning hebdomadaire admin. */
require_once __DIR__ . '/../config/bootstrap.php';
(new PlanningController())->index();
