/* ============================================================
   NutriSmart — Front Office
   Ce fichier gère :
     1. La lecture/écriture des données avec MySQL via PHP
     2. L'affichage des catégories
     3. L'affichage des événements
     4. L'inscription d'un participant
   ============================================================ */


(function () {

/* ------------------------------------------------------------
   SECTION 1 : DONNÉES
   ------------------------------------------------------------ */

var API_URL = '/nutrismart_evenement/Controller/NutrismartController.php';
var DEFAULT_EVENT_IMAGE = 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80';
var donneesCategories = [];
var donneesEvenements = [];
var donneesParticipants = [];


/* ------------------------------------------------------------
   SECTION 2 : LECTURE ET ÉCRITURE (PHP/MySQL)
   ------------------------------------------------------------ */

function appliquerDonnees(data) {
  donneesCategories = data.categories || [];
  donneesEvenements = data.events || [];
  donneesParticipants = data.participants || [];

  donneesCategories.forEach(function(c) {
    c.id = Number(c.id);
  });
  donneesEvenements.forEach(function(e) {
    e.id = Number(e.id);
    e.categoryId = Number(e.categoryId);
    e.seats = Number(e.seats || 0);
  });
  donneesParticipants.forEach(function(p) {
    p.id = Number(p.id);
    p.eventId = Number(p.eventId);
  });
}

async function api(action, data) {
  var options = action === 'all'
    ? {}
    : {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data || {})
      };

  var response = await fetch(API_URL + '?action=' + encodeURIComponent(action), options);
  var json = await response.json();
  if (!response.ok) {
    throw new Error(json.error || 'Erreur serveur');
  }
  appliquerDonnees(json);
  return json;
}

// Raccourcis pour lire chaque collection
function getCategories()   { return donneesCategories;   }
function getEvenements()   { return donneesEvenements;   }
function getParticipants() { return donneesParticipants; }


/* ------------------------------------------------------------
   SECTION 3 : FONCTIONS UTILITAIRES
   ------------------------------------------------------------ */

// Retourne le nom d'une catégorie à partir de son ID
function getNomCategorie(categoryId) {
  var cat = getCategories().find(function(c) { return c.id === Number(categoryId); });
  return cat ? cat.name : 'Sans catégorie';
}

// Compte le nombre de participants inscrits à un événement
function getNbParticipants(eventId) {
  return getParticipants().filter(function(p) { return p.eventId === Number(eventId); }).length;
}

// Formate une date (ex: "2026-05-12" → "12 mai 2026")
function formaterDate(dateStr) {
  if (!dateStr) return '-';
  return new Intl.DateTimeFormat('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(dateStr));
}

// Génère les initiales d'une catégorie pour l'icône (ex: "Nutrition Sportive" → "NS")
function initiales(nom) {
  var mots = nom.split(' ').filter(Boolean);
  return (mots[0] ? mots[0][0] : 'N') + (mots[1] ? mots[1][0] : '');
}


/* ------------------------------------------------------------
   SECTION 4 : AFFICHAGE DES CATÉGORIES
   ------------------------------------------------------------ */

var categorieSelectionnee = null; // ID de la catégorie filtrée (null = tout afficher)

function afficherCategories() {
  var categories = getCategories();
  var conteneur = document.getElementById('categoryList');
  conteneur.innerHTML = '';

  categories.forEach(function(cat, index) {
    // Créer la carte
    var carte = document.createElement('article');
    carte.className = 'category-card reveal' + (categorieSelectionnee === cat.id ? ' active' : '');
    carte.style.transitionDelay = (index * 70) + 'ms';
    carte.innerHTML =
      '<span class="shine"></span>' +
      '<span class="category-icon">' + initiales(cat.name) + '</span>' +
      '<h3>' + cat.name + '</h3>' +
      '<p>' + (cat.description || 'Catégorie disponible.') + '</p>' +
      '<span class="category-tag">Voir les événements</span>';

    // Clic → filtrer les événements par cette catégorie
    carte.addEventListener('click', function() {
      categorieSelectionnee = cat.id;
      afficherCategories();
      afficherEvenements();
    });

    // Effet 3D au mouvement de la souris
    carte.addEventListener('mousemove', function(e) {
      var rect = carte.getBoundingClientRect();
      var rotY =  ((e.clientX - rect.left  - rect.width  / 2) / (rect.width  / 2)) * 10;
      var rotX = -((e.clientY - rect.top   - rect.height / 2) / (rect.height / 2)) * 8;
      carte.style.transform = 'translateY(-14px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg) scale(1.03)';
    });

    // Réinitialiser la rotation quand la souris quitte la carte
    carte.addEventListener('mouseleave', function() {
      carte.style.transform = '';
    });

    conteneur.appendChild(carte);
  });

  activerAnimations();
}


/* ------------------------------------------------------------
   SECTION 5 : AFFICHAGE DES ÉVÉNEMENTS
   ------------------------------------------------------------ */

function afficherEvenements() {
  var tousLesEvenements = getEvenements();
  var titre = document.getElementById('eventsTitle');
  var conteneur = document.getElementById('eventList');

  // Filtrer par catégorie si une est sélectionnée
  var evenements = categorieSelectionnee
    ? tousLesEvenements.filter(function(e) { return e.categoryId === categorieSelectionnee; })
    : tousLesEvenements;

  titre.textContent = categorieSelectionnee
    ? 'Événements : ' + getNomCategorie(categorieSelectionnee)
    : 'Tous les événements';

  conteneur.innerHTML = '';

  // Aucun événement trouvé
  if (evenements.length === 0) {
    conteneur.innerHTML = '<div class="no-data reveal visible">Aucun événement dans cette catégorie.</div>';
    return;
  }

  evenements.forEach(function(ev, index) {
    var carte = document.createElement('article');
    carte.className = 'event-card reveal';
    carte.style.transitionDelay = (index * 90) + 'ms';
    var image = ev.image && ev.image.trim() ? ev.image.trim() : DEFAULT_EVENT_IMAGE;
    carte.innerHTML =
      '<img class="event-image" src="' + image + '" alt="' + ev.title + '" onerror="this.onerror=null;this.src=\'' + DEFAULT_EVENT_IMAGE + '\'">' +
      '<div class="event-content">' +
        '<span class="event-badge">' + getNomCategorie(ev.categoryId) + '</span>' +
        '<h3>' + ev.title + '</h3>' +
        '<p>' + ev.description + '</p>' +
        '<p class="meta"><strong>Date :</strong> ' + formaterDate(ev.date) + ' à ' + ev.time + '</p>' +
        '<p class="meta"><strong>Lieu :</strong> ' + ev.location + '</p>' +
        '<p class="meta"><strong>Places :</strong> ' + ev.seats + '</p>' +
        '<div class="event-actions">' +
          '<span class="counter">' + getNbParticipants(ev.id) + ' participant(s)</span>' +
          '<button class="primary-btn" data-event-id="' + ev.id + '">Participer</button>' +
        '</div>' +
      '</div>';
    conteneur.appendChild(carte);
  });

  // Attacher les boutons "Participer"
  conteneur.querySelectorAll('[data-event-id]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      ouvrirModal(Number(btn.dataset.eventId));
    });
  });

  activerAnimations();
}


/* ------------------------------------------------------------
   SECTION 6 : INSCRIPTION D'UN PARTICIPANT (Modal)
   ------------------------------------------------------------ */

function ouvrirModal(eventId) {
  var ev = getEvenements().find(function(e) { return e.id === eventId; });
  if (!ev) return;
  document.getElementById('participantEventId').value = eventId;
  document.getElementById('selectedEventText').textContent = 'Événement : ' + ev.title;
  document.getElementById('messageBox').textContent = '';
  document.getElementById('participantForm').reset();
  document.getElementById('participantEventId').value = eventId;
  document.getElementById('registerModal').classList.remove('hidden');
}

function fermerModal() {
  document.getElementById('registerModal').classList.add('hidden');
}

// Bouton "Afficher tout" → annuler le filtre
document.getElementById('showAllBtn').addEventListener('click', function() {
  categorieSelectionnee = null;
  afficherCategories();
  afficherEvenements();
});

// Fermer le modal
document.getElementById('closeModalBtn').addEventListener('click', fermerModal);
document.getElementById('registerModal').addEventListener('click', function(e) {
  if (e.target === this) fermerModal();
});

// Soumettre le formulaire d'inscription
document.getElementById('participantForm').addEventListener('submit', async function(e) {
  e.preventDefault();

  try {
    await api('addParticipant', {
      fullName: document.getElementById('fullName').value.trim(),
      email:    document.getElementById('email').value.trim(),
      phone:    document.getElementById('phone').value.trim(),
      eventId:  Number(document.getElementById('participantEventId').value)
    });

    document.getElementById('messageBox').textContent = 'Participation enregistrée avec succès !';
    afficherEvenements();
    setTimeout(fermerModal, 1000);
  } catch (error) {
    document.getElementById('messageBox').textContent = error.message;
  }
});


/* ------------------------------------------------------------
   SECTION 7 : ANIMATIONS D'APPARITION (scroll)
   ------------------------------------------------------------ */

function activerAnimations() {
  var elements = document.querySelectorAll('.reveal:not(.observer-ready)');
  var observateur = new IntersectionObserver(function(entrees) {
    entrees.forEach(function(entree) {
      if (entree.isIntersecting) {
        entree.target.classList.add('visible');
        observateur.unobserve(entree.target);
      }
    });
  }, { threshold: 0.15 });

  elements.forEach(function(el) {
    el.classList.add('observer-ready');
    observateur.observe(el);
  });
}


/* ------------------------------------------------------------
   DÉMARRAGE : afficher les données au chargement de la page
   ------------------------------------------------------------ */
async function initialiserFrontOffice() {
  try {
    await api('all');
    afficherCategories();
    afficherEvenements();
    activerAnimations();
  } catch (error) {
    document.getElementById('categoryList').innerHTML = '<div class="no-data visible">Impossible de charger la base de données.</div>';
    document.getElementById('eventList').innerHTML = '<div class="no-data visible">' + error.message + '</div>';
  }
}

initialiserFrontOffice();
})();
