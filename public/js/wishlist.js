/**
 * NutriSmart — Wishlist (Favorites)
 * Gestion des favoris avec localStorage
 */

// Récupérer la wishlist depuis localStorage
function getWishlist() {
    const wishlist = localStorage.getItem('nutrismart_wishlist');
    return wishlist ? JSON.parse(wishlist) : [];
}

// Sauvegarder la wishlist dans localStorage
function saveWishlist(wishlist) {
    localStorage.setItem('nutrismart_wishlist', JSON.stringify(wishlist));
    updateWishlistBadge();
}

// Vérifier si un produit est dans la wishlist
function isInWishlist(produitId) {
    const wishlist = getWishlist();
    return wishlist.some(item => item.id === produitId);
}

// Ajouter/Retirer un produit de la wishlist (toggle)
function toggleWishlist(produit) {
    const wishlist = getWishlist();
    const index = wishlist.findIndex(item => item.id === produit.id);
    
    if (index > -1) {
        // Retirer de la wishlist
        wishlist.splice(index, 1);
        showNotification('💔 Retiré des favoris', 'info');
    } else {
        // Ajouter à la wishlist
        wishlist.push({
            id: produit.id,
            nom: produit.nom,
            prix: produit.prix,
            categorie: produit.categorie,
            regime: produit.regime,
            description: produit.description || ''
        });
        showNotification('❤️ Ajouté aux favoris', 'success');
    }
    
    saveWishlist(wishlist);
    
    // Mettre à jour l'icône du bouton
    updateHeartIcon(produit.id);
    
    // Si on est sur la page wishlist, rafraîchir l'affichage
    if (window.location.search.includes('page=wishlist')) {
        afficherWishlist();
    }
}

// Mettre à jour l'icône coeur
function updateHeartIcon(produitId) {
    const heartBtn = document.querySelector(`[data-product-id="${produitId}"]`);
    if (heartBtn) {
        const isFavorite = isInWishlist(produitId);
        heartBtn.classList.toggle('active', isFavorite);
        
        const svg = heartBtn.querySelector('svg');
        if (svg) {
            if (isFavorite) {
                svg.setAttribute('fill', 'currentColor');
            } else {
                svg.setAttribute('fill', 'none');
            }
        }
    }
}

// Mettre à jour le badge de la wishlist
function updateWishlistBadge() {
    const wishlist = getWishlist();
    const badge = document.getElementById('wishlistBadge');
    
    if (badge) {
        if (wishlist.length > 0) {
            badge.textContent = wishlist.length;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Afficher la wishlist dans une page dédiée
function afficherWishlist() {
    const wishlist = getWishlist();
    const container = document.getElementById('wishlistContainer');
    
    if (!container) return;
    
    if (wishlist.length === 0) {
        container.innerHTML = `
            <div class="empty-wishlist">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                <h3>Votre liste de favoris est vide</h3>
                <p>Ajoutez des produits à vos favoris pour les retrouver facilement</p>
                <a href="index.php" class="primary-btn">Découvrir nos produits</a>
            </div>
        `;
        return;
    }
    
    let html = '<div class="products-grid">';
    wishlist.forEach(item => {
        html += `
            <article class="product-card reveal visible" data-product-id="${item.id}">
                <div class="product-content">
                    <div class="product-header-row">
                        <div class="product-badges">
                            <span class="small-badge">${item.categorie}</span>
                            <span class="small-badge">${item.regime}</span>
                        </div>
                        <button class="wishlist-heart-btn active" data-product-id="${item.id}" onclick="toggleWishlist({id: ${item.id}, nom: '${item.nom.replace(/'/g, "\\'")}', prix: ${item.prix}, categorie: '${item.categorie}', regime: '${item.regime}', description: '${(item.description || '').replace(/'/g, "\\'")}'})">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>
                    </div>
                    <h3>${item.nom}</h3>
                    <p class="product-description">${item.description.substring(0, 120)}...</p>
                    <div class="product-meta">
                        <span class="product-price">${item.prix} TND</span>
                    </div>
                    <div class="product-actions">
                        <button onclick="ajouterAuPanier({id: ${item.id}, nom: '${item.nom.replace(/'/g, "\\'")}', prix: ${item.prix}, categorie: '${item.categorie}', regime: '${item.regime}'})" class="primary-btn">Ajouter au panier</button>
                        <a href="index.php?page=detail&id=${item.id}" class="secondary-btn-small">Détails</a>
                    </div>
                </div>
            </article>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

// Initialiser les icônes coeur au chargement
function initWishlistIcons() {
    const wishlist = getWishlist();
    wishlist.forEach(item => {
        updateHeartIcon(item.id);
    });
}

// Notification personnalisée pour wishlist
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification-toast ${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 2500);
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    updateWishlistBadge();
    initWishlistIcons();
    
    // Si on est sur la page wishlist, afficher les produits
    if (window.location.search.includes('page=wishlist')) {
        afficherWishlist();
    }
});
