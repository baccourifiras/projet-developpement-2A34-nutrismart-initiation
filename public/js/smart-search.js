/**
 * NutriSmart Smart Search
 * Recherche intelligente avec NLP et scoring de pertinence
 */

class SmartSearch {
    constructor() {
        this.synonymes = {
            'diabete': ['sucre', 'glycemie', 'insuline', 'diabetique', 'glucose'],
            'vegan': ['vegetalien', 'vegetal', 'plantes', 'sans viande', 'vegetarien'],
            'gluten': ['ble', 'cereales', 'celiac', 'intolerance', 'sans gluten'],
            'perte de poids': ['maigrir', 'minceur', 'regime', 'calories', 'perdre du poids'],
            'muscle': ['proteine', 'musculation', 'fitness', 'sport', 'masse'],
            'coaching': ['entraineur', 'coach', 'suivi', 'accompagnement'],
            'plan': ['programme', 'menu', 'planning', 'organisation'],
            'guide': ['manuel', 'tutoriel', 'aide', 'conseil']
        };
        
        this.stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'pour', 'avec', 'sans', 'et', 'ou'];
    }
    
    /**
     * Recherche intelligente avec scoring
     */
    rechercher(query, produits) {
        if (!query || query.trim().length < 2) {
            return produits;
        }
        
        query = query.toLowerCase().trim();
        const mots = this.extraireMots(query);
        
        // Calculer le score pour chaque produit
        const produitsAvecScore = produits.map(produit => {
            const score = this.calculerScore(mots, produit, query);
            return { ...produit, searchScore: score };
        });
        
        // Filtrer et trier par score
        return produitsAvecScore
            .filter(p => p.searchScore > 0)
            .sort((a, b) => b.searchScore - a.searchScore);
    }
    
    /**
     * Extraire les mots significatifs
     */
    extraireMots(query) {
        return query
            .split(/\s+/)
            .filter(mot => mot.length > 2 && !this.stopWords.includes(mot));
    }
    
    /**
     * Calculer le score de pertinence
     */
    calculerScore(mots, produit, queryComplete) {
        let score = 0;
        
        const nom = produit.nom.toLowerCase();
        const description = produit.description.toLowerCase();
        const categorie = produit.categorie.toLowerCase();
        const regime = produit.regime_cible.toLowerCase();
        
        // 1. Correspondance exacte dans le nom (score élevé)
        if (nom.includes(queryComplete)) {
            score += 100;
        }
        
        // 2. Correspondance exacte dans la description
        if (description.includes(queryComplete)) {
            score += 50;
        }
        
        // 3. Correspondance par mots dans le nom
        mots.forEach(mot => {
            if (nom.includes(mot)) {
                score += 30;
            }
        });
        
        // 4. Correspondance par mots dans la description
        mots.forEach(mot => {
            if (description.includes(mot)) {
                score += 10;
            }
        });
        
        // 5. Correspondance dans catégorie et régime
        mots.forEach(mot => {
            if (categorie.includes(mot)) score += 25;
            if (regime.includes(mot)) score += 25;
        });
        
        // 6. Correspondance par synonymes
        for (let [concept, synonymes] of Object.entries(this.synonymes)) {
            const matchSynonyme = synonymes.some(syn => queryComplete.includes(syn));
            
            if (matchSynonyme) {
                if (regime.includes(concept)) score += 40;
                if (categorie.includes(concept)) score += 30;
                if (nom.includes(concept)) score += 20;
                if (description.includes(concept)) score += 15;
            }
        }
        
        // 7. Bonus pour disponibilité
        if (produit.disponible) {
            score += 5;
        }
        
        // 8. Pénalité pour produits indisponibles
        if (!produit.disponible) {
            score *= 0.5;
        }
        
        return score;
    }
    
    /**
     * Générer des suggestions de recherche
     */
    genererSuggestions(query, produits) {
        if (!query || query.length < 2) {
            return [];
        }
        
        const suggestions = new Set();
        query = query.toLowerCase();
        
        // Suggestions basées sur les noms de produits
        produits.forEach(produit => {
            if (produit.nom.toLowerCase().includes(query)) {
                suggestions.add(produit.nom);
            }
        });
        
        // Suggestions basées sur les synonymes
        for (let [concept, synonymes] of Object.entries(this.synonymes)) {
            if (synonymes.some(syn => syn.includes(query)) || concept.includes(query)) {
                suggestions.add(concept);
            }
        }
        
        return Array.from(suggestions).slice(0, 5);
    }
    
    /**
     * Mettre en surbrillance les termes de recherche
     */
    highlightText(text, query) {
        if (!query) return text;
        
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }
    
    /**
     * Analyser l'intention de recherche
     */
    analyserIntention(query) {
        query = query.toLowerCase();
        
        const intentions = {
            prix: /prix|cout|tarif|combien/i,
            disponibilite: /disponible|stock|dispo/i,
            comparaison: /meilleur|comparer|difference/i,
            recommendation: /recommand|conseil|suggere/i,
            information: /qu'est-ce|c'est quoi|info|detail/i
        };
        
        for (let [intention, pattern] of Object.entries(intentions)) {
            if (pattern.test(query)) {
                return intention;
            }
        }
        
        return 'recherche';
    }
}

// Initialiser la recherche intelligente
window.smartSearch = new SmartSearch();

// Fonction pour intégrer avec la recherche existante
function enhanceSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (!searchInput) return;
    
    // Créer un conteneur pour les suggestions
    const suggestionsContainer = document.createElement('div');
    suggestionsContainer.id = 'search-suggestions';
    suggestionsContainer.className = 'search-suggestions';
    searchInput.parentNode.insertBefore(suggestionsContainer, searchInput.nextSibling);
    
    // Écouter les changements dans l'input
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        
        debounceTimer = setTimeout(() => {
            const query = this.value.trim();
            
            if (query.length >= 2) {
                // Récupérer tous les produits
                const cards = Array.from(document.querySelectorAll('.product-card'));
                const produits = cards.map(card => ({
                    element: card,
                    nom: card.querySelector('h3').textContent,
                    description: card.querySelector('.product-description').textContent,
                    categorie: card.getAttribute('data-categorie'),
                    regime_cible: card.getAttribute('data-regime'),
                    disponible: true
                }));
                
                // Recherche intelligente
                const resultats = window.smartSearch.rechercher(query, produits);
                
                // Afficher les résultats
                cards.forEach(card => card.style.display = 'none');
                resultats.forEach(resultat => {
                    resultat.element.style.display = 'block';
                });
                
                // Afficher le nombre de résultats
                afficherNombreResultats(resultats.length, produits.length);
            } else {
                // Réafficher tous les produits
                const cards = document.querySelectorAll('.product-card');
                cards.forEach(card => card.style.display = 'block');
                masquerNombreResultats();
            }
        }, 300);
    });
}

function afficherNombreResultats(nombre, total) {
    let resultCounter = document.getElementById('search-result-counter');
    
    if (!resultCounter) {
        resultCounter = document.createElement('div');
        resultCounter.id = 'search-result-counter';
        resultCounter.className = 'search-result-counter';
        
        const grid = document.getElementById('productsGrid');
        if (grid) {
            grid.parentNode.insertBefore(resultCounter, grid);
        }
    }
    
    resultCounter.textContent = `${nombre} résultat${nombre > 1 ? 's' : ''} trouvé${nombre > 1 ? 's' : ''} sur ${total}`;
    resultCounter.style.display = 'block';
}

function masquerNombreResultats() {
    const resultCounter = document.getElementById('search-result-counter');
    if (resultCounter) {
        resultCounter.style.display = 'none';
    }
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', enhanceSearch);
