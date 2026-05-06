<?php
/** Route : Liste des notifications. */
require_once __DIR__ . '/../config/bootstrap.php';
(new NotificationController())->index();
