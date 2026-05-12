<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de Recettes - NutriSmart</title>
    <link rel="stylesheet" href="css/recipe-generator.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🍳 Générateur de Recettes IA</h1>
            <p>Créez des recettes personnalisées avec l'intelligence artificielle</p>
        </header>

        <div class="main-content">
            <!-- Recipe Generator Form -->
            <section class="generator-section">
                <h2>Générer une Recette</h2>
                <form id="recipeForm">
                    <div class="form-group">
                        <label for="ingredients">Ingrédients disponibles *</label>
                        <div class="ingredient-input">
                            <input type="text" id="ingredientInput" placeholder="Ex: poulet, riz, tomates...">
                            <button type="button" id="addIngredient" class="btn-secondary">Ajouter</button>
                        </div>
                        <div id="ingredientsList" class="tags-container"></div>
                    </div>

                    <div class="form-group">
                        <label>Restrictions alimentaires</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="dietary" value="vegan"> Vegan</label>
                            <label><input type="checkbox" name="dietary" value="vegetarian"> Végétarien</label>
                            <label><input type="checkbox" name="dietary" value="gluten-free"> Sans gluten</label>
                            <label><input type="checkbox" name="dietary" value="halal"> Halal</label>
                            <label><input type="checkbox" name="dietary" value="dairy-free"> Sans lactose</label>
                            <label><input type="checkbox" name="dietary" value="keto"> Keto</label>
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

                    <button type="submit" id="generateBtn" class="btn-primary">
                        <span class="btn-text">Générer la Recette</span>
                        <span class="btn-loader" style="display: none;">⏳ Génération...</span>
                    </button>
                </form>
            </section>

            <!-- Recipe Suggestions -->
            <section class="suggestions-section">
                <h2>Suggestions du Jour</h2>
                <button id="getSuggestions" class="btn-secondary">Obtenir des Suggestions</button>
                <div id="suggestionsList" class="recipes-grid"></div>
            </section>

            <!-- Generated Recipe Display -->
            <section id="recipeDisplay" class="recipe-display" style="display: none;">
                <h2>Votre Recette</h2>
                <div class="recipe-card">
                    <div class="recipe-header">
                        <h3 id="recipeTitle"></h3>
                        <span id="recipeDifficulty" class="badge"></span>
                    </div>
                    
                    <p id="recipeDescription" class="recipe-description"></p>

                    <div class="recipe-meta">
                        <div class="meta-item">
                            <span class="icon">🔥</span>
                            <span id="recipeCalories"></span> kcal
                        </div>
                        <div class="meta-item">
                            <span class="icon">⏱️</span>
                            <span id="recipePrepTime"></span> min préparation
                        </div>
                        <div class="meta-item">
                            <span class="icon">🍳</span>
                            <span id="recipeCookTime"></span> min cuisson
                        </div>
                    </div>

                    <div class="recipe-macros">
                        <div class="macro-item">
                            <span class="macro-label">Protéines</span>
                            <span id="recipeProtein" class="macro-value"></span>g
                        </div>
                        <div class="macro-item">
                            <span class="macro-label">Glucides</span>
                            <span id="recipeCarbs" class="macro-value"></span>g
                        </div>
                        <div class="macro-item">
                            <span class="macro-label">Lipides</span>
                            <span id="recipeFats" class="macro-value"></span>g
                        </div>
                    </div>

                    <div class="recipe-content">
                        <div class="ingredients-section">
                            <h4>📝 Ingrédients</h4>
                            <ul id="recipeIngredients"></ul>
                        </div>

                        <div class="steps-section">
                            <h4>👨‍🍳 Étapes</h4>
                            <ol id="recipeSteps"></ol>
                        </div>
                    </div>

                    <div id="recipeVideo" class="video-section" style="display: none;">
                        <h4>🎥 Tutoriel Vidéo</h4>
                        <div id="videoContainer"></div>
                    </div>

                    <div class="recipe-actions">
                        <button id="saveRecipe" class="btn-primary">💾 Sauvegarder</button>
                        <button id="shareRecipe" class="btn-secondary">📤 Partager</button>
                        <button id="printRecipe" class="btn-secondary">🖨️ Imprimer</button>
                    </div>
                </div>
            </section>

            <!-- Saved Recipes -->
            <section class="saved-recipes-section">
                <h2>Mes Recettes Sauvegardées</h2>
                <div id="savedRecipesList" class="recipes-grid"></div>
            </section>
        </div>
    </div>

    <script src="js/recipe-generator.js"></script>
</body>
</html>
