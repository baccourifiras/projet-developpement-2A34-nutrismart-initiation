/* ============================================================
   NutriSmart — Backoffice script.js
   Ce fichier gère TOUT le backoffice :
     1. La lecture / écriture avec MySQL via PHP
     3. Ajouter / Modifier / Supprimer : Catégories, Événements, Participants
     4. Affichage des tableaux
     5. Les modals (modification + confirmation suppression)
   ============================================================ */


(function () {

/* ------------------------------------------------------------
   SECTION 1 : DONNÉES
   ------------------------------------------------------------ */

var API_URL = '/nutrismart_evenement/Controller/NutrismartController.php';
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

// Raccourcis pour accéder à chaque collection
function getCategories()   { return donneesCategories;   }
function getEvenements()   { return donneesEvenements;   }
function getParticipants() { return donneesParticipants; }

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
  if (!dateStr) return '-';
  return new Intl.DateTimeFormat('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' }).format(new Date(dateStr));
}

// Badge HTML pour les IDs dans les tableaux
function badgeId(id) {
  return '<span class="id-badge">' + (Number(id) || '-') + '</span>';
}

function afficherMessageSucces(type, titre, message) {
  var ancien = document.getElementById('successToast');
  if (ancien) ancien.remove();

  var icones = {
    add: '+',
    edit: '✓',
    delete: '×'
  };

  var toast = document.createElement('div');
  toast.id = 'successToast';
  toast.className = 'success-toast ' + type;
  toast.innerHTML =
    '<span class="toast-icon">' + (icones[type] || '✓') + '</span>' +
    '<span class="toast-content">' +
      '<strong>' + titre + '</strong>' +
      '<small>' + message + '</small>' +
    '</span>' +
    '<span class="toast-progress"></span>';
  document.body.appendChild(toast);

  setTimeout(function() {
    toast.classList.add('hide');
  }, 2200);
  setTimeout(function() {
    if (toast.parentNode) toast.remove();
  }, 2700);
}


/* ------------------------------------------------------------
   SECTION 3 : AJOUTER

   Ces fonctions ajoutent un nouvel élément dans MySQL.
   ------------------------------------------------------------ */

async function ajouterCategorie(data) { 
  await api('addCategory', data);
}

async function ajouterEvenement(data) {
  await api('addEvent', data);
}


/* ------------------------------------------------------------
   SECTION 4 : MODIFIER

   Ces fonctions trouvent un élément par son ID et le mettent à jour.
   ------------------------------------------------------------ */

async function modifierCategorie(id, data) {
  data.id = Number(id);
  await api('updateCategory', data);
}

async function modifierEvenement(id, data) {
  data.id = Number(id);
  await api('updateEvent', data);
}

async function modifierParticipant(id, data) {
  data.id = Number(id);
  await api('updateParticipant', data);
}


/* ------------------------------------------------------------
   SECTION 5 : SUPPRIMER

   La suppression d'une catégorie supprime aussi ses événements
   et les participants de ces événements (cascade).
   ------------------------------------------------------------ */

async function supprimerCategorie(id) {
  await api('deleteCategory', { id: Number(id) });
}

async function supprimerEvenement(id) {
  await api('deleteEvent', { id: Number(id) });
}

async function supprimerParticipant(id) {
  await api('deleteParticipant', { id: Number(id) });
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
  document.getElementById('confirmerSuppr').addEventListener('click', async function() {
    try {
      await fonctionSupprimer();
      modal.remove();
      toutRafraichir();
      afficherMessageSucces('delete', 'Suppression réussie', 'Les données ont été retirées de la base.');
    } catch (error) {
      alert(error.message);
    }
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

  document.getElementById('sauvegarderModif').addEventListener('click', async function() {
    try {
      await modifierCategorie(id, {
        name:        document.getElementById('mCatNom').value.trim(),
        description: document.getElementById('mCatDesc').value.trim()
      });
      modal.remove();
      toutRafraichir();
      afficherMessageSucces('edit', 'Modification enregistrée', 'Les changements sont sauvegardés.');
    } catch (error) {
      alert(error.message);
    }
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

  document.getElementById('sauvegarderModif').addEventListener('click', async function() {
    try {
      await modifierEvenement(id, {
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
      afficherMessageSucces('edit', 'Modification enregistrée', 'Les changements sont sauvegardés.');
    } catch (error) {
      alert(error.message);
    }
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

  document.getElementById('sauvegarderModif').addEventListener('click', async function() {
    try {
      await modifierParticipant(id, {
        fullName: document.getElementById('mPNom').value.trim(),
        email:    document.getElementById('mPEmail').value.trim(),
        phone:    document.getElementById('mPTel').value.trim(),
        eventId:  document.getElementById('mPEvent').value
      });
      modal.remove();
      toutRafraichir();
      afficherMessageSucces('edit', 'Modification enregistrée', 'Les changements sont sauvegardés.');
    } catch (error) {
      alert(error.message);
    }
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
document.getElementById('categoryForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  try {
    await ajouterCategorie({
      name:        document.getElementById('categoryName').value.trim(),
      description: document.getElementById('categoryDescription').value.trim()
    });
    this.reset();
    toutRafraichir();
    afficherMessageSucces('add', 'Ajout réussi', 'La catégorie est ajoutée à la base.');
  } catch (error) {
    alert(error.message);
  }
});

// Formulaire : ajouter un événement
document.getElementById('eventForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  try {
    await ajouterEvenement({
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
    afficherMessageSucces('add', 'Ajout réussi', 'L’événement est ajouté à la base.');
  } catch (error) {
    alert(error.message);
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
async function initialiserBackOffice() {
  try {
    await api('all');
    toutRafraichir();
    initMenuActif();
  } catch (error) {
    alert('Impossible de charger la base de données : ' + error.message);
  }
}

window.afficherConfirmSuppression = afficherConfirmSuppression;
window.afficherModalModifCategorie = afficherModalModifCategorie;
window.afficherModalModifEvenement = afficherModalModifEvenement;
window.afficherModalModifParticipant = afficherModalModifParticipant;
window.supprimerCategorie = supprimerCategorie;
window.supprimerEvenement = supprimerEvenement;
window.supprimerParticipant = supprimerParticipant;

initialiserBackOffice();
})();
