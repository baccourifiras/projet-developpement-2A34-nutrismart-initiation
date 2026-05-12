<?php
/**
 * =====================================================================
 *  NutriSmart - frontoffice/generateur-recettes.php
 *  Page publique : Générateur de recettes IA
 * =====================================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart - Générateur de Recettes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;900&display=swap">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <nav class="navbar" id="navbar">
    <div class="nav-brand">
      <div class="logo">NutriSmart</div>
      <div class="slogan">Eat Smart Live Smart</div>
    </div>
    <div class="nav-links">
      <a href="regimes.php">Regimes</a>
      <a href="suivis.php">Suivis</a>
      <a href="recommandations.php">Recommandations</a>
      <a href="generateur-recettes.php" class="active">Recettes IA</a>
      <a href="analyse-nutrition.php">Analyse Nutrition</a>
      <a href="../backoffice/index.php" class="nav-dashboard">Administration</a>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Intelligence Artificielle</p>
    <h1>Générateur de Recettes</h1>
    <p class="subtitle">Créez des recettes personnalisées avec l'intelligence artificielle basée sur vos ingrédients et préférences alimentaires.</p>
  </header>

  <main class="container">
    
    <!-- Recipe Generation Form Section -->
    <section class="section">
      <h2>Créer une Recette</h2>
      
      <form id="recipeForm" class="form-grid">
        <div class="form-row">
          <div class="form-group">
            <label for="ingredientInput">Ingrédients disponibles</label>
            <div class="input-with-button">
              <input type="text" id="ingredientInput" placeholder="Ex: poulet, riz, tomates">
              <button type="button" id="addIngredient" class="secondary-btn">Ajouter</button>
            </div>
            <div id="ingredientsList" class="tags-list"></div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label>Restrictions alimentaires</label>
            <div class="checkbox-grid">
              <label class="checkbox-label">
                <input type="checkbox" name="dietary" value="vegan">
                <span>Vegan</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="dietary" value="vegetarian">
                <span>Végétarien</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="dietary" value="gluten-free">
                <span>Sans gluten</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="dietary" value="halal">
                <span>Halal</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="dietary" value="dairy-free">
                <span>Sans lactose</span>
              </label>
              <label class="checkbox-label">
                <input type="checkbox" name="dietary" value="keto">
                <span>Keto</span>
              </label>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="difficulty">Difficulté</label>
            <select id="difficulty" name="difficulty">
              <option value="easy">Facile</option>
              <option value="medium" selected>Moyen</option>
              <option value="hard">Difficile</option>
            </select>
          </div>

          <div class="form-group">
            <label for="mealType">Type de repas</label>
            <select id="mealType" name="mealType">
              <option value="breakfast">Petit-déjeuner</option>
              <option value="lunch" selected>Déjeuner</option>
              <option value="dinner">Dîner</option>
              <option value="snack">Collation</option>
            </select>
          </div>

          <div class="form-group">
            <label for="calories">Calories cibles</label>
            <input type="number" id="calories" name="calories" value="500" min="100" max="2000" step="50">
          </div>
        </div>

        <button type="submit" id="generateBtn" class="primary-btn">
          <span id="btnText">Générer la Recette</span>
          <span id="btnLoader" style="display: none;">Génération en cours...</span>
        </button>
      </form>
    </section>

    <!-- Generated Recipe Display Section -->
    <section id="recipeDisplay" class="section" style="display: none;">
      <div class="section-title-row">
        <h2 id="recipeTitle"></h2>
        <span id="recipeDifficulty" class="recipe-badge"></span>
      </div>
      
      <p id="recipeDescription" class="recipe-desc"></p>

      <div class="recipe-meta">
        <span class="meta-chip"><strong id="recipeCalories"></strong> kcal</span>
        <span class="meta-chip"><strong id="recipePrepTime"></strong> min préparation</span>
        <span class="meta-chip"><strong id="recipeCookTime"></strong> min cuisson</span>
      </div>

      <div class="macros-grid">
        <div class="macro-card">
          <div class="macro-label">Protéines</div>
          <div class="macro-value"><span id="recipeProtein">0</span>g</div>
        </div>
        <div class="macro-card">
          <div class="macro-label">Glucides</div>
          <div class="macro-value"><span id="recipeCarbs">0</span>g</div>
        </div>
        <div class="macro-card">
          <div class="macro-label">Lipides</div>
          <div class="macro-value"><span id="recipeFats">0</span>g</div>
        </div>
      </div>

      <div class="recipe-details">
        <div class="recipe-section">
          <h3>Ingrédients</h3>
          <ul id="recipeIngredients" class="recipe-ingredients"></ul>
        </div>

        <div class="recipe-section">
          <h3>Étapes de préparation</h3>
          <ol id="recipeSteps" class="recipe-steps"></ol>
        </div>
      </div>
    </section>

    <!-- Saved Recipes Section -->
    <section class="section">
      <h2>Recettes Récentes</h2>
      <div id="savedRecipesList" class="recipes-grid"></div>
    </section>

  </main>

  <script>
    // API endpoint for recipe generation
    const API_BASE = '../../Controller/api-recipe.php';
    // Array to store user-added ingredients
    let ingredients = [];

    // Event listeners for adding ingredients
    document.getElementById('addIngredient').addEventListener('click', addIngredient);
    document.getElementById('ingredientInput').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        addIngredient();
      }
    });
    // Event listener for form submission
    document.getElementById('recipeForm').addEventListener('submit', generateRecipe);

    // Function to add an ingredient to the list
    function addIngredient() {
      const input = document.getElementById('ingredientInput');
      const value = input.value.trim();
      if (value && !ingredients.includes(value)) {
        ingredients.push(value);
        renderIngredients();
        input.value = '';
      }
    }

    // Function to remove an ingredient from the list
    function removeIngredient(ingredient) {
      ingredients = ingredients.filter(i => i !== ingredient);
      renderIngredients();
    }

    // Function to render the ingredients list in the UI
    function renderIngredients() {
      const list = document.getElementById('ingredientsList');
      list.innerHTML = ingredients.map(ing => `
        <span class="tag">
          ${ing}
          <button type="button" onclick="removeIngredient('${ing}')" class="tag-remove">×</button>
        </span>
      `).join('');
    }

    // Function to generate a recipe based on form data
    async function generateRecipe(e) {
      e.preventDefault();
      
      if (ingredients.length === 0) {
        alert('Veuillez ajouter au moins un ingrédient');
        return;
      }
      
      const formData = new FormData(e.target);
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
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(requestData)
        });
        
        const data = await response.json();
        
        if (data.success) {
          displayRecipe(data.recipe);
        } else {
          alert('Erreur: ' + data.error);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Erreur de connexion au serveur');
      } finally {
        setLoading(false);
      }
    }

    // Function to display the generated recipe in the UI
    function displayRecipe(recipe) {
      document.getElementById('recipeTitle').textContent = recipe.title;
      document.getElementById('recipeDescription').textContent = recipe.description;
      document.getElementById('recipeDifficulty').textContent = getDifficultyLabel(recipe.difficulty);
      document.getElementById('recipeDifficulty').className = `recipe-badge regime-${recipe.difficulty}`;
      
      document.getElementById('recipeCalories').textContent = recipe.calories;
      document.getElementById('recipePrepTime').textContent = recipe.prep_time;
      document.getElementById('recipeCookTime').textContent = recipe.cook_time;
      
      document.getElementById('recipeProtein').textContent = recipe.macros.protein;
      document.getElementById('recipeCarbs').textContent = recipe.macros.carbs;
      document.getElementById('recipeFats').textContent = recipe.macros.fats;
      
      const ingredientsHTML = recipe.ingredients.map(ing => 
        `<li><strong>${ing.quantity}</strong> ${ing.name}</li>`
      ).join('');
      document.getElementById('recipeIngredients').innerHTML = ingredientsHTML;
      
      const stepsHTML = recipe.steps.map(step => 
        `<li>${step}</li>`
      ).join('');
      document.getElementById('recipeSteps').innerHTML = stepsHTML;
      
      document.getElementById('recipeDisplay').style.display = 'block';
      document.getElementById('recipeDisplay').scrollIntoView({ behavior: 'smooth' });
      
      loadSavedRecipes();
    }

    // Function to get the difficulty label in French
    function getDifficultyLabel(difficulty) {
      const labels = { 'easy': 'Facile', 'medium': 'Moyen', 'hard': 'Difficile' };
      return labels[difficulty] || difficulty;
    }

    // Function to set the loading state of the generate button
    function setLoading(loading) {
      const btnText = document.getElementById('btnText');
      const btnLoader = document.getElementById('btnLoader');
      const btn = document.getElementById('generateBtn');
      
      if (loading) {
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
        btn.disabled = true;
      } else {
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        btn.disabled = false;
      }
    }

    // Function to load and display saved recipes
    async function loadSavedRecipes() {
      try {
        const response = await fetch(`${API_BASE}?action=list&limit=6`);
        const data = await response.json();
        
        if (data.success && data.recipes.length > 0) {
          const html = data.recipes.map(recipe => `
            <article class="recipe-card">
              <div class="recipe-content">
                <h2>${recipe.title}</h2>
                <p class="recipe-desc">${recipe.description}</p>
                <div class="recipe-meta">
                  <span class="meta-chip"><strong>${recipe.calories}</strong> kcal</span>
                  <span class="meta-chip"><strong>${recipe.total_time}</strong> min</span>
                </div>
              </div>
            </article>
          `).join('');
          document.getElementById('savedRecipesList').innerHTML = html;
        }
      } catch (error) {
        console.error('Error loading recipes:', error);
      }
    }

    // Load saved recipes on page load
    loadSavedRecipes();
  </script>

  <style>
    /* Custom styles for the recipe generator page */
    /* Enhanced Input with Button */
    .input-with-button {
      display: flex;
      gap: 10px;
      align-items: stretch;
    }
    .input-with-button input {
      flex: 1;
      padding: 14px 18px;
      border: 2px solid rgba(31,164,99,.2);
      border-radius: 14px;
      background: white;
      font-size: 15px;
      transition: all 0.3s ease;
    }
    .input-with-button input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(31,164,99,.1);
      transform: translateY(-1px);
    }
    .input-with-button .secondary-btn {
      padding: 14px 24px;
      white-space: nowrap;
      font-weight: 700;
      box-shadow: 0 4px 12px rgba(31,164,99,0.15);
    }
    
    /* Enhanced Tags List */
    .tags-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 16px;
      min-height: 40px;
      padding: 12px;
      background: linear-gradient(135deg, rgba(31,164,99,.03), rgba(255,255,255,.5));
      border-radius: 12px;
      border: 1px dashed rgba(31,164,99,.2);
    }
    .tags-list:empty::before {
      content: 'Aucun ingrédient ajouté';
      color: var(--muted);
      font-size: 14px;
      font-style: italic;
    }
    .tag {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 8px 14px;
      border-radius: 999px;
      font-size: 14px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(31,164,99,0.25);
      transition: all 0.3s ease;
      animation: tagSlideIn 0.3s ease;
    }
    .tag:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(31,164,99,0.35);
    }
    @keyframes tagSlideIn {
      from { opacity: 0; transform: scale(0.8); }
      to { opacity: 1; transform: scale(1); }
    }
    .tag-remove {
      background: rgba(255,255,255,0.2);
      border: none;
      color: white;
      font-size: 18px;
      cursor: pointer;
      padding: 0;
      width: 22px;
      height: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.2s ease;
    }
    .tag-remove:hover {
      background: rgba(255,255,255,0.3);
      transform: rotate(90deg);
    }
    
    /* Enhanced Checkbox Grid */
    .checkbox-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 12px;
      margin-top: 12px;
    }
    .checkbox-label {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      background: white;
      border: 2px solid rgba(31,164,99,.15);
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    .checkbox-label:hover {
      border-color: var(--primary);
      background: rgba(31,164,99,.05);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(31,164,99,0.1);
    }
    .checkbox-label input[type="checkbox"] {
      width: 20px;
      height: 20px;
      cursor: pointer;
      accent-color: var(--primary);
    }
    .checkbox-label input[type="checkbox"]:checked + span {
      color: var(--primary-dark);
      font-weight: 700;
    }
    
    /* Enhanced Form Elements */
    .form-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .form-group label {
      font-weight: 700;
      color: var(--text);
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .form-group label::before {
      content: '●';
      color: var(--primary);
      font-size: 10px;
    }
    .form-group input,
    .form-group select {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid rgba(31,164,99,.2);
      border-radius: 12px;
      background: white;
      font-size: 15px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(31,164,99,.1);
      transform: translateY(-1px);
    }
    .form-group select {
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231fa463' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 16px center;
      padding-right: 40px;
    }
    
    /* Enhanced Macros Grid */
    .macros-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    .macro-card {
      background: linear-gradient(135deg, rgba(31,164,99,.08), white);
      border: 2px solid rgba(31,164,99,.15);
      border-radius: 18px;
      padding: 24px;
      text-align: center;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    .macro-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    }
    .macro-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(31,164,99,0.15);
      border-color: var(--primary);
    }
    .macro-label {
      font-size: 12px;
      color: var(--muted);
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-weight: 700;
      margin-bottom: 10px;
    }
    .macro-value {
      font-size: 32px;
      font-weight: 900;
      color: var(--primary-dark);
      line-height: 1;
    }
    
    /* Enhanced Recipe Details */
    .recipe-details {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 30px;
      margin-top: 30px;
    }
    .recipe-section {
      background: white;
      padding: 24px;
      border-radius: 16px;
      border: 2px solid rgba(31,164,99,.1);
    }
    .recipe-section h3 {
      font-size: 20px;
      margin-bottom: 20px;
      color: var(--text);
      padding-bottom: 12px;
      border-bottom: 3px solid var(--primary);
      display: inline-block;
    }
    .recipe-ingredients,
    .recipe-steps {
      padding-left: 24px;
      margin: 0;
    }
    .recipe-ingredients li {
      margin-bottom: 14px;
      line-height: 1.6;
      color: var(--text);
      position: relative;
      padding-left: 8px;
    }
    .recipe-ingredients li::marker {
      color: var(--primary);
      font-weight: 700;
    }
    .recipe-ingredients li strong {
      color: var(--primary-dark);
      font-weight: 700;
    }
    .recipe-steps li {
      margin-bottom: 18px;
      line-height: 1.7;
      color: var(--text);
      padding-left: 12px;
    }
    .recipe-steps li::marker {
      color: var(--primary);
      font-weight: 800;
      font-size: 18px;
    }
    
    /* Enhanced Recipe Cards */
    .recipes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 24px;
    }
    .recipe-card {
      background: white;
      border: 2px solid rgba(31,164,99,.15);
      border-radius: 18px;
      padding: 24px;
      transition: all 0.3s ease;
      cursor: pointer;
    }
    .recipe-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 16px 32px rgba(31,164,99,0.15);
      border-color: var(--primary);
    }
    .recipe-card h2 {
      font-size: 20px;
      margin: 0 0 12px;
      color: var(--text);
    }
    .recipe-desc {
      color: var(--muted);
      line-height: 1.6;
      margin-bottom: 16px;
    }
    
    @media (max-width: 768px) {
      .recipe-details {
        grid-template-columns: 1fr;
      }
      .macros-grid {
        grid-template-columns: 1fr;
      }
      .checkbox-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>

</body>
</html>
