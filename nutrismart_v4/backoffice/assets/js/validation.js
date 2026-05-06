/* =====================================================================
 *  NutriSmart - assets/js/validation.js
 *  Validation cote client SANS attributs HTML5 (required, pattern, ...).
 *  Principe :
 *    - ecoute l'evenement "submit" du formulaire
 *    - appelle e.preventDefault() pour bloquer si invalide
 *    - affiche les messages dans les <span class="field-error">
 * =================================================================== */

(function () {
  'use strict';

  /* ------------------------------------------------------------------
   * Utilitaires
   * ---------------------------------------------------------------- */
  function setError(input, message) {
    var span = document.querySelector('[data-error-for="' + input.name + '"]');
    if (span) { span.textContent = message; }
    input.classList.add('input-invalid');
  }

  function clearError(input) {
    var span = document.querySelector('[data-error-for="' + input.name + '"]');
    if (span) { span.textContent = ''; }
    input.classList.remove('input-invalid');
  }

  function isEmpty(value) {
    return value === null || value === undefined || String(value).trim() === '';
  }

  function isNumber(value) {
    if (isEmpty(value)) { return false; }
    return !isNaN(parseFloat(value)) && isFinite(value);
  }

  /* Verifie le format YYYY-MM-DD ET la validite de la date */
  function isValidDate(value) {
    if (isEmpty(value)) { return false; }
    if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) { return false; }
    var d = new Date(value);
    if (isNaN(d.getTime())) { return false; }
    /* On verifie que la reconstruction matche la saisie
       (evite les dates du type 2026-02-31 qui sont re-interpretees). */
    var parts = value.split('-');
    return d.getUTCFullYear() === parseInt(parts[0], 10)
        && d.getUTCMonth() + 1 === parseInt(parts[1], 10)
        && d.getUTCDate() === parseInt(parts[2], 10);
  }

  /* ------------------------------------------------------------------
   * Regles par nom de champ
   * ---------------------------------------------------------------- */
  var REGLES = {

    /* --- Regime --- */
    type_regime: [
      { test: function (v) { return !isEmpty(v); },
        message: 'Le type de regime est obligatoire.' },
      { test: function (v) { return ['cut', 'bulk', 'equilibre'].indexOf(v) !== -1; },
        message: 'Valeurs acceptees : cut, bulk, equilibre.' }
    ],
    calories_cible: [
      { test: function (v) { return !isEmpty(v); },
        message: 'Les calories cible sont obligatoires.' },
      { test: function (v) { return isNumber(v); },
        message: 'Les calories doivent etre un nombre.' },
      { test: function (v) { return parseFloat(v) > 0; },
        message: 'Les calories doivent etre superieures a 0.' }
    ],
    date_debut: [
      { test: function (v) { return !isEmpty(v); },
        message: 'La date de debut est obligatoire.' },
      { test: function (v) { return isValidDate(v); },
        message: 'Format attendu : YYYY-MM-DD (date valide).' }
    ],
    poids_initial: [
      { test: function (v) { return !isEmpty(v); },
        message: 'Le poids initial est obligatoire.' },
      { test: function (v) { return isNumber(v); },
        message: 'Le poids doit etre un nombre.' },
      { test: function (v) { return parseFloat(v) > 0; },
        message: 'Le poids doit etre superieur a 0.' }
    ],
    duree: [
      { test: function (v) { return !isEmpty(v); },
        message: 'La duree est obligatoire.' },
      { test: function (v) { return isNumber(v); },
        message: 'La duree doit etre un nombre.' },
      { test: function (v) { return parseInt(v, 10) > 0; },
        message: 'La duree doit etre superieure a 0 jour.' }
    ],

    /* --- Suivi_regime --- */
    id_regime: [
      { test: function (v) { return !isEmpty(v); },
        message: 'Vous devez choisir un regime.' }
    ],
    date: [
      { test: function (v) { return !isEmpty(v); },
        message: 'La date est obligatoire.' },
      { test: function (v) { return isValidDate(v); },
        message: 'Format attendu : YYYY-MM-DD (date valide).' }
    ],
    poids: [
      { test: function (v) { return !isEmpty(v); },
        message: 'Le poids est obligatoire.' },
      { test: function (v) { return isNumber(v); },
        message: 'Le poids doit etre un nombre.' },
      { test: function (v) { return parseFloat(v) > 0; },
        message: 'Le poids doit etre superieur a 0.' }
    ],
    calories_consommees: [
      { test: function (v) { return !isEmpty(v); },
        message: 'Les calories consommees sont obligatoires.' },
      { test: function (v) { return isNumber(v); },
        message: 'Les calories doivent etre un nombre.' },
      { test: function (v) { return parseFloat(v) >= 0; },
        message: 'Les calories doivent etre positives.' }
    ],

    /* --- Historique --- */
    recommandation: [
      { test: function (v) { return !isEmpty(v); },
        message: 'La recommandation est obligatoire.' },
      { test: function (v) { return v.trim().length >= 5; },
        message: 'La recommandation doit contenir au moins 5 caracteres.' }
    ]
  };

  /* ------------------------------------------------------------------
   * Validation d'un champ selon ses regles
   * ---------------------------------------------------------------- */
  function validerChamp(input) {
    var regles = REGLES[input.name];
    if (!regles) { return true; }

    clearError(input);
    for (var i = 0; i < regles.length; i++) {
      if (!regles[i].test(input.value)) {
        setError(input, regles[i].message);
        return false;
      }
    }
    return true;
  }

  /* ------------------------------------------------------------------
   * Attache la validation a un formulaire donne
   * ---------------------------------------------------------------- */
  function attacherValidation(form) {
    if (!form) { return; }

    var inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(function (input) {
      input.addEventListener('blur', function () { validerChamp(input); });
    });

    form.addEventListener('submit', function (e) {
      var tousValides = true;

      inputs.forEach(function (input) {
        if (REGLES[input.name]) {
          if (!validerChamp(input)) { tousValides = false; }
        }
      });

      if (!tousValides) {
        e.preventDefault();
        var premierErr = form.querySelector('.input-invalid');
        if (premierErr) { premierErr.focus(); }
      }
    });
  }

  /* ------------------------------------------------------------------
   * Init : attache la validation sur les formulaires des 3 entites
   * ---------------------------------------------------------------- */
  document.addEventListener('DOMContentLoaded', function () {
    attacherValidation(document.getElementById('formRegime'));
    attacherValidation(document.getElementById('formSuivi'));
    attacherValidation(document.getElementById('formHistorique'));
  });
})();
