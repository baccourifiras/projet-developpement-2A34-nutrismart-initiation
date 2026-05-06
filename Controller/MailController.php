<?php
/**
 * MailController.php — Envoi d'emails de rappel aux participants
 *
 * Utilise Gmail SMTP via socket PHP (sans librairie externe).
 * Envoie un email HTML bien présenté à tous les participants d'un événement.
 *
 * Usage : POST /nutrismart_evenement/Controller/MailController.php
 * Body  : JSON { "eventId": 1 }
 *      ou GET  : ?action=sendReminder&eventId=1 (depuis le BackOffice)
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/EventController.php';
require_once __DIR__ . '/CategoryController.php';
require_once __DIR__ . '/ParticipantController.php';

// ============================================================
// ⚙️  CONFIGURATION GMAIL SMTP
// ⚠️  Utiliser un mot de passe d'application Google, pas votre vrai mot de passe
// ============================================================
define('MAIL_FROM',     'nutrismartevent@gmail.com');
define('MAIL_FROM_NAME','NutriSmart Événements');
define('MAIL_PASSWORD', 'jxrgplukqqrvsdiq');
define('MAIL_SMTP',     'smtp.gmail.com');
define('MAIL_PORT',     587);
// ============================================================

// Accepter GET et POST
$method   = $_SERVER['REQUEST_METHOD'];
$eventId  = null;

if ($method === 'POST') {
    $body    = file_get_contents('php://input');
    $data    = json_decode($body, true);
    $eventId = isset($data['eventId']) ? (int) $data['eventId'] : 0;
} else {
    $eventId = isset($_GET['eventId']) ? (int) $_GET['eventId'] : 0;
}

if ($eventId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'eventId invalide.']);
    exit;
}

// Charger les données
try {
    $pdo               = config::getConnexion();
    $eventCtrl         = new EventController($pdo);
    $categoryCtrl      = new CategoryController($pdo);
    $participantCtrl   = new ParticipantController($pdo);

    $events     = $eventCtrl->getAll();
    $categories = $categoryCtrl->getAll();
    $participants = $participantCtrl->getAll();

    // Trouver l'événement
    $event = null;
    foreach ($events as $ev) {
        if ((int)$ev['id'] === $eventId) {
            $event = $ev;
            break;
        }
    }

    if (!$event) {
        echo json_encode(['success' => false, 'error' => 'Événement introuvable.']);
        exit;
    }

    // Trouver les participants de cet événement
    $destinataires = [];
    foreach ($participants as $p) {
        if ((int)$p['eventId'] === $eventId && !empty($p['email'])) {
            $destinataires[] = $p;
        }
    }

    if (empty($destinataires)) {
        echo json_encode(['success' => false, 'error' => 'Aucun participant inscrit à cet événement.']);
        exit;
    }

    // Nom de la catégorie
    $catNom = 'NutriSmart';
    foreach ($categories as $cat) {
        if ((int)$cat['id'] === (int)$event['categoryId']) {
            $catNom = $cat['name'];
            break;
        }
    }

    // Formater la date
    $dateFr = $event['date'] ? date('d/m/Y', strtotime($event['date'])) : 'Date à confirmer';
    $heure  = $event['time'] ? substr($event['time'], 0, 5) : '';

    // Envoyer les emails
    $envoyes  = 0;
    $echecs   = 0;
    $erreurs  = [];

    foreach ($destinataires as $participant) {
        $sujet = '📅 Rappel : ' . $event['title'] . ' — ' . $dateFr;
        $html  = buildEmailHtml($participant['fullName'], $event, $catNom, $dateFr, $heure);

        $ok = envoyerEmailGmail(
            $participant['email'],
            $participant['fullName'],
            $sujet,
            $html
        );

        if ($ok === true) {
            $envoyes++;
        } else {
            $echecs++;
            $erreurs[] = $participant['email'] . ': ' . $ok;
        }
    }

    echo json_encode([
        'success'  => $envoyes > 0,
        'envoyes'  => $envoyes,
        'echecs'   => $echecs,
        'total'    => count($destinataires),
        'erreurs'  => $erreurs,
        'message'  => $envoyes . ' email(s) envoyé(s) sur ' . count($destinataires) . ' participant(s).',
    ]);

} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// ── Construire le HTML de l'email ────────────────────────────
function buildEmailHtml($nom, $event, $catNom, $dateFr, $heure) {
    $titre    = htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8');
    $lieu     = htmlspecialchars($event['location'], ENT_QUOTES, 'UTF-8');
    $desc     = htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8');
    $nomUser  = htmlspecialchars($nom, ENT_QUOTES, 'UTF-8');
    $image    = htmlspecialchars($event['image'], ENT_QUOTES, 'UTF-8');
    $mapsLink = !empty($event['googleMapsLink']) ? htmlspecialchars($event['googleMapsLink'], ENT_QUOTES, 'UTF-8') : '';

    $mapsBtn = $mapsLink
        ? '<a href="' . $mapsLink . '" style="display:inline-block;margin-top:8px;padding:10px 20px;background:#1fa463;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:14px;">📍 Voir sur Google Maps</a>'
        : '';

    return '<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rappel événement NutriSmart</title>
</head>
<body style="margin:0;padding:0;background:#f4fbf7;font-family:Inter,Arial,sans-serif;">

  <!-- Wrapper -->
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4fbf7;padding:32px 16px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 8px 40px rgba(15,45,30,0.12);">

          <!-- Header vert -->
          <tr>
            <td style="background:linear-gradient(135deg,#0f6c42,#1fa463);padding:32px 36px;text-align:center;">
              <h1 style="margin:0;color:#ffffff;font-size:28px;font-weight:900;letter-spacing:0.02em;">NutriSmart</h1>
              <p style="margin:6px 0 0;color:rgba(255,255,255,0.8);font-size:12px;font-weight:700;letter-spacing:0.18em;text-transform:uppercase;">Eat Smart · Live Smart</p>
            </td>
          </tr>

          <!-- Image de l\'événement -->
          <tr>
            <td style="padding:0;">
              <img src="' . $image . '" alt="' . $titre . '" width="600" style="width:100%;height:220px;object-fit:cover;display:block;" />
            </td>
          </tr>

          <!-- Corps -->
          <tr>
            <td style="padding:36px;">

              <!-- Salutation -->
              <p style="margin:0 0 20px;font-size:16px;color:#10281b;">Bonjour <strong>' . $nomUser . '</strong> 👋</p>

              <!-- Badge catégorie -->
              <span style="display:inline-block;padding:6px 14px;border-radius:999px;background:rgba(31,164,99,0.12);color:#0f6c42;font-size:12px;font-weight:800;margin-bottom:16px;">' . htmlspecialchars($catNom, ENT_QUOTES, 'UTF-8') . '</span>

              <!-- Titre événement -->
              <h2 style="margin:0 0 12px;font-size:24px;font-weight:900;color:#10281b;">' . $titre . '</h2>

              <!-- Description -->
              <p style="margin:0 0 24px;font-size:15px;color:#638070;line-height:1.7;">' . $desc . '</p>

              <!-- Infos événement -->
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4fbf7;border-radius:14px;padding:20px;margin-bottom:24px;">
                <tr>
                  <td style="padding:8px 0;">
                    <span style="font-size:18px;">📅</span>
                    <span style="font-size:15px;color:#10281b;font-weight:700;margin-left:10px;">' . $dateFr . ($heure ? ' à ' . $heure : '') . '</span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:8px 0;border-top:1px solid rgba(31,164,99,0.12);">
                    <span style="font-size:18px;">📌</span>
                    <span style="font-size:15px;color:#10281b;font-weight:700;margin-left:10px;">' . $lieu . '</span>
                  </td>
                </tr>
              </table>

              <!-- Bouton Maps -->
              ' . $mapsBtn . '

              <!-- Message de rappel -->
              <div style="margin-top:28px;padding:18px 20px;background:linear-gradient(135deg,rgba(31,164,99,0.08),rgba(255,255,255,0.9));border-left:4px solid #1fa463;border-radius:0 12px 12px 0;">
                <p style="margin:0;font-size:14px;color:#10281b;line-height:1.7;">
                  🌿 <strong>Rappel :</strong> Vous êtes inscrit(e) à cet événement. Nous avons hâte de vous accueillir !<br>
                  N\'hésitez pas à consulter notre plateforme pour plus d\'informations.
                </p>
              </div>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#f4fbf7;padding:24px 36px;text-align:center;border-top:1px solid rgba(31,164,99,0.12);">
              <p style="margin:0;font-size:12px;color:#638070;line-height:1.8;">
                Cet email vous a été envoyé car vous êtes inscrit(e) à un événement NutriSmart.<br>
                <strong style="color:#0f6c42;">NutriSmart</strong> · Plateforme nutrition & bien-être
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>';
}

// ── Envoi via Gmail SMTP (socket PHP natif) ──────────────────
function envoyerEmailGmail($toEmail, $toName, $sujet, $htmlBody) {
    $from     = MAIL_FROM;
    $fromName = MAIL_FROM_NAME;
    $password = MAIL_PASSWORD;
    $smtp     = MAIL_SMTP;
    $port     = MAIL_PORT;

    // Vérifier que le mot de passe est configuré
    if ($password === 'VOTRE_MOT_DE_PASSE_APPLICATION' || $password === '') {
        return 'Mot de passe SMTP non configuré. Voir Controller/MailController.php';
    }

    try {
        // Connexion SMTP
        $socket = fsockopen('tcp://' . $smtp, $port, $errno, $errstr, 10);
        if (!$socket) {
            return "Connexion SMTP impossible: $errstr ($errno)";
        }

        $read = fgets($socket, 512);
        if (substr($read, 0, 3) !== '220') {
            fclose($socket);
            return "SMTP greeting failed: $read";
        }

        // EHLO
        fputs($socket, "EHLO localhost\r\n");
        while ($line = fgets($socket, 512)) {
            if (substr($line, 3, 1) === ' ') break;
        }

        // STARTTLS
        fputs($socket, "STARTTLS\r\n");
        $read = fgets($socket, 512);
        if (substr($read, 0, 3) !== '220') {
            fclose($socket);
            return "STARTTLS failed: $read";
        }

        // Upgrade vers TLS
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        // EHLO après TLS
        fputs($socket, "EHLO localhost\r\n");
        while ($line = fgets($socket, 512)) {
            if (substr($line, 3, 1) === ' ') break;
        }

        // AUTH LOGIN
        fputs($socket, "AUTH LOGIN\r\n");
        fgets($socket, 512);
        fputs($socket, base64_encode($from) . "\r\n");
        fgets($socket, 512);
        fputs($socket, base64_encode($password) . "\r\n");
        $authResp = fgets($socket, 512);
        if (substr($authResp, 0, 3) !== '235') {
            fclose($socket);
            return "Auth failed: $authResp";
        }

        // MAIL FROM
        fputs($socket, "MAIL FROM:<$from>\r\n");
        fgets($socket, 512);

        // RCPT TO
        fputs($socket, "RCPT TO:<$toEmail>\r\n");
        $rcptResp = fgets($socket, 512);
        if (substr($rcptResp, 0, 3) !== '250') {
            fclose($socket);
            return "RCPT failed: $rcptResp";
        }

        // DATA
        fputs($socket, "DATA\r\n");
        fgets($socket, 512);

        // Headers + Body
        $boundary = md5(uniqid());
        $headers  = "From: =?UTF-8?B?" . base64_encode($fromName) . "?= <$from>\r\n"
                  . "To: =?UTF-8?B?" . base64_encode($toName) . "?= <$toEmail>\r\n"
                  . "Subject: =?UTF-8?B?" . base64_encode($sujet) . "?=\r\n"
                  . "MIME-Version: 1.0\r\n"
                  . "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n"
                  . "Date: " . date('r') . "\r\n"
                  . "\r\n";

        $body = "--$boundary\r\n"
              . "Content-Type: text/plain; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: base64\r\n\r\n"
              . chunk_split(base64_encode(strip_tags($htmlBody))) . "\r\n"
              . "--$boundary\r\n"
              . "Content-Type: text/html; charset=UTF-8\r\n"
              . "Content-Transfer-Encoding: base64\r\n\r\n"
              . chunk_split(base64_encode($htmlBody)) . "\r\n"
              . "--$boundary--\r\n";

        fputs($socket, $headers . $body . "\r\n.\r\n");
        $dataResp = fgets($socket, 512);

        // QUIT
        fputs($socket, "QUIT\r\n");
        fclose($socket);

        if (substr($dataResp, 0, 3) !== '250') {
            return "DATA failed: $dataResp";
        }

        return true;

    } catch (Throwable $e) {
        return $e->getMessage();
    }
}
?>
