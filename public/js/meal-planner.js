/**
 * NutriSmart Meal Planner
 * Générateur de plans de repas selon le régime alimentaire
 */

class MealPlanner {
    constructor() {
        this.regimes = {
            diabete: {
                nom: 'Diabète',
                description: 'Plan adapté pour le contrôle glycémique',
                petitDej: [
                    'Flocons d\'avoine avec cannelle',
                    'Yaourt grec nature',
                    'Fruits rouges (myrtilles, framboises)',
                    'Amandes (poignée)',
                    'Thé vert sans sucre'
                ],
                dejeuner: [
                    'Poulet grillé (150g)',
                    'Quinoa (100g cuit)',
                    'Légumes vapeur (brocoli, carottes)',
                    'Salade verte avec vinaigrette légère',
                    'Pomme'
                ],
                diner: [
                    'Poisson blanc (saumon ou cabillaud)',
                    'Riz complet (80g cuit)',
                    'Haricots verts',
                    'Salade de tomates',
                    'Yaourt nature'
                ],
                collations: [
                    'Noix mélangées (30g)',
                    'Fromage blanc 0%',
                    'Légumes crus (concombre, carottes)'
                ]
            },
            vegan: {
                nom: 'Vegan',
                description: '100% végétal, riche en protéines',
                petitDej: [
                    'Smoothie protéiné (banane, épinards, lait d\'amande)',
                    'Pain complet avec beurre d\'amande',
                    'Graines de chia',
                    'Fruits frais de saison',
                    'Café ou thé'
                ],
                dejeuner: [
                    'Tofu mariné grillé (150g)',
                    'Lentilles corail (100g cuites)',
                    'Légumes rôtis (aubergine, poivrons)',
                    'Salade de quinoa',
                    'Orange'
                ],
                diner: [
                    'Burger végétal (pois chiches)',
                    'Patate douce rôtie',
                    'Avocat en tranches',
                    'Salade mixte',
                    'Compote de pommes sans sucre'
                ],
                collations: [
                    'Houmous avec crudités',
                    'Fruits secs et noix',
                    'Smoothie vert'
                ]
            },
            sans_gluten: {
                nom: 'Sans Gluten',
                description: 'Certifié sans gluten, équilibré',
                petitDej: [
                    'Oeufs brouillés (2 oeufs)',
                    'Pain sans gluten grillé',
                    'Avocat écrasé',
                    'Fruits frais',
                    'Jus d\'orange frais'
                ],
                dejeuner: [
                    'Saumon grillé (150g)',
                    'Riz basmati (100g cuit)',
                    'Brocoli vapeur',
                    'Salade verte',
                    'Poire'
                ],
                diner: [
                    'Poulet rôti (120g)',
                    'Pommes de terre au four',
                    'Haricots verts',
                    'Salade de betteraves',
                    'Yaourt nature'
                ],
                collations: [
                    'Fruits frais',
                    'Noix de cajou',
                    'Fromage blanc'
                ]
            }
        };
    }
    
    /**
     * Générer un plan de repas sur plusieurs jours
     */
    genererPlan(regime, jours = 7) {
        const regimeData = this.regimes[regime];
        
        if (!regimeData) {
            return null;
        }
        
        const plan = [];
        const joursNoms = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        
        for (let i = 0; i < jours; i++) {
            plan.push({
                jour: joursNoms[i % 7],
                numero: i + 1,
                petitDejeuner: this.varierRepas(regimeData.petitDej, i),
                dejeuner: this.varierRepas(regimeData.dejeuner, i),
                diner: this.varierRepas(regimeData.diner, i),
                collations: regimeData.collations
            });
        }
        
        return {
            regime: regimeData.nom,
            description: regimeData.description,
            jours: plan
        };
    }
    
    /**
     * Varier légèrement les repas pour éviter la monotonie
     */
    varierRepas(repas, jourIndex) {
        // Rotation simple des éléments
        const rotated = [...repas];
        const rotation = jourIndex % repas.length;
        
        for (let i = 0; i < rotation; i++) {
            rotated.push(rotated.shift());
        }
        
        return rotated;
    }
    
    /**
     * Afficher le plan dans l'interface
     */
    afficherPlan(regime, containerId = 'meal-plan-container') {
        const plan = this.genererPlan(regime, 7);
        const container = document.getElementById(containerId);
        
        if (!container || !plan) return;
        
        let html = `
            <div class="meal-plan-header">
                <h2>📅 Plan de Repas - ${plan.regime}</h2>
                <p class="meal-plan-description">${plan.description}</p>
                <div class="meal-plan-actions">
                    <button onclick="mealPlanner.exporterPDF('${regime}')" class="primary-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Télécharger PDF
                    </button>
                    <button onclick="mealPlanner.imprimerPlan()" class="secondary-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
                        </svg>
                        Imprimer
                    </button>
                </div>
            </div>
            
            <div class="meal-plan-grid">
        `;
        
        plan.jours.forEach(jour => {
            html += `
                <div class="meal-day-card">
                    <div class="meal-day-header">
                        <h3>${jour.jour}</h3>
                        <span class="day-badge">Jour ${jour.numero}</span>
                    </div>
                    
                    <div class="meal-section">
                        <h4>🌅 Petit-déjeuner</h4>
                        <ul>
                            ${jour.petitDejeuner.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="meal-section">
                        <h4>☀️ Déjeuner</h4>
                        <ul>
                            ${jour.dejeuner.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="meal-section">
                        <h4>🌙 Dîner</h4>
                        <ul>
                            ${jour.diner.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="meal-section collations">
                        <h4>🍎 Collations</h4>
                        <ul>
                            ${jour.collations.map(item => `<li>${item}</li>`).join('')}
                        </ul>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    }
    
    /**
     * Exporter le plan en PDF (simulation)
     */
    exporterPDF(regime) {
        alert('Fonctionnalité d\'export PDF à venir ! Pour l\'instant, utilisez l\'impression (Ctrl+P).');
        // TODO: Intégrer une bibliothèque comme jsPDF pour générer un vrai PDF
    }
    
    /**
     * Imprimer le plan
     */
    imprimerPlan() {
        window.print();
    }
    
    /**
     * Calculer les calories approximatives (optionnel)
     */
    calculerCalories(regime) {
        const calories = {
            diabete: { min: 1600, max: 1800 },
            vegan: { min: 1700, max: 1900 },
            sans_gluten: { min: 1800, max: 2000 }
        };
        
        return calories[regime] || { min: 1700, max: 1900 };
    }
}

// Initialiser le meal planner
window.mealPlanner = new MealPlanner();
