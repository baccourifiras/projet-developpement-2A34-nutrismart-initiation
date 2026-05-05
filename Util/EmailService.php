<?php
/**
 * Service d'envoi d'emails pour NutriSmart
 * Utilise PHPMailer avec Gmail SMTP
 */

// Essayer de charger PHPMailer
$phpmailerPath = __DIR__ . '/../vendor/phpmailer/phpmailer/src';
if (file_exists($phpmailerPath . '/PHPMailer.php')) {
    require_once $phpmailerPath . '/PHPMailer.php';
    require_once $phpmailerPath . '/SMTP.php';
    require_once $phpmailerPath . '/Exception.php';
}

class EmailService {
    private $fromEmail = 'aniskontra123@gmail.com';
    private $fromName = 'NutriSmart';
    private $gmailPassword = 'zqdw rfvm jdgo iigd';
    private $usePhpMailer = false;
    
    public function __construct() {
        // Vérifier si PHPMailer est disponible
        $this->usePhpMailer = class_exists('PHPMailer\PHPMailer\PHPMailer');
        
        if ($this->usePhpMailer) {
            error_log("EmailService: PHPMailer détecté - utilisation de Gmail SMTP");
        } else {
            error_log("EmailService: PHPMailer non disponible - emails désactivés");
        }
    }
    
    /**
     * Envoyer un email de confirmation de commande
     */
    public function envoyerConfirmationCommande($email, $nomClient, $commande) {
        $subject = '✅ Confirmation de votre commande NutriSmart #' . $commande['id'];
        $html = $this->getTemplateConfirmation($nomClient, $commande);
        
        if ($this->usePhpMailer) {
            return $this->envoyerEmailPHPMailer($email, $nomClient, $subject, $html);
        } else {
            return $this->envoyerEmailNatif($email, $nomClient, $subject, $html);
        }
    }
    
    /**
     * Envoyer un email de changement de statut
     */
    public function envoyerChangementStatut($email, $nomClient, $commande, $nouveauStatut) {
        $statuts = [
            'confirmee' => '✅ Votre commande a été confirmée',
            'annulee' => '❌ Votre commande a été annulée',
            'en_attente' => '⏳ Votre commande est en attente'
        ];
        
        $subject = $statuts[$nouveauStatut] ?? 'Mise à jour de votre commande';
        $html = $this->getTemplateStatut($nomClient, $commande, $nouveauStatut);
        
        if ($this->usePhpMailer) {
            return $this->envoyerEmailPHPMailer($email, $nomClient, $subject, $html);
        } else {
            return $this->envoyerEmailNatif($email, $nomClient, $subject, $html);
        }
    }
    
    /**
     * Envoyer email avec PHPMailer (Gmail SMTP)
     */
    private function envoyerEmailPHPMailer($toEmail, $toName, $subject, $htmlBody) {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuration SMTP Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->fromEmail;
            $mail->Password = $this->gmailPassword;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = 'UTF-8';
            
            // Expéditeur et destinataire
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);
            
            $mail->send();
            error_log("Email envoyé avec succès à {$toEmail} via Gmail SMTP");
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            error_log("Erreur PHPMailer: {$mail->ErrorInfo}");
            return false;
        } catch (Exception $e) {
            error_log("Erreur générale email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoyer email avec mail() natif (fallback)
     */
    private function envoyerEmailNatif($toEmail, $toName, $subject, $htmlBody) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "Reply-To: {$this->fromEmail}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Supprimer les warnings et logger l'erreur
        $success = @mail($toEmail, $subject, $htmlBody, $headers);
        
        if (!$success) {
            error_log("Email non envoyé à {$toEmail} - Configurez XAMPP sendmail ou installez PHPMailer");
        } else {
            error_log("Email envoyé avec succès à {$toEmail}");
        }
        
        // Retourner true pour ne pas bloquer le processus
        // L'email est optionnel, la commande est l'important
        return true;
    }
    
    /**
     * Template HTML pour confirmation de commande
     */
    private function getTemplateConfirmation($nomClient, $commande) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4fbf7; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #1fa463, #0f6c42); color: white; padding: 40px 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 800; }
                .header p { margin: 10px 0 0; opacity: 0.9; }
                .content { padding: 30px; }
                .greeting { font-size: 18px; color: #10281b; margin-bottom: 20px; }
                .order-details { background: #f4fbf7; border-radius: 12px; padding: 20px; margin: 20px 0; border-left: 4px solid #1fa463; }
                .order-details h3 { margin: 0 0 15px; color: #1fa463; font-size: 20px; }
                .order-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #e0e0e0; }
                .order-item:last-child { border-bottom: none; }
                .order-item span:first-child { color: #638070; }
                .order-item strong { color: #10281b; }
                .total { font-size: 24px; font-weight: 900; color: #1fa463; margin-top: 15px; text-align: right; }
                .message { color: #638070; line-height: 1.8; margin: 20px 0; }
                .btn { display: inline-block; background: linear-gradient(135deg, #1fa463, #0f6c42); color: white; padding: 14px 32px; text-decoration: none; border-radius: 10px; margin: 20px 0; font-weight: 700; box-shadow: 0 4px 12px rgba(31, 164, 99, 0.3); }
                .btn:hover { box-shadow: 0 6px 16px rgba(31, 164, 99, 0.4); }
                .footer { background: #f4fbf7; padding: 25px; text-align: center; color: #638070; font-size: 14px; }
                .footer p { margin: 5px 0; }
                .badge { display: inline-block; background: #dcfce7; color: #0f6c42; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-bottom: 10px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='badge'>🎉 COMMANDE CONFIRMÉE</div>
                    <h1>Merci pour votre commande !</h1>
                    <p>Votre commande a été enregistrée avec succès</p>
                </div>
                <div class='content'>
                    <p class='greeting'>Bonjour <strong>{$nomClient}</strong>,</p>
                    <p class='message'>
                        Nous avons bien reçu votre commande sur NutriSmart. 
                        Notre équipe va la traiter dans les plus brefs délais.
                    </p>
                    
                    <div class='order-details'>
                        <h3>📋 Détails de la commande</h3>
                        <div class='order-item'>
                            <span>Numéro de commande</span>
                            <strong>#{$commande['id']}</strong>
                        </div>
                        <div class='order-item'>
                            <span>Produit</span>
                            <strong>{$commande['produit']}</strong>
                        </div>
                        <div class='order-item'>
                            <span>Quantité</span>
                            <strong>{$commande['quantite']}</strong>
                        </div>
                        <div class='order-item'>
                            <span>Mode de paiement</span>
                            <strong>{$commande['paiement']}</strong>
                        </div>
                        <div class='total'>
                            Total: {$commande['total']} TND
                        </div>
                    </div>
                    
                    <p class='message'>
                        Vous recevrez un email de confirmation dès que votre commande sera validée par notre équipe.
                    </p>
                    
                    <center>
                        <a href='http://localhost/NutriSmart' class='btn'>Voir mes commandes</a>
                    </center>
                </div>
                <div class='footer'>
                    <p><strong>© 2026 NutriSmart</strong></p>
                    <p>Plateforme de Nutrition pour Régimes Spéciaux</p>
                    <p style='margin-top: 15px; font-size: 12px;'>
                        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Template HTML pour changement de statut
     */
    private function getTemplateStatut($nomClient, $commande, $statut) {
        $messages = [
            'confirmee' => 'Bonne nouvelle ! Votre commande a été confirmée et est en cours de traitement. Nous vous tiendrons informé de son avancement.',
            'annulee' => 'Nous sommes désolés, votre commande a été annulée. Si vous avez des questions, n\'hésitez pas à nous contacter.',
            'en_attente' => 'Votre commande est en attente de validation. Notre équipe va la traiter dans les plus brefs délais.'
        ];
        
        $colors = [
            'confirmee' => '#16a34a',
            'annulee' => '#dc2626',
            'en_attente' => '#f59e0b'
        ];
        
        $icons = [
            'confirmee' => '✅',
            'annulee' => '❌',
            'en_attente' => '⏳'
        ];
        
        $color = $colors[$statut] ?? '#1fa463';
        $message = $messages[$statut] ?? 'Le statut de votre commande a été mis à jour.';
        $icon = $icons[$statut] ?? '📦';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4fbf7; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .header { background: {$color}; color: white; padding: 40px 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 28px; font-weight: 800; }
                .content { padding: 30px; }
                .status-badge { display: inline-block; background: {$color}; color: white; padding: 10px 20px; border-radius: 25px; font-weight: 800; font-size: 16px; margin: 20px 0; }
                .message { color: #638070; line-height: 1.8; margin: 20px 0; font-size: 16px; }
                .order-info { background: #f4fbf7; padding: 20px; border-radius: 12px; margin: 20px 0; }
                .footer { background: #f4fbf7; padding: 20px; text-align: center; color: #638070; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>{$icon} Mise à jour de commande</h1>
                </div>
                <div class='content'>
                    <p style='font-size: 18px;'>Bonjour <strong>{$nomClient}</strong>,</p>
                    <p class='message'>{$message}</p>
                    
                    <center>
                        <div class='status-badge'>" . strtoupper(str_replace('_', ' ', $statut)) . "</div>
                    </center>
                    
                    <div class='order-info'>
                        <p style='margin: 0;'><strong>Commande #{$commande['id']}</strong></p>
                    </div>
                    
                    <p class='message'>
                        Pour toute question, n'hésitez pas à nous contacter.
                    </p>
                </div>
                <div class='footer'>
                    <p><strong>© 2026 NutriSmart</strong></p>
                    <p>Plateforme de Nutrition pour Régimes Spéciaux</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
