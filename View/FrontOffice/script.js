/* ============================================================
   NutriSmart — Front Office
   Ce fichier gère :
     1. La lecture/écriture des données (localStorage)
     2. L'affichage des catégories
     3. L'affichage des événements
     4. L'inscription d'un participant
   ============================================================ */


/* ------------------------------------------------------------
   SECTION 1 : DONNÉES PAR DÉFAUT
   Ces données sont chargées la PREMIÈRE fois seulement.
   Après, c'est le localStorage qui prend le relais.
   ------------------------------------------------------------ */

var donneesCategories = [
  { id: 1, name: 'Nutrition sportive', description: 'Événements pour améliorer les performances sportives grâce à une alimentation adaptée.' },
  { id: 2, name: 'Régime minceur',     description: 'Ateliers et conférences pour la perte de poids et le rééquilibrage alimentaire.' },
  { id: 3, name: 'Alimentation saine', description: 'Conseils pratiques pour adopter une nutrition équilibrée au quotidien.' }
];

var donneesEvenements = [
  { id: 1, title: 'Atelier repas équilibré',      description: 'Un atelier pratique pour apprendre à composer des repas sains.', date: '2026-05-12', time: '10:00', location: 'Tunis',  categoryId: 3, seats: 30, image: 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80' },
  { id: 2, title: 'Conférence nutrition sportive', description: 'Une conférence dédiée à la nutrition avant et après le sport.',   date: '2026-05-18', time: '14:00', location: 'Sfax',   categoryId: 1, seats: 50, image: 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80' },
  { id: 3, title: 'Journée régime minceur',        description: 'Une journée de conseils sur le régime minceur et les bonnes habitudes.', date: '2026-05-22', time: '09:30', location: 'Sousse', categoryId: 2, seats: 40, image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=1200&q=80' }
];

var donneesParticipants = [
  { id: 1, fullName: 'Amine Abidi', email: 'amine@example.com', phone: '22111222', eventId: 1, registeredAt: '2026-04-09' }
];


/* ------------------------------------------------------------
   SECTION 2 : LECTURE ET ÉCRITURE (localStorage)

   localStorage = stockage dans le navigateur.
   Les données restent même si on ferme l'onglet ou le navigateur.

   IMPORTANT : Pour l'instant c'est localStorage (côté client).
   Plus tard, ces fonctions seront remplacées par des appels PHP/MySQL.
   ------------------------------------------------------------ */

// Lire les données depuis localStorage
// Si elles n'existent pas encore → on sauvegarde et retourne les données par défaut
function lireDonnees(cle, defaut) {
  var stocke = localStorage.getItem(cle);
  if (stocke === null) {
    // Première visite : on initialise avec les données par défaut
    localStorage.setItem(cle, JSON.stringify(defaut));
    return JSON.parse(JSON.stringify(defaut));
  }
  return JSON.parse(stocke);
}

// Écrire/mettre à jour les données dans localStorage
function sauvegarder(cle, valeur) {
  localStorage.setItem(cle, JSON.stringify(valeur));
}

// Raccourcis pour lire chaque collection
function getCategories()   { return lireDonnees('nutrismart_categories',   donneesCategories);   }
function getEvenements()   { return lireDonnees('nutrismart_events',       donneesEvenements);   }
function getParticipants() { return lireDonnees('nutrismart_participants', donneesParticipants); }

// Générer un nouvel ID unique (prend le plus grand ID existant + 1)
function nouvelId(liste) {
  return liste.reduce(function(max, item) { return Math.max(max, item.id || 0); }, 0) + 1;
}


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
    carte.innerHTML =
      '<img class="event-image" src="' + ev.image + '" alt="' + ev.title + '">' +
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
document.getElementById('participantForm').addEventListener('submit', function(e) {
  e.preventDefault();

  // Ajouter le nouveau participant dans localStorage
  var participants = getParticipants();
  participants.push({
    id:           nouvelId(participants),
    fullName:     document.getElementById('fullName').value.trim(),
    email:        document.getElementById('email').value.trim(),
    phone:        document.getElementById('phone').value.trim(),
    eventId:      Number(document.getElementById('participantEventId').value),
    registeredAt: new Date().toISOString().slice(0, 10)
  });
  sauvegarder('nutrismart_participants', participants);

  document.getElementById('messageBox').textContent = 'Participation enregistrée avec succès !';
  afficherEvenements();
  setTimeout(fermerModal, 1000);
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
afficherCategories();
afficherEvenements();
activerAnimations();
