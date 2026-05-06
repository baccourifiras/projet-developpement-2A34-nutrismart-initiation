<?php
/** Route : Menu de la semaine (lecture seule, public). */
require_once __DIR__ . '/../config/bootstrap.php';
(new PlanningController())->index();
