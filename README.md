# NutriSmart — Module Recettes & Ingrédients

Application PHP/MySQL en architecture **MVC pragmatique** ajoutant un module complet
de gestion de recettes, ingrédients et de leurs associations (Many-to-Many) au
projet NutriSmart existant.

---

## 🚀 Installation rapide (XAMPP)

1. **Décompresser** ce dossier dans `htdocs/` de XAMPP. Le dossier doit s'appeler
   exactement `NutriSmart` (chemin : `htdocs/NutriSmart/`).
2. **Démarrer** Apache + MySQL depuis le panneau XAMPP.
3. Ouvrir **phpMyAdmin** (`http://localhost/phpmyadmin`) puis :
   - Si la base `nutrismart` n'existe pas encore : importer **`database/nutrismart.sql`**.
   - Importer ensuite **`database/recettes_module.sql`** (crée les 3 nouvelles tables
     + les données de démonstration).
4. Naviguer vers :
   - Front office  : `http://localhost/NutriSmart/frontoffice/accueil.php`
   - Back office   : `http://localhost/NutriSmart/backoffice/index.php`

> ⚠️ Le projet doit être placé dans un dossier nommé `NutriSmart` (la constante
> `BASE_URL` est calculée à partir du nom du dossier racine).
> Si vous voulez le renommer, modifiez aussi `config/bootstrap.php`.

---

## 🗂️ Structure

```
NutriSmart/
├── config/
│   ├── Database.php          # Singleton PDO
│   └── bootstrap.php          # Autoloader + session + helpers
├── core/
│   ├── Controller.php         # Classe parent (render, json)
│   ├── Validator.php          # Validation backend fluide
│   └── helpers.php            # e(), redirect(), flash, csrf, old
├── lib/
│   └── Exporter.php           # CSV / Excel (XML SS) / PDF natif
├── models/
│   ├── Recette.php            # CRUD + paginate + stats + sync ingrédients
│   └── Ingredient.php         # CRUD + paginate + stats + relations
├── controllers/
│   ├── frontoffice/           # Lecture seule (public)
│   └── backoffice/            # CRUD admin
├── views/
│   ├── layouts/               # front_header/footer, back_header/footer
│   ├── frontoffice/recettes|ingredients/   (index + show)
│   └── backoffice/recettes|ingredients/    (index + form + show) + dashboard
├── assets/
│   ├── css/front-extras.css   # Compléments CSS front
│   ├── css/back-extras.css    # Compléments CSS back
│   ├── js/front.js            # Animations, recherche live
│   └── js/back.js             # Validation, tri, builder M:N
├── frontoffice/               # Routes publiques (.php tinies)
│   ├── accueil.php (existant, navbar mise à jour)
│   ├── recettes.php / recette.php
│   └── ingredients.php / ingredient.php
├── backoffice/                # Routes admin (.php tinies)
│   ├── index.php              # Dashboard
│   ├── recettes.php, recette_form.php, recette_show.php,
│   │ recette_store.php, recette_update.php, recette_delete.php,
│   │ recettes_export.php
│   └── ingredients.php, ingredient_form.php, ingredient_show.php,
│     ingredient_store.php, ingredient_update.php, ingredient_delete.php,
│     ingredients_export.php
├── database/
│   ├── nutrismart.sql         # Base initiale (existante)
│   └── recettes_module.sql    # NOUVELLES tables + données démo
└── public/uploads/recettes/   # Dossier d'upload des images
```

---

## 🧩 Architecture MVC

Chaque URL (`/backoffice/recettes.php`, `/frontoffice/recette.php?id=2`...) est un
**point d'entrée** minimaliste qui ne fait que :

1. Charger `config/bootstrap.php` (autoloader + session + helpers).
2. Instancier le bon Controller.
3. Appeler la méthode appropriée.

Le Controller utilise un Model (`Recette` / `Ingredient`) qui parle à la base via
**PDO en requêtes préparées**. Il rend une **Vue** PHP avec un layout (front ou back).

---

## ✅ Fonctionnalités

- CRUD complet recettes + ingrédients (back office).
- Vue publique (lecture seule) recettes + ingrédients (front office).
- Recherche, filtres, tri cliquable, pagination.
- Upload d'image avec validation (format, taille).
- Association **Many-to-Many** recette ↔ ingrédient avec quantité + unité.
- Builder d'associations dynamique (HTML5 `<template>` + JS).
- Validation **JS côté client** (data-rule-* attrs) + **PHP côté serveur** (`Validator`).
- Protection CSRF sur tous les POST.
- Échappement XSS systématique (`e()`).
- Messages flash (succès / erreur).
- Exports CSV, Excel (SpreadsheetML XML) et PDF — **100% PHP natif, zéro dépendance**.
- Statistiques dashboard.
- Aucune modification destructive du template existant (CSS/HTML conservés).

---

## 🔐 Sécurité

| Mécanisme           | Implémentation                                       |
|---------------------|------------------------------------------------------|
| Injection SQL       | PDO préparé (`EMULATE_PREPARES = false`)             |
| XSS                 | Helper `e()` à chaque sortie HTML                    |
| CSRF                | Token de session vérifié sur chaque POST             |
| Upload de fichiers  | Whitelist mime + extension + taille max 4 Mo         |
| Validation          | Côté client (JS) + côté serveur (`Validator`)        |

---

## 🛠️ Personnalisation

- **Connexion BDD** : `config/Database.php` (host, user, pass, base).
- **Catégories d'ingrédients** : tableau `Ingredient::CATEGORIES`.
- **Niveaux de recettes** : enum SQL + `Recette::NIVEAUX`.
- **Unités de mesure** : `Ingredient::UNITES`.

---

## 📝 Conventions

- Tout est en **français** (UI, commentaires, slugs).
- PHP 7.4+ requis (types stricts utilisés).
- Encodage `utf8mb4` partout.
- Arborescence stricte : `core/`, `models/`, `controllers/`, `views/`, `lib/`,
  `assets/`, `public/`, `config/`.
