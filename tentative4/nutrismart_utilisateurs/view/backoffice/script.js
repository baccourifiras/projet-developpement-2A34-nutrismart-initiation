/* ============================================================
   NutriSmart - Backoffice : Gestion Utilisateurs
   ============================================================
   Cours JavaScript Chapitre 3 - Notions appliquees :
     - DOM : document.getElementById(), querySelector()
     - Evenements : addEventListener()
     - submit : soumission formulaire + e.preventDefault()
     - blur   : validation champ par champ (perte de focus)
     - click  : boutons modifier / supprimer
     - Controle de saisie JavaScript (sans HTML5)
   ============================================================ */

var API = 'UtilisateurController.php';


/* ------------------------------------------------------------
   SECTION 1 : CONTROLE DE SAISIE JAVASCRIPT
   Sans HTML5 (required, type="email"... non utilises)
   Cours : validation personnalisee avec JS
   ------------------------------------------------------------ */

/**
 * Valide les donnees du formulaire utilisateur
 * isModif = true => mot de passe optionnel
 */
function validerFormulaire(data, isModif) {
    var erreurs = {};

    // Validation NOM
    if (!data.nom || data.nom.trim() === '') {
        erreurs.nom = 'Le nom est obligatoire.';
    } else if (data.nom.trim().length < 2 || data.nom.trim().length > 50) {
        erreurs.nom = 'Le nom doit avoir entre 2 et 50 caracteres.';
    } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(data.nom.trim())) {
        erreurs.nom = 'Le nom ne doit contenir que des lettres.';
    }

    // Validation PRENOM
    if (!data.prenom || data.prenom.trim() === '') {
        erreurs.prenom = 'Le prenom est obligatoire.';
    } else if (data.prenom.trim().length < 2 || data.prenom.trim().length > 50) {
        erreurs.prenom = 'Le prenom doit avoir entre 2 et 50 caracteres.';
    } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(data.prenom.trim())) {
        erreurs.prenom = 'Le prenom ne doit contenir que des lettres.';
    }

    // Validation EMAIL
    if (!data.email || data.email.trim() === '') {
        erreurs.email = "L'email est obligatoire.";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(data.email.trim())) {
        erreurs.email = "L'adresse email n'est pas valide.";
    }

    // Validation MOT DE PASSE
    if (!isModif) {
        if (!data.mot_de_passe || data.mot_de_passe === '') {
            erreurs.mot_de_passe = 'Le mot de passe est obligatoire.';
        } else if (data.mot_de_passe.length < 8) {
            erreurs.mot_de_passe = 'Minimum 8 caracteres.';
        } else if (!/[A-Z]/.test(data.mot_de_passe)) {
            erreurs.mot_de_passe = 'Au moins une majuscule.';
        } else if (!/[0-9]/.test(data.mot_de_passe)) {
            erreurs.mot_de_passe = 'Au moins un chiffre.';
        }
    } else if (data.mot_de_passe && data.mot_de_passe !== '') {
        if (data.mot_de_passe.length < 8) {
            erreurs.mot_de_passe = 'Minimum 8 caracteres.';
        } else if (!/[A-Z]/.test(data.mot_de_passe)) {
            erreurs.mot_de_passe = 'Au moins une majuscule.';
        } else if (!/[0-9]/.test(data.mot_de_passe)) {
            erreurs.mot_de_passe = 'Au moins un chiffre.';
        }
    }

    // Validation ROLE
    if (!data.role || data.role === '') {
        erreurs.role = 'Veuillez choisir un role.';
    }

    // Validation PROVIDER
    if (!data.provider_login || data.provider_login === '') {
        erreurs.provider_login = 'Veuillez choisir un provider.';
    }

    return { valide: Object.keys(erreurs).length === 0, erreurs: erreurs };
}


/* ------------------------------------------------------------
   SECTION 2 : AFFICHAGE DES ERREURS DANS LES CHAMPS
   DOM : getElementById(), classList
   ------------------------------------------------------------ */

function afficherErreurs(erreurs, prefixe) {
    prefixe = prefixe || '';
    var champs = ['nom', 'prenom', 'email', 'mot_de_passe', 'role', 'provider_login'];

    // On nettoie tous les champs d'abord
    champs.forEach(function(c) {
        var span  = document.getElementById(prefixe + 'err-' + c);
        var input = document.getElementById(prefixe + c);
        if (span)  span.textContent = '';
        if (input) {
            input.classList.remove('invalid');
            input.classList.remove('valid');
        }
    });

    // On affiche les erreurs
    Object.keys(erreurs).forEach(function(champ) {
        var span  = document.getElementById(prefixe + 'err-' + champ);
        var input = document.getElementById(prefixe + champ);
        if (span)  span.textContent = erreurs[champ];
        if (input) {
            input.classList.add('invalid');
            input.classList.remove('valid');
        }
    });

    // On marque les champs valides
    champs.forEach(function(c) {
        if (!erreurs[c]) {
            var input = document.getElementById(prefixe + c);
            if (input && input.value.trim() !== '') {
                input.classList.add('valid');
            }
        }
    });
}

function viderErreurs(prefixe) {
    prefixe = prefixe || '';
    ['nom','prenom','email','mot_de_passe','role','provider_login'].forEach(function(c) {
        var span  = document.getElementById(prefixe + 'err-' + c);
        var input = document.getElementById(prefixe + c);
        if (span)  span.textContent = '';
        if (input) {
            input.classList.remove('invalid');
            input.classList.remove('valid');
        }
    });
}

function afficherMessage(texte, type) {
    var banner = document.getElementById('formErrors');
    if (!banner) return;
    banner.textContent = texte;
    banner.className   = (type === 'succes') ? 'success-banner' : 'error-banner';
    banner.classList.remove('hidden');
    setTimeout(function() { banner.classList.add('hidden'); }, 4000);
}


/* ------------------------------------------------------------
   SECTION 3 : EVENEMENT BLUR - Validation champ par champ
   Cours JS Chapitre 3 :
   "L'evenement Blur se declenche lors de la perte de focus"
   element.addEventListener('blur', function() {...})
   ------------------------------------------------------------ */

function ajouterValidationBlur() {
    var champs = ['nom', 'prenom', 'email', 'mot_de_passe', 'role', 'provider_login'];

    champs.forEach(function(champ) {
        var input = document.getElementById(champ);
        if (!input) return;

        // Cours : element.addEventListener(event, function)
        // event = 'blur' => se declenche quand le champ perd le focus
        input.addEventListener('blur', function() {
            var valeur = this.value.trim();
            var span   = document.getElementById('err-' + champ);
            var erreur = '';

            if (champ === 'nom') {
                if (valeur === '') {
                    erreur = 'Le nom est obligatoire.';
                } else if (valeur.length < 2 || valeur.length > 50) {
                    erreur = 'Entre 2 et 50 caracteres.';
                } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(valeur)) {
                    erreur = 'Lettres uniquement.';
                }
            }

            if (champ === 'prenom') {
                if (valeur === '') {
                    erreur = 'Le prenom est obligatoire.';
                } else if (valeur.length < 2 || valeur.length > 50) {
                    erreur = 'Entre 2 et 50 caracteres.';
                } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(valeur)) {
                    erreur = 'Lettres uniquement.';
                }
            }

            if (champ === 'email') {
                if (valeur === '') {
                    erreur = "L'email est obligatoire.";
                } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valeur)) {
                    erreur = "Email invalide.";
                }
            }

            if (champ === 'mot_de_passe') {
                if (valeur === '') {
                    erreur = 'Le mot de passe est obligatoire.';
                } else if (valeur.length < 8) {
                    erreur = 'Minimum 8 caracteres.';
                } else if (!/[A-Z]/.test(valeur)) {
                    erreur = 'Au moins une majuscule.';
                } else if (!/[0-9]/.test(valeur)) {
                    erreur = 'Au moins un chiffre.';
                }
            }

            if (champ === 'role') {
                if (valeur === '') erreur = 'Choisissez un role.';
            }

            if (champ === 'provider_login') {
                if (valeur === '') erreur = 'Choisissez un provider.';
            }

            // Affichage de l'erreur ou validation visuelle
            if (span) span.textContent = erreur;
            if (erreur) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else if (valeur !== '') {
                this.classList.remove('invalid');
                this.classList.add('valid');
            }
        });
    });
}


/* ------------------------------------------------------------
   SECTION 4 : AFFICHAGE DU TABLEAU DES UTILISATEURS
   DOM : createElement, innerHTML
   ------------------------------------------------------------ */

function badgeId(id) {
    return '<span class="id-badge">' + id + '</span>';
}

function badgeRole(role) {
    return '<span class="role-badge role-' + role + '">' + role + '</span>';
}

function badgeProvider(provider) {
    return '<span class="provider-badge">' + provider + '</span>';
}

function formaterDate(dateStr) {
    if (!dateStr) return '-';
    try {
        return new Intl.DateTimeFormat('fr-FR', {
            year: 'numeric', month: 'long', day: 'numeric'
        }).format(new Date(dateStr));
    } catch(e) { return dateStr; }
}

function escHtml(str) {
    return String(str || '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;')
        .replace(/'/g,'&#39;');
}

function afficherTableau(liste) {
    var conteneur = document.getElementById('userTableContainer');

    if (!liste || liste.length === 0) {
        conteneur.innerHTML = '<p class="note" style="margin-top:16px;">Aucun utilisateur enregistre.</p>';
        return;
    }

    var html = '<div class="table-wrapper" style="margin-top:20px;">'
             + '<table class="table">'
             + '<thead><tr>'
             + '<th>ID</th><th>Nom</th><th>Prenom</th><th>Email</th>'
             + '<th>Role</th><th>Provider</th><th>Date</th><th>Actions</th>'
             + '</tr></thead><tbody>';

    liste.forEach(function(u) {
        html += '<tr>'
             + '<td>' + badgeId(u.id_user) + '</td>'
             + '<td><span class="small-badge">' + escHtml(u.nom) + '</span></td>'
             + '<td>' + escHtml(u.prenom) + '</td>'
             + '<td>' + escHtml(u.email) + '</td>'
             + '<td>' + badgeRole(u.role) + '</td>'
             + '<td>' + badgeProvider(u.provider_login) + '</td>'
             + '<td>' + formaterDate(u.date_inscription) + '</td>'
             + '<td class="action-cell">'
             + '<button class="edit-btn" onclick="afficherModalModif(' + u.id_user + ')">✏️ Modifier</button>'
             + '<button class="delete-btn" onclick="confirmerSuppression(' + u.id_user + ', \'' + escHtml(u.nom + ' ' + u.prenom) + '\')">🗑️ Supprimer</button>'
             + '</td></tr>';
    });

    html += '</tbody></table></div>';
    conteneur.innerHTML = html;
}


/* ------------------------------------------------------------
   SECTION 5 : APPELS FETCH VERS LE CONTROLEUR PHP
   Cours : fetch() remplace la soumission classique du formulaire
   ------------------------------------------------------------ */

function chargerUtilisateurs() {
    var fd = new FormData();
    fd.append('action', 'liste');

    fetch(API, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.succes) {
                afficherTableau(data.utilisateurs);
            } else {
                document.getElementById('userTableContainer').innerHTML =
                    '<p class="note" style="color:#dc2626;margin-top:16px;">Erreur : ' + (data.message || '') + '</p>';
            }
        })
        .catch(function() {
            document.getElementById('userTableContainer').innerHTML =
                '<p class="note" style="color:#dc2626;margin-top:16px;">Serveur inaccessible. Verifiez que XAMPP est demarre.</p>';
        });
}

function chargerStats() {
    var fd = new FormData();
    fd.append('action', 'stats');

    fetch(API, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.succes) {
                document.getElementById('totalCount').textContent  = data.total;
                document.getElementById('adminCount').textContent  = data.parRole.admin;
                document.getElementById('nutriCount').textContent  = data.parRole.nutritionniste;
                document.getElementById('clientCount').textContent = data.parRole.client;
            }
        });
}

function toutRafraichir() {
    chargerUtilisateurs();
    chargerStats();
}


/* ------------------------------------------------------------
   SECTION 6 : FORMULAIRE D'AJOUT
   Cours JS : element.addEventListener('submit', function(e) {
                 e.preventDefault();  // empeche l'envoi HTML
              })
   ------------------------------------------------------------ */

// Cours : document.getElementById() = Manipuler le DOM
var form = document.getElementById('userForm');

form.addEventListener('submit', function(e) {
    // Cours : e.preventDefault() empeche la soumission HTML
    e.preventDefault();

    var data = {
        nom:            document.getElementById('nom').value,
        prenom:         document.getElementById('prenom').value,
        email:          document.getElementById('email').value,
        mot_de_passe:   document.getElementById('mot_de_passe').value,
        role:           document.getElementById('role').value,
        provider_login: document.getElementById('provider_login').value
    };

    // Validation JavaScript complete (sans HTML5)
    var result = validerFormulaire(data, false);
    afficherErreurs(result.erreurs);

    if (!result.valide) {
        afficherMessage('Veuillez corriger les erreurs.', 'erreur');
        return;
    }

    // Envoi vers le controleur PHP via fetch
    var msg = document.getElementById('loadingMsg');
    msg.classList.remove('hidden');

    var fd = new FormData();
    fd.append('action', 'ajouter');
    Object.keys(data).forEach(function(k) { fd.append(k, data[k]); });

    fetch(API, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            msg.classList.add('hidden');
            if (resp.succes) {
                form.reset();
                viderErreurs();
                afficherMessage('✅ ' + resp.message, 'succes');
                toutRafraichir();
            } else if (resp.erreurs) {
                afficherErreurs(resp.erreurs);
                afficherMessage('Veuillez corriger les erreurs.', 'erreur');
            } else {
                afficherMessage('❌ ' + (resp.message || 'Erreur.'), 'erreur');
            }
        })
        .catch(function() {
            msg.classList.add('hidden');
            afficherMessage('❌ Serveur inaccessible.', 'erreur');
        });
});


/* ------------------------------------------------------------
   SECTION 7 : MODAL DE MODIFICATION
   Cours JS : addEventListener('click', function() {...})
   ------------------------------------------------------------ */

function afficherModalModif(id) {
    var fd = new FormData();
    fd.append('action', 'liste');

    fetch(API, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.succes) return;
            var u = data.utilisateurs.find(function(x) {
                return Number(x.id_user) === Number(id);
            });
            if (!u) return;

            // Supprimer l'ancien modal si existant
            var ancien = document.getElementById('editModal');
            if (ancien) ancien.remove();

            // Creer le modal
            var modal = document.createElement('div');
            modal.id = 'editModal';
            modal.innerHTML =
                '<div class="confirm-overlay">'
              +   '<div class="confirm-box edit-box">'
              +     '<h3>✏️ Modifier l\'utilisateur</h3>'
              +     '<div id="merreurs" class="error-banner hidden"></div>'
              +     '<div class="form-grid two-columns">'
              +       '<div><label>Nom <span class="required-star">*</span></label>'
              +       '<input id="m-nom" type="text" value="' + escHtml(u.nom) + '" />'
              +       '<span class="field-error" id="m-err-nom"></span></div>'
              +       '<div><label>Prenom <span class="required-star">*</span></label>'
              +       '<input id="m-prenom" type="text" value="' + escHtml(u.prenom) + '" />'
              +       '<span class="field-error" id="m-err-prenom"></span></div>'
              +       '<div><label>Email <span class="required-star">*</span></label>'
              +       '<input id="m-email" type="text" value="' + escHtml(u.email) + '" />'
              +       '<span class="field-error" id="m-err-email"></span></div>'
              +       '<div><label>Nouveau mot de passe <small style="color:#688273;">(laisser vide = inchange)</small></label>'
              +       '<div class="password-wrapper">'
              +       '<input id="m-mot_de_passe" type="password" placeholder="Min. 8 car., 1 maj., 1 chiffre" />'
              +       '<button type="button" class="toggle-pwd" onclick="togglePwd(\'m-mot_de_passe\', this)">👁</button>'
              +       '</div>'
              +       '<span class="field-error" id="m-err-mot_de_passe"></span></div>'
              +       '<div><label>Role <span class="required-star">*</span></label>'
              +       '<select id="m-role">'
              +         '<option value="admin"'          + (u.role === 'admin'          ? ' selected' : '') + '>Admin</option>'
              +         '<option value="nutritionniste"' + (u.role === 'nutritionniste' ? ' selected' : '') + '>Nutritionniste</option>'
              +         '<option value="client"'         + (u.role === 'client'         ? ' selected' : '') + '>Client</option>'
              +       '</select>'
              +       '<span class="field-error" id="m-err-role"></span></div>'
              +       '<div><label>Provider <span class="required-star">*</span></label>'
              +       '<select id="m-provider_login">'
              +         '<option value="local"'    + (u.provider_login === 'local'    ? ' selected' : '') + '>Local</option>'
              +         '<option value="google"'   + (u.provider_login === 'google'   ? ' selected' : '') + '>Google</option>'
              +         '<option value="facebook"' + (u.provider_login === 'facebook' ? ' selected' : '') + '>Facebook</option>'
              +       '</select>'
              +       '<span class="field-error" id="m-err-provider_login"></span></div>'
              +     '</div>'
              +     '<div class="confirm-actions">'
              +       '<button class="cancel-btn" id="mAnnuler">Annuler</button>'
              +       '<button class="primary-btn" id="mSauvegarder">💾 Enregistrer</button>'
              +     '</div>'
              +   '</div>'
              + '</div>';

            document.body.appendChild(modal);

            // Cours : addEventListener('click') pour fermer
            document.getElementById('mAnnuler').addEventListener('click', function() {
                modal.remove();
            });

            modal.querySelector('.confirm-overlay').addEventListener('click', function(e) {
                if (e.target === modal.querySelector('.confirm-overlay')) modal.remove();
            });

            // Cours : addEventListener('click') pour enregistrer
            document.getElementById('mSauvegarder').addEventListener('click', function() {
                var mData = {
                    nom:            document.getElementById('m-nom').value,
                    prenom:         document.getElementById('m-prenom').value,
                    email:          document.getElementById('m-email').value,
                    mot_de_passe:   document.getElementById('m-mot_de_passe').value,
                    role:           document.getElementById('m-role').value,
                    provider_login: document.getElementById('m-provider_login').value
                };

                var res = validerFormulaire(mData, true);
                afficherErreurs(res.erreurs, 'm-');

                if (!res.valide) {
                    var mb = document.getElementById('merreurs');
                    mb.textContent = 'Veuillez corriger les erreurs.';
                    mb.className   = 'error-banner';
                    return;
                }

                var fd2 = new FormData();
                fd2.append('action', 'modifier');
                fd2.append('id_user', id);
                Object.keys(mData).forEach(function(k) { fd2.append(k, mData[k]); });

                fetch(API, { method: 'POST', body: fd2 })
                    .then(function(r) { return r.json(); })
                    .then(function(resp) {
                        if (resp.succes) {
                            modal.remove();
                            afficherMessage('✅ ' + resp.message, 'succes');
                            toutRafraichir();
                        } else if (resp.erreurs) {
                            afficherErreurs(resp.erreurs, 'm-');
                        } else {
                            var mb = document.getElementById('merreurs');
                            mb.textContent = '❌ ' + (resp.message || 'Erreur.');
                            mb.className   = 'error-banner';
                        }
                    });
            });
        });
}


/* ------------------------------------------------------------
   SECTION 8 : MODAL DE CONFIRMATION SUPPRESSION
   Cours JS : addEventListener('click')
   ------------------------------------------------------------ */

function confirmerSuppression(id, nomComplet) {
    var ancien = document.getElementById('deleteModal');
    if (ancien) ancien.remove();

    var modal = document.createElement('div');
    modal.id = 'deleteModal';
    modal.innerHTML =
        '<div class="confirm-overlay">'
      +   '<div class="confirm-box">'
      +     '<div class="confirm-icon">🗑️</div>'
      +     '<h3>Confirmer la suppression</h3>'
      +     '<p>Supprimer <strong>' + escHtml(nomComplet) + '</strong> ? Cette action est irreversible.</p>'
      +     '<div class="confirm-actions">'
      +       '<button class="cancel-btn" id="annulerSuppr">Annuler</button>'
      +       '<button class="danger-btn" id="confirmerSuppr" style="width:auto;">Supprimer</button>'
      +     '</div>'
      +   '</div>'
      + '</div>';

    document.body.appendChild(modal);

    document.getElementById('annulerSuppr').addEventListener('click', function() {
        modal.remove();
    });

    modal.querySelector('.confirm-overlay').addEventListener('click', function(e) {
        if (e.target === modal.querySelector('.confirm-overlay')) modal.remove();
    });

    // Cours : addEventListener('click') sur le bouton supprimer
    document.getElementById('confirmerSuppr').addEventListener('click', function() {
        var fd = new FormData();
        fd.append('action', 'supprimer');
        fd.append('id_user', id);

        fetch(API, { method: 'POST', body: fd })
            .then(function(r) { return r.json(); })
            .then(function(resp) {
                modal.remove();
                if (resp.succes) {
                    afficherMessage('✅ ' + resp.message, 'succes');
                } else {
                    afficherMessage('❌ ' + (resp.message || 'Erreur.'), 'erreur');
                }
                toutRafraichir();
            });
    });
}


/* ------------------------------------------------------------
   SECTION 9 : UTILITAIRES
   ------------------------------------------------------------ */

// Basculer visibilite du mot de passe
function togglePwd(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type      = 'text';
        btn.textContent = '🙈';
    } else {
        input.type      = 'password';
        btn.textContent = '👁';
    }
}

// Menu actif au scroll
// Cours JS : IntersectionObserver + addEventListener
function initMenuActif() {
    var liens    = Array.from(document.querySelectorAll('.menu a[href^="#"]'));
    var sections = liens.map(function(l) {
        return document.querySelector(l.getAttribute('href'));
    }).filter(Boolean);

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
   DEMARRAGE
   Cours JS : evenement 'load' = chargement de la page termine
   window.addEventListener('load', function() {...})
   ------------------------------------------------------------ */
window.addEventListener('load', function() {
    // Charger les donnees au chargement
    toutRafraichir();
    initMenuActif();

    // Activer la validation blur sur chaque champ
    // Cours : "blur = declenche lors de la perte de focus"
    ajouterValidationBlur();
});
