/**
 * NutriSmart — Panier (Shopping Cart)
 * Gestion du panier avec localStorage
 */

// Récupérer le panier depuis localStorage
function getPanier() {
    const panier = localStorage.getItem('nutrismart_panier');
    return panier ? JSON.parse(panier) : [];
}

// Sauvegarder le panier dans localStorage
function savePanier(panier) {
    localStorage.setItem('nutrismart_panier', JSON.stringify(panier));
    updatePanierBadge();
}

// Ajouter un produit au panier
function ajouterAuPanier(produit) {
    const panier = getPanier();
    
    // Vérifier si le produit existe déjà
    const existant = panier.find(item => item.id === produit.id);
    
    if (existant) {
        existant.quantite += 1;
    } else {
        panier.push({
            id: produit.id,
            nom: produit.nom,
            prix: produit.prix,
            categorie: produit.categorie,
            regime: produit.regime,
            quantite: 1
        });
    }
    
    savePanier(panier);
    showNotification('✓ Produit ajouté au panier');
}

// Retirer un produit du panier
function retirerDuPanier(produitId) {
    let panier = getPanier();
    panier = panier.filter(item => item.id !== produitId);
    savePanier(panier);
    afficherPanier();
}

// Modifier la quantité d'un produit
function modifierQuantite(produitId, nouvelleQuantite) {
    const panier = getPanier();
    const produit = panier.find(item => item.id === produitId);
    
    if (produit) {
        if (nouvelleQuantite <= 0) {
            retirerDuPanier(produitId);
        } else {
            produit.quantite = parseInt(nouvelleQuantite);
            savePanier(panier);
            afficherPanier();
        }
    }
}

// Vider le panier
function viderPanier() {
    if (confirm('Êtes-vous sûr de vouloir vider le panier ?')) {
        localStorage.removeItem('nutrismart_panier');
        updatePanierBadge();
        afficherPanier();
        showNotification('Panier vidé');
    }
}

// Calculer le total du panier
function calculerTotal() {
    const panier = getPanier();
    return panier.reduce((total, item) => total + (item.prix * item.quantite), 0);
}

// Mettre à jour le badge du panier (nombre d'articles)
function updatePanierBadge() {
    const panier = getPanier();
    const totalItems = panier.reduce((sum, item) => sum + item.quantite, 0);
    const badge = document.getElementById('panierBadge');
    
    if (badge) {
        if (totalItems > 0) {
            badge.textContent = totalItems;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

// Afficher le panier dans le modal
function afficherPanier() {
    const panier = getPanier();
    const container = document.getElementById('panierItems');
    const totalElement = document.getElementById('panierTotal');
    
    if (!container) return;
    
    if (panier.length === 0) {
        container.innerHTML = '<div class="panier-vide">Votre panier est vide</div>';
        totalElement.textContent = '0.00 TND';
        return;
    }
    
    let html = '';
    panier.forEach(item => {
        html += `
            <div class="panier-item">
                <div class="panier-item-info">
                    <h4>${item.nom}</h4>
                    <p class="panier-item-meta">
                        <span class="small-badge">${item.categorie}</span>
                        <span class="small-badge">${item.regime}</span>
                    </p>
                    <p class="panier-item-prix">${item.prix} TND</p>
                </div>
                <div class="panier-item-actions">
                    <div class="quantity-control">
                        <button onclick="modifierQuantite(${item.id}, ${item.quantite - 1})" class="qty-btn">−</button>
                        <input type="number" value="${item.quantite}" min="1" onchange="modifierQuantite(${item.id}, this.value)" class="qty-input">
                        <button onclick="modifierQuantite(${item.id}, ${item.quantite + 1})" class="qty-btn">+</button>
                    </div>
                    <button onclick="retirerDuPanier(${item.id})" class="remove-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    totalElement.textContent = calculerTotal().toFixed(2) + ' TND';
}

// Ouvrir le modal du panier
function ouvrirPanier() {
    afficherPanier();
    document.getElementById('panierModal').classList.remove('hidden');
}

// Fermer le modal du panier
function fermerPanier() {
    document.getElementById('panierModal').classList.add('hidden');
}

// Valider la commande
function validerCommande() {
    const panier = getPanier();
    
    if (panier.length === 0) {
        alert('Votre panier est vide');
        return;
    }
    
    // Rediriger vers la page de paiement Stripe
    window.location.href = 'index.php?page=payment';
}

// Notification toast
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification-toast';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    updatePanierBadge();
    
    // Fermer le modal en cliquant à l'extérieur
    const modal = document.getElementById('panierModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                fermerPanier();
            }
        });
    }
});
