# 🚀 Quick Start Guide - NutriSmart Advanced Features

## ⚡ 5-Minute Setup

### Step 1: Get Your FREE Groq API Key (2 minutes)

1. Go to [https://console.groq.com](https://console.groq.com)
2. Click "Sign Up" (you can use Google/GitHub)
3. Once logged in, click "API Keys" in the left menu
4. Click "Create API Key"
5. Copy the key (starts with `gsk_...`)

### Step 2: Configure the API Key (1 minute)

Open `Config/config.php` and replace:

```php
define('GROQ_API_KEY', 'your-groq-api-key-here');
```

With your actual key:

```php
define('GROQ_API_KEY', 'gsk_your_actual_key_here');
```

### Step 3: Verify Setup (1 minute)

Open in your browser:
```
http://localhost/your-project-path/setup-check.php
```

You should see all green checkmarks ✅

### Step 4: Start Using! (1 minute)

**Recipe Generator:**
```
http://localhost/your-project-path/View/FrontOffice/recipe-generator.php
```

**Food Analysis:**
```
http://localhost/your-project-path/View/FrontOffice/food-analysis.php
```

---

## 🎯 Try These Examples

### Example 1: Generate a Recipe

1. Open Recipe Generator page
2. Add ingredients: `chicken`, `rice`, `broccoli`
3. Select dietary restrictions: `halal`, `gluten-free`
4. Set difficulty: `Easy`
5. Click "Générer la Recette"
6. Wait 5-10 seconds for AI to generate your recipe!

### Example 2: Analyze Food

1. Open Food Analysis page
2. Take a photo of your meal or drag an image
3. Select meal type: `Lunch`
4. Click "Analyser"
5. See detected foods, calories, and macros!

### Example 3: Scan a Barcode

1. Open Food Analysis page
2. Scroll to "Scanner un Code-Barres"
3. Enter: `3017620422003` (Nutella)
4. Click "Scanner"
5. See complete nutritional information!

---

## 📱 Test with Sample Data

### Test Recipe Generation (via API)

```bash
curl -X POST http://localhost/your-project/Controller/api-recipe.php?action=generate \
  -H "Content-Type: application/json" \
  -d '{
    "ingredients": ["chicken", "rice", "tomatoes"],
    "dietary_restrictions": ["halal"],
    "difficulty": "easy",
    "meal_type": "lunch",
    "target_calories": 500
  }'
```

### Test Barcode Scanning (via API)

```bash
curl "http://localhost/your-project/Controller/api-food-analysis.php?action=barcode&code=3017620422003"
```

---

## 🔧 Common Issues & Quick Fixes

### Issue: "Groq API key not configured"
**Fix:** Make sure you saved `Config/config.php` after adding your API key

### Issue: "Failed to save uploaded file"
**Fix:** Run this command:
```bash
mkdir -p uploads/food-images
chmod 755 uploads/food-images
```

### Issue: "Database connection failed"
**Fix:** Check your database credentials in `Model/Database.php`:
```php
$dsn = 'mysql:host=127.0.0.1;dbname=nutrismart;charset=utf8mb4';
// username: 'root', password: ''
```

### Issue: Recipe generation is slow
**Normal:** First request takes 5-10 seconds. This is normal for AI processing.

---

## 🎨 Features Overview

### Recipe Generator Features:
- ✅ Generate recipes from ingredients
- ✅ 8 dietary restrictions (vegan, halal, keto, etc.)
- ✅ 3 difficulty levels
- ✅ Calorie targeting
- ✅ Complete nutritional info
- ✅ Step-by-step instructions
- ✅ Video tutorials (if YouTube API configured)
- ✅ Save and manage recipes

### Food Analysis Features:
- ✅ Image recognition for food
- ✅ Automatic calorie calculation
- ✅ Macro breakdown (protein, carbs, fats)
- ✅ Barcode scanning
- ✅ Daily calorie tracking
- ✅ Analysis history
- ✅ Meal type categorization

---

## 📊 API Endpoints Quick Reference

### Recipe Endpoints:
- `POST /api-recipe.php?action=generate` - Generate recipe
- `GET /api-recipe.php?action=suggest` - Get suggestions
- `GET /api-recipe.php?action=list` - List all recipes
- `GET /api-recipe.php?action=show&id=1` - Get recipe by ID

### Food Analysis Endpoints:
- `POST /api-food-analysis.php?action=analyze` - Analyze image
- `GET /api-food-analysis.php?action=barcode&code=XXX` - Scan barcode
- `GET /api-food-analysis.php?action=history&user_id=1` - Get history
- `GET /api-food-analysis.php?action=daily&date=2024-01-15` - Daily summary

---

## 💡 Pro Tips

1. **Better Recipe Results:**
   - Be specific with ingredients (e.g., "chicken breast" not just "chicken")
   - Add 3-5 ingredients for best results
   - Try different difficulty levels

2. **Better Food Analysis:**
   - Use well-lit photos
   - Show food from above
   - Include the whole plate
   - Use JPEG or PNG format

3. **Barcode Scanning:**
   - Works with international products
   - Uses OpenFoodFacts database (free, no API key needed)
   - Try common products: Nutella (3017620422003), Coca-Cola, etc.

---

## 🎓 Next Steps

1. ✅ Complete the 5-minute setup above
2. ✅ Try the examples
3. ✅ Read the full documentation: `README_ADVANCED_FEATURES.md`
4. ✅ Customize the UI in `View/FrontOffice/css/`
5. ✅ Add your own features!

---

## 🆘 Need Help?

1. Run `setup-check.php` to diagnose issues
2. Check `README_ADVANCED_FEATURES.md` for detailed docs
3. Review the troubleshooting section
4. Test with the provided test files:
   - `Controller/Tests/test-recipe-generator.php`
   - `Controller/Tests/test-food-analysis.php`

---

## 🎉 You're Ready!

Your NutriSmart application now has:
- 🤖 AI-powered recipe generation
- 📸 Smart food image analysis
- 📊 Nutritional tracking
- 🎥 Video tutorials
- 📱 Modern, responsive UI

**Start creating amazing recipes and tracking nutrition with AI! 🚀**

---

**Estimated Setup Time:** 5 minutes  
**Difficulty:** Easy  
**Cost:** FREE (Groq free tier is generous)

Enjoy! 🍽️
