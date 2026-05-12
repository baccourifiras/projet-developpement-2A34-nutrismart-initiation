// Food Analysis JavaScript
const API_BASE = '../../Controller/api-food-analysis.php';

let selectedImage = null;
let currentAnalysis = null;

// DOM Elements
const uploadArea = document.getElementById('uploadArea');
const imageInput = document.getElementById('imageInput');
const selectImageBtn = document.getElementById('selectImageBtn');
const imagePreview = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');
const removeImageBtn = document.getElementById('removeImage');
const analyzeBtn = document.getElementById('analyzeBtn');
const mealTypeSelect = document.getElementById('mealType');
const analysisResults = document.getElementById('analysisResults');
const barcodeInput = document.getElementById('barcodeInput');
const scanBarcodeBtn = document.getElementById('scanBarcodeBtn');
const summaryDate = document.getElementById('summaryDate');
const loadSummaryBtn = document.getElementById('loadSummary');

// New text analysis elements
const foodDescription = document.getElementById('foodDescription');
const mealTypeText = document.getElementById('mealTypeText');
const analyzeTextBtn = document.getElementById('analyzeTextBtn');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    summaryDate.valueAsDate = new Date();
    loadDailySummary();
    loadAnalysisHistory();
});

function setupEventListeners() {
    selectImageBtn.addEventListener('click', () => imageInput.click());
    imageInput.addEventListener('change', handleImageSelect);
    removeImageBtn.addEventListener('click', removeImage);
    analyzeBtn.addEventListener('click', analyzeImage);
    analyzeTextBtn.addEventListener('click', analyzeFromText);
    scanBarcodeBtn.addEventListener('click', scanBarcode);
    loadSummaryBtn.addEventListener('click', loadDailySummary);
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('drag-over');
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('drag-over');
    });
    
    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleImageFile(file);
        }
    });
    
    document.getElementById('saveAnalysis')?.addEventListener('click', saveAnalysis);
    document.getElementById('addToDaily')?.addEventListener('click', addToDaily);
}

function handleImageSelect(e) {
    const file = e.target.files[0];
    if (file) {
        handleImageFile(file);
    }
}

function handleImageFile(file) {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        showNotification('Type de fichier invalide. Utilisez JPEG, PNG ou WebP', 'error');
        return;
    }
    
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        showNotification('Fichier trop volumineux. Maximum 5MB', 'error');
        return;
    }
    
    selectedImage = file;
    
    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImg.src = e.target.result;
        uploadArea.style.display = 'none';
        imagePreview.style.display = 'block';
        analyzeBtn.disabled = false;
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    selectedImage = null;
    imageInput.value = '';
    previewImg.src = '';
    uploadArea.style.display = 'block';
    imagePreview.style.display = 'none';
    analyzeBtn.disabled = true;
    analysisResults.style.display = 'none';
}

async function analyzeFromText() {
    const description = foodDescription.value.trim();
    
    if (!description) {
        showNotification('Veuillez décrire votre repas', 'error');
        return;
    }
    
    const requestData = {
        description: description,
        meal_type: mealTypeText.value,
        user_id: 1 // Replace with actual user ID
    };
    
    setTextAnalyzeLoading(true);
    
    try {
        const response = await fetch(`${API_BASE}?action=analyze-text`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentAnalysis = data.analysis;
            displayAnalysisResults(data.analysis);
            if (data.suggestions) {
                showNotification('Analyse terminée! ' + data.suggestions, 'success');
            } else {
                showNotification('Analyse terminée avec succès!', 'success');
            }
            loadAnalysisHistory();
        } else {
            showNotification('Erreur: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Erreur de connexion au serveur', 'error');
    } finally {
        setTextAnalyzeLoading(false);
    }
}

function setTextAnalyzeLoading(loading) {
    const btnText = analyzeTextBtn.querySelector('.btn-text');
    const btnLoader = analyzeTextBtn.querySelector('.btn-loader');
    
    if (loading) {
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
        analyzeTextBtn.disabled = true;
    } else {
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        analyzeTextBtn.disabled = false;
    }
}

async function analyzeImage() {
    if (!selectedImage) {
        showNotification('Veuillez sélectionner une image', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('image', selectedImage);
    formData.append('meal_type', mealTypeSelect.value);
    formData.append('user_id', '1'); // Replace with actual user ID
    
    setAnalyzeLoading(true);
    
    try {
        const response = await fetch(`${API_BASE}?action=analyze`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            currentAnalysis = data.analysis;
            displayAnalysisResults(data.analysis);
            showNotification('Analyse terminée avec succès!', 'success');
            loadAnalysisHistory();
        } else {
            showNotification('Erreur: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Erreur de connexion au serveur', 'error');
    } finally {
        setAnalyzeLoading(false);
    }
}

function displayAnalysisResults(analysis) {
    // Total calories
    document.getElementById('totalCalories').textContent = analysis.total_calories;
    
    // Detected foods
    const foodsHTML = analysis.detected_foods.map(food => `
        <div class="food-item">
            <div class="food-info">
                <span class="food-name">${food.name}</span>
                <span class="food-quantity">${food.quantity}</span>
            </div>
            <div class="food-calories">
                ${food.calories} kcal
                <span class="confidence confidence-${food.confidence}">${food.confidence}</span>
            </div>
        </div>
    `).join('');
    document.getElementById('detectedFoods').innerHTML = foodsHTML;
    
    // Macros
    const macros = analysis.macros;
    const totalMacros = macros.protein + macros.carbs + macros.fats;
    
    document.getElementById('proteinValue').textContent = macros.protein;
    document.getElementById('carbsValue').textContent = macros.carbs;
    document.getElementById('fatsValue').textContent = macros.fats;
    document.getElementById('fiberValue').textContent = macros.fiber || 0;
    
    document.getElementById('proteinBar').style.width = `${(macros.protein / totalMacros) * 100}%`;
    document.getElementById('carbsBar').style.width = `${(macros.carbs / totalMacros) * 100}%`;
    document.getElementById('fatsBar').style.width = `${(macros.fats / totalMacros) * 100}%`;
    document.getElementById('fiberBar').style.width = `${((macros.fiber || 0) / totalMacros) * 100}%`;
    
    // Micronutrients
    if (analysis.micronutrients && Object.keys(analysis.micronutrients).length > 0) {
        const microHTML = Object.entries(analysis.micronutrients).map(([key, value]) => `
            <div class="nutrient-item">
                <span class="nutrient-name">${formatNutrientName(key)}</span>
                <span class="nutrient-value">${value}</span>
            </div>
        `).join('');
        document.getElementById('micronutrientsList').innerHTML = microHTML;
        document.getElementById('micronutrients').style.display = 'block';
    }
    
    analysisResults.style.display = 'block';
    analysisResults.scrollIntoView({ behavior: 'smooth' });
}

async function scanBarcode() {
    const barcode = barcodeInput.value.trim();
    
    if (!barcode) {
        showNotification('Veuillez entrer un code-barres', 'error');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE}?action=barcode&code=${barcode}`);
        const data = await response.json();
        
        if (data.success) {
            displayBarcodeResult(data.product);
            showNotification('Produit trouvé!', 'success');
        } else {
            showNotification('Produit non trouvé: ' + data.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Erreur de connexion', 'error');
    }
}

function displayBarcodeResult(product) {
    const resultHTML = `
        <div class="product-card">
            ${product.image_url ? `<img src="${product.image_url}" alt="${product.name}">` : ''}
            <h3>${product.name}</h3>
            <p class="product-brand">${product.brand}</p>
            <div class="product-nutrition">
                <div class="nutrition-item">
                    <span class="label">Calories</span>
                    <span class="value">${product.calories} kcal/100g</span>
                </div>
                <div class="nutrition-item">
                    <span class="label">Protéines</span>
                    <span class="value">${product.macros.protein}g</span>
                </div>
                <div class="nutrition-item">
                    <span class="label">Glucides</span>
                    <span class="value">${product.macros.carbs}g</span>
                </div>
                <div class="nutrition-item">
                    <span class="label">Lipides</span>
                    <span class="value">${product.macros.fats}g</span>
                </div>
            </div>
            <p class="serving-size">Portion: ${product.serving_size}</p>
        </div>
    `;
    
    const barcodeResult = document.getElementById('barcodeResult');
    barcodeResult.innerHTML = resultHTML;
    barcodeResult.style.display = 'block';
}

async function loadDailySummary() {
    const date = summaryDate.value;
    
    try {
        const response = await fetch(`${API_BASE}?action=daily&date=${date}&user_id=1`);
        const data = await response.json();
        
        if (data.success) {
            displayDailySummary(data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayDailySummary(data) {
    document.getElementById('dailyCalories').textContent = data.total_calories;
    document.getElementById('mealCount').textContent = data.meal_count;
    
    if (data.meals && data.meals.length > 0) {
        const mealsHTML = data.meals.map(meal => `
            <div class="meal-item">
                <div class="meal-time">${formatTime(meal.analysis_date)}</div>
                <div class="meal-info">
                    <span class="meal-type">${getMealTypeLabel(meal.meal_type)}</span>
                    <span class="meal-calories">${meal.total_calories} kcal</span>
                </div>
            </div>
        `).join('');
        document.getElementById('dailyMeals').innerHTML = mealsHTML;
    } else {
        document.getElementById('dailyMeals').innerHTML = '<p>Aucun repas analysé ce jour</p>';
    }
}

async function loadAnalysisHistory() {
    try {
        const response = await fetch(`${API_BASE}?action=history&user_id=1&limit=6`);
        const data = await response.json();
        
        if (data.success && data.history.length > 0) {
            displayAnalysisHistory(data.history);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayAnalysisHistory(history) {
    const historyHTML = history.map(analysis => `
        <div class="history-card">
            <div class="history-date">${formatDate(analysis.analysis_date)}</div>
            <div class="history-info">
                <span class="meal-type">${getMealTypeLabel(analysis.meal_type)}</span>
                <span class="calories">${analysis.total_calories} kcal</span>
            </div>
            <div class="history-foods">
                ${analysis.detected_foods.slice(0, 3).map(f => f.name).join(', ')}
            </div>
        </div>
    `).join('');
    
    document.getElementById('analysisHistory').innerHTML = historyHTML;
}

function saveAnalysis() {
    if (currentAnalysis) {
        showNotification('Analyse sauvegardée!', 'success');
        loadAnalysisHistory();
    }
}

function addToDaily() {
    if (currentAnalysis) {
        showNotification('Ajouté au suivi quotidien!', 'success');
        loadDailySummary();
    }
}

// Utility functions
function formatNutrientName(key) {
    const names = {
        'vitamin_c': 'Vitamine C',
        'iron': 'Fer',
        'calcium': 'Calcium',
        'vitamin_d': 'Vitamine D',
        'vitamin_b12': 'Vitamine B12'
    };
    return names[key] || key;
}

function getMealTypeLabel(type) {
    const labels = {
        'breakfast': 'Petit-déjeuner',
        'lunch': 'Déjeuner',
        'dinner': 'Dîner',
        'snack': 'Collation'
    };
    return labels[type] || type;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric' 
    });
}

function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('fr-FR', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
}

function setAnalyzeLoading(loading) {
    const btnText = analyzeBtn.querySelector('.btn-text');
    const btnLoader = analyzeBtn.querySelector('.btn-loader');
    
    if (loading) {
        btnText.style.display = 'none';
        btnLoader.style.display = 'inline';
        analyzeBtn.disabled = true;
    } else {
        btnText.style.display = 'inline';
        btnLoader.style.display = 'none';
        analyzeBtn.disabled = false;
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
