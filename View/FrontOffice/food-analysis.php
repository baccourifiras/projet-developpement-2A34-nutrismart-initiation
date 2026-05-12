<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Nutritionnelle - NutriSmart</title>
    <link rel="stylesheet" href="css/food-analysis.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>📸 Analyse Nutritionnelle IA</h1>
            <p>Analysez vos repas par photo ou code-barres</p>
        </header>

        <div class="main-content">
            <!-- Text Description Section (NEW - Recommended) -->
            <section class="description-section">
                <h2>✨ Décrire votre Repas (Recommandé)</h2>
                <p class="info-text">💡 Décrivez votre repas en texte pour une analyse précise avec l'IA</p>
                
                <div class="form-group">
                    <label for="foodDescription">Description du repas</label>
                    <textarea id="foodDescription" rows="4" placeholder="Ex: Un bol de riz blanc (200g), poulet grillé (150g), brocolis vapeur (100g), une cuillère d'huile d'olive"></textarea>
                </div>

                <div class="form-group">
                    <label for="mealTypeText">Type de repas</label>
                    <select id="mealTypeText">
                        <option value="">Sélectionner...</option>
                        <option value="breakfast">Petit-déjeuner</option>
                        <option value="lunch">Déjeuner</option>
                        <option value="dinner">Dîner</option>
                        <option value="snack">Collation</option>
                    </select>
                </div>

                <button id="analyzeTextBtn" class="btn-primary">
                    <span class="btn-text">🔍 Analyser avec IA</span>
                    <span class="btn-loader" style="display: none;">⏳ Analyse en cours...</span>
                </button>
            </section>

            <!-- Image Upload Section -->
            <section class="upload-section">
                <h2>📸 Analyser une Photo de Repas</h2>
                <p class="warning-text">⚠️ Note: L'analyse par image est limitée. Utilisez la description textuelle pour de meilleurs résultats.</p>
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📷</div>
                    <p>Glissez une photo ici ou cliquez pour sélectionner</p>
                    <input type="file" id="imageInput" accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
                    <button type="button" id="selectImageBtn" class="btn-primary">Choisir une Photo</button>
                </div>

                <div id="imagePreview" class="image-preview" style="display: none;">
                    <img id="previewImg" src="" alt="Preview">
                    <button id="removeImage" class="btn-remove">✕</button>
                </div>

                <div class="form-group">
                    <label for="mealType">Type de repas</label>
                    <select id="mealType">
                        <option value="">Sélectionner...</option>
                        <option value="breakfast">Petit-déjeuner</option>
                        <option value="lunch">Déjeuner</option>
                        <option value="dinner">Dîner</option>
                        <option value="snack">Collation</option>
                    </select>
                </div>

                <button id="analyzeBtn" class="btn-primary" disabled>
                    <span class="btn-text">🔍 Analyser</span>
                    <span class="btn-loader" style="display: none;">⏳ Analyse en cours...</span>
                </button>
            </section>

            <!-- Barcode Scanner Section -->
            <section class="barcode-section">
                <h2>Scanner un Code-Barres</h2>
                <div class="barcode-input">
                    <input type="text" id="barcodeInput" placeholder="Entrez le code-barres ou scannez">
                    <button id="scanBarcodeBtn" class="btn-secondary">🔍 Scanner</button>
                </div>
                <div id="barcodeResult" class="result-card" style="display: none;"></div>
            </section>

            <!-- Analysis Results -->
            <section id="analysisResults" class="results-section" style="display: none;">
                <h2>Résultats de l'Analyse</h2>
                <div class="analysis-card">
                    <div class="analysis-header">
                        <h3>Aliments Détectés</h3>
                        <div class="total-calories">
                            <span class="calories-label">Total:</span>
                            <span id="totalCalories" class="calories-value">0</span> kcal
                        </div>
                    </div>

                    <div id="detectedFoods" class="foods-list"></div>

                    <div class="macros-chart">
                        <h4>Macronutriments</h4>
                        <div class="macros-bars">
                            <div class="macro-bar">
                                <div class="macro-info">
                                    <span class="macro-name">Protéines</span>
                                    <span id="proteinValue" class="macro-value">0g</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="proteinBar" class="progress-fill protein" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="macro-bar">
                                <div class="macro-info">
                                    <span class="macro-name">Glucides</span>
                                    <span id="carbsValue" class="macro-value">0g</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="carbsBar" class="progress-fill carbs" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="macro-bar">
                                <div class="macro-info">
                                    <span class="macro-name">Lipides</span>
                                    <span id="fatsValue" class="macro-value">0g</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="fatsBar" class="progress-fill fats" style="width: 0%"></div>
                                </div>
                            </div>
                            <div class="macro-bar">
                                <div class="macro-info">
                                    <span class="macro-name">Fibres</span>
                                    <span id="fiberValue" class="macro-value">0g</span>
                                </div>
                                <div class="progress-bar">
                                    <div id="fiberBar" class="progress-fill fiber" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="micronutrients" class="micronutrients-section" style="display: none;">
                        <h4>Micronutriments</h4>
                        <div id="micronutrientsList" class="nutrients-grid"></div>
                    </div>

                    <div class="analysis-actions">
                        <button id="saveAnalysis" class="btn-primary">💾 Sauvegarder</button>
                        <button id="addToDaily" class="btn-secondary">➕ Ajouter au Suivi</button>
                    </div>
                </div>
            </section>

            <!-- Daily Summary -->
            <section class="daily-summary-section">
                <h2>Résumé du Jour</h2>
                <div class="date-selector">
                    <input type="date" id="summaryDate" value="">
                    <button id="loadSummary" class="btn-secondary">Charger</button>
                </div>
                <div id="dailySummary" class="summary-card">
                    <div class="summary-stats">
                        <div class="stat-item">
                            <span class="stat-label">Calories Totales</span>
                            <span id="dailyCalories" class="stat-value">0</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Repas Analysés</span>
                            <span id="mealCount" class="stat-value">0</span>
                        </div>
                    </div>
                    <div id="dailyMeals" class="meals-timeline"></div>
                </div>
            </section>

            <!-- Analysis History -->
            <section class="history-section">
                <h2>Historique des Analyses</h2>
                <div id="analysisHistory" class="history-grid"></div>
            </section>
        </div>
    </div>

    <script src="js/food-analysis.js"></script>
</body>
</html>
