/* ============================================================
   NutriSmart - Front Office : Gestion Utilisateurs
   ============================================================
   Cours JavaScript Chapitre 3 - Notions appliquees :
     - DOM : document.getElementById(), querySelector()
     - Evenements : addEventListener()
     - submit + e.preventDefault()
     - blur : validation au moment de quitter chaque champ
     - Controle de saisie JS (sans attributs HTML5)
   ============================================================ */

var API = 'UtilisateurController.php';


/* ------------------------------------------------------------
   SECTION 1 : CONTROLE DE SAISIE JAVASCRIPT
   Cours : validation personnalisee sans HTML5
   ------------------------------------------------------------ */

function validerInscription(data) {
    var erreurs = {};

    // NOM
    if (!data.nom || data.nom.trim() === '') {
        erreurs.nom = 'Le nom est obligatoire.';
    } else if (data.nom.trim().length < 2 || data.nom.trim().length > 50) {
        erreurs.nom = 'Le nom doit avoir entre 2 et 50 caracteres.';
    } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(data.nom.trim())) {
        erreurs.nom = 'Le nom ne doit contenir que des lettres.';
    }

    // PRENOM
    if (!data.prenom || data.prenom.trim() === '') {
        erreurs.prenom = 'Le prenom est obligatoire.';
    } else if (data.prenom.trim().length < 2 || data.prenom.trim().length > 50) {
        erreurs.prenom = 'Le prenom doit avoir entre 2 et 50 caracteres.';
    } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(data.prenom.trim())) {
        erreurs.prenom = 'Le prenom ne doit contenir que des lettres.';
    }

    // EMAIL
    if (!data.email || data.email.trim() === '') {
        erreurs.email = "L'email est obligatoire.";
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(data.email.trim())) {
        erreurs.email = "L'adresse email n'est pas valide.";
    }

    // MOT DE PASSE
    if (!data.mot_de_passe || data.mot_de_passe === '') {
        erreurs.mot_de_passe = 'Le mot de passe est obligatoire.';
    } else if (data.mot_de_passe.length < 8) {
        erreurs.mot_de_passe = 'Minimum 8 caracteres.';
    } else if (!/[A-Z]/.test(data.mot_de_passe)) {
        erreurs.mot_de_passe = 'Au moins une majuscule.';
    } else if (!/[0-9]/.test(data.mot_de_passe)) {
        erreurs.mot_de_passe = 'Au moins un chiffre.';
    }

    // ROLE
    if (!data.role || data.role === '') {
        erreurs.role = 'Veuillez choisir un role.';
    }

    // PROVIDER
    if (!data.provider_login || data.provider_login === '') {
        erreurs.provider_login = 'Veuillez choisir un provider.';
    }

    return { valide: Object.keys(erreurs).length === 0, erreurs: erreurs };
}


/* ------------------------------------------------------------
   SECTION 2 : AFFICHAGE DES ERREURS
   DOM : getElementById, classList
   ------------------------------------------------------------ */

var champs = ['nom','prenom','email','mot_de_passe','role','provider_login'];

function afficherErreursR(erreurs) {
    champs.forEach(function(c) {
        var span  = document.getElementById('r-err-' + c);
        var input = document.getElementById('r-' + c);
        if (span)  span.textContent = erreurs[c] || '';
        if (input) {
            input.classList.remove('invalid', 'valid');
            if (erreurs[c]) {
                input.classList.add('invalid');
            } else if (input.value.trim() !== '') {
                input.classList.add('valid');
            }
        }
    });
}

function viderErreursR() {
    champs.forEach(function(c) {
        var span  = document.getElementById('r-err-' + c);
        var input = document.getElementById('r-' + c);
        if (span)  span.textContent = '';
        if (input) input.classList.remove('invalid', 'valid');
    });
}

function afficherMsgR(texte, type) {
    var box = document.getElementById('registerMsg');
    box.textContent = texte;
    box.className   = 'msg-box ' + type;
    box.classList.remove('hidden');
    setTimeout(function() { box.classList.add('hidden'); }, 5000);
}


/* ------------------------------------------------------------
   SECTION 3 : EVENEMENT BLUR - Validation champ par champ
   Cours JS : "L'evenement Blur se declenche lors de la perte
               de focus d'un element"
   ------------------------------------------------------------ */

function ajouterValidationBlurFront() {
    var definitionsChamps = [
        { id: 'r-nom',            cle: 'nom' },
        { id: 'r-prenom',         cle: 'prenom' },
        { id: 'r-email',          cle: 'email' },
        { id: 'r-mot_de_passe',   cle: 'mot_de_passe' },
        { id: 'r-role',           cle: 'role' },
        { id: 'r-provider_login', cle: 'provider_login' }
    ];

    definitionsChamps.forEach(function(def) {
        var input = document.getElementById(def.id);
        if (!input) return;

        // Cours : element.addEventListener(event, function)
        // 'blur' = evenement de perte de focus
        input.addEventListener('blur', function() {
            var valeur = this.value.trim();
            var span   = document.getElementById('r-err-' + def.cle);
            var erreur = '';

            switch (def.cle) {
                case 'nom':
                    if (valeur === '') erreur = 'Le nom est obligatoire.';
                    else if (valeur.length < 2 || valeur.length > 50) erreur = 'Entre 2 et 50 caracteres.';
                    else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(valeur)) erreur = 'Lettres uniquement.';
                    break;

                case 'prenom':
                    if (valeur === '') erreur = 'Le prenom est obligatoire.';
                    else if (valeur.length < 2 || valeur.length > 50) erreur = 'Entre 2 et 50 caracteres.';
                    else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(valeur)) erreur = 'Lettres uniquement.';
                    break;

                case 'email':
                    if (valeur === '') erreur = "L'email est obligatoire.";
                    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(valeur)) erreur = "Email invalide.";
                    break;

                case 'mot_de_passe':
                    if (valeur === '') erreur = 'Le mot de passe est obligatoire.';
                    else if (valeur.length < 8) erreur = 'Minimum 8 caracteres.';
                    else if (!/[A-Z]/.test(valeur)) erreur = 'Au moins une majuscule.';
                    else if (!/[0-9]/.test(valeur)) erreur = 'Au moins un chiffre.';
                    break;

                case 'role':
                    if (valeur === '') erreur = 'Choisissez un role.';
                    break;

                case 'provider_login':
                    if (valeur === '') erreur = 'Choisissez un provider.';
                    break;
            }

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
   SECTION 4 : AFFICHAGE DES CARTES UTILISATEURS
   DOM : innerHTML, createElement
   ------------------------------------------------------------ */

function initiales(nom, prenom) {
    var n = nom    ? nom.trim()[0]    : '?';
    var p = prenom ? prenom.trim()[0] : '';
    return (n + p).toUpperCase();
}

function formaterDate(dateStr) {
    if (!dateStr) return '';
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

function afficherCartes(liste) {
    var conteneur = document.getElementById('userListContainer');
    if (!conteneur) return;

    if (!liste || liste.length === 0) {
        conteneur.innerHTML = '<div class="empty-box">Aucun utilisateur enregistre.</div>';
        return;
    }

    var html = '<div class="user-grid">';
    liste.forEach(function(u) {
        html += '<div class="user-card">'
              + '<div class="user-avatar">' + initiales(u.nom, u.prenom) + '</div>'
              + '<h3>' + escHtml(u.nom) + ' ' + escHtml(u.prenom) + '</h3>'
              + '<p class="user-email">✉️ ' + escHtml(u.email) + '</p>'
              + '<div class="user-card-badges">'
              + '<span class="role-chip role-' + u.role + '">' + u.role + '</span>'
              + '<span class="provider-chip">' + u.provider_login + '</span>'
              + '</div>'
              + (u.date_inscription ? '<p class="user-date">Inscrit le ' + formaterDate(u.date_inscription) + '</p>' : '')
              + '</div>';
    });
    html += '</div>';
    conteneur.innerHTML = html;
}


/* ------------------------------------------------------------
   SECTION 5 : CHARGEMENT VIA FETCH (PHP/PDO)
   ------------------------------------------------------------ */

function chargerUtilisateurs() {
    var conteneur = document.getElementById('userListContainer');
    if (!conteneur) return;

    var fd = new FormData();
    fd.append('action', 'liste');

    fetch(API, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.succes) {
                afficherCartes(data.utilisateurs);
            } else {
                document.getElementById('userListContainer').innerHTML =
                    '<div class="empty-box" style="color:#dc2626;">Erreur : ' + (data.message || '') + '</div>';
            }
        })
        .catch(function() {
            document.getElementById('userListContainer').innerHTML =
                '<div class="empty-box" style="color:#dc2626;">Serveur inaccessible. Verifiez XAMPP.</div>';
        });
}


/* ------------------------------------------------------------
   SECTION 6 : FORMULAIRE D'INSCRIPTION
   Cours JS : addEventListener('submit', function(e) {
                 e.preventDefault();
              })
   ------------------------------------------------------------ */

document.getElementById('registerForm').addEventListener('submit', function(e) {
    // Cours : e.preventDefault() empeche l'envoi HTML5
    e.preventDefault();

    var data = {
        nom:            document.getElementById('r-nom').value,
        prenom:         document.getElementById('r-prenom').value,
        email:          document.getElementById('r-email').value,
        mot_de_passe:   document.getElementById('r-mot_de_passe').value,
        role:           document.getElementById('r-role').value,
        provider_login: document.getElementById('r-provider_login').value
    };

    // Validation JavaScript (sans HTML5)
    var result = validerInscription(data);
    afficherErreursR(result.erreurs);

    if (!result.valide) {
        afficherMsgR('Veuillez corriger les erreurs dans le formulaire.', 'erreur');
        return;
    }

    var fd = new FormData();
    fd.append('action', 'ajouter');
    Object.keys(data).forEach(function(k) { fd.append(k, data[k]); });

    fetch(API, { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(resp) {
            if (resp.succes) {
                document.getElementById('registerForm').reset();
                viderErreursR();
                afficherMsgR('✅ ' + resp.message, 'succes');
                chargerUtilisateurs();
            } else if (resp.erreurs) {
                afficherErreursR(resp.erreurs);
                afficherMsgR('Veuillez corriger les erreurs.', 'erreur');
            } else {
                afficherMsgR('❌ ' + (resp.message || 'Erreur.'), 'erreur');
            }
        })
        .catch(function() {
            afficherMsgR('❌ Serveur inaccessible.', 'erreur');
        });
});


/* ------------------------------------------------------------
   UTILITAIRE : basculer visibilite mot de passe
   ------------------------------------------------------------ */

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


/* ------------------------------------------------------------
   NAVBAR : evenement scroll
   Cours JS : window.addEventListener('scroll', function() {...})
   ------------------------------------------------------------ */
(function() {
    var navbar = document.getElementById('navbar');
    if (!navbar) return;

    // Cours : addEventListener sur la window
    window.addEventListener('scroll', function() {
        // Cours : classList.toggle()
        navbar.classList.toggle('scrolled', window.scrollY > 50);
    });

    // Cours : querySelector + forEach + addEventListener
    var currentPage = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-links a').forEach(function(link) {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
})();


/* ------------------------------------------------------------
   DEMARRAGE
   Cours JS : evenement 'load' = fin de chargement de la page
   ------------------------------------------------------------ */
window.addEventListener('load', function() {
    ajouterValidationBlurFront();
});
