/* ============================================================
   NutriSmart - Backoffice JS
   /assets/js/back.js

   Couvre :
     1. Validation custom des formulaires (côté client)
     2. Confirmation de suppression
     3. Builder dynamique d'associations recette<->ingrédient
     4. Recherche live (debounce + soumission auto du form)
     5. Tri cliquable sur les colonnes des tables
   ============================================================ */

(function () {
  'use strict';

  // ============================================================
  // 1. VALIDATION CUSTOM DES FORMULAIRES
  //    Règles déclaratives via attributs data-* sur les inputs.
  //    Exemples :
  //      data-rule-required="true"
  //      data-rule-min-length="3"
  //      data-rule-max-length="150"
  //      data-rule-numeric="true"
  //      data-rule-min="1"
  //      data-rule-message="Texte personnalisé"
  // ============================================================

  function validateField(field) {
    var v = (field.value || '').trim();
    var msg = '';

    if (field.dataset.ruleRequired === 'true' && v === '') {
      msg = field.dataset.ruleRequiredMessage || 'Ce champ est obligatoire.';
    }
    else if (field.dataset.ruleMinLength && v.length > 0 && v.length < parseInt(field.dataset.ruleMinLength, 10)) {
      msg = 'Au moins ' + field.dataset.ruleMinLength + ' caractères requis.';
    }
    else if (field.dataset.ruleMaxLength && v.length > parseInt(field.dataset.ruleMaxLength, 10)) {
      msg = 'Maximum ' + field.dataset.ruleMaxLength + ' caractères.';
    }
    else if (field.dataset.ruleNumeric === 'true' && v !== '' && isNaN(parseFloat(v))) {
      msg = 'Valeur numérique attendue.';
    }
    else if (field.dataset.ruleMin && v !== '' && parseFloat(v) < parseFloat(field.dataset.ruleMin)) {
      msg = 'Valeur minimale : ' + field.dataset.ruleMin + '.';
    }

    setFieldError(field, msg);
    return msg === '';
  }

  function setFieldError(field, message) {
    var holder = field.parentElement;
    var existing = holder.querySelector('.field-error');
    if (existing) existing.remove();
    field.classList.toggle('input-invalid', message !== '');
    if (message !== '') {
      var span = document.createElement('span');
      span.className = 'field-error';
      span.textContent = message;
      holder.appendChild(span);
    }
  }

  function attachValidation(form) {
    var fields = form.querySelectorAll('[data-rule-required], [data-rule-min-length], [data-rule-max-length], [data-rule-numeric], [data-rule-min]');

    fields.forEach(function (f) {
      f.addEventListener('blur',  function () { validateField(f); });
      f.addEventListener('input', function () {
        if (f.classList.contains('input-invalid')) validateField(f);
      });
    });

    form.addEventListener('submit', function (e) {
      var allOk = true;
      fields.forEach(function (f) { if (!validateField(f)) allOk = false; });

      // Validation custom : au moins 1 ingrédient si bloc présent
      var assoc = form.querySelector('.assoc-builder');
      if (assoc && assoc.querySelectorAll('.assoc-row').length === 0) {
        allOk = false;
        var hint = assoc.querySelector('.assoc-empty-hint');
        if (!hint) {
          hint = document.createElement('div');
          hint.className = 'field-error assoc-empty-hint';
          hint.textContent = 'Ajoutez au moins un ingrédient.';
          assoc.appendChild(hint);
        }
      }
      if (!allOk) {
        e.preventDefault();
        var firstError = form.querySelector('.input-invalid, .assoc-empty-hint');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }

  document.querySelectorAll('form[data-validate]').forEach(attachValidation);

  // ============================================================
  // 2. CONFIRMATION DE SUPPRESSION
  // ============================================================
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      var msg = form.dataset.confirm || 'Confirmer la suppression ?';
      if (!window.confirm(msg)) e.preventDefault();
    });
  });

  // ============================================================
  // 3. BUILDER DYNAMIQUE INGREDIENTS <-> RECETTE
  //    Le serveur fournit un <template id="assoc-row-template"> et
  //    les options d'ingrédients dans un <select> caché.
  // ============================================================
  var builder = document.querySelector('.assoc-builder');
  if (builder) {
    var template = document.getElementById('assoc-row-template');
    var addBtn   = builder.querySelector('.add-row');

    function bindRow(row) {
      var rm = row.querySelector('.remove-row');
      if (rm) {
        rm.addEventListener('click', function () {
          row.remove();
          var hint = builder.querySelector('.assoc-empty-hint');
          if (hint) hint.remove();
        });
      }
      // Quand on choisit un ingrédient, suggérer son unité par défaut
      var sel = row.querySelector('.ing-select');
      var uniteInput = row.querySelector('.ing-unite');
      if (sel && uniteInput) {
        sel.addEventListener('change', function () {
          var opt = sel.options[sel.selectedIndex];
          if (opt && opt.dataset.unite) uniteInput.value = opt.dataset.unite;
        });
      }
    }

    if (addBtn && template) {
      addBtn.addEventListener('click', function () {
        var clone = template.content.firstElementChild.cloneNode(true);
        builder.insertBefore(clone, addBtn);
        bindRow(clone);
        var hint = builder.querySelector('.assoc-empty-hint');
        if (hint) hint.remove();
      });
    }
    builder.querySelectorAll('.assoc-row').forEach(bindRow);
  }

  // ============================================================
  // 4. RECHERCHE LIVE (auto-submit du formulaire avec debounce)
  // ============================================================
  document.querySelectorAll('input[data-live-search]').forEach(function (input) {
    var form = input.form;
    if (!form) return;
    var timer = null;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { form.submit(); }, 350);
    });
  });

  // ============================================================
  // 5. TRI CLIQUABLE SUR LES COLONNES (modifie sort/dir dans l'URL)
  // ============================================================
  document.querySelectorAll('th.sortable').forEach(function (th) {
    th.addEventListener('click', function () {
      var key = th.dataset.sort;
      if (!key) return;
      var url = new URL(window.location.href);
      var currentSort = url.searchParams.get('sort');
      var currentDir  = url.searchParams.get('dir') || 'asc';
      url.searchParams.set('sort', key);
      url.searchParams.set('dir',  (currentSort === key && currentDir === 'asc') ? 'desc' : 'asc');
      url.searchParams.delete('page');
      window.location.href = url.toString();
    });
  });
})();

/* ============================================================
   Planning de menus - modal d'assignation
   ============================================================ */
(function () {
  var modal = document.getElementById('planning-modal');
  if (!modal) return;

  var titleEl = document.getElementById('planning-modal-title');
  var dateInp = document.getElementById('planning-date');
  var momInp  = document.getElementById('planning-moment');
  var recSel  = document.getElementById('planning-recette');
  var nbInp   = document.getElementById('planning-nb');
  var notesInp = document.getElementById('planning-notes');

  function openFor(cell) {
    dateInp.value = cell.dataset.date;
    momInp.value  = cell.dataset.moment;
    titleEl.textContent = '🍽 ' + cell.dataset.momentLabel + ' — ' + cell.dataset.jourLabel;
    // Reset valeurs
    if (recSel) recSel.selectedIndex = 0;
    if (nbInp)  nbInp.value  = 2;
    if (notesInp) notesInp.value = '';
    modal.style.display = 'grid';
    setTimeout(function () { if (recSel) recSel.focus(); }, 100);
  }

  function close() { modal.style.display = 'none'; }

  // Clic sur cellule vide
  document.querySelectorAll('.planning-cell-empty .planning-cell-add').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var cell = btn.closest('.planning-cell');
      if (cell) openFor(cell);
    });
  });

  // Clic sur bouton "modifier" d'une carte
  document.querySelectorAll('.planning-edit-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var cell = btn.closest('.planning-cell');
      if (cell) openFor(cell);
    });
  });

  // Fermeture
  modal.querySelector('.planning-modal-close').addEventListener('click', close);
  modal.querySelector('.planning-modal-backdrop').addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.style.display === 'grid') close();
  });
})();
