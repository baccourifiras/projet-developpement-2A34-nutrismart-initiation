// Recipe Generator JavaScript
const API_BASE = '../../Controller/api-recipe.php';

let ingredients = [];
let currentRecipe = null;

// DOM Elements
const ingredientInput = document.getElementById('ingredientInput');
const addIngredientBtn = document.getElementById('addIngredient');
const ingredientsList = document.getElementById('ingredientsList');
const recipeForm = document.getElementById('recipeForm');
const generateBtn = document.getElementById('generateBtn');
const recipeDisplay = document.getElementById('recipeDisplay');
const getSuggestionsBtn = document.getElementById('getSuggestions');
const suggestionsList = document.getElementById('suggestionsList');
const savedRecipesList = document.getElementById('savedRecipesList');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadSavedRecipes();
    setupEventListeners();
});

function setupEventListeners() {
    addIngredientBtn.addEventListener('click', addIngredient);
    ingredientInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addIngredient();
        }
    });
    
    recipeForm.addEventListener('submit', generateRecipe);
    getSuggestionsBtn.addEventListener('click', getSuggestions);
    
    document.getElementById('saveRecipe')?.addEventListener('click', saveRecipe);
    document.getElementById('shareRecipe')?.addEventListener('click', shareRecipe);
    document.getElementById('printRecipe')?.addEventListener('click', printRecipe);
}

function addIngredient() {
    const value = ingredientInput.value.trim();
    if (value && !ingredients.includes(value)) {
        ingredients.push(value);
        renderIngredients();
        ingredientInput.value = '';
    }
}

function removeIngredient(ingredient) {
    ingredients = ingredients.filter(i => i !== ingredient);
    renderIngredients();
}

function renderIngredients() {
    ingredientsList.innerHTML = ingredients.map(ing => `
        <span class="tag">
            ${ing}
            <button type="button" onclick="removeIngredient('${ing}')" class="tag-remove">×</button>
        </span>
    `).join('');
}

async function generateRecipe(e) {
    e.preventDefault();
    
    if (ingredients.length === 0) {
        alert('Veuillez ajouter au moins un ingrédient');
        return;
    }
    
    const formData = new FormData(recipeForm);
    const dietaryRestrictions = Array.from(formData.getAll('dietary'));
    
    const requestData = {
        ingredients: ingredients,
        dietary_restrictions: dietaryRestrictions,
        difficulty: formData.get('difficulty'),
        meal_type: formData.get('mealType'),
        target_calories: parseInt(formData.get('calories'))
    };
    
    setLoading(true);
    
    try {
        const response = await fetch(`${API_BASE}?action=generate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentRecipe = data.recipe;
            displayRecipe(data.recipe);
            showNotification('Recette générée avec succès!', 'success');
        } else {
            showNotification('Erreur: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Erreur de connexion au serveur', 'error');
    } finally {
        setLoading(false);
    }
}

function displayRecipe(recipe) {
    document.getElementById('recipeTitle').textContent = recipe.title;
    document.getElementById('recipeDescription').textContent = recipe.description;
    document.getElementById('recipeDifficulty').textContent = getDifficultyLabel(recipe.difficulty);
    document.getElementById('recipeDifficulty').className = `badge badge-${recipe.difficulty}`;
    
    document.getElementById('recipeCalories').textContent = recipe.calories;
    document.getElementById('recipePrepTime').textContent = recipe.prep_time;
    document.getElementById('recipeCookTime').textContent = recipe.cook_time;
    
    document.getElementById('recipeProtein').textContent = recipe.macros.protein;
    document.getElementById('recipeCarbs').textContent = recipe.macros.carbs;
    document.getElementById('recipeFats').textContent = recipe.macros.fats;
    
    // Ingredients
    const ingredientsHTML = recipe.ingredients.map(ing => 
        `<li><strong>${ing.quantity}</strong> ${ing.name}</li>`
    ).join('');
    document.getElementById('recipeIngredients').innerHTML = ingredientsHTML;
    
    // Steps
    const stepsHTML = recipe.steps.map(step => 
        `<li>${step}</li>`
    ).join('');
    document.getElementById('recipeSteps').innerHTML = stepsHTML;
    
    // Video
    if (recipe.video_url) {
        const videoId = extractYouTubeId(recipe.video_url);
        if (videoId) {
            document.getElementById('videoContainer').innerHTML = `
                <iframe width="100%" height="315" 
                    src="https://www.youtube.com/embed/${videoId}" 
                    frameborder="0" allowfullscreen>
                </iframe>
            `;
            document.getElementById('recipeVideo').style.display = 'block';
        }
    }
    
    recipeDisplay.style.display = 'block';
    recipeDisplay.scrollIntoView({ behavior: 'smooth' });
}

async function getSuggestions() {
    const formData = new FormData(recipeForm);
    const dietaryRestrictions = Array.from(formData.getAll('dietary')).join(',');
    const difficulty = formData.get('difficulty');
    
    try {
        const response = await fetch(
            `${API_BASE}?action=suggest&dietary_restrictions=${dietaryRestrictions}&difficulty=${difficulty}&count=3`
        );
        const data = await response.json();
        
        if (data.success) {
            displaySuggestions(data.recipes);
        } else {
            showNotification('Erreur: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Erreur de connexion', 'error');
    }
}

function displaySuggestions(recipes) {
    suggestionsList.innerHTML = recipes.map(recipe => `
        <div class="recipe-card-mini">
            <h4>${recipe.title}</h4>
            <p>${recipe.description}</p>
            <div class="recipe-meta-mini">
                <span>🔥 ${recipe.calories} kcal</span>
                <span>⏱️ ${recipe.total_time} min</span>
                <span class="badge badge-${recipe.difficulty}">${getDifficultyLabel(recipe.difficulty)}</span>
            </div>
            <button onclick="viewRecipe(${recipe.id_recipe})" class="btn-secondary btn-sm">Voir la recette</button>
        </div>
    `).join('');
}

async function loadSavedRecipes() {
    try {
        const response = await fetch(`${API_BASE}?action=list&limit=6`);
        const data = await response.json();
        
        if (data.success && data.recipes.length > 0) {
            displaySavedRecipes(data.recipes);
        }
    } catch (error) {
        console.error('Error loading saved recipes:', error);
    }
}

function displaySavedRecipes(recipes) {
    savedRecipesList.innerHTML = recipes.map(recipe => `
        <div class="recipe-card-mini">
            <h4>${recipe.title}</h4>
            <div class="recipe-meta-mini">
                <span>🔥 ${recipe.calories} kcal</span>
                <span>⏱️ ${recipe.total_time} min</span>
            </div>
            <button onclick="viewRecipe(${recipe.id_recipe})" class="btn-secondary btn-sm">Voir</button>
        </div>
    `).join('');
}

async function viewRecipe(id) {
    try {
        const response = await fetch(`${API_BASE}?action=show&id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            currentRecipe = data.recipe;
            displayRecipe(data.recipe);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function saveRecipe() {
    if (currentRecipe) {
        showNotification('Recette sauvegardée!', 'success');
        loadSavedRecipes();
    }
}

function shareRecipe() {
    if (currentRecipe) {
        const text = `Découvrez cette recette: ${currentRecipe.title}`;
        if (navigator.share) {
            navigator.share({ title: currentRecipe.title, text: text });
        } else {
            navigator.clipboard.writeText(text);
            showNotification('Lien copié dans le presse-papier', 'success');
        }
    }
}

function printRecipe() {
    window.print();
}

function getDifficultyLabel(difficulty) {
    const labels = {
        'easy': 'Facile',
        'medium': 'Moyen',
        'hard': 'Difficile'
    };
    return labels[difficulty] || difficulty;
}

function extractYouTubeId(url) {
    const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/);
    return match ? match[1] : null;
}

function setLoading(loading) {
    const btnText = generateBtn.querySelector('.btn-text');
    const btnLoader = generateBtn.querySelector('.btn-loader');
    
    if (loading) {
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
        generateBtn.disabled = true;
    } else {
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        generateBtn.disabled = false;
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
