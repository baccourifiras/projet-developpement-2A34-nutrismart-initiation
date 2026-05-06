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
    data = data || {};

    if (!Object.prototype.hasOwnProperty.call(data, 'redirect')) {
      data.redirect = window.location.pathname + window.location.hash;
    }

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
            '<div><label>Nom</label><input id="mCatNom" type="text" minlength="3" maxlength="80" title="Le nom doit contenir entre 3 et 80 caracteres." value="' + echapperHtml(bouton.dataset.name) + '" /></div>' +
            '<div><label>Description</label><input id="mCatDesc" type="text" maxlength="255" title="La description ne doit pas depasser 255 caracteres." value="' + echapperHtml(bouton.dataset.description) + '" /></div>' +
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
            '<div><label>Titre</label><input id="mEvTitre" type="text" minlength="3" maxlength="120" title="Le titre doit contenir entre 3 et 120 caracteres." value="' + echapperHtml(bouton.dataset.title) + '" /></div>' +
            '<div><label>Categorie</label><select id="mEvCat">' + optionsCat + '</select></div>' +
            '<div><label>Date</label><input id="mEvDate" type="date" min="' + new Date().toISOString().split('T')[0] + '" value="' + echapperHtml(bouton.dataset.date) + '" /></div>' +
            '<div><label>Heure</label><input id="mEvHeure" type="time" value="' + echapperHtml(bouton.dataset.time) + '" /></div>' +
            '<div><label>Lieu</label><input id="mEvLieu" type="text" minlength="3" maxlength="120" title="Le lieu doit contenir entre 3 et 120 caracteres." value="' + echapperHtml(bouton.dataset.location) + '" /></div>' +
            '<div><label>Places</label><input id="mEvPlaces" type="number" min="1" step="1" title="Le nombre de places doit etre superieur a 0." value="' + echapperHtml(bouton.dataset.seats) + '" /></div>' +
            '<div class="full-width"><label>Description</label>' +
              '<div class="description-wrapper">' +
                '<textarea id="mEvDesc" rows="3" minlength="10" maxlength="1000" title="La description doit contenir entre 10 et 1000 caracteres.">' + echapperHtml(bouton.dataset.description) + '</textarea>' +
                '<button type="button" class="ia-btn" id="genererIaBtnModal"><span class="ia-btn-icon">✦</span><span class="ia-btn-text">Générer avec IA</span></button>' +
              '</div>' +
            '</div>' +
            '<div class="full-width"><label>Image URL</label><input id="mEvImage" type="url" pattern="https?://.+" title="L URL doit commencer par http:// ou https://." value="' + echapperHtml(bouton.dataset.image) + '" /></div>' +
            '<div class="full-width"><label>📍 Localisation <span style="font-size:11px;font-weight:400;color:#688273">— cliquez sur la carte pour repositionner</span></label>' +
              '<div id="mapPickerModal" class="map-picker-modal"></div>' +
              '<div class="map-coords-row">' +
                '<span id="editMapCoordsText" class="map-coords-text">Aucune position sélectionnée</span>' +
                '<button type="button" class="map-clear-btn" id="editMapClearBtn">✕ Effacer</button>' +
              '</div>' +
              '<input type="hidden" id="mEvLatitude"  value="' + echapperHtml(bouton.dataset.latitude  || '') + '" />' +
              '<input type="hidden" id="mEvLongitude" value="' + echapperHtml(bouton.dataset.longitude || '') + '" />' +
              '<input type="hidden" id="mEvGoogleMaps" value="' + echapperHtml(bouton.dataset.googleMapsLink || '') + '" />' +
            '</div>' +
          '</div>' +
          '<div class="confirm-actions">' +
            '<button class="cancel-btn" id="annulerModif">Annuler</button>' +
            '<button class="primary-btn" id="sauvegarderModif">Enregistrer</button>' +
          '</div>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);
    document.getElementById('mEvCat').value = bouton.dataset.categoryId;

    // Initialiser la carte Leaflet dans la modal
    setTimeout(function() {
      var lat = parseFloat(bouton.dataset.latitude)  || 36.8065;
      var lng = parseFloat(bouton.dataset.longitude) || 10.1815;
      var zoom = (bouton.dataset.latitude && bouton.dataset.latitude !== '') ? 13 : 7;

      window._editMap = L.map('mapPickerModal').setView([lat, lng], zoom);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
      }).addTo(window._editMap);

      // Si coordonnées existantes, placer le marqueur
      if (bouton.dataset.latitude && bouton.dataset.latitude !== '') {
        window._editMapMarker = L.marker([lat, lng]).addTo(window._editMap);
        var ct = document.getElementById('editMapCoordsText');
        if (ct) { ct.textContent = 'Position : ' + lat.toFixed(5) + ', ' + lng.toFixed(5); ct.classList.add('active'); }
      }

      // Clic sur la carte
      window._editMap.on('click', function(e) {
        placerMarqueur(window._editMap, e.latlng.lat, e.latlng.lng, 'edit');
      });

      // Bouton effacer
      var btnClear = document.getElementById('editMapClearBtn');
      if (btnClear) btnClear.addEventListener('click', function() { effacerMarqueur('edit'); });
    }, 150);

    // Bouton IA dans la modal de modification
    document.getElementById('genererIaBtnModal').addEventListener('click', function() {
      genererDescriptionIA(
        document.getElementById('mEvTitre'),
        document.getElementById('mEvCat'),
        document.getElementById('mEvLieu'),
        document.getElementById('mEvDate'),
        document.getElementById('mEvDesc')
      );
    });

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
        image: document.getElementById('mEvImage').value.trim(),
        googleMapsLink: document.getElementById('mEvGoogleMaps').value.trim(),
        latitude:  document.getElementById('mEvLatitude').value,
        longitude: document.getElementById('mEvLongitude').value
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
            '<div><label>Nom complet</label><input id="mPNom" type="text" minlength="3" maxlength="120" title="Le nom complet doit contenir entre 3 et 120 caracteres." value="' + echapperHtml(bouton.dataset.fullName) + '" /></div>' +
            '<div><label>Email</label><input id="mPEmail" type="email" title="Veuillez saisir une adresse email valide." value="' + echapperHtml(bouton.dataset.email) + '" /></div>' +
            '<div><label>Telephone</label><input id="mPTel" type="text" minlength="8" maxlength="8" pattern="[2459][0-9]{7}" title="Le telephone doit contenir 8 chiffres et commencer par 2, 4, 5 ou 9." value="' + echapperHtml(bouton.dataset.phone) + '" /></div>' +
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

  // ── Envoi de rappel email aux participants ───────────────────
  function envoyerRappel(eventId, eventTitle) {
    fermerModal('deleteModal');

    var modal = document.createElement('div');
    modal.id = 'deleteModal';
    modal.innerHTML =
      '<div class="confirm-overlay">' +
        '<div class="confirm-box">' +
          '<div class="confirm-icon">📧</div>' +
          '<h3>Envoyer un rappel</h3>' +
          '<p>Envoyer un email de rappel à tous les participants inscrits à <strong>' + echapperHtml(eventTitle) + '</strong> ?</p>' +
          '<div class="confirm-actions">' +
            '<button class="cancel-btn" id="annulerRappel">Annuler</button>' +
            '<button class="primary-btn" id="confirmerRappel">📧 Envoyer</button>' +
          '</div>' +
          '<p id="rappelStatus" style="margin-top:12px;font-size:13px;color:#688273;"></p>' +
        '</div>' +
      '</div>';
    document.body.appendChild(modal);

    document.getElementById('annulerRappel').addEventListener('click', function() { fermerModal('deleteModal'); });

    document.getElementById('confirmerRappel').addEventListener('click', function() {
      var btn    = document.getElementById('confirmerRappel');
      var status = document.getElementById('rappelStatus');
      btn.disabled = true;
      btn.textContent = '⏳ Envoi en cours...';
      status.textContent = '';

      fetch('/nutrismart_evenement/Controller/MailController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ eventId: eventId })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.success) {
          status.style.color = '#16a34a';
          status.textContent = '✅ ' + data.message;
          btn.textContent = '✅ Envoyé';
          setTimeout(function() { fermerModal('deleteModal'); }, 2500);
        } else {
          status.style.color = '#dc2626';
          status.textContent = '❌ ' + (data.error || data.message || 'Erreur inconnue');
          btn.disabled = false;
          btn.textContent = '📧 Réessayer';
        }
      })
      .catch(function() {
        status.style.color = '#dc2626';
        status.textContent = '❌ Erreur réseau. Vérifiez la configuration SMTP.';
        btn.disabled = false;
        btn.textContent = '📧 Réessayer';
      });
    });
  }

  document.getElementById('categoryForm').addEventListener('submit', function(e) {
    var data = {
      name: document.getElementById('categoryName').value.trim(),
      description: document.getElementById('categoryDescription').value.trim()
    };
    if (!controlerCategorie(data)) e.preventDefault();
  });

  document.getElementById('eventForm').addEventListener('submit', function(e) {
    // Synchroniser l'URL manuelle dans le champ hidden avant validation
    var urlInput  = document.getElementById('eventImageUrl');
    var hiddenImg = document.getElementById('eventImage');
    if (urlInput && hiddenImg && urlInput.value.trim() !== '' && hiddenImg.value.trim() === '') {
      hiddenImg.value = urlInput.value.trim();
    }

    var data = {
      title: document.getElementById('eventTitle').value.trim(),
      categoryId: document.getElementById('eventCategory').value,
      date: document.getElementById('eventDate').value,
      time: document.getElementById('eventTime').value,
      location: document.getElementById('eventLocation').value.trim(),
      seats: document.getElementById('eventSeats').value,
      description: document.getElementById('eventDescription').value.trim(),
      image: hiddenImg ? hiddenImg.value.trim() : '',
      googleMapsLink: document.getElementById('eventGoogleMaps') ? document.getElementById('eventGoogleMaps').value.trim() : '',
      latitude:  document.getElementById('eventLatitude')  ? document.getElementById('eventLatitude').value  : '',
      longitude: document.getElementById('eventLongitude') ? document.getElementById('eventLongitude').value : ''
    };
    if (!controlerEvenement(data)) e.preventDefault();
  });

  // ============================================================
  // GENERATEUR IA — utilise l'API Grok (xAI) pour créer une description
  // Si l'API n'est pas disponible, une description locale est générée
  // ============================================================

  /**
   * Génère une description pour un événement.
   * Utilise l'API Grok si une clé est configurée, sinon génère localement.
   *
   * @param {HTMLElement} champTitre    - Input du titre
   * @param {HTMLElement} champCategorie - Select de la catégorie
   * @param {HTMLElement} champLieu     - Input du lieu
   * @param {HTMLElement} champDate     - Input de la date
   * @param {HTMLElement} champDesc     - Textarea de la description
   */
  function genererDescriptionIA(champTitre, champCategorie, champLieu, champDate, champDesc) {
    var titre     = champTitre ? champTitre.value.trim() : '';
    var categorie = champCategorie ? champCategorie.options[champCategorie.selectedIndex].text : '';
    var lieu      = champLieu ? champLieu.value.trim() : '';
    var date      = champDate ? champDate.value : '';

    if (!titre || !lieu) {
      alert('Veuillez remplir le titre et le lieu avant de générer une description.');
      return;
    }

    // Indicateur visuel de chargement
    champDesc.value = '⏳ Génération en cours...';
    champDesc.disabled = true;

    // Appel au proxy PHP Gemini (la clé API reste côté serveur)
    fetch('/nutrismart_evenement/Controller/GeminiController.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        titre:     titre,
        categorie: (categorie !== 'Choisir une categorie') ? categorie : '',
        lieu:      lieu,
        date:      date
      })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
      if (data.description) {
        champDesc.value = data.description;
        // Indiquer visuellement la source
        var source = data.source === 'gemini' ? '✦ Généré par Gemini IA' : '✦ Généré localement';
        var hint = champDesc.parentNode.querySelector('.ia-source-hint');
        if (!hint) {
          hint = document.createElement('span');
          hint.className = 'ia-source-hint';
          champDesc.parentNode.appendChild(hint);
        }
        hint.textContent = source;
        hint.style.color = data.source === 'gemini' ? '#1a73e8' : '#688273';
      } else {
        champDesc.value = genererDescriptionLocale(titre, categorie, lieu);
      }
    })
    .catch(function() {
      champDesc.value = genererDescriptionLocale(titre, categorie, lieu);
    })
    .finally(function() {
      champDesc.disabled = false;
    });
  }

  function genererDescriptionLocale(titre, categorie, lieu) {
    var cat = (categorie && categorie !== 'Choisir une categorie') ? categorie : 'nutrition et bien-être';
    var templates = [
      'Une expérience unique autour de ' + cat + ' vous attend à ' + lieu + ' — ' + titre + ' promet de transformer votre rapport à la santé.',
      'Plongez au cœur de ' + cat + ' à ' + lieu + ' : ' + titre + ' est l\'occasion idéale d\'explorer de nouvelles habitudes alimentaires.',
      'À ' + lieu + ', ' + titre + ' réunit passionnés et experts de ' + cat + ' pour une journée riche en découvertes.',
      'Vivez ' + titre + ' différemment à ' + lieu + ' : cet événement dédié à ' + cat + ' vous offre des outils concrets pour mieux vivre.',
    ];
    // Choisir un template basé sur le titre pour avoir de la variété
    var idx = titre.length % templates.length;
    return templates[idx];
  }

  // Listener du bouton IA dans le formulaire d'ajout
  var btnIA = document.getElementById('genererIaBtn');
  if (btnIA) {
    btnIA.addEventListener('click', function() {
      genererDescriptionIA(
        document.getElementById('eventTitle'),
        document.getElementById('eventCategory'),
        document.getElementById('eventLocation'),
        document.getElementById('eventDate'),
        document.getElementById('eventDescription')
      );
    });
  }

  // ============================================================
  // CARTE LEAFLET — sélecteur de localisation dans le formulaire
  // Utilise OpenStreetMap (gratuit, sans clé API)
  // ============================================================

  var mapPickerInstance = null;   // instance Leaflet du formulaire d'ajout
  var mapPickerMarker   = null;   // marqueur actuel

  /**
   * Initialise la carte Leaflet dans le formulaire d'ajout.
   * Appelé une seule fois au chargement de la page.
   */
  function initMapPicker() {
    var container = document.getElementById('mapPicker');
    if (!container || typeof L === 'undefined') return;

    // Centre par défaut : Tunis (adaptez selon votre pays)
    mapPickerInstance = L.map('mapPicker').setView([36.8065, 10.1815], 7);

    // Tuiles OpenStreetMap — gratuites, sans clé API
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 19
    }).addTo(mapPickerInstance);

    // Clic sur la carte → placer un marqueur et enregistrer les coordonnées
    mapPickerInstance.on('click', function(e) {
      placerMarqueur(mapPickerInstance, e.latlng.lat, e.latlng.lng, 'add');
    });

    // Bouton effacer
    var btnClear = document.getElementById('mapClearBtn');
    if (btnClear) {
      btnClear.addEventListener('click', function() {
        effacerMarqueur('add');
      });
    }
  }

  /**
   * Place un marqueur sur la carte et met à jour les champs cachés.
   * @param {L.Map} map       - Instance Leaflet
   * @param {number} lat      - Latitude
   * @param {number} lng      - Longitude
   * @param {string} context  - 'add' (formulaire) ou 'edit' (modal)
   */
  function placerMarqueur(map, lat, lng, context) {
    if (context === 'add') {
      if (mapPickerMarker) map.removeLayer(mapPickerMarker);
      mapPickerMarker = L.marker([lat, lng]).addTo(map);

      // Mettre à jour les champs cachés
      document.getElementById('eventLatitude').value  = lat.toFixed(7);
      document.getElementById('eventLongitude').value = lng.toFixed(7);

      // Afficher les coordonnées
      var texte = document.getElementById('mapCoordsText');
      if (texte) {
        texte.textContent = 'Position : ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
        texte.classList.add('active');
      }

      // Générer automatiquement le lien Google Maps
      var lienInput = document.getElementById('eventGoogleMaps');
      if (lienInput) {
        lienInput.value = 'https://www.google.com/maps?q=' + lat.toFixed(7) + ',' + lng.toFixed(7);
      }
    } else {
      // Contexte modal de modification
      if (window._editMapMarker) map.removeLayer(window._editMapMarker);
      window._editMapMarker = L.marker([lat, lng]).addTo(map);

      var latInput = document.getElementById('mEvLatitude');
      var lngInput = document.getElementById('mEvLongitude');
      var mapsInput = document.getElementById('mEvGoogleMaps');
      var coordsText = document.getElementById('editMapCoordsText');

      if (latInput)  latInput.value  = lat.toFixed(7);
      if (lngInput)  lngInput.value  = lng.toFixed(7);
      if (mapsInput) mapsInput.value = 'https://www.google.com/maps?q=' + lat.toFixed(7) + ',' + lng.toFixed(7);
      if (coordsText) {
        coordsText.textContent = 'Position : ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
        coordsText.classList.add('active');
      }
    }
  }

  /**
   * Efface le marqueur et réinitialise les champs.
   * @param {string} context - 'add' ou 'edit'
   */
  function effacerMarqueur(context) {
    if (context === 'add') {
      if (mapPickerMarker && mapPickerInstance) {
        mapPickerInstance.removeLayer(mapPickerMarker);
        mapPickerMarker = null;
      }
      document.getElementById('eventLatitude').value  = '';
      document.getElementById('eventLongitude').value = '';
      document.getElementById('eventGoogleMaps').value = '';
      var texte = document.getElementById('mapCoordsText');
      if (texte) { texte.textContent = 'Aucune position sélectionnée'; texte.classList.remove('active'); }
    } else {
      if (window._editMapMarker && window._editMap) {
        window._editMap.removeLayer(window._editMapMarker);
        window._editMapMarker = null;
      }
      var latInput  = document.getElementById('mEvLatitude');
      var lngInput  = document.getElementById('mEvLongitude');
      var mapsInput = document.getElementById('mEvGoogleMaps');
      var coordsText = document.getElementById('editMapCoordsText');
      if (latInput)  latInput.value  = '';
      if (lngInput)  lngInput.value  = '';
      if (mapsInput) mapsInput.value = '';
      if (coordsText) { coordsText.textContent = 'Aucune position sélectionnée'; coordsText.classList.remove('active'); }
    }
  }

  // Initialiser la carte après que Leaflet soit chargé
  // (Leaflet est chargé après ce script, donc on attend DOMContentLoaded)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() { setTimeout(initMapPicker, 100); });
  } else {
    setTimeout(initMapPicker, 100);
  }

  // ============================================================
  // UPLOAD D'IMAGE — bouton moderne avec drag & drop et prévisualisation
  // ============================================================

  function initUploadImage() {
    var zone       = document.getElementById('uploadZone');
    var preview    = document.getElementById('uploadPreview');
    var previewImg = document.getElementById('uploadPreviewImg');
    var fileInput  = document.getElementById('uploadFileInput');
    var hiddenUrl  = document.getElementById('eventImage');
    var urlInput   = document.getElementById('eventImageUrl');
    var btnUpload  = document.getElementById('uploadBtn');
    var btnChange  = document.getElementById('uploadChangeBtn');
    var btnRemove  = document.getElementById('uploadRemoveBtn');
    var progress   = document.getElementById('uploadProgress');
    var progressBar = document.getElementById('uploadProgressBar');

    if (!zone || !fileInput) return;

    // Ouvrir le sélecteur de fichier
    function ouvrirSelecteur() { fileInput.click(); }
    if (btnUpload) btnUpload.addEventListener('click', ouvrirSelecteur);
    if (btnChange) btnChange.addEventListener('click', ouvrirSelecteur);

    // Supprimer l'image
    if (btnRemove) {
      btnRemove.addEventListener('click', function() {
        hiddenUrl.value = '';
        if (urlInput) urlInput.value = '';
        previewImg.src = '';
        preview.classList.add('hidden');
        zone.classList.remove('hidden');
        fileInput.value = '';
      });
    }

    // Drag & drop
    zone.addEventListener('dragover', function(e) {
      e.preventDefault();
      zone.classList.add('drag-over');
    });
    zone.addEventListener('dragleave', function() {
      zone.classList.remove('drag-over');
    });
    zone.addEventListener('drop', function(e) {
      e.preventDefault();
      zone.classList.remove('drag-over');
      var files = e.dataTransfer.files;
      if (files.length > 0) uploadFichier(files[0]);
    });

    // Sélection via input file
    fileInput.addEventListener('change', function() {
      if (fileInput.files.length > 0) uploadFichier(fileInput.files[0]);
    });

    // URL manuelle — synchronise le champ hidden à chaque frappe
    if (urlInput) {
      urlInput.addEventListener('input', function() {
        var url = urlInput.value.trim();
        // Toujours mettre à jour le hidden
        hiddenUrl.value = url;
        // Afficher la prévisualisation si l'URL semble valide
        if (url.match(/^https?:\/\/.+/i)) {
          previewImg.src = url;
          zone.classList.add('hidden');
          preview.classList.remove('hidden');
        } else if (url === '') {
          // URL effacée — revenir à la zone de drop
          previewImg.src = '';
          preview.classList.add('hidden');
          zone.classList.remove('hidden');
        }
      });
    }

    /**
     * Envoie le fichier au serveur via fetch et affiche la progression.
     */
    function uploadFichier(file) {
      // Vérification côté client
      var allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
      if (!allowed.includes(file.type)) {
        alert('Type non autorisé. Utilisez JPG, PNG, WEBP ou GIF.');
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        alert('Fichier trop volumineux (max 5 Mo).');
        return;
      }

      // Prévisualisation locale immédiate
      var reader = new FileReader();
      reader.onload = function(e) {
        previewImg.src = e.target.result;
        zone.classList.add('hidden');
        preview.classList.remove('hidden');
        progress.classList.remove('hidden');
        progressBar.style.width = '0%';
      };
      reader.readAsDataURL(file);

      // Upload via fetch
      var formData = new FormData();
      formData.append('image', file);

      var xhr = new XMLHttpRequest();
      xhr.open('POST', '/nutrismart_evenement/Controller/UploadController.php');

      xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
          var pct = Math.round((e.loaded / e.total) * 100);
          progressBar.style.width = pct + '%';
        }
      });

      xhr.addEventListener('load', function() {
        progress.classList.add('hidden');
        try {
          var resp = JSON.parse(xhr.responseText);
          if (resp.url) {
            hiddenUrl.value = resp.url;
            previewImg.src  = resp.url;
            if (urlInput) urlInput.value = resp.url;
          } else {
            alert('Erreur upload : ' + (resp.error || 'inconnue'));
            // Garder la prévisualisation locale
          }
        } catch(e) {
          alert('Erreur serveur lors de l\'upload.');
        }
      });

      xhr.addEventListener('error', function() {
        progress.classList.add('hidden');
        alert('Erreur réseau lors de l\'upload.');
      });

      xhr.send(formData);
    }
  }

  // Initialiser l'upload au chargement
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initUploadImage);
  } else {
    initUploadImage();
  }

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

  function paramsToUrlWithHash(params) {
    var base = window.location.pathname;
    var query = params.toString();
    var url = base;
    if (query) url += '?' + query;
    return url;
  }

  function tableMeta(tableKey) {
    // map UI tableKey -> param prefix, input id, section anchor, export table name
    if (tableKey === 'categories') {
      return { prefix: 'cat_', inputId: 'catSearchId', anchor: '#categorySection', exportTable: 'categories' };
    }
    if (tableKey === 'events') {
      return { prefix: 'evt_', inputId: 'evtSearchId', anchor: '#eventSection', exportTable: 'events' };
    }
    return { prefix: 'par_', inputId: 'parSearchId', anchor: '#participantSection', exportTable: 'participants' };
  }

  function initBackofficeTableControls() {
    // Tri immédiat sur les en-têtes (reload page)
    document.addEventListener('click', function(e) {
      var th = e.target && e.target.closest ? e.target.closest('th.sortable[data-table][data-sort]') : null;
      if (!th) return;

      var tableKey = th.getAttribute('data-table');
      var sortField = th.getAttribute('data-sort');
      if (!tableKey || !sortField) return;

      var meta = tableMeta(tableKey);
      var params = new URLSearchParams(window.location.search);

      var currentSort = params.get(meta.prefix + 'sort') || 'id';
      var currentDir = (params.get(meta.prefix + 'dir') || 'ASC').toUpperCase();

      var currentSortNorm = String(currentSort).toLowerCase();
      var sortFieldNorm = String(sortField).toLowerCase();

      var nextDir = 'ASC';
      if (currentSortNorm === sortFieldNorm) {
        nextDir = currentDir === 'ASC' ? 'DESC' : 'ASC';
      }

      params.set(meta.prefix + 'sort', sortField);
      params.set(meta.prefix + 'dir', nextDir);

      // Preserve already-applied search id param (meta.prefix + 'id') if present
      var url = paramsToUrlWithHash(params) + meta.anchor;
      window.location.href = url;
    });

    // Recherche par ID + reload page
    var applyButtons = document.querySelectorAll('button.apply-btn[data-apply-table]');
    applyButtons.forEach(function(btn) {
      btn.addEventListener('click', function() {
        var tableKey = btn.getAttribute('data-apply-table');
        if (!tableKey) return;

        var meta = tableMeta(tableKey);
        var params = new URLSearchParams(window.location.search);

        var input = document.getElementById(meta.inputId);
        var rawVal = input ? String(input.value || '').trim() : '';
        if (rawVal !== '') {
          params.set(meta.prefix + 'id', rawVal);
        } else {
          params.delete(meta.prefix + 'id');
        }

        // Ensure sort defaults if missing
        if (!params.get(meta.prefix + 'sort')) params.set(meta.prefix + 'sort', 'id');
        if (!params.get(meta.prefix + 'dir')) params.set(meta.prefix + 'dir', 'ASC');

        var url = paramsToUrlWithHash(params) + meta.anchor;
        window.location.href = url;
      });
    });

    // Export PDF (print)
    var exportButtons = document.querySelectorAll('button.export-pdf-btn[data-export-table]');
    exportButtons.forEach(function(btn) {
      btn.addEventListener('click', function() {
        var tableKey = btn.getAttribute('data-export-table');
        if (!tableKey) return;

        var meta = tableMeta(tableKey);
        var params = new URLSearchParams(window.location.search);

        var exportParams = new URLSearchParams();
        exportParams.set('table', meta.exportTable);

        var idVal = params.get(meta.prefix + 'id');
        if (idVal) exportParams.set(meta.prefix + 'id', idVal);

        exportParams.set(meta.prefix + 'sort', params.get(meta.prefix + 'sort') || 'id');
        exportParams.set(meta.prefix + 'dir', params.get(meta.prefix + 'dir') || 'ASC');

        var url = '/nutrismart_evenement/View/BackOffice/export.php?' + exportParams.toString();
        window.open(url, '_blank');
      });
    });
  }

  window.afficherConfirmSuppression = afficherConfirmSuppression;
  window.afficherModalModifCategorie = afficherModalModifCategorie;
  window.afficherModalModifEvenement = afficherModalModifEvenement;
  window.afficherModalModifParticipant = afficherModalModifParticipant;
  window.supprimerCategorie = supprimerCategorie;
  window.supprimerEvenement = supprimerEvenement;
  window.supprimerParticipant = supprimerParticipant;
  window.envoyerRappel = envoyerRappel;

  var success = lireParametre('success');
  afficherAnimationSucces(success);
  if (success !== '') nettoyerParametreSucces();
  initMenuActif();
  initBackofficeTableControls();
})();
