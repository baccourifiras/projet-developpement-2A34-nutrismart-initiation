# NutriSmart - Plateforme de Nutrition pour Régimes Spéciaux

Application web PHP MVC complète pour la gestion de produits nutritionnels adaptés aux régimes spéciaux (diabète, vegan, sans gluten).

## 🚀 Fonctionnalités

### FrontOffice
- Page d'accueil avec liste de produits
- Filtrage par catégorie et régime
- Recherche en temps réel
- Tri des produits (prix, nom)
- Page détail produit
- **🛒 Panier d'achat** (localStorage)
  - Ajout de produits au panier
  - Gestion des quantités
  - Badge avec nombre d'articles
  - Modal panier avec total
  - Notifications toast
- Système de commande

### BackOffice
- Gestion CRUD complète des produits
- Liste des commandes avec changement de statut
- Recherche et tri dans les tables
- Validation JavaScript multi-niveaux
- Interface d'administration moderne

## 📋 Prérequis

- XAMPP / WAMP (PHP 8+, MySQL, Apache)
- Navigateur web moderne

## 🔧 Installation

### 1. Copier les fichiers
Placez le dossier `NutriSmart` dans le répertoire `htdocs` de XAMPP :
```
C:\xampp\htdocs\NutriSmart\
```

### 2. Créer la base de données
1. Ouvrez phpMyAdmin : `http://localhost/phpmyadmin`
2. Cliquez sur "Importer"
3. Sélectionnez le fichier `nutrismart.sql`
4. Cliquez sur "Exécuter"

Ou via ligne de commande :
```bash
mysql -u root -p < nutrismart.sql
```

### 3. Configuration
Vérifiez les paramètres de connexion dans `config.php` :
- Host : `localhost`
- Database : `NutriSmart`
- User : `root`
- Password : `` (vide par défaut)

### 4. Lancer l'application
Ouvrez votre navigateur et accédez à :
```
http://localhost/NutriSmart/
```

## 📁 Structure du Projet

```
NutriSmart/
├── config.php                  # Configuration PDO
├── index.php                   # Routeur principal
├── nutrismart.sql             # Script SQL
├── README.md                  # Documentation
├── Model/
│   ├── Produit.php           # Modèle Produit
│   └── Commande.php          # Modèle Commande
├── Controller/
│   ├── ProduitController.php  # Contrôleur Produit
│   └── CommandeController.php # Contrôleur Commande
├── View/
│   ├── FrontOffice/
│   │   ├── index.php         # Page d'accueil
│   │   └── detail.php        # Détail produit
│   └── BackOffice/
│       ├── listProduits.php   # Liste produits
│       ├── addProduit.php     # Ajouter produit
│       ├── updateProduit.php  # Modifier produit
│       └── listCommandes.php  # Liste commandes
└── public/
    ├── css/
    │   ├── style.css         # Styles FrontOffice
    │   └── backoffice.css    # Styles BackOffice
    └── js/
        ├── addProduit.js     # Validation JavaScript
        ├── panier.js         # Gestion du panier
        └── tableUtils.js     # Tri et recherche tables
```

## 🎯 Utilisation

### Navigation
- **FrontOffice** : `http://localhost/NutriSmart/`
- **BackOffice** : `http://localhost/NutriSmart/index.php?page=backoffice&action=list`
- **Commandes** : `http://localhost/NutriSmart/index.php?page=commande&action=list`

### Gestion des Produits
1. Accédez au BackOffice
2. Cliquez sur "Ajouter un Produit"
3. Remplissez le formulaire (validation en temps réel)
4. Soumettez le formulaire

### Utiliser le Panier
1. Depuis le FrontOffice, cliquez sur "Ajouter au panier" sur un produit
2. Le badge du panier se met à jour automatiquement
3. Cliquez sur l'icône panier pour voir votre sélection
4. Modifiez les quantités avec les boutons +/-
5. Cliquez sur "Valider la commande" pour finaliser

### Passer une Commande (ancienne méthode)
1. Depuis le FrontOffice, cliquez sur un produit
2. Cliquez sur "Voir détail"
3. Remplissez le formulaire de commande
4. Validez

## 🔒 Sécurité

- Requêtes préparées PDO (protection SQL injection)
- Validation côté client et serveur
- Échappement HTML (XSS protection)
- Mode d'erreur PDO en EXCEPTION

## 🎨 Design

- Design moderne avec palette verte (#1fa463)
- Navbar fixe avec effets de scroll
- Glassmorphism et dégradés
- Animations 3D sur les cartes
- Responsive (CSS Grid & Flexbox)
- Badges colorés pour les statuts
- Effets hover et transitions fluides

## 📊 Base de Données

### Table `produit`
- id_produit, nom, description, categorie, regime_cible
- type_vente, prix, disponible, date_ajout

### Table `commande`
- id_commande, id_utilisateur, id_produit, quantite
- prix_total, statut, mode_paiement, date_commande, date_paiement

## 🧪 Données de Test

Le fichier SQL inclut :
- 6 produits (plans, guides, coaching, premium)
- 4 commandes avec différents statuts

## 📝 Validation JavaScript

Trois niveaux de validation dans `addProduit.js` :
1. **onClick** : Alert avec liste d'erreurs
2. **onSubmit** : Messages rouge/vert par champ
3. **Temps réel** : keyup, blur, change events

## 🛠️ Technologies

- **Backend** : PHP 8+ (POO, MVC)
- **Base de données** : MySQL avec PDO
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Serveur** : Apache (XAMPP)

## 📄 Licence

Projet éducatif - Libre d'utilisation

## 👨‍💻 Auteur

Développé pour la plateforme NutriSmart
