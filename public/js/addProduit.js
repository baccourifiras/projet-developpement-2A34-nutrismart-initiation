/**
 * Validation JavaScript pour le formulaire d'ajout/modification de produit
 * Trois niveaux de validation : onClick, onSubmit, et événements en temps réel
 */

// Références aux éléments du formulaire
const form = document.getElementById('addProduitForm');
const nomInput = document.getElementById('nom');
const prixInput = document.getElementById('prix');
const categorieSelect = document.getElementById('categorie');
const typeVenteSelect = document.getElementById('type_vente');
const disponibleCheckbox = document.getElementById('disponible');
const submitBtn = document.getElementById('submitBtn');

// Messages de validation
const nomMessage = document.getElementById('nom-message');
const prixMessage = document.getElementById('prix-message');
const categorieMessage = document.getElementById('categorie-message');
const typeVenteMessage = document.getElementById('type_vente-message');
const disponibleMessage = document.getElementById('disponible-message');

/**
 * Fonction de validation du nom
 * @returns {boolean}
 */
function validateNom() {
    const value = nomInput.value.trim();
    
    if (value.length === 0) {
        showError(nomMessage, 'Le nom du produit est requis');
        return false;
    }
    
    if (value.length < 3) {
        showError(nomMessage, 'Le nom doit contenir au moins 3 caractères');
        return false;
    }
    
    showSuccess(nomMessage, 'Nom valide ✓');
    return true;
}

/**
 * Fonction de validation du prix
 * @returns {boolean}
 */
function validatePrix() {
    const value = prixInput.value.trim();
    
    if (value === '') {
        showError(prixMessage, 'Le prix est requis');
        return false;
    }
    
    const prix = parseFloat(value);
    
    if (isNaN(prix)) {
        showError(prixMessage, 'Le prix doit être un nombre valide');
        return false;
    }
    
    if (prix <= 0) {
        showError(prixMessage, 'Le prix doit être positif');
        return false;
    }
    
    showSuccess(prixMessage, 'Prix valide ✓');
    return true;
}

/**
 * Fonction de validation de la catégorie
 * @returns {boolean}
 */
function validateCategorie() {
    const value = categorieSelect.value;
    const validCategories = ['plan', 'premium', 'coaching', 'guide'];
    
    if (value === '') {
        showError(categorieMessage, 'Veuillez sélectionner une catégorie');
        return false;
    }
    
    if (!validCategories.includes(value)) {
        showError(categorieMessage, 'Catégorie invalide');
        return false;
    }
    
    showSuccess(categorieMessage, 'Catégorie valide ✓');
    return true;
}

/**
 * Fonction de validation du type de vente
 * @returns {boolean}
 */
function validateTypeVente() {
    const value = typeVenteSelect.value;
    const validTypes = ['abonnement', 'achat_unique'];
    
    if (value === '') {
        showError(typeVenteMessage, 'Veuillez sélectionner un type de vente');
        return false;
    }
    
    if (!validTypes.includes(value)) {
        showError(typeVenteMessage, 'Type de vente invalide');
        return false;
    }
    
    showSuccess(typeVenteMessage, 'Type de vente valide ✓');
    return true;
}

/**
 * Affiche un message d'erreur
 * @param {HTMLElement} element
 * @param {string} message
 */
function showError(element, message) {
    element.textContent = message;
    element.className = 'validation-message error';
}

/**
 * Affiche un message de succès
 * @param {HTMLElement} element
 * @param {string} message
 */
function showSuccess(element, message) {
    element.textContent = message;
    element.className = 'validation-message success';
}

/**
 * Efface un message de validation
 * @param {HTMLElement} element
 */
function clearMessage(element) {
    element.textContent = '';
    element.className = 'validation-message';
}

/**
 * NIVEAU 1 : Validation onClick sur le bouton Submit
 */
submitBtn.addEventListener('click', function(e) {
    const errors = [];
    
    if (nomInput.value.trim().length < 3) {
        errors.push('Le nom doit contenir au moins 3 caractères');
    }
    
    if (prixInput.value === '' || parseFloat(prixInput.value) <= 0) {
        errors.push('Le prix doit être un nombre positif');
    }
    
    if (categorieSelect.value === '') {
        errors.push('Veuillez sélectionner une catégorie');
    }
    
    if (typeVenteSelect.value === '') {
        errors.push('Veuillez sélectionner un type de vente');
    }
    
    if (errors.length > 0) {
        e.preventDefault();
        alert('Erreurs de validation :\n\n' + errors.join('\n'));
    }
});

/**
 * NIVEAU 2 : Validation onSubmit du formulaire
 */
form.addEventListener('submit', function(e) {
    // Valider tous les champs
    const isNomValid = validateNom();
    const isPrixValid = validatePrix();
    const isCategorieValid = validateCategorie();
    const isTypeVenteValid = validateTypeVente();
    
    // Si au moins un champ est invalide, empêcher la soumission
    if (!isNomValid || !isPrixValid || !isCategorieValid || !isTypeVenteValid) {
        e.preventDefault();
    }
});

/**
 * NIVEAU 3 : Validation en temps réel
 */

// Validation du nom en temps réel (keyup)
nomInput.addEventListener('keyup', function() {
    validateNom();
});

// Validation du prix au blur (perte de focus)
prixInput.addEventListener('blur', function() {
    validatePrix();
});

// Validation de la catégorie au changement
categorieSelect.addEventListener('change', function() {
    if (validateCategorie()) {
        showSuccess(categorieMessage, `Catégorie "${this.options[this.selectedIndex].text}" sélectionnée ✓`);
    }
});

// Validation du type de vente au changement
typeVenteSelect.addEventListener('change', function() {
    validateTypeVente();
});

// Affichage du statut de disponibilité
disponibleCheckbox.addEventListener('change', function() {
    if (this.checked) {
        showSuccess(disponibleMessage, 'Produit visible ✓');
    } else {
        showError(disponibleMessage, 'Produit masqué');
    }
});

// Initialiser le message de disponibilité au chargement
window.addEventListener('DOMContentLoaded', function() {
    if (disponibleCheckbox.checked) {
        showSuccess(disponibleMessage, 'Produit visible ✓');
    } else {
        showError(disponibleMessage, 'Produit masqué');
    }
});
