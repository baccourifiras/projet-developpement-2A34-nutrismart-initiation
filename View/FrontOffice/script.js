/*
   Front Office
   JavaScript garde seulement les interactions de la page :
   filtre, modal, controle de saisie et animations.
*/

(function () {
  var categorieSelectionnee = null;

  function activerAnimations() {
    var elements = document.querySelectorAll('.reveal:not(.observer-ready)');
    var observateur = new IntersectionObserver(function(entrees) {
      for (var i = 0; i < entrees.length; i++) {
        if (entrees[i].isIntersecting) {
          entrees[i].target.classList.add('visible');
          observateur.unobserve(entrees[i].target);
        }
      }
    }, { threshold: 0.15 });

    for (var i = 0; i < elements.length; i++) {
      elements[i].classList.add('observer-ready');
      observateur.observe(elements[i]);
    }
  }

  function filtrerEvenements() {
    var titre = document.getElementById('eventsTitle');
    var cartes = document.querySelectorAll('.event-card');
    var nbVisible = 0;

    for (var i = 0; i < cartes.length; i++) {
      var visible = !categorieSelectionnee || cartes[i].dataset.categoryId === categorieSelectionnee;
      cartes[i].style.display = visible ? '' : 'none';
      if (visible) nbVisible++;
    }

    if (categorieSelectionnee) {
      var categorie = document.querySelector('.category-card[data-category-id="' + categorieSelectionnee + '"]');
      var nomCategorie = categorie ? categorie.dataset.categoryName : '';
      titre.textContent = 'Evenements : ' + nomCategorie;
    } else {
      titre.textContent = 'Tous les evenements';
    }

    var message = document.getElementById('noFilteredEvents');
    if (!message) {
      message = document.createElement('div');
      message.id = 'noFilteredEvents';
      message.className = 'no-data reveal visible';
      message.textContent = 'Aucun evenement dans cette categorie.';
      document.getElementById('eventList').appendChild(message);
    }
    message.style.display = nbVisible === 0 ? '' : 'none';
  }

  function connecterCategories() {
    var categories = document.querySelectorAll('.category-card[data-category-id]');

    for (var i = 0; i < categories.length; i++) {
      categories[i].addEventListener('click', function() {
        categorieSelectionnee = this.dataset.categoryId;

        var cartes = document.querySelectorAll('.category-card');
        for (var j = 0; j < cartes.length; j++) {
          cartes[j].classList.toggle('active', cartes[j] === this);
        }

        filtrerEvenements();
      });

      categories[i].addEventListener('mousemove', function(e) {
        var rect = this.getBoundingClientRect();
        var rotY = ((e.clientX - rect.left - rect.width / 2) / (rect.width / 2)) * 10;
        var rotX = -((e.clientY - rect.top - rect.height / 2) / (rect.height / 2)) * 8;
        this.style.transform = 'translateY(-14px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg) scale(1.03)';
      });

      categories[i].addEventListener('mouseleave', function() {
        this.style.transform = '';
      });
    }
  }

  function ouvrirModal(bouton) {
    document.getElementById('participantForm').reset();
    document.getElementById('participantEventId').value = bouton.dataset.eventId;
    document.getElementById('selectedEventText').textContent = 'Evenement : ' + bouton.dataset.eventTitle;
    document.getElementById('messageBox').textContent = '';
    document.getElementById('registerModal').classList.remove('hidden');
  }

  function fermerModal() {
    document.getElementById('registerModal').classList.add('hidden');
  }

  function connecterBoutonsParticipation() {
    var boutons = document.querySelectorAll('[data-event-id]');

    for (var i = 0; i < boutons.length; i++) {
      boutons[i].addEventListener('click', function() {
        ouvrirModal(this);
      });
    }
  }

  function afficherErreur(message) {
    document.getElementById('messageBox').textContent = message;
  }

  function afficherAnimationSucces(type) {
    if (type !== 'add') return;

    var toast = document.getElementById('frontSuccessToast');
    if (!toast) {
      toast = document.createElement('div');
      toast.className = 'front-success-toast';
      toast.innerHTML =
        '<strong>Participation enregistree</strong>' +
        '<span>Votre inscription a ete ajoutee avec succes.</span>';
      document.body.appendChild(toast);
    }

    setTimeout(function() {
      toast.classList.add('hide');
    }, 2200);
    setTimeout(function() {
      if (toast.parentNode) toast.remove();
    }, 2700);
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

  function controlerParticipant() {
    var nom = document.getElementById('fullName').value.trim();
    var email = document.getElementById('email').value.trim();
    var phone = document.getElementById('phone').value.trim();
    var eventId = document.getElementById('participantEventId').value;
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    var phoneRegex = /^[2459][0-9]{7}$/;

    if (nom.length < 3) {
      afficherErreur('Le nom complet doit contenir au moins 3 caracteres.');
      return false;
    }
    if (!emailRegex.test(email)) {
      afficherErreur('Veuillez saisir une adresse email valide.');
      return false;
    }
    if (!phoneRegex.test(phone)) {
      afficherErreur('Le telephone doit contenir 8 chiffres et commencer par 2, 4, 5 ou 9.');
      return false;
    }
    if (!eventId) {
      afficherErreur('Veuillez choisir un evenement.');
      return false;
    }

    afficherErreur('');
    return true;
  }

  document.getElementById('showAllBtn').addEventListener('click', function() {
    categorieSelectionnee = null;
    var cartes = document.querySelectorAll('.category-card');
    for (var i = 0; i < cartes.length; i++) {
      cartes[i].classList.remove('active');
    }
    filtrerEvenements();
  });

  document.getElementById('closeModalBtn').addEventListener('click', fermerModal);
  document.getElementById('registerModal').addEventListener('click', function(e) {
    if (e.target === this) fermerModal();
  });

  document.getElementById('participantForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (controlerParticipant()) this.submit();
  });

  connecterCategories();
  connecterBoutonsParticipation();
  filtrerEvenements();
  afficherAnimationSucces(lireParametre('success'));
  activerAnimations();
})();
