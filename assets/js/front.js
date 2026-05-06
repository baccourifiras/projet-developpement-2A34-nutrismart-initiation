/* ============================================================
   NutriSmart - Frontoffice JS (extras)
   /assets/js/front.js
   ============================================================ */
(function () {
  'use strict';

  // Apparition au scroll des cards
  var observed = document.querySelectorAll('.recipe-card, .info-feature-card, .empty-state, .detail-hero');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.style.opacity = 1;
          entry.target.style.transform = 'translateY(0)';
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });

    observed.forEach(function (el) {
      el.style.opacity = 0;
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity .55s ease, transform .55s ease';
      io.observe(el);
    });
  }

  // Recherche live (debounce sur les filtres front)
  document.querySelectorAll('input[data-live-search], select[data-live-filter]').forEach(function (el) {
    var form = el.form;
    if (!form) return;
    var timer = null;
    var evt = el.tagName === 'SELECT' ? 'change' : 'input';
    el.addEventListener(evt, function () {
      clearTimeout(timer);
      timer = setTimeout(function () { form.submit(); }, evt === 'change' ? 0 : 350);
    });
  });
})();

/* ============================================================
   Bandeau "nouveautés" - bouton de fermeture
   ============================================================ */
(function () {
  var banner = document.querySelector('.news-banner');
  if (!banner) return;

  // Si déjà fermé pendant cette session, on cache directement
  if (sessionStorage.getItem('newsBannerClosed') === '1') {
    banner.style.display = 'none';
    return;
  }
  var btn = banner.querySelector('.news-close');
  if (btn) {
    btn.addEventListener('click', function () {
      banner.classList.add('hidden');
      sessionStorage.setItem('newsBannerClosed', '1');
      setTimeout(function () { banner.style.display = 'none'; }, 350);
    });
  }
})();

/* ============================================================
   Chatbot - logique du widget
   ============================================================ */
(function () {
  'use strict';

  var toggle      = document.getElementById('chatbot-toggle');
  var panel       = document.getElementById('chatbot-panel');
  var closeBtn    = document.getElementById('chatbot-close');
  var resetBtn    = document.getElementById('chatbot-reset');
  var form        = document.getElementById('chatbot-form');
  var input       = document.getElementById('chatbot-input');
  var sendBtn     = document.getElementById('chatbot-send');
  var messages    = document.getElementById('chatbot-messages');
  var suggestions = document.getElementById('chatbot-suggestions');

  if (!toggle || !panel) return;

  // Détermination du chemin de base (gère /NutriSmart/ ou /NutriSmart/NutriSmart/)
  // On lit depuis l'URL courante : tout ce qui précède "/frontoffice/" ou "/backoffice/"
  function getBaseUrl() {
    var path = window.location.pathname;
    var idx = Math.max(path.indexOf('/frontoffice/'), path.indexOf('/backoffice/'));
    return idx > -1 ? path.substring(0, idx) : '';
  }
  var ENDPOINT_SEND  = getBaseUrl() + '/frontoffice/chatbot_send.php';
  var ENDPOINT_RESET = getBaseUrl() + '/frontoffice/chatbot_reset.php';

  // ---- Ouverture / fermeture ----
  function openPanel() {
    panel.classList.add('is-open');
    panel.setAttribute('aria-hidden', 'false');
    toggle.classList.add('is-open');
    setTimeout(function () { input && input.focus(); }, 350);
  }
  function closePanel() {
    panel.classList.remove('is-open');
    panel.setAttribute('aria-hidden', 'true');
    toggle.classList.remove('is-open');
  }
  toggle.addEventListener('click', function () {
    panel.classList.contains('is-open') ? closePanel() : openPanel();
  });
  closeBtn && closeBtn.addEventListener('click', closePanel);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && panel.classList.contains('is-open')) closePanel();
  });

  // ---- Auto-resize du textarea ----
  function autoResize() {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 100) + 'px';
  }
  input.addEventListener('input', autoResize);

  // ---- Envoi par Enter (Shift+Enter = nouvelle ligne) ----
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      form.dispatchEvent(new Event('submit', { cancelable: true }));
    }
  });

  // ---- Suggestions cliquables ----
  if (suggestions) {
    suggestions.querySelectorAll('.chatbot-suggestion-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        input.value = btn.dataset.text;
        autoResize();
        form.dispatchEvent(new Event('submit', { cancelable: true }));
      });
    });
  }

  // ---- Construction d'un message dans le DOM ----
  function appendMessage(role, text, options) {
    options = options || {};
    var wrap = document.createElement('div');
    wrap.className = 'chatbot-msg chatbot-msg-' + role;
    if (options.error) wrap.className += ' chatbot-msg-error';
    if (options.typing) wrap.className += ' chatbot-msg-typing';

    var avatar = document.createElement('div');
    avatar.className = 'chatbot-msg-avatar';
    avatar.textContent = role === 'user' ? '🙂' : '👨‍🍳';

    var bubble = document.createElement('div');
    bubble.className = 'chatbot-msg-bubble';

    if (options.typing) {
      bubble.innerHTML = '<div class="chatbot-typing-dots"><span></span><span></span><span></span></div>';
    } else {
      bubble.innerHTML = formatText(text);
    }

    wrap.appendChild(avatar);
    wrap.appendChild(bubble);
    messages.appendChild(wrap);

    // Scroll vers le bas
    messages.scrollTop = messages.scrollHeight;
    return wrap;
  }

  // ---- Mise en forme légère de la réponse (markdown simple) ----
  function formatText(text) {
    // Échapper le HTML d'abord (anti-XSS)
    var div = document.createElement('div');
    div.textContent = text || '';
    var safe = div.innerHTML;

    // Markdown léger : **gras**, *italique*, `code`, retours ligne
    safe = safe
      .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
      .replace(/\*([^*]+)\*/g,     '<em>$1</em>')
      .replace(/`([^`]+)`/g,       '<code>$1</code>')
      .replace(/\n/g, '<br>');
    return safe;
  }

  // ---- Soumission du formulaire ----
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var msg = (input.value || '').trim();
    if (!msg) return;

    // On masque les suggestions au premier message
    if (suggestions) suggestions.style.display = 'none';

    // Affiche le message utilisateur
    appendMessage('user', msg);
    input.value = '';
    autoResize();

    // Désactive l'input et affiche "écriture en cours"
    input.disabled = true;
    sendBtn.disabled = true;
    var typingEl = appendMessage('bot', '', { typing: true });

    // Appel AJAX
    fetch(ENDPOINT_SEND, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: msg }),
    })
      .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
      .then(function (resp) {
        typingEl.remove();
        if (resp.ok && resp.data.success) {
          appendMessage('bot', resp.data.reply);
        } else {
          appendMessage('bot', '⚠️ ' + (resp.data.error || 'Une erreur est survenue.'), { error: true });
        }
      })
      .catch(function (err) {
        typingEl.remove();
        appendMessage('bot', '⚠️ Connexion impossible : ' + err.message, { error: true });
      })
      .finally(function () {
        input.disabled = false;
        sendBtn.disabled = false;
        input.focus();
      });
  });

  // ---- Reset de la conversation ----
  resetBtn && resetBtn.addEventListener('click', function () {
    if (!confirm('Démarrer une nouvelle conversation ?')) return;
    fetch(ENDPOINT_RESET, { method: 'POST' })
      .then(function () {
        // Vider l'UI mais garder le message d'accueil
        messages.querySelectorAll('.chatbot-msg').forEach(function (m, i) {
          if (i > 0) m.remove();
        });
        if (suggestions) suggestions.style.display = '';
      });
  });
})();
