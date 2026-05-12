<?php
/**
 * =====================================================================
 *  NutriSmart - frontoffice/analyse-nutrition.php
 *  Page publique : Analyse nutritionnelle IA
 * =====================================================================
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NutriSmart - Analyse Nutritionnelle</title>
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
      <a href="generateur-recettes.php">Recettes IA</a>
      <a href="analyse-nutrition.php" class="active">Analyse Nutrition</a>
      <a href="../backoffice/index.php" class="nav-dashboard">Administration</a>
    </div>
  </nav>

  <header class="page-header">
    <p class="badge">Intelligence Artificielle</p>
    <h1>Analyse Nutritionnelle</h1>
    <p class="subtitle">Analysez vos repas par description textuelle ou code-barres pour obtenir des informations nutritionnelles détaillées.</p>
  </header>

  <main class="container">
    
    <!-- Analyse par description -->
    <section class="section">
      <h2>Décrire votre Repas</h2>
      <p class="info-note">Décrivez votre repas en détail pour une analyse nutritionnelle précise par intelligence artificielle.</p>
      
      <form id="textAnalysisForm" class="form-grid">
        <div class="form-group">
          <label for="foodDescription">Description du repas</label>
          <textarea id="foodDescription" rows="4" placeholder="Ex: Un bol de riz blanc (200g), poulet grillé (150g), brocolis vapeur (100g), une cuillère d'huile d'olive"></textarea>
        </div>

        <div class="form-row">
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
        </div>

        <button type="submit" id="analyzeTextBtn" class="primary-btn">
          <span id="textBtnText">Analyser</span>
          <span id="textBtnLoader" style="display: none;">Analyse en cours...</span>
        </button>
      </form>
    </section>

    <!-- Upload d'image -->
    <section class="section">
      <h2>Analyser une Photo de Repas</h2>
      <p class="info-note">Téléchargez une photo de votre repas pour une analyse visuelle (Note: Fonctionnalité limitée, la description textuelle est recommandée).</p>
      
      <div class="upload-area" id="uploadArea">
        <div class="upload-icon">📷</div>
        <p>Glissez une photo ici ou cliquez pour sélectionner</p>
        <input type="file" id="imageInput" accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
        <button type="button" id="selectImageBtn" class="secondary-btn">Choisir une Photo</button>
      </div>

      <div id="imagePreview" class="image-preview" style="display: none;">
        <img id="previewImg" src="" alt="Preview">
        <button type="button" id="removeImage" class="btn-remove">×</button>
      </div>

      <div class="form-group" style="margin-top: 16px;">
        <label for="mealTypeImage">Type de repas</label>
        <select id="mealTypeImage">
          <option value="">Sélectionner...</option>
          <option value="breakfast">Petit-déjeuner</option>
          <option value="lunch">Déjeuner</option>
          <option value="dinner">Dîner</option>
          <option value="snack">Collation</option>
        </select>
      </div>

      <button id="analyzeImageBtn" class="primary-btn" disabled>
        <span id="imageBtnText">Analyser</span>
        <span id="imageBtnLoader" style="display: none;">Analyse en cours...</span>
      </button>
    </section>

    <!-- Scanner code-barres -->
    <section class="section">
      <h2>Scanner un Code-Barres</h2>
      <p class="info-note">Entrez le code-barres d'un produit alimentaire pour obtenir ses informations nutritionnelles.</p>
      
      <div class="barcode-input-group">
        <input type="text" id="barcodeInput" placeholder="Entrez le code-barres (ex: 3017620422003)">
        <button id="scanBarcodeBtn" class="secondary-btn">Scanner</button>
      </div>
      
      <div id="barcodeResult" class="result-card" style="display: none;"></div>
    </section>

    <!-- Résultats de l'analyse -->
    <section id="analysisResults" class="section" style="display: none;">
      <div class="section-title-row">
        <h2>Résultats de l'Analyse</h2>
        <div class="total-calories-badge">
          <span class="calories-label">Total:</span>
          <span id="totalCalories" class="calories-value">0</span> kcal
        </div>
      </div>

      <div id="detectedFoods" class="foods-list"></div>

      <div class="macros-section">
        <h3>Macronutriments</h3>
        <div class="macros-bars">
          <div class="macro-bar-item">
            <div class="macro-bar-header">
              <span class="macro-name">Protéines</span>
              <span id="proteinValue" class="macro-value">0g</span>
            </div>
            <div class="progress-bar">
              <div id="proteinBar" class="progress-fill protein-fill" style="width: 0%"></div>
            </div>
          </div>
          <div class="macro-bar-item">
            <div class="macro-bar-header">
              <span class="macro-name">Glucides</span>
              <span id="carbsValue" class="macro-value">0g</span>
            </div>
            <div class="progress-bar">
              <div id="carbsBar" class="progress-fill carbs-fill" style="width: 0%"></div>
            </div>
          </div>
          <div class="macro-bar-item">
            <div class="macro-bar-header">
              <span class="macro-name">Lipides</span>
              <span id="fatsValue" class="macro-value">0g</span>
            </div>
            <div class="progress-bar">
              <div id="fatsBar" class="progress-fill fats-fill" style="width: 0%"></div>
            </div>
          </div>
          <div class="macro-bar-item">
            <div class="macro-bar-header">
              <span class="macro-name">Fibres</span>
              <span id="fiberValue" class="macro-value">0g</span>
            </div>
            <div class="progress-bar">
              <div id="fiberBar" class="progress-fill fiber-fill" style="width: 0%"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <script>
    const API_BASE = '../../Controller/api-food-analysis.php';

    document.getElementById('textAnalysisForm').addEventListener('submit', analyzeFromText);
    document.getElementById('scanBarcodeBtn').addEventListener('click', scanBarcode);
    
    // Image upload event listeners
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imageInput');
    const selectImageBtn = document.getElementById('selectImageBtn');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');
    const analyzeImageBtn = document.getElementById('analyzeImageBtn');
    
    let selectedFile = null;
    
    selectImageBtn.addEventListener('click', () => imageInput.click());
    imageInput.addEventListener('change', handleImageSelect);
    removeImageBtn.addEventListener('click', removeImage);
    analyzeImageBtn.addEventListener('click', analyzeImage);
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = 'var(--primary)';
      uploadArea.style.background = 'rgba(31,164,99,.08)';
    });
    
    uploadArea.addEventListener('dragleave', () => {
      uploadArea.style.borderColor = '';
      uploadArea.style.background = '';
    });
    
    uploadArea.addEventListener('drop', (e) => {
      e.preventDefault();
      uploadArea.style.borderColor = '';
      uploadArea.style.background = '';
      
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        handleImageFile(files[0]);
      }
    });
    
    uploadArea.addEventListener('click', (e) => {
      if (e.target === uploadArea || e.target.closest('.upload-area')) {
        imageInput.click();
      }
    });

    function handleImageSelect(e) {
      const file = e.target.files[0];
      if (file) {
        handleImageFile(file);
      }
    }
    
    function handleImageFile(file) {
      if (!file.type.startsWith('image/')) {
        alert('Veuillez sélectionner une image valide');
        return;
      }
      
      selectedFile = file;
      
      const reader = new FileReader();
      reader.onload = (e) => {
        previewImg.src = e.target.result;
        uploadArea.style.display = 'none';
        imagePreview.style.display = 'block';
        analyzeImageBtn.disabled = false;
      };
      reader.readAsDataURL(file);
    }
    
    function removeImage() {
      selectedFile = null;
      previewImg.src = '';
      imageInput.value = '';
      uploadArea.style.display = 'flex';
      imagePreview.style.display = 'none';
      analyzeImageBtn.disabled = true;
    }
    
    async function analyzeImage() {
      if (!selectedFile) {
        alert('Veuillez sélectionner une image');
        return;
      }
      
      const formData = new FormData();
      formData.append('image', selectedFile);
      formData.append('meal_type', document.getElementById('mealTypeImage').value);
      formData.append('user_id', '1');
      
      setImageLoading(true);
      
      try {
        const response = await fetch(`${API_BASE}?action=analyze`, {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
          displayAnalysisResults(data.analysis);
        } else {
          alert('Erreur: ' + data.error);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Erreur de connexion au serveur');
      } finally {
        setImageLoading(false);
      }
    }

    async function analyzeFromText(e) {
      e.preventDefault();
      
      const description = document.getElementById('foodDescription').value.trim();
      if (!description) {
        alert('Veuillez décrire votre repas');
        return;
      }
      
      const requestData = {
        description: description,
        meal_type: document.getElementById('mealTypeText').value,
        user_id: 1
      };
      
      setTextLoading(true);
      
      try {
        const response = await fetch(`${API_BASE}?action=analyze-text`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(requestData)
        });
        
        const data = await response.json();
        
        if (data.success) {
          displayAnalysisResults(data.analysis);
        } else {
          alert('Erreur: ' + data.error);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Erreur de connexion au serveur');
      } finally {
        setTextLoading(false);
      }
    }

    async function scanBarcode() {
      const barcode = document.getElementById('barcodeInput').value.trim();
      if (!barcode) {
        alert('Veuillez entrer un code-barres');
        return;
      }
      
      try {
        const response = await fetch(`${API_BASE}?action=barcode&code=${barcode}`);
        const data = await response.json();
        
        if (data.success) {
          displayBarcodeResult(data.product);
        } else {
          alert('Produit non trouvé: ' + data.error);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Erreur de connexion');
      }
    }

    function displayAnalysisResults(analysis) {
      document.getElementById('totalCalories').textContent = analysis.total_calories;
      
      const foodsHTML = analysis.detected_foods.map(food => `
        <div class="food-item">
          <div class="food-info">
            <span class="food-name">${food.name}</span>
            <span class="food-quantity">${food.quantity}</span>
          </div>
          <div class="food-calories">
            ${food.calories} kcal
            <span class="confidence-badge confidence-${food.confidence}">${food.confidence}</span>
          </div>
        </div>
      `).join('');
      document.getElementById('detectedFoods').innerHTML = foodsHTML;
      
      const macros = analysis.macros;
      const totalMacros = macros.protein + macros.carbs + macros.fats;
      
      document.getElementById('proteinValue').textContent = macros.protein + 'g';
      document.getElementById('carbsValue').textContent = macros.carbs + 'g';
      document.getElementById('fatsValue').textContent = macros.fats + 'g';
      document.getElementById('fiberValue').textContent = (macros.fiber || 0) + 'g';
      
      document.getElementById('proteinBar').style.width = `${(macros.protein / totalMacros) * 100}%`;
      document.getElementById('carbsBar').style.width = `${(macros.carbs / totalMacros) * 100}%`;
      document.getElementById('fatsBar').style.width = `${(macros.fats / totalMacros) * 100}%`;
      document.getElementById('fiberBar').style.width = `${((macros.fiber || 0) / totalMacros) * 100}%`;
      
      document.getElementById('analysisResults').style.display = 'block';
      document.getElementById('analysisResults').scrollIntoView({ behavior: 'smooth' });
    }

    function displayBarcodeResult(product) {
      const html = `
        <div class="product-info">
          ${product.image_url ? `<img src="${product.image_url}" alt="${product.name}" class="product-image">` : ''}
          <div class="product-details">
            <h3>${product.name}</h3>
            <p class="product-brand">${product.brand}</p>
            <div class="nutrition-grid">
              <div class="nutrition-item">
                <span class="nutrition-label">Calories</span>
                <span class="nutrition-value">${product.calories} kcal/100g</span>
              </div>
              <div class="nutrition-item">
                <span class="nutrition-label">Protéines</span>
                <span class="nutrition-value">${product.macros.protein}g</span>
              </div>
              <div class="nutrition-item">
                <span class="nutrition-label">Glucides</span>
                <span class="nutrition-value">${product.macros.carbs}g</span>
              </div>
              <div class="nutrition-item">
                <span class="nutrition-label">Lipides</span>
                <span class="nutrition-value">${product.macros.fats}g</span>
              </div>
            </div>
            <p class="serving-size">Portion: ${product.serving_size}</p>
          </div>
        </div>
      `;
      
      const resultDiv = document.getElementById('barcodeResult');
      resultDiv.innerHTML = html;
      resultDiv.style.display = 'block';
    }

    function setTextLoading(loading) {
      const btnText = document.getElementById('textBtnText');
      const btnLoader = document.getElementById('textBtnLoader');
      const btn = document.getElementById('analyzeTextBtn');
      
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
    
    function setImageLoading(loading) {
      const btnText = document.getElementById('imageBtnText');
      const btnLoader = document.getElementById('imageBtnLoader');
      const btn = document.getElementById('analyzeImageBtn');
      
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
  </script>

  <style>
    /* Enhanced Info Note */
    .info-note {
      background: linear-gradient(135deg, rgba(31,164,99,.08), white);
      border: 2px solid rgba(31,164,99,.2);
      border-left: 5px solid var(--primary);
      padding: 16px 20px;
      border-radius: 14px;
      color: var(--text);
      margin-bottom: 24px;
      line-height: 1.7;
      font-weight: 500;
      box-shadow: 0 4px 12px rgba(31,164,99,0.08);
    }
    
    /* Enhanced Upload Area */
    .upload-area {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 18px;
      padding: 60px 32px;
      border: 3px dashed rgba(31,164,99,.3);
      border-radius: 20px;
      background: linear-gradient(135deg, rgba(31,164,99,.04), white);
      cursor: pointer;
      transition: all 0.4s ease;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .upload-area::before {
      content: '';
      position: absolute;
      inset: 0;
      background: radial-gradient(circle at center, rgba(31,164,99,.1), transparent 70%);
      opacity: 0;
      transition: opacity 0.4s ease;
    }
    .upload-area:hover::before {
      opacity: 1;
    }
    .upload-area:hover {
      border-color: var(--primary);
      background: rgba(31,164,99,.1);
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(31,164,99,0.15);
    }
    .upload-icon {
      font-size: 56px;
      opacity: 0.7;
      transition: all 0.3s ease;
    }
    .upload-area:hover .upload-icon {
      transform: scale(1.1);
      opacity: 1;
    }
    .upload-area p {
      color: var(--text);
      margin: 0;
      font-size: 16px;
      font-weight: 600;
      position: relative;
      z-index: 1;
    }
    .upload-area .secondary-btn {
      position: relative;
      z-index: 1;
      box-shadow: 0 6px 16px rgba(31,164,99,0.2);
    }
    
    /* Enhanced Image Preview */
    .image-preview {
      position: relative;
      border-radius: 20px;
      overflow: hidden;
      border: 3px solid var(--primary);
      max-width: 600px;
      margin: 0 auto;
      box-shadow: 0 12px 32px rgba(31,164,99,0.2);
      animation: imageZoomIn 0.4s ease;
    }
    @keyframes imageZoomIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }
    .image-preview img {
      width: 100%;
      height: auto;
      display: block;
    }
    .btn-remove {
      position: absolute;
      top: 16px;
      right: 16px;
      width: 42px;
      height: 42px;
      border-radius: 50%;
      background: linear-gradient(135deg, #dc2626, #b91c1c);
      color: white;
      border: 3px solid white;
      font-size: 24px;
      line-height: 1;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
      font-weight: 700;
    }
    .btn-remove:hover {
      background: linear-gradient(135deg, #b91c1c, #991b1b);
      transform: scale(1.15) rotate(90deg);
      box-shadow: 0 8px 20px rgba(220, 38, 38, 0.5);
    }
    
    /* Enhanced Form Elements */
    textarea {
      width: 100%;
      padding: 16px 18px;
      border: 2px solid rgba(31,164,99,.2);
      border-radius: 14px;
      background: white;
      font-family: inherit;
      resize: vertical;
      min-height: 120px;
      font-size: 15px;
      line-height: 1.6;
      transition: all 0.3s ease;
    }
    textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(31,164,99,.1);
      transform: translateY(-2px);
    }
    textarea::placeholder {
      color: rgba(99, 128, 112, 0.5);
    }
    
    .form-grid {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
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
    .form-group select,
    .form-group input {
      width: 100%;
      padding: 14px 16px;
      border: 2px solid rgba(31,164,99,.2);
      border-radius: 12px;
      background: white;
      font-family: inherit;
      font-size: 15px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .form-group select:focus,
    .form-group input:focus {
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
    
    /* Enhanced Barcode Input */
    .barcode-input-group {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      align-items: stretch;
    }
    .barcode-input-group input {
      flex: 1;
      padding: 16px 20px;
      border: 2px solid rgba(31,164,99,.2);
      border-radius: 14px;
      background: white;
      font-size: 16px;
      font-weight: 600;
      letter-spacing: 0.05em;
      transition: all 0.3s ease;
    }
    .barcode-input-group input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px rgba(31,164,99,.1);
      transform: translateY(-1px);
    }
    .barcode-input-group .secondary-btn {
      padding: 16px 28px;
      font-weight: 700;
      box-shadow: 0 6px 16px rgba(31,164,99,0.2);
    }
    
    /* Enhanced Result Card */
    .result-card {
      margin-top: 24px;
      padding: 28px;
      background: linear-gradient(135deg, rgba(31,164,99,.06), white);
      border: 2px solid rgba(31,164,99,.2);
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(31,164,99,0.12);
      animation: resultSlideIn 0.4s ease;
    }
    @keyframes resultSlideIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .product-info {
      display: flex;
      gap: 28px;
      align-items: flex-start;
    }
    .product-image {
      width: 180px;
      height: 180px;
      object-fit: cover;
      border-radius: 16px;
      border: 3px solid rgba(31,164,99,.2);
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .product-details {
      flex: 1;
    }
    .product-details h3 {
      margin: 0 0 10px;
      font-size: 26px;
      color: var(--text);
      font-weight: 800;
    }
    .product-brand {
      color: var(--primary);
      margin-bottom: 20px;
      font-weight: 600;
      font-size: 16px;
    }
    .nutrition-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 14px;
      margin-bottom: 20px;
    }
    .nutrition-item {
      background: white;
      padding: 16px;
      border-radius: 12px;
      text-align: center;
      border: 2px solid rgba(31,164,99,.15);
      transition: all 0.3s ease;
    }
    .nutrition-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(31,164,99,0.15);
      border-color: var(--primary);
    }
    .nutrition-label {
      display: block;
      font-size: 11px;
      color: var(--muted);
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-weight: 700;
    }
    .nutrition-value {
      display: block;
      font-size: 20px;
      font-weight: 800;
      color: var(--primary-dark);
    }
    .serving-size {
      color: var(--muted);
      font-style: italic;
      font-size: 14px;
      padding: 12px;
      background: rgba(31,164,99,.05);
      border-radius: 8px;
      border-left: 3px solid var(--primary);
    }
    
    /* Enhanced Total Calories Badge */
    .total-calories-badge {
      display: flex;
      align-items: baseline;
      gap: 10px;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 14px 26px;
      border-radius: 999px;
      font-weight: 700;
      box-shadow: 0 8px 20px rgba(31,164,99,0.3);
      animation: badgePulse 2s ease-in-out infinite;
    }
    @keyframes badgePulse {
      0%, 100% { box-shadow: 0 8px 20px rgba(31,164,99,0.3); }
      50% { box-shadow: 0 8px 24px rgba(31,164,99,0.5); }
    }
    .calories-label {
      font-size: 14px;
      opacity: 0.9;
    }
    .calories-value {
      font-size: 28px;
      font-weight: 900;
    }
    
    /* Enhanced Foods List */
    .foods-list {
      margin-bottom: 32px;
    }
    .food-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 18px 20px;
      background: white;
      border: 2px solid rgba(31,164,99,.15);
      border-radius: 14px;
      margin-bottom: 14px;
      transition: all 0.3s ease;
    }
    .food-item:hover {
      transform: translateX(6px);
      box-shadow: 0 8px 20px rgba(31,164,99,0.12);
      border-color: var(--primary);
    }
    .food-info {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    .food-name {
      font-weight: 700;
      color: var(--text);
      font-size: 16px;
    }
    .food-quantity {
      font-size: 14px;
      color: var(--muted);
      font-weight: 500;
    }
    .food-calories {
      display: flex;
      align-items: center;
      gap: 12px;
      font-weight: 700;
      color: var(--primary-dark);
      font-size: 18px;
    }
    .confidence-badge {
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .confidence-high {
      background: linear-gradient(135deg, #d1fae5, #a7f3d0);
      color: #065f46;
      box-shadow: 0 2px 8px rgba(6, 95, 70, 0.2);
    }
    .confidence-medium {
      background: linear-gradient(135deg, #fef3c7, #fde68a);
      color: #92400e;
      box-shadow: 0 2px 8px rgba(146, 64, 14, 0.2);
    }
    .confidence-low {
      background: linear-gradient(135deg, #fee2e2, #fecaca);
      color: #991b1b;
      box-shadow: 0 2px 8px rgba(153, 27, 27, 0.2);
    }
    
    /* Enhanced Macros Section */
    .macros-section {
      margin-top: 32px;
      padding: 28px;
      background: white;
      border-radius: 18px;
      border: 2px solid rgba(31,164,99,.15);
    }
    .macros-section h3 {
      font-size: 20px;
      margin-bottom: 24px;
      color: var(--text);
      padding-bottom: 12px;
      border-bottom: 3px solid var(--primary);
      display: inline-block;
    }
    .macros-bars {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }
    .macro-bar-item {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .macro-bar-header {
      display: flex;
      justify-content: space-between;
      font-size: 15px;
    }
    .macro-name {
      font-weight: 700;
      color: var(--text);
      text-transform: uppercase;
      letter-spacing: 0.05em;
      font-size: 13px;
    }
    .macro-value {
      font-weight: 800;
      color: var(--primary-dark);
      font-size: 16px;
    }
    .progress-bar {
      height: 14px;
      background: #f0f0f0;
      border-radius: 999px;
      overflow: hidden;
      box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
    }
    .progress-fill {
      height: 100%;
      border-radius: 999px;
      transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }
    .progress-fill::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    .protein-fill {
      background: linear-gradient(90deg, #f093fb, #f5576c);
      box-shadow: 0 2px 8px rgba(245, 87, 108, 0.4);
    }
    .carbs-fill {
      background: linear-gradient(90deg, #4facfe, #00f2fe);
      box-shadow: 0 2px 8px rgba(79, 172, 254, 0.4);
    }
    .fats-fill {
      background: linear-gradient(90deg, #43e97b, #38f9d7);
      box-shadow: 0 2px 8px rgba(67, 233, 123, 0.4);
    }
    .fiber-fill {
      background: linear-gradient(90deg, #fa709a, #fee140);
      box-shadow: 0 2px 8px rgba(250, 112, 154, 0.4);
    }
    
    @media (max-width: 768px) {
      .product-info {
        flex-direction: column;
      }
      .product-image {
        width: 100%;
        height: 220px;
      }
      .nutrition-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>

</body>
</html>
