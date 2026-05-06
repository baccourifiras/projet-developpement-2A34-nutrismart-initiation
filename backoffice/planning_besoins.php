<?php
/** Route : Besoins ingrédients pour la semaine. */
require_once __DIR__ . '/../config/bootstrap.php';
(new PlanningController())->besoins();
