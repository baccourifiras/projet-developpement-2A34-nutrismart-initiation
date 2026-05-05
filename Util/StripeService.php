<?php
/**
 * Service de paiement Stripe pour NutriSmart
 */

class StripeService {
    private $secretKey;
    private $publicKey;
    
    public function __construct() {
        // Load keys from environment or config file
        // For production, use environment variables
        $this->secretKey = getenv('STRIPE_SECRET_KEY') ?: 'your_stripe_secret_key_here';
        $this->publicKey = getenv('STRIPE_PUBLIC_KEY') ?: 'your_stripe_public_key_here';
    }
    
    /**
     * Créer une session de paiement Stripe
     */
    public function creerSessionPaiement($panier, $successUrl, $cancelUrl) {
        // Préparer les données pour Stripe avec le bon format
        $postData = [];
        $postData['mode'] = 'payment';
        $postData['success_url'] = $successUrl;
        $postData['cancel_url'] = $cancelUrl;
        $postData['payment_method_types[0]'] = 'card';
        
        // Ajouter chaque item du panier
        $index = 0;
        foreach ($panier as $item) {
            $postData["line_items[{$index}][price_data][currency]"] = 'eur';
            $postData["line_items[{$index}][price_data][product_data][name]"] = $item['nom'];
            $postData["line_items[{$index}][price_data][product_data][description]"] = $item['categorie'] . ' - ' . $item['regime'];
            $postData["line_items[{$index}][price_data][unit_amount]"] = intval($item['prix'] * 100); // Stripe utilise les centimes pour EUR (1 TND ≈ 0.30 EUR)
            $postData["line_items[{$index}][quantity]"] = $item['quantite'];
            $index++;
        }
        
        // Appel API Stripe
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        } else {
            error_log("Erreur Stripe (HTTP {$httpCode}): " . $response);
            if ($error) {
                error_log("Erreur cURL: " . $error);
            }
            return null;
        }
    }
    
    /**
     * Vérifier le statut d'un paiement
     */
    public function verifierPaiement($sessionId) {
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $session = json_decode($response, true);
            return $session['payment_status'] === 'paid';
        }
        
        return false;
    }
    
    /**
     * Obtenir les détails d'une session de paiement
     */
    public function getSessionDetails($sessionId) {
        $ch = curl_init('https://api.stripe.com/v1/checkout/sessions/' . $sessionId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->secretKey,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return null;
    }
    
    /**
     * Obtenir la clé publique
     */
    public function getPublicKey() {
        return $this->publicKey;
    }
}
