/* ============================================================
   NutriSmart — Backoffice script.js
   Ce fichier gère TOUT le backoffice :
     1. Les données par défaut
     2. La lecture / écriture (localStorage — temporaire)
     3. Ajouter / Modifier / Supprimer : Catégories, Événements, Participants
     4. Affichage des tableaux
     5. Les modals (modification + confirmation suppression)

   NOTE IMPORTANTE : Pour l'instant les données sont stockées dans
   le localStorage du navigateur. Quand le projet sera connecté à
   une base de données MySQL + PHP, il suffira de remplacer les
   fonctions lireDonnees() et sauvegarder() par des appels fetch()
   vers les fichiers PHP.
   ============================================================ */


/* ------------------------------------------------------------
   SECTION 1 : DONNÉES PAR DÉFAUT
   Chargées uniquement à la première ouverture du site.
   ------------------------------------------------------------ */

var donneesCategories = [
  { id: 1, name: 'Nutrition sportive',  description: 'Événements pour améliorer les performances sportives grâce à une alimentation adaptée.' },
  { id: 2, name: 'Régime minceur',      description: 'Ateliers et conférences pour la perte de poids et le rééquilibrage alimentaire.' },
  { id: 3, name: 'Alimentation saine',  description: 'Conseils pratiques pour adopter une nutrition équilibrée au quotidien.' }
];

var donneesEvenements = [
  { id: 1, title: 'Atelier repas équilibré',       description: 'Un atelier pratique pour apprendre à composer des repas sains.',             date: '2026-05-12', time: '10:00', location: 'Tunis',  categoryId: 3, seats: 30, image: 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80' },
  { id: 2, title: 'Conférence nutrition sportive',  description: "Une conférence dédiée à la nutrition avant et après l'activité physique.",   date: '2026-05-18', time: '14:00', location: 'Sfax',   categoryId: 1, seats: 50, image: 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1200&q=80' },
  { id: 3, title: 'Journée régime minceur',         description: "Une journée d'accompagnement avec des conseils sur le régime minceur.",      date: '2026-05-22', time: '09:30', location: 'Sousse', categoryId: 2, seats: 40, image: 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=1200&q=80' }
];

var donneesParticipants = [
  { id: 1, fullName: 'Amine Abidi', email: 'amine@example.com', phone: '22111222', eventId: 1, registeredAt: '2026-04-09' }
];


/* ------------------------------------------------------------
   SECTION 2 : LECTURE ET ÉCRITURE (localStorage)

   localStorage = stockage dans le navigateur.
   Les données restent tant que l'utilisateur ne vide pas le cache.

   ⚠️  ATTENTION : Si vous ouvrez le fichier .html directement
   depuis votre ordinateur (sans serveur), les données sont bien
   sauvegardées dans votre navigateur. Mais si vous changez de
   navigateur ou videz le cache, elles disparaissent.

   🔜  FUTURE CONNEXION PHP/MySQL :
   Remplacer lireDonnees() par : fetch('api/categories.php')
   Remplacer sauvegarder() par : fetch('api/save.php', {method:'POST', body:...})
   ------------------------------------------------------------ */

// Lire les données depuis localStorage
// Si la clé n'existe pas → première visite → on sauvegarde les données par défaut
function lireDonnees(cle, defaut) {
  var stocke = localStorage.getItem(cle);
  if (stocke === null) {
    localStorage.setItem(cle, JSON.stringify(defaut));
    return JSON.parse(JSON.stringify(defaut));
  }
  return JSON.parse(stocke);
}

// Écrire les données dans localStorage
function sauvegarder(cle, valeur) {
  localStorage.setItem(cle, JSON.stringify(valeur));
}

// Raccourcis pour accéder à chaque collection
function getCategories()   { return lireDonnees('nutrismart_categories',   donneesCategories);   }
function getEvenements()   { return lireDonnees('nutrismart_events',       donneesEvenements);   }
function getParticipants() { return lireDonnees('nutrismart_participants', donneesParticipants); }

// Génère un nouvel ID unique = plus grand ID existant + 1
function nouvelId(liste) {
  return liste.reduce(function(max, item) { return Math.max(max, item.id || 0); }, 0) + 1;
}

// Retourne le nom d'une catégorie depuis son ID
function getNomCategorie(id) {
  var cat = getCategories().find(function(c) { return c.id === Number(id); });
  return cat ? cat.name : 'Sans catégorie';
}

// Retourne le titre d'un événement depuis son ID
function getTitreEvenement(id) {
  var ev = getEvenements().find(function(e) { return e.id === Number(id); });
  return ev ? ev.title : 'Événement introuvable';
}

// Formate une date ISO en français (ex: "2026-05-12" → "12 mai 2026")
function formaterDate(dateStr) {
  return new Intl.DateTimeFormat('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(dateStr));
}

// Badge HTML pour les IDs dans les tableaux
function badgeId(id) {
  return '<span class="id-badge">' + (Number(id) || '-') + '</span>';
}


/* ------------------------------------------------------------
   SECTION 3 : AJOUTER

   Ces fonctions ajoutent un nouvel élément dans localStorage.
   🔜 PHP : remplacer par fetch('api/ajouter_categorie.php', ...)
   ------------------------------------------------------------ */

function ajouterCategorie(data) { 
  var liste = getCategories();
  liste.push({ id: nouvelId(liste), name: data.name, description: data.description || '' });
  sauvegarder('nutrismart_categories', liste);
}

function ajouterEvenement(data) {
  var liste = getEvenements();
  liste.push({
    id: nouvelId(liste),
    title:       data.title,
    description: data.description,
    date:        data.date,
    time:        data.time,
    location:    data.location,
    categoryId:  Number(data.categoryId),
    seats:       Number(data.seats),
    image:       data.image || 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?auto=format&fit=crop&w=1200&q=80'
  });
  sauvegarder('nutrismart_events', liste);
}


/* ------------------------------------------------------------
   SECTION 4 : MODIFIER

   Ces fonctions trouvent un élément par son ID et le mettent à jour.
   🔜 PHP : remplacer par fetch('api/modifier_categorie.php', ...)
   ------------------------------------------------------------ */

function modifierCategorie(id, data) {
  var liste = getCategories();
  var index = liste.findIndex(function(c) { return c.id === Number(id); });
  if (index === -1) return; // ID introuvable
  liste[index].name= data.name;
  liste[index].description = data.description || '';
  sauvegarder('nutrismart_categories', liste);
}

function modifierEvenement(id, data) {
  var liste = getEvenements();
  var index = liste.findIndex(function(e) { return e.id === Number(id); });
  if (index === -1) return;
  liste[index].title       = data.title;
  liste[index].description = data.description;
  liste[index].date        = data.date;
  liste[index].time        = data.time;
  liste[index].location    = data.location;
  liste[index].categoryId  = Number(data.categoryId);
  liste[index].seats       = Number(data.seats);
  liste[index].image       = data.image || liste[index].image;
  sauvegarder('nutrismart_events', liste);
}

function modifierParticipant(id, data) {
  var liste = getParticipants();
  var index = liste.findIndex(function(p) { return p.id === Number(id); });
  if (index === -1) return;
  liste[index].fullName = data.fullName;
  liste[index].email    = data.email;
  liste[index].phone    = data.phone;
  liste[index].eventId  = Number(data.eventId);
  sauvegarder('nutrismart_participants', liste);
}


/* ------------------------------------------------------------
   SECTION 5 : SUPPRIMER

   La suppression d'une catégorie supprime aussi ses événements
   et les participants de ces événements (cascade).
   🔜 PHP : remplacer par fetch('api/supprimer_categorie.php', ...)
   ------------------------------------------------------------ */

function supprimerCategorie(id) {
  id = Number(id);

  // Trouver les IDs des événements liés à cette catégorie
  var idsEvenements = getEvenements()
    .filter(function(e) { return e.categoryId === id; })
    .map(function(e) { return e.id; });

  // Supprimer les participants de ces événements
  sauvegarder('nutrismart_participants',
    getParticipants().filter(function(p) { return !idsEvenements.includes(p.eventId); })
  );

  // Supprimer les événements de cette catégorie
  sauvegarder('nutrismart_events',
    getEvenements().filter(function(e) { return e.categoryId !== id; })
  );

  // Supprimer la catégorie elle-même
  sauvegarder('nutrismart_categories',
    getCategories().filter(function(c) { return c.id !== id; })
  );
}

function supprimerEvenement(id) {
  id = Number(id);

  // Supprimer les participants de cet événement
  sauvegarder('nutrismart_participants',
    getParticipants().filter(function(p) { return p.eventId !== id; })
  );

  // Supprimer l'événement
  sauvegarder('nutrismart_events',
    getEvenements().filter(function(e) { return e.id !== id; })
  );
}

function supprimerParticipant(id) {
  sauvegarder('nutrismart_participants',
    getParticipants().filter(function(p) { return p.id !== Number(id); })
  );
}


/* ------------------------------------------------------------
   SECTION 6 : AFFICHAGE DES TABLEAUX
   ------------------------------------------------------------ */

function afficherTableauCategories() {
  var liste = getCategories();
  document.getElementById('categoryTableContainer').innerHTML =
    '<div class="table-wrapper"><table class="table">' +
    '<thead><tr><th>ID</th><th>Nom</th><th>Description</th><th>Actions</th></tr></thead>' +
    '<tbody>' +
    liste.map(function(c) {
      return '<tr>' +
        '<td>' + badgeId(c.id) + '</td>' +
        '<td><span class="small-badge">' + c.name + '</span></td>' +
        '<td>' + (c.description || '-') + '</td>' +
        '<td class="action-cell">' +
          '<button class="edit-btn"   onclick="afficherModalModifCategorie(' + c.id + ')">✏️ Modifier</button>' +
          '<button class="delete-btn" onclick="afficherConfirmSuppression(\'Supprimer la catégorie <strong>' + c.name + '</strong> ? Ses événements et participants seront aussi supprimés.\', function(){ supprimerCategorie(' + c.id + '); })">🗑️ Supprimer</button>' +
        '</td>' +
      '</tr>';
    }).join('') +
    '</tbody></table></div>';
}

function afficherTableauEvenements() {
  var liste = getEvenements();
  document.getElementById('eventTableContainer').innerHTML =
    '<div class="table-wrapper"><table class="table">' +
    '<thead><tr><th>ID</th><th>Titre</th><th>Catégorie</th><th>Date</th><th>Lieu</th><th>Actions</th></tr></thead>' +
    '<tbody>' +
    liste.map(function(e) {
      return '<tr>' +
        '<td>' + badgeId(e.id) + '</td>' +
        '<td>' + e.title + '</td>' +
        '<td>' + getNomCategorie(e.categoryId) + '</td>' +
        '<td>' + formaterDate(e.date) + '</td>' +
        '<td>' + e.location + '</td>' +
        '<td class="action-cell">' +
          '<button class="edit-btn"   onclick="afficherModalModifEvenement(' + e.id + ')">✏️ Modifier</button>' +
          '<button class="delete-btn" onclick="afficherConfirmSuppression(\'Supprimer <strong>' + e.title + '</strong> ? Ses participants seront aussi supprimés.\', function(){ supprimerEvenement(' + e.id + '); })">🗑️ Supprimer</button>' +
        '</td>' +
      '</tr>';
    }).join('') +
    '</tbody></table></div>';
}

function afficherTableauParticipants() {
  var liste = getParticipants();
  if (liste.length === 0) {
    document.getElementById('participantTableContainer').innerHTML = '<p class="note">Aucun participant enregistré pour le moment.</p>';
    return;
  }
  document.getElementById('participantTableContainer').innerHTML =
    '<div class="table-wrapper"><table class="table">' +
    '<thead><tr><th>ID</th><th>Participant</th><th>Email</th><th>Téléphone</th><th>Événement</th><th>Inscrit le</th><th>Actions</th></tr></thead>' +
    '<tbody>' +
    liste.map(function(p) {
      return '<tr>' +
        '<td>' + badgeId(p.id) + '</td>' +
        '<td>' + p.fullName + '</td>' +
        '<td>' + p.email + '</td>' +
        '<td>' + p.phone + '</td>' +
        '<td>' + getTitreEvenement(p.eventId) + '</td>' +
        '<td>' + p.registeredAt + '</td>' +
        '<td class="action-cell">' +
          '<button class="edit-btn"   onclick="afficherModalModifParticipant(' + p.id + ')">✏️ Modifier</button>' +
          '<button class="delete-btn" onclick="afficherConfirmSuppression(\'Supprimer le participant <strong>' + p.fullName + '</strong> ?\', function(){ supprimerParticipant(' + p.id + '); })">🗑️ Supprimer</button>' +
        '</td>' +
      '</tr>';
    }).join('') +
    '</tbody></table></div>';
}


/* ------------------------------------------------------------
   SECTION 7 : MODALS

   a) Modal de confirmation de suppression
   b) Modal de modification d'une catégorie
   c) Modal de modification d'un événement
   d) Modal de modification d'un participant
   ------------------------------------------------------------ */

// a) Affiche un modal de confirmation avant de supprimer
function afficherConfirmSuppression(message, fonctionSupprimer) {
  // Supprimer l'ancien modal s'il existe
  var ancien = document.getElementById('deleteModal');
  if (ancien) ancien.remove();

  var modal = document.createElement('div');
  modal.id = 'deleteModal';
  modal.innerHTML =
    '<div class="confirm-overlay">' +
      '<div class="confirm-box">' +
        '<div class="confirm-icon">🗑️</div>' +
        '<h3>Confirmer la suppression</h3>' +
        '<p>' + message + '</p>' +
        '<div class="confirm-actions">' +
          '<button class="cancel-btn" id="annulerSuppr">Annuler</button>' +
          '<button class="danger-btn"  id="confirmerSuppr">Supprimer</button>' +
        '</div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(modal);

  // Clic "Supprimer" → exécute la fonction passée en paramètre
  document.getElementById('confirmerSuppr').addEventListener('click', function() {
    fonctionSupprimer();
    modal.remove();
    toutRafraichir();
  });

  // Clic "Annuler" ou sur le fond → fermer
  document.getElementById('annulerSuppr').addEventListener('click', function() { modal.remove(); });
  modal.querySelector('.confirm-overlay').addEventListener('click', function(e) {
    if (e.target === modal.querySelector('.confirm-overlay')) modal.remove();
  });
}

// b) Modal modification d'une catégorie
function afficherModalModifCategorie(id) {
  var cat = getCategories().find(function(c) { return c.id === Number(id); });
  if (!cat) return;

  var ancien = document.getElementById('editModal');
  if (ancien) ancien.remove();

  var modal = document.createElement('div');
  modal.id = 'editModal';
  modal.innerHTML =
    '<div class="confirm-overlay">' +
      '<div class="confirm-box edit-box">' +
        '<h3>✏️ Modifier la catégorie</h3>' +
        '<div class="form-grid">' +
          '<div><label>Nom</label><input id="mCatNom" type="text" value="' + cat.name + '" /></div>' +
          '<div><label>Description</label><input id="mCatDesc" type="text" value="' + (cat.description || '') + '" /></div>' +
        '</div>' +
        '<div class="confirm-actions">' +
          '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
          '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
        '</div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(modal);

  document.getElementById('sauvegarderModif').addEventListener('click', function() {
    modifierCategorie(id, {
      name:        document.getElementById('mCatNom').value.trim(),
      description: document.getElementById('mCatDesc').value.trim()
    });
    modal.remove();
    toutRafraichir();
  });
  document.getElementById('annulerModif').addEventListener('click', function() { modal.remove(); });
}

// c) Modal modification d'un événement
function afficherModalModifEvenement(id) {
  var ev = getEvenements().find(function(e) { return e.id === Number(id); });
  if (!ev) return;

  var ancien = document.getElementById('editModal');
  if (ancien) ancien.remove();

  // Construire les options du select catégorie
  var optionsCat = getCategories().map(function(c) {
    return '<option value="' + c.id + '"' + (c.id === ev.categoryId ? ' selected' : '') + '>' + c.name + '</option>';
  }).join('');

  var modal = document.createElement('div');
  modal.id = 'editModal';
  modal.innerHTML =
    '<div class="confirm-overlay">' +
      '<div class="confirm-box edit-box">' +
        '<h3>✏️ Modifier l\'événement</h3>' +
        '<div class="form-grid two-columns">' +
          '<div><label>Titre</label><input id="mEvTitre" type="text" value="' + ev.title + '" /></div>' +
          '<div><label>Catégorie</label><select id="mEvCat">' + optionsCat + '</select></div>' +
          '<div><label>Date</label><input id="mEvDate" type="date" value="' + ev.date + '" /></div>' +
          '<div><label>Heure</label><input id="mEvHeure" type="time" value="' + ev.time + '" /></div>' +
          '<div><label>Lieu</label><input id="mEvLieu" type="text" value="' + ev.location + '" /></div>' +
          '<div><label>Places</label><input id="mEvPlaces" type="number" min="1" value="' + ev.seats + '" /></div>' +
          '<div class="full-width"><label>Description</label><textarea id="mEvDesc" rows="3">' + ev.description + '</textarea></div>' +
          '<div class="full-width"><label>Image URL</label><input id="mEvImage" type="url" value="' + (ev.image || '') + '" /></div>' +
        '</div>' +
        '<div class="confirm-actions">' +
          '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
          '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
        '</div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(modal);

  document.getElementById('sauvegarderModif').addEventListener('click', function() {
    modifierEvenement(id, {
      title:       document.getElementById('mEvTitre').value.trim(),
      categoryId:  document.getElementById('mEvCat').value,
      date:        document.getElementById('mEvDate').value,
      time:        document.getElementById('mEvHeure').value,
      location:    document.getElementById('mEvLieu').value.trim(),
      seats:       document.getElementById('mEvPlaces').value,
      description: document.getElementById('mEvDesc').value.trim(),
      image:       document.getElementById('mEvImage').value.trim()
    });
    modal.remove();
    toutRafraichir();
  });
  document.getElementById('annulerModif').addEventListener('click', function() { modal.remove(); });
}

// d) Modal modification d'un participant
function afficherModalModifParticipant(id) {
  var p = getParticipants().find(function(p) { return p.id === Number(id); });
  if (!p) return;

  var ancien = document.getElementById('editModal');
  if (ancien) ancien.remove();

  // Options des événements
  var optionsEv = getEvenements().map(function(e) {
    return '<option value="' + e.id + '"' + (e.id === p.eventId ? ' selected' : '') + '>' + e.title + '</option>';
  }).join('');

  var modal = document.createElement('div');
  modal.id = 'editModal';
  modal.innerHTML =
    '<div class="confirm-overlay">' +
      '<div class="confirm-box edit-box">' +
        '<h3>✏️ Modifier le participant</h3>' +
        '<div class="form-grid two-columns">' +
          '<div><label>Nom complet</label><input id="mPNom" type="text" value="' + p.fullName + '" /></div>' +
          '<div><label>Email</label><input id="mPEmail" type="email" value="' + p.email + '" /></div>' +
          '<div><label>Téléphone</label><input id="mPTel" type="text" value="' + p.phone + '" /></div>' +
          '<div><label>Événement</label><select id="mPEvent">' + optionsEv + '</select></div>' +
        '</div>' +
        '<div class="confirm-actions">' +
          '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
          '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
        '</div>' +
      '</div>' +
    '</div>';
  document.body.appendChild(modal);

  document.getElementById('sauvegarderModif').addEventListener('click', function() {
    modifierParticipant(id, {
      fullName: document.getElementById('mPNom').value.trim(),
      email:    document.getElementById('mPEmail').value.trim(),
      phone:    document.getElementById('mPTel').value.trim(),
      eventId:  document.getElementById('mPEvent').value
    });
    modal.remove();
    toutRafraichir();
  });
  document.getElementById('annulerModif').addEventListener('click', function() { modal.remove(); });
}


/* ------------------------------------------------------------
   SECTION 8 : STATISTIQUES ET SELECT CATÉGORIE
   ------------------------------------------------------------ */

function mettreAJourStats() {
  document.getElementById('categoryCount').textContent    = getCategories().length;
  document.getElementById('eventCount').textContent       = getEvenements().length;
  document.getElementById('participantCount').textContent = getParticipants().length;
}

function remplirSelectCategorie() {
  var select = document.getElementById('eventCategory');
  var cats   = getCategories();
  select.innerHTML = '<option value="">Choisir une catégorie</option>';
  cats.forEach(function(c) {
    var opt = document.createElement('option');
    opt.value = c.id;
    opt.textContent = c.name;
    select.appendChild(opt);
  });
}

// Rafraîchit tous les éléments de la page
function toutRafraichir() {
  mettreAJourStats();
  remplirSelectCategorie();
  afficherTableauCategories();
  afficherTableauEvenements();
  afficherTableauParticipants();
}


/* ------------------------------------------------------------
   SECTION 9 : FORMULAIRES D'AJOUT
   ------------------------------------------------------------ */

// Formulaire : ajouter une catégorie
document.getElementById('categoryForm').addEventListener('submit', function(e) {
  e.preventDefault();
  ajouterCategorie({
    name:        document.getElementById('categoryName').value.trim(),
    description: document.getElementById('categoryDescription').value.trim()
  });
  this.reset();
  toutRafraichir();
});

// Formulaire : ajouter un événement
document.getElementById('eventForm').addEventListener('submit', function(e) {
  e.preventDefault();
  ajouterEvenement({
    title:       document.getElementById('eventTitle').value.trim(),
    categoryId:  document.getElementById('eventCategory').value,
    date:        document.getElementById('eventDate').value,
    time:        document.getElementById('eventTime').value,
    location:    document.getElementById('eventLocation').value.trim(),
    seats:       document.getElementById('eventSeats').value,
    description: document.getElementById('eventDescription').value.trim(),
    image:       document.getElementById('eventImage').value.trim()
  });
  this.reset();
  toutRafraichir();
});

// Bouton : réinitialiser toutes les données aux valeurs par défaut
document.getElementById('resetDataBtn').addEventListener('click', function() {
  if (confirm('Réinitialiser toutes les données aux valeurs par défaut ?')) {
    sauvegarder('nutrismart_categories',   donneesCategories);
    sauvegarder('nutrismart_events',       donneesEvenements);
    sauvegarder('nutrismart_participants', donneesParticipants);
    toutRafraichir();
  }
});


/* ------------------------------------------------------------
   SECTION 10 : MENU ACTIF AU SCROLL
   ------------------------------------------------------------ */

function initMenuActif() {
  var liens    = Array.from(document.querySelectorAll('.menu a[href^="#"]'));
  var sections = liens.map(function(l) { return document.querySelector(l.getAttribute('href')); }).filter(Boolean);

  var obs = new IntersectionObserver(function(entrees) {
    entrees.forEach(function(entree) {
      if (!entree.isIntersecting) return;
      liens.forEach(function(l) {
        l.classList.toggle('active', l.getAttribute('href') === '#' + entree.target.id);
      });
    });
  }, { threshold: 0.35 });

  sections.forEach(function(s) { obs.observe(s); });
}


/* ------------------------------------------------------------
   DÉMARRAGE : initialiser la page
   ------------------------------------------------------------ */
toutRafraichir();
initMenuActif();
