/**
 * NutriSmart Chatbot avec Groq API
 * Assistant intelligent pour recommandations nutritionnelles
 */

class NutriSmartChatbot {
    constructor() {
        this.messages = [];
        this.isOpen = false;
        this.isTyping = false;
        this.init();
    }
    
    init() {
        this.createChatWidget();
        this.attachEventListeners();
        
        // Message de bienvenue
        this.addMessage(
            'Bonjour ! 👋 Je suis l\'assistant NutriSmart. Je peux vous aider à choisir des produits adaptés à votre régime alimentaire. Comment puis-je vous aider ?',
            'bot'
        );
    }
    
    createChatWidget() {
        const chatHTML = `
            <div id="chatbot-container" class="chatbot-container">
                <!-- Bouton flottant -->
                <button id="chatbot-toggle" class="chatbot-toggle">
                    <svg class="chat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <svg class="close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                    <span class="chatbot-badge">1</span>
                </button>
                
                <!-- Fenêtre de chat -->
                <div id="chatbot-window" class="chatbot-window">
                    <div class="chatbot-header">
                        <div class="chatbot-header-info">
                            <div class="chatbot-avatar">🤖</div>
                            <div>
                                <h3>Assistant NutriSmart</h3>
                                <p class="chatbot-status">
                                    <span class="status-dot"></span>
                                    En ligne
                                </p>
                            </div>
                        </div>
                        <button id="chatbot-minimize" class="chatbot-minimize">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div id="chatbot-messages" class="chatbot-messages">
                        <!-- Messages will be inserted here -->
                    </div>
                    
                    <div class="chatbot-suggestions" id="chatbot-suggestions">
                        <button class="suggestion-btn" data-message="Quels produits pour le diabète ?">
                            🩺 Diabète
                        </button>
                        <button class="suggestion-btn" data-message="Montrez-moi les produits vegan">
                            🌱 Vegan
                        </button>
                        <button class="suggestion-btn" data-message="Produits sans gluten">
                            🌾 Sans gluten
                        </button>
                        <button class="suggestion-btn" data-message="Quel est le prix du coaching ?">
                            💰 Prix
                        </button>
                    </div>
                    
                    <div class="chatbot-input-container">
                        <input 
                            type="text" 
                            id="chatbot-input" 
                            class="chatbot-input" 
                            placeholder="Posez votre question..."
                            autocomplete="off"
                        />
                        <button id="chatbot-send" class="chatbot-send">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatHTML);
    }
    
    attachEventListeners() {
        const toggle = document.getElementById('chatbot-toggle');
        const minimize = document.getElementById('chatbot-minimize');
        const sendBtn = document.getElementById('chatbot-send');
        const input = document.getElementById('chatbot-input');
        const suggestions = document.querySelectorAll('.suggestion-btn');
        
        toggle.addEventListener('click', () => this.toggleChat());
        minimize.addEventListener('click', () => this.toggleChat());
        sendBtn.addEventListener('click', () => this.sendMessage());
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });
        
        suggestions.forEach(btn => {
            btn.addEventListener('click', () => {
                const message = btn.getAttribute('data-message');
                input.value = message;
                this.sendMessage();
            });
        });
    }
    
    toggleChat() {
        this.isOpen = !this.isOpen;
        const container = document.getElementById('chatbot-container');
        const badge = document.querySelector('.chatbot-badge');
        
        container.classList.toggle('open', this.isOpen);
        
        if (this.isOpen) {
            badge.style.display = 'none';
            document.getElementById('chatbot-input').focus();
        }
    }
    
    async sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (!message || this.isTyping) return;
        
        // Ajouter le message de l'utilisateur
        this.addMessage(message, 'user');
        input.value = '';
        
        // Masquer les suggestions après le premier message
        document.getElementById('chatbot-suggestions').style.display = 'none';
        
        // Afficher l'indicateur de frappe
        this.showTypingIndicator();
        
        // Envoyer à Groq API
        try {
            const response = await this.callGroqAPI(message);
            this.hideTypingIndicator();
            this.addMessage(response, 'bot');
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage(
                'Désolé, je rencontre un problème technique. Pouvez-vous réessayer ?',
                'bot'
            );
            console.error('Erreur Groq API:', error);
        }
    }
    
    async callGroqAPI(userMessage) {
        // Vérifier que la configuration est chargée
        if (typeof GROQ_CONFIG === 'undefined') {
            console.error('GROQ_CONFIG not loaded!');
            throw new Error('Configuration Groq non chargée');
        }
        
        if (!GROQ_CONFIG.apiKey) {
            console.error('Groq API key not configured!');
            throw new Error('Clé API Groq non configurée');
        }
        
        // Construire le contexte NutriSmart
        const systemPrompt = `Tu es un assistant nutritionnel pour NutriSmart, une plateforme tunisienne de nutrition pour régimes spéciaux.

PRODUITS DISPONIBLES:
- Plans nutritionnels (diabète, vegan, sans gluten) : 45-120 TND/mois
- Coaching personnalisé : 80-150 TND/mois
- Guides pratiques : 25-40 TND (achat unique)
- Fonctionnalités premium : 34-60 TND/mois

RÉGIMES SPÉCIALISÉS:
- Diabète : Contrôle glycémique, index glycémique bas
- Vegan : 100% végétal, riche en protéines végétales
- Sans gluten : Certifié sans gluten, adapté aux intolérances

INSTRUCTIONS:
- Réponds en français
- Sois concis (2-3 phrases max)
- Recommande des produits spécifiques
- Utilise des emojis pertinents
- Mentionne les prix en TND
- Sois chaleureux et professionnel`;

        this.messages.push({
            role: 'user',
            content: userMessage
        });
        
        console.log('Appel Groq API...', {
            model: GROQ_CONFIG.model,
            apiUrl: GROQ_CONFIG.apiUrl,
            hasApiKey: !!GROQ_CONFIG.apiKey
        });
        
        const response = await fetch(GROQ_CONFIG.apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${GROQ_CONFIG.apiKey}`
            },
            body: JSON.stringify({
                messages: [
                    { role: 'system', content: systemPrompt },
                    ...this.messages
                ],
                model: GROQ_CONFIG.model,
                temperature: GROQ_CONFIG.temperature,
                max_tokens: GROQ_CONFIG.maxTokens
            })
        });
        
        console.log('Réponse Groq API:', response.status, response.statusText);
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            console.error('Erreur API Groq:', {
                status: response.status,
                statusText: response.statusText,
                error: errorData
            });
            
            // Messages d'erreur plus spécifiques
            if (response.status === 401) {
                throw new Error('Clé API invalide ou expirée');
            } else if (response.status === 429) {
                throw new Error('Limite de requêtes atteinte');
            } else if (response.status === 500) {
                throw new Error('Erreur serveur Groq');
            }
            
            throw new Error(`Erreur API: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Réponse Groq reçue:', data);
        
        const botMessage = data.choices[0].message.content;
        
        this.messages.push({
            role: 'assistant',
            content: botMessage
        });
        
        return botMessage;
    }
    
    addMessage(text, sender) {
        const messagesContainer = document.getElementById('chatbot-messages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chatbot-message ${sender}-message`;
        
        if (sender === 'bot') {
            messageDiv.innerHTML = `
                <div class="message-avatar">🤖</div>
                <div class="message-content">${this.formatMessage(text)}</div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="message-content">${this.escapeHtml(text)}</div>
            `;
        }
        
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Animation d'entrée
        setTimeout(() => messageDiv.classList.add('show'), 10);
    }
    
    formatMessage(text) {
        // Convertir les liens en cliquables
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        
        // Convertir les retours à la ligne
        text = text.replace(/\n/g, '<br>');
        
        return text;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    showTypingIndicator() {
        this.isTyping = true;
        const messagesContainer = document.getElementById('chatbot-messages');
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-message bot-message typing-indicator';
        typingDiv.id = 'typing-indicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">🤖</div>
            <div class="message-content">
                <div class="typing-dots">
                    <span></span><span></span><span></span>
                </div>
            </div>
        `;
        messagesContainer.appendChild(typingDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    hideTypingIndicator() {
        this.isTyping = false;
        const indicator = document.getElementById('typing-indicator');
        if (indicator) indicator.remove();
    }
}

// Initialiser le chatbot au chargement de la page
document.addEventListener('DOMContentLoaded', () => {
    window.nutriSmartChatbot = new NutriSmartChatbot();
});
