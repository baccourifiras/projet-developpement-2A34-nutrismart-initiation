/**
 * Configuration Groq API
 * 
 * GROQ API - Fast AI Inference
 * Documentation: https://console.groq.com/
 */

const GROQ_CONFIG = {
    apiKey: 'your_groq_api_key_here', // Replace with your Groq API key
    apiUrl: 'https://api.groq.com/openai/v1/chat/completions',
    model: 'llama-3.1-8b-instant', // Modèle actuel et rapide
    temperature: 0.7,
    maxTokens: 500
};

// Vérifier que la clé API est configurée
if (!GROQ_CONFIG.apiKey || GROQ_CONFIG.apiKey === 'your_groq_api_key_here') {
    console.warn('⚠️ Groq API key not configured! Please update config/groq-config.js');
}

// Export pour utilisation dans chatbot.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GROQ_CONFIG;
}
