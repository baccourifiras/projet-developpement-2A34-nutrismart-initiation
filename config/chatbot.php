<?php
/**
 * ============================================================
 *  NutriSmart - Configuration de l'API Gemini
 *  /config/chatbot.php
 *
 *  INSTALLATION
 *
 *  1. Allez sur : https://aistudio.google.com/apikey
 *  2. Connectez-vous avec un compte Google
 *  3. Cliquez sur "Create API key" et copiez la cle generee
 *  4. Collez-la ci-dessous a la place de "VOTRE_CLE_API_ICI"
 *
 *  COUT
 *  L'API Gemini propose un quota gratuit largement suffisant pour
 *  un projet etudiant (15 requetes/min, 1500 requetes/jour sur 1.5-flash).
 * ============================================================
 */

return [
    // Votre cle API Gemini (obtenue sur https://aistudio.google.com/apikey)
    'api_key' => 'AIzaSyDS0RcNNZhIHF9ADgj9LpAxUbIb5BJ0TvQ',

    // Modele Gemini a utiliser. 'gemini-1.5-flash' est rapide et gratuit.
    // Alternatives : 'gemini-1.5-flash-8b' (plus rapide), 'gemini-1.5-pro'
    'model' => 'gemini-flash-latest',

    // Personnalite du chatbot (system prompt) - syntaxe simple sans heredoc
    'system_prompt' =>
        "Tu es Chef NutriSmart, un assistant culinaire chaleureux et expert. " .
        "Ton role est d'aider les utilisateurs avec leurs questions sur la cuisine, " .
        "la nutrition, les techniques culinaires, les recettes et les conseils nutritionnels. " .
        "Regles : " .
        "1) Reponds toujours en francais. " .
        "2) Sois concis : 2 a 4 phrases maximum, sauf si l'utilisateur demande plus de details. " .
        "3) Utilise un ton chaleureux et accessible. " .
        "4) Si la question n'a aucun lien avec la cuisine ou la nutrition, redirige poliment. " .
        "5) Ne donne jamais de conseils medicaux : suggere un nutritionniste. " .
        "6) Tu peux utiliser des emojis avec parcimonie.",

    // Limite de taille des messages (securite)
    'max_message_length' => 1000,

    // Nombre max d'allers-retours conserves en historique de session
    'max_history' => 10,
];
