<?php
/** Route : POST marquer toutes comme lues. */
require_once __DIR__ . '/../config/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Méthode non autorisée'); }
(new NotificationController())->marquerToutesLues();
