/*
   Front Office
   JavaScript centralise les interactions communes et les comportements des pages.
*/

(function () {
  function initNavbar() {
    var navbar = document.getElementById('navbar');
    var links = document.querySelectorAll('.nav-links a');
    if (!navbar || !links.length) return;

    function majNavbarScroll() {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    }

    majNavbarScroll();
    window.addEventListener('scroll', majNavbarScroll);

    var currentPage = window.location.pathname.split('/').pop() || 'index.php';
    for (var i = 0; i < links.length; i++) {
      if (links[i].getAttribute('href') === currentPage) {
        links[i].classList.add('active');
      }

      links[i].addEventListener('click', function () {
        this.style.transform = 'scale(0.93)';
        var self = this;
        setTimeout(function () {
          self.style.transform = '';
        }, 150);
      });
    }
  }

  function appliquerDelaisReveal() {
    var elements = document.querySelectorAll('[data-reveal-delay]');
    for (var i = 0; i < elements.length; i++) {
      elements[i].style.transitionDelay = elements[i].dataset.revealDelay + 'ms';
    }
  }

  function activerAnimations() {
    var elements = document.querySelectorAll('.reveal:not(.observer-ready)');
    if (!elements.length || typeof IntersectionObserver === 'undefined') return;

    var observateur = new IntersectionObserver(function (entrees) {
      for (var i = 0; i < entrees.length; i++) {
        if (entrees[i].isIntersecting) {
          entrees[i].target.classList.add('visible');
          observateur.unobserve(entrees[i].target);
        }
      }
    }, { threshold: 0.15 });

    for (var j = 0; j < elements.length; j++) {
      elements[j].classList.add('observer-ready');
      observateur.observe(elements[j]);
    }
  }

  function initPageEvenements() {
    var categoryList = document.getElementById('categoryList');
    var eventList = document.getElementById('eventList');
    var eventsTitle = document.getElementById('eventsTitle');
    var showAllBtn = document.getElementById('showAllBtn');
    var modal = document.getElementById('registerModal');
    var form = document.getElementById('participantForm');
    var closeModalBtn = document.getElementById('closeModalBtn');

    if (!categoryList || !eventList || !eventsTitle || !showAllBtn || !modal || !form || !closeModalBtn) {
      return;
    }

    var categorieSelectionnee = null;

    function filtrerEvenements() {
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
        eventsTitle.textContent = 'Evenements : ' + nomCategorie;
      } else {
        eventsTitle.textContent = 'Tous les evenements';
      }

      var message = document.getElementById('noFilteredEvents');
      if (!message) {
        message = document.createElement('div');
        message.id = 'noFilteredEvents';
        message.className = 'no-data reveal visible';
        message.textContent = 'Aucun evenement dans cette categorie.';
        eventList.appendChild(message);
      }
      message.style.display = nbVisible === 0 ? '' : 'none';
    }

    function connecterCategories() {
      var categories = document.querySelectorAll('.category-card[data-category-id]');

      for (var i = 0; i < categories.length; i++) {
        categories[i].addEventListener('click', function () {
          categorieSelectionnee = this.dataset.categoryId;

          var cartes = document.querySelectorAll('.category-card');
          for (var j = 0; j < cartes.length; j++) {
            cartes[j].classList.toggle('active', cartes[j] === this);
          }

          filtrerEvenements();
        });

        categories[i].addEventListener('mousemove', function (e) {
          var rect = this.getBoundingClientRect();
          var rotY = ((e.clientX - rect.left - rect.width / 2) / (rect.width / 2)) * 10;
          var rotX = -((e.clientY - rect.top - rect.height / 2) / (rect.height / 2)) * 8;
          this.style.transform = 'translateY(-14px) rotateX(' + rotX + 'deg) rotateY(' + rotY + 'deg) scale(1.03)';
        });

        categories[i].addEventListener('mouseleave', function () {
          this.style.transform = '';
        });
      }
    }

    function ouvrirModal(bouton) {
      document.getElementById('participantEventId').value = bouton.dataset.eventId;
      document.getElementById('selectedEventText').textContent = 'Evenement : ' + bouton.dataset.eventTitle;
      document.getElementById('messageBox').textContent = '';
      form.reset();
      modal.classList.remove('hidden');
    }

    function fermerModal() {
      modal.classList.add('hidden');
    }

    function connecterBoutonsParticipation() {
      var boutons = document.querySelectorAll('[data-event-id]');
      for (var i = 0; i < boutons.length; i++) {
        boutons[i].addEventListener('click', function () {
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
        toast.innerHTML = '<strong>Participation enregistree</strong><span>Votre inscription a ete ajoutee avec succes.</span>';
        document.body.appendChild(toast);
      }

      setTimeout(function () {
        toast.classList.add('hide');
      }, 2200);
      setTimeout(function () {
        if (toast.parentNode) {
          toast.remove();
        }
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

    showAllBtn.addEventListener('click', function () {
      categorieSelectionnee = null;
      var cartes = document.querySelectorAll('.category-card');
      for (var i = 0; i < cartes.length; i++) {
        cartes[i].classList.remove('active');
      }
      filtrerEvenements();
    });

    closeModalBtn.addEventListener('click', fermerModal);
    modal.addEventListener('click', function (e) {
      if (e.target === this) {
        fermerModal();
      }
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (controlerParticipant()) {
        this.submit();
      }
    });

    connecterCategories();
    connecterBoutonsParticipation();
    filtrerEvenements();
    afficherAnimationSucces(lireParametre('success'));
  }

  initNavbar();
  appliquerDelaisReveal();
  initPageEvenements();
  activerAnimations();
})();
