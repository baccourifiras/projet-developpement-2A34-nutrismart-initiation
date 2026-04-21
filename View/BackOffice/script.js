/*
   Back Office
   JavaScript garde seulement les controles, les modals et l'envoi POST classique.
*/

(function () {
  var CONTROLLER_URL = '/nutrismart_evenement/Controller/NutrismartController.php';

  function texteValide(valeur, min, label) {
    if (!valeur || valeur.trim().length < min) {
      alert(label + ' doit contenir au moins ' + min + ' caracteres.');
      return false;
    }
    return true;
  }

  function echapperHtml(valeur) {
    return String(valeur || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function afficherAnimationSucces(type) {
    var textes = {
      add: ['Ajout reussi', 'Les donnees sont enregistrees.'],
      edit: ['Modification reussie', 'Les changements sont sauvegardes.'],
      'delete': ['Suppression reussie', 'Les donnees sont supprimees.']
    };
    var info = textes[type];
    if (!info) return;

    var toast = document.createElement('div');
    toast.className = 'success-toast ' + type;
    toast.innerHTML =
      '<span class="toast-icon">' + (type === 'delete' ? 'x' : '+') + '</span>' +
      '<span class="toast-content">' +
        '<strong>' + info[0] + '</strong>' +
        '<small>' + info[1] + '</small>' +
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

  function controlerCategorie(data) {
    return texteValide(data.name, 3, 'Le nom de la categorie');
  }

  function controlerEvenement(data) {
    var dateEvenement = data.date ? new Date(data.date + 'T00:00:00') : null;
    var aujourdHui = new Date();
    aujourdHui.setHours(0, 0, 0, 0);

    if (!texteValide(data.title, 3, 'Le titre')) return false;
    if (!data.categoryId) {
      alert('Veuillez choisir une categorie.');
      return false;
    }
    if (!data.date || !dateEvenement || dateEvenement < aujourdHui) {
      alert('Veuillez choisir une date valide, aujourd hui ou plus tard.');
      return false;
    }
    if (!data.time) {
      alert('Veuillez saisir une heure.');
      return false;
    }
    if (!texteValide(data.location, 3, 'Le lieu')) return false;
    if (!Number(data.seats) || Number(data.seats) < 1) {
      alert('Le nombre de places doit etre superieur a 0.');
      return false;
    }
    if (!texteValide(data.description, 10, 'La description')) return false;
    if (data.image && !/^https?:\/\/.+/i.test(data.image)) {
      alert('L URL de l image doit commencer par http:// ou https://.');
      return false;
    }
    return true;
  }

  function controlerParticipant(data) {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var phoneRegex = /^[2459][0-9]{7}$/;

    if (!texteValide(data.fullName, 3, 'Le nom complet')) return false;
    if (!emailRegex.test(data.email || '')) {
      alert('Veuillez saisir une adresse email valide.');
      return false;
    }
    if (!phoneRegex.test(data.phone || '')) {
      alert('Le telephone doit contenir 8 chiffres et commencer par 2, 4, 5 ou 9.');
      return false;
    }
    if (!data.eventId) {
      alert('Veuillez choisir un evenement.');
      return false;
    }
    return true;
  }

  function envoyerFormulaire(action, data) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = CONTROLLER_URL + '?action=' + encodeURIComponent(action);

    var champs = Object.keys(data || {});
    for (var i = 0; i < champs.length; i++) {
      var key = champs[i];
      var input = document.createElement('input');
      input.type = 'hidden';
      input.name = key;
      input.value = data[key];
      form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
  }

  function fermerModal(id) {
    var modal = document.getElementById(id);
    if (modal) modal.remove();
  }

  function afficherConfirmSuppression(message, fonctionSupprimer) {
    fermerModal('deleteModal');

    var modal = document.createElement('div');
    modal.id = 'deleteModal';
    modal.innerHTML =
      '<div class="confirm-overlay">' +
        '<div class="confirm-box">' +
          '<div class="confirm-icon">!</div>' +
          '<h3>Confirmer la suppression</h3>' +
          '<p>' + message + '</p>' +
          '<div class="confirm-actions">' +
            '<button class="cancel-btn" id="annulerSuppr">Annuler</button>' +
            '<button class="danger-btn" id="confirmerSuppr">Supprimer</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);

    document.getElementById('confirmerSuppr').addEventListener('click', fonctionSupprimer);
    document.getElementById('annulerSuppr').addEventListener('click', function() { fermerModal('deleteModal'); });
    modal.querySelector('.confirm-overlay').addEventListener('click', function(e) {
      if (e.target === modal.querySelector('.confirm-overlay')) fermerModal('deleteModal');
    });
  }

  function afficherModalModifCategorie(bouton) {
    fermerModal('editModal');

    var modal = document.createElement('div');
    modal.id = 'editModal';
    modal.innerHTML =
      '<div class="confirm-overlay">' +
        '<div class="confirm-box edit-box">' +
          '<h3>Modifier la categorie</h3>' +
          '<div class="form-grid">' +
            '<div><label>Nom</label><input id="mCatNom" type="text" value="' + echapperHtml(bouton.dataset.name) + '" /></div>' +
            '<div><label>Description</label><input id="mCatDesc" type="text" value="' + echapperHtml(bouton.dataset.description) + '" /></div>' +
          '</div>' +
          '<div class="confirm-actions">' +
            '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
            '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);

    document.getElementById('sauvegarderModif').addEventListener('click', function() {
      var data = {
        id: bouton.dataset.id,
        name: document.getElementById('mCatNom').value.trim(),
        description: document.getElementById('mCatDesc').value.trim()
      };
      if (controlerCategorie(data)) envoyerFormulaire('updateCategory', data);
    });
    document.getElementById('annulerModif').addEventListener('click', function() { fermerModal('editModal'); });
  }

  function afficherModalModifEvenement(bouton) {
    fermerModal('editModal');

    var optionsCat = document.getElementById('eventCategory').innerHTML;
    var modal = document.createElement('div');
    modal.id = 'editModal';
    modal.innerHTML =
      '<div class="confirm-overlay">' +
        '<div class="confirm-box edit-box">' +
          '<h3>Modifier l evenement</h3>' +
          '<div class="form-grid two-columns">' +
            '<div><label>Titre</label><input id="mEvTitre" type="text" value="' + echapperHtml(bouton.dataset.title) + '" /></div>' +
            '<div><label>Categorie</label><select id="mEvCat">' + optionsCat + '</select></div>' +
            '<div><label>Date</label><input id="mEvDate" type="date" value="' + echapperHtml(bouton.dataset.date) + '" /></div>' +
            '<div><label>Heure</label><input id="mEvHeure" type="time" value="' + echapperHtml(bouton.dataset.time) + '" /></div>' +
            '<div><label>Lieu</label><input id="mEvLieu" type="text" value="' + echapperHtml(bouton.dataset.location) + '" /></div>' +
            '<div><label>Places</label><input id="mEvPlaces" type="number" min="1" value="' + echapperHtml(bouton.dataset.seats) + '" /></div>' +
            '<div class="full-width"><label>Description</label><textarea id="mEvDesc" rows="3">' + echapperHtml(bouton.dataset.description) + '</textarea></div>' +
            '<div class="full-width"><label>Image URL</label><input id="mEvImage" type="url" value="' + echapperHtml(bouton.dataset.image) + '" /></div>' +
          '</div>' +
          '<div class="confirm-actions">' +
            '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
            '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);
    document.getElementById('mEvCat').value = bouton.dataset.categoryId;

    document.getElementById('sauvegarderModif').addEventListener('click', function() {
      var data = {
        id: bouton.dataset.id,
        title: document.getElementById('mEvTitre').value.trim(),
        categoryId: document.getElementById('mEvCat').value,
        date: document.getElementById('mEvDate').value,
        time: document.getElementById('mEvHeure').value,
        location: document.getElementById('mEvLieu').value.trim(),
        seats: document.getElementById('mEvPlaces').value,
        description: document.getElementById('mEvDesc').value.trim(),
        image: document.getElementById('mEvImage').value.trim()
      };
      if (controlerEvenement(data)) envoyerFormulaire('updateEvent', data);
    });
    document.getElementById('annulerModif').addEventListener('click', function() { fermerModal('editModal'); });
  }

  function afficherModalModifParticipant(bouton) {
    fermerModal('editModal');

    var optionsEv = document.getElementById('eventOptionsSource').innerHTML;
    var modal = document.createElement('div');
    modal.id = 'editModal';
    modal.innerHTML =
      '<div class="confirm-overlay">' +
        '<div class="confirm-box edit-box">' +
          '<h3>Modifier le participant</h3>' +
          '<div class="form-grid two-columns">' +
            '<div><label>Nom complet</label><input id="mPNom" type="text" value="' + echapperHtml(bouton.dataset.fullName) + '" /></div>' +
            '<div><label>Email</label><input id="mPEmail" type="email" value="' + echapperHtml(bouton.dataset.email) + '" /></div>' +
            '<div><label>Telephone</label><input id="mPTel" type="text" minlength="8" maxlength="8" pattern="[2459][0-9]{7}" value="' + echapperHtml(bouton.dataset.phone) + '" /></div>' +
            '<div><label>Evenement</label><select id="mPEvent">' + optionsEv + '</select></div>' +
          '</div>' +
          '<div class="confirm-actions">' +
            '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
            '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);
    document.getElementById('mPEvent').value = bouton.dataset.eventId;

    document.getElementById('sauvegarderModif').addEventListener('click', function() {
      var data = {
        id: bouton.dataset.id,
        fullName: document.getElementById('mPNom').value.trim(),
        email: document.getElementById('mPEmail').value.trim(),
        phone: document.getElementById('mPTel').value.trim(),
        eventId: document.getElementById('mPEvent').value
      };
      if (controlerParticipant(data)) envoyerFormulaire('updateParticipant', data);
    });
    document.getElementById('annulerModif').addEventListener('click', function() { fermerModal('editModal'); });
  }

  function supprimerCategorie(id) {
    envoyerFormulaire('deleteCategory', { id: Number(id) });
  }

  function supprimerEvenement(id) {
    envoyerFormulaire('deleteEvent', { id: Number(id) });
  }

  function supprimerParticipant(id) {
    envoyerFormulaire('deleteParticipant', { id: Number(id) });
  }

  document.getElementById('categoryForm').addEventListener('submit', function(e) {
    var data = {
      name: document.getElementById('categoryName').value.trim(),
      description: document.getElementById('categoryDescription').value.trim()
    };
    if (!controlerCategorie(data)) e.preventDefault();
  });

  document.getElementById('eventForm').addEventListener('submit', function(e) {
    var data = {
      title: document.getElementById('eventTitle').value.trim(),
      categoryId: document.getElementById('eventCategory').value,
      date: document.getElementById('eventDate').value,
      time: document.getElementById('eventTime').value,
      location: document.getElementById('eventLocation').value.trim(),
      seats: document.getElementById('eventSeats').value,
      description: document.getElementById('eventDescription').value.trim(),
      image: document.getElementById('eventImage').value.trim()
    };
    if (!controlerEvenement(data)) e.preventDefault();
  });

  function initMenuActif() {
    var liens = Array.from(document.querySelectorAll('.menu a[href^="#"]'));
    var sections = [];

    for (var i = 0; i < liens.length; i++) {
      var section = document.querySelector(liens[i].getAttribute('href'));
      if (section) sections.push(section);
    }

    var obs = new IntersectionObserver(function(entrees) {
      for (var i = 0; i < entrees.length; i++) {
        if (!entrees[i].isIntersecting) continue;
        for (var j = 0; j < liens.length; j++) {
          liens[j].classList.toggle('active', liens[j].getAttribute('href') === '#' + entrees[i].target.id);
        }
      }
    }, { threshold: 0.35 });

    for (var i = 0; i < sections.length; i++) {
      obs.observe(sections[i]);
    }
  }

  function lireParametre(nom) {
    var query = window.location.search.substring(1);
    var params = query.split('&');

    for (var i = 0; i < params.length; i++) {
      var morceau = params[i].split('=');
      if (decodeURIComponent(morceau[0]) === nom) {
        return decodeURIComponent(morceau[1] || '');
      }
    }

    return '';
  }

  function nettoyerParametreSucces() {
    if (!window.history || !window.history.replaceState) return;

    var query = window.location.search.substring(1);
    var params = query.split('&');
    var nouvelleQuery = [];

    for (var i = 0; i < params.length; i++) {
      if (params[i] === '') continue;
      var nom = params[i].split('=')[0];
      if (decodeURIComponent(nom) !== 'success') {
        nouvelleQuery.push(params[i]);
      }
    }

    var nouvelleUrl = window.location.pathname;
    if (nouvelleQuery.length > 0) {
      nouvelleUrl += '?' + nouvelleQuery.join('&');
    }
    nouvelleUrl += window.location.hash;

    window.history.replaceState(null, '', nouvelleUrl);
  }

  window.afficherConfirmSuppression = afficherConfirmSuppression;
  window.afficherModalModifCategorie = afficherModalModifCategorie;
  window.afficherModalModifEvenement = afficherModalModifEvenement;
  window.afficherModalModifParticipant = afficherModalModifParticipant;
  window.supprimerCategorie = supprimerCategorie;
  window.supprimerEvenement = supprimerEvenement;
  window.supprimerParticipant = supprimerParticipant;

  var success = lireParametre('success');
  afficherAnimationSucces(success);
  if (success !== '') nettoyerParametreSucces();
  initMenuActif();
})();
