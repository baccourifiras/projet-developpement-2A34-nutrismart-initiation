<?php
/**
 * Composant : Widget Chatbot (front office)
 * /views/components/chatbot_widget.php
 *
 * Inclus automatiquement dans le footer frontoffice.
 * Contient le bouton flottant + le panneau de chat.
 */
$baseUrl = '/' . basename(BASE_PATH);
?>

<!-- Bouton flottant -->
<button type="button" id="chatbot-toggle" class="chatbot-toggle" aria-label="Ouvrir l'assistant culinaire">
  <span class="chatbot-toggle-icon-open">💬</span>
  <span class="chatbot-toggle-icon-close">✕</span>
  <span class="chatbot-toggle-pulse"></span>
</button>

<!-- Panneau de chat -->
<aside id="chatbot-panel" class="chatbot-panel" aria-label="Assistant culinaire" aria-hidden="true">
  <header class="chatbot-header">
    <div class="chatbot-header-icon">👨‍🍳</div>
    <div class="chatbot-header-info">
      <h3>Chef NutriSmart</h3>
      <p><span class="chatbot-status-dot"></span> En ligne</p>
    </div>
    <button type="button" id="chatbot-reset" class="chatbot-header-btn" title="Nouvelle conversation" aria-label="Nouvelle conversation">🔄</button>
    <button type="button" id="chatbot-close" class="chatbot-header-btn" title="Fermer" aria-label="Fermer">✕</button>
  </header>

  <div id="chatbot-messages" class="chatbot-messages" role="log" aria-live="polite">
    <!-- Message d'accueil -->
    <div class="chatbot-msg chatbot-msg-bot">
      <div class="chatbot-msg-avatar">👨‍🍳</div>
      <div class="chatbot-msg-bubble">
        Bonjour ! Je suis votre assistant culinaire 🌿
        <br>Posez-moi vos questions sur la cuisine, la nutrition ou les recettes !
      </div>
    </div>

    <!-- Suggestions de démarrage -->
    <div class="chatbot-suggestions" id="chatbot-suggestions">
      <button type="button" class="chatbot-suggestion-btn" data-text="Comment cuire un riz parfait ?">🍚 Cuire un riz parfait</button>
      <button type="button" class="chatbot-suggestion-btn" data-text="Quels aliments riches en protéines pour un végétarien ?">🥗 Protéines végétariennes</button>
      <button type="button" class="chatbot-suggestion-btn" data-text="Une idée de recette rapide pour ce soir ?">⚡ Recette rapide</button>
      <button type="button" class="chatbot-suggestion-btn" data-text="Comment conserver les herbes fraîches ?">🌿 Conserver les herbes</button>
    </div>
  </div>

  <form id="chatbot-form" class="chatbot-form">
    <textarea
      id="chatbot-input"
      class="chatbot-input"
      placeholder="Posez votre question…"
      rows="1"
      maxlength="1000"
      aria-label="Votre message"
      required></textarea>
    <button type="submit" id="chatbot-send" class="chatbot-send" aria-label="Envoyer">
      <span class="chatbot-send-icon">➤</span>
    </button>
  </form>

  <p class="chatbot-footer-note">Propulsé par Google Gemini · Réponses générées par IA</p>
</aside>
