<!-- NutriSmart — Mini Chatbot Bilan Santé -->

<style>
#chatbotOverlay {
  display: none; position: fixed; inset: 0; z-index: 99999;
  background: rgba(5,46,22,.45); backdrop-filter: blur(6px);
  align-items: center; justify-content: center;
  animation: cbFadeIn .25s ease;
}
#chatbotOverlay.open { display: flex; }
@keyframes cbFadeIn { from{opacity:0} to{opacity:1} }

#chatbotBox {
  background: #fff; border-radius: 28px;
  box-shadow: 0 32px 80px rgba(5,46,22,.22);
  width: min(520px, 94vw); max-height: 92vh;
  overflow: hidden; display: flex; flex-direction: column;
  animation: cbSlideUp .35s cubic-bezier(.23,1,.32,1);
}
@keyframes cbSlideUp {
  from{opacity:0;transform:translateY(40px) scale(.97)}
  to{opacity:1;transform:translateY(0) scale(1)}
}

#cbHeader {
  background: linear-gradient(135deg,#064e3b,#0f6c42);
  padding: 22px 28px 20px; display: flex; align-items: center; gap: 14px; flex-shrink: 0;
}
.cb-avatar {
  width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,.15);
  display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;
  border:2px solid rgba(255,255,255,.25);
}
.cb-header-text h3{margin:0 0 3px;font-size:16px;font-weight:800;color:#fff}
.cb-header-text p{margin:0;font-size:12px;color:rgba(255,255,255,.65);font-weight:500}
.cb-dot{width:8px;height:8px;border-radius:50%;background:#4ade80;display:inline-block;margin-right:5px;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
#cbCloseBtn{margin-left:auto;background:rgba(255,255,255,.12);border:none;color:#fff;width:32px;height:32px;border-radius:50%;font-size:16px;cursor:pointer;flex-shrink:0;transition:background .2s}
#cbCloseBtn:hover{background:rgba(255,255,255,.22)}

#cbProgress{height:4px;background:#e5e7eb;flex-shrink:0}
#cbProgressFill{height:100%;background:linear-gradient(90deg,#10b981,#059669);border-radius:999px;transition:width .5s ease}

#cbBody{
  flex:1;overflow-y:auto;padding:24px 24px 12px;
  display:flex;flex-direction:column;gap:16px;
  scrollbar-width:thin;scrollbar-color:#d1fae5 transparent;min-height:0;
}
.cb-msg{display:flex;align-items:flex-start;gap:10px}
.cb-bot-icon{
  width:32px;height:32px;border-radius:50%;
  background:linear-gradient(135deg,#10b981,#059669);
  display:flex;align-items:center;justify-content:center;
  font-size:15px;flex-shrink:0;margin-top:2px;
}
.cb-bubble{
  background:#f0fdf7;border:1px solid rgba(16,185,129,.18);
  border-radius:4px 18px 18px 18px;padding:12px 16px;
  font-size:14px;color:#1a3a2a;line-height:1.65;max-width:85%;
}
.cb-bubble strong{color:#065f46}

.cb-question-block{width:100%}
.cb-question-label{font-size:13px;font-weight:700;color:#374151;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.cb-emojis{display:flex;gap:8px;flex-wrap:wrap}
.cb-emoji-btn{
  display:flex;flex-direction:column;align-items:center;gap:3px;
  background:#f9fafb;border:2px solid #e5e7eb;border-radius:16px;
  padding:10px 12px;cursor:pointer;transition:all .2s;min-width:58px;font-family:inherit;
}
.cb-emoji-btn .e-icon{font-size:22px;line-height:1}
.cb-emoji-btn .e-lbl{font-size:10px;color:#6b7280;font-weight:600;white-space:nowrap}
.cb-emoji-btn:hover{border-color:#10b981;background:#f0fdf7;transform:translateY(-2px)}
.cb-emoji-btn.selected{border-color:#059669;background:#d1fae5;transform:translateY(-2px);box-shadow:0 4px 12px rgba(16,185,129,.2)}
.cb-emoji-btn.selected .e-lbl{color:#065f46}

#cbFooter{
  padding:14px 24px 18px;flex-shrink:0;
  display:flex;gap:10px;align-items:center;border-top:1px solid #f0fdf7;
}
#cbNextBtn{
  flex:1;padding:13px 20px;
  background:linear-gradient(135deg,#10b981,#059669);
  color:#fff;border:none;border-radius:14px;font-size:14px;font-weight:800;
  font-family:inherit;cursor:pointer;transition:transform .2s,box-shadow .2s;
  box-shadow:0 8px 20px rgba(16,185,129,.25);
  display:flex;align-items:center;justify-content:center;gap:6px;
}
#cbNextBtn:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 14px 28px rgba(16,185,129,.32)}
#cbNextBtn:disabled{background:#d1d5db;box-shadow:none;cursor:not-allowed}
#cbSkipBtn{
  padding:13px 16px;background:none;border:1.5px solid #e5e7eb;border-radius:14px;
  font-size:13px;font-weight:600;color:#9ca3af;cursor:pointer;font-family:inherit;transition:all .2s;
}
#cbSkipBtn:hover{border-color:#d1d5db;color:#6b7280}

/* ═══ SECTION CONSEILS ═══ */
#cbConseil{
  display:none;
  padding:0 20px 20px;
  flex-shrink:0;
  max-height:58vh;
  overflow-y:auto;
  scrollbar-width:thin;
  scrollbar-color:#d1fae5 transparent;
}
.conseil-card{
  background:linear-gradient(135deg,#f0fdf7,#ecfdf5);
  border:1.5px solid rgba(16,185,129,.25);
  border-radius:18px;padding:18px 20px;
}
.conseil-title{
  font-size:12px;font-weight:800;color:#065f46;
  text-transform:uppercase;letter-spacing:.1em;margin-bottom:14px;
  display:flex;align-items:center;gap:6px;
  border-bottom:1px solid rgba(16,185,129,.15);padding-bottom:10px;
}
.conseil-score{
  display:flex;align-items:center;justify-content:space-between;
  margin-bottom:12px;padding:10px 14px;
  background:rgba(16,185,129,.1);border-radius:12px;
  border:1px solid rgba(16,185,129,.2);
}
.conseil-score-label{font-size:12px;font-weight:700;color:#065f46}
.conseil-score-stars{font-size:18px}
.conseil-lines{display:flex;flex-direction:column;gap:10px}
.conseil-line{
  font-size:13.5px;color:#1a3a2a;line-height:1.65;
  padding:10px 14px;background:rgba(255,255,255,.75);
  border-radius:12px;border:1px solid rgba(16,185,129,.12);
}
#cbDoneBtn{
  width:100%;margin-top:14px;padding:14px;
  background:linear-gradient(135deg,#10b981,#059669);
  color:#fff;border:none;border-radius:14px;
  font-size:14px;font-weight:800;font-family:inherit;
  cursor:pointer;transition:transform .2s;
  box-shadow:0 8px 20px rgba(16,185,129,.25);
}
#cbDoneBtn:hover{transform:translateY(-2px)}

.cb-step-count{
  font-size:11px;color:rgba(255,255,255,.55);font-weight:700;
  margin-left:auto;letter-spacing:.06em;
  background:rgba(255,255,255,.12);padding:4px 10px;border-radius:999px;
}
.cb-loader{display:flex;gap:6px;align-items:center;padding:4px 0}
.cb-loader span{width:8px;height:8px;background:#10b981;border-radius:50%;animation:cbBounce 1.2s infinite ease-in-out}
.cb-loader span:nth-child(2){animation-delay:.2s}
.cb-loader span:nth-child(3){animation-delay:.4s}
@keyframes cbBounce{0%,80%,100%{transform:scale(0);opacity:.4}40%{transform:scale(1);opacity:1}}
</style>

<div id="chatbotOverlay">
  <div id="chatbotBox">
    <div id="cbHeader">
      <div class="cb-avatar">🥗</div>
      <div class="cb-header-text">
        <h3>NutriBot</h3>
        <p><span class="cb-dot"></span><span id="cbHeaderStatus">Bilan santé du jour</span></p>
      </div>
      <span class="cb-step-count" id="cbStepCount">1 / 5</span>
      <button id="cbCloseBtn" onclick="fermerChatbot()" title="Fermer">✕</button>
    </div>

    <div id="cbProgress"><div id="cbProgressFill" style="width:0%"></div></div>

    <div id="cbBody">
      <div class="cb-msg">
        <div class="cb-bot-icon">🤖</div>
        <div class="cb-bubble">
          Bonjour <strong id="cbPrenom"></strong> ! 👋<br>
          Avant de commencer ta journée, réponds à quelques questions rapides.
          Ça m'aidera à te donner des conseils personnalisés aujourd'hui. 🌿
        </div>
      </div>
      <div id="cbQuestionZone"></div>
    </div>

    <!-- CONSEILS — s'affiche après les questions -->
    <div id="cbConseil">
      <div class="conseil-card">
        <div class="conseil-title">💡 Mes conseils pour toi aujourd'hui</div>
        <div id="cbScoreZone"></div>
        <div class="conseil-lines" id="cbConseilLines"></div>
      </div>
      <button id="cbDoneBtn" onclick="fermerChatbot()">Merci NutriBot ! Bonne journée 🌿</button>
    </div>

    <div id="cbFooter">
      <button id="cbSkipBtn" onclick="passerQuestion()">Passer</button>
      <button id="cbNextBtn" onclick="questionSuivante()" disabled>Suivant →</button>
    </div>
  </div>
</div>

<script>
(function() {

  var QUESTIONS = [
    {
      id: 'fatigue', label: 'Comment est ton niveau d\'énergie aujourd\'hui ? 💪',
      emojis: [
        {v:1,icon:'😴',lbl:'Épuisé(e)'},{v:2,icon:'😪',lbl:'Fatigué(e)'},
        {v:3,icon:'😐',lbl:'Normal'},{v:4,icon:'😊',lbl:'En forme'},{v:5,icon:'🤩',lbl:'Au top !'}
      ]
    },
    {
      id: 'humeur', label: 'Comment est ton humeur en ce moment ? 😊',
      emojis: [
        {v:1,icon:'😞',lbl:'Triste'},{v:2,icon:'😰',lbl:'Stressé(e)'},
        {v:3,icon:'😑',lbl:'Neutre'},{v:4,icon:'🙂',lbl:'Bien'},{v:5,icon:'😄',lbl:'Heureux(se)'}
      ]
    },
    {
      id: 'hydratation', label: 'As-tu bien bu de l\'eau aujourd\'hui ? 💧',
      emojis: [
        {v:1,icon:'🏜️',lbl:'Pas du tout'},{v:2,icon:'😓',lbl:'Très peu'},
        {v:3,icon:'🥤',lbl:'Un peu'},{v:4,icon:'💧',lbl:'Assez bien'},{v:5,icon:'🌊',lbl:'Très bien'}
      ]
    },
    {
      id: 'appetit', label: 'Comment est ton appétit aujourd\'hui ? 🍽️',
      emojis: [
        {v:1,icon:'😶',lbl:'Aucun'},{v:2,icon:'🫤',lbl:'Faible'},
        {v:3,icon:'😌',lbl:'Normal'},{v:4,icon:'🍴',lbl:'Bon'},{v:5,icon:'🤤',lbl:'Très bon'}
      ]
    },
    {
      id: 'sommeil', label: 'Comment as-tu dormi cette nuit ? 🌙',
      emojis: [
        {v:1,icon:'😩',lbl:'Très mal'},{v:2,icon:'😕',lbl:'Mal'},
        {v:3,icon:'😐',lbl:'Moyen'},{v:4,icon:'😴',lbl:'Bien'},{v:5,icon:'⭐',lbl:'Excellent'}
      ]
    }
  ];

  var currentStep = 0;
  var reponses    = {};
  var totalSteps  = QUESTIONS.length;

  /* ── Ouvrir ─────────────────────────────────────────────── */
  window.ouvrirChatbot = function(prenom) {
    document.getElementById('cbPrenom').textContent       = prenom || '';
    document.getElementById('cbHeaderStatus').textContent = 'Bilan santé du jour';
    currentStep = 0; reponses = {};

    document.getElementById('cbConseil').style.display   = 'none';
    document.getElementById('cbFooter').style.display    = 'flex';
    document.getElementById('cbProgressFill').style.width = '0%';
    document.getElementById('cbStepCount').textContent   = '1 / ' + totalSteps;
    document.getElementById('cbNextBtn').textContent     = 'Suivant →';
    document.getElementById('cbNextBtn').disabled        = true;
    document.getElementById('cbSkipBtn').disabled        = false;

    // Réinitialiser body : garder 1er message + recréer zone question
    var body = document.getElementById('cbBody');
    while (body.children.length > 1) body.removeChild(body.lastChild);
    var qz = document.createElement('div'); qz.id = 'cbQuestionZone';
    body.appendChild(qz);

    afficherQuestion(0);
    document.getElementById('chatbotOverlay').classList.add('open');
  };

  window.fermerChatbot = function() {
    document.getElementById('chatbotOverlay').classList.remove('open');
  };

  document.getElementById('chatbotOverlay').addEventListener('click', function(e) {
    if (e.target === this) fermerChatbot();
  });

  /* ── Afficher question ──────────────────────────────────── */
  function afficherQuestion(idx) {
    var q = QUESTIONS[idx];
    document.getElementById('cbProgressFill').style.width = Math.round((idx/totalSteps)*100) + '%';
    document.getElementById('cbStepCount').textContent    = (idx+1) + ' / ' + totalSteps;
    document.getElementById('cbNextBtn').disabled         = true;
    document.getElementById('cbNextBtn').textContent      = 'Suivant →';

    var zone = document.getElementById('cbQuestionZone');
    zone.innerHTML = '';

    var block = document.createElement('div'); block.className = 'cb-question-block';
    var label = document.createElement('div'); label.className = 'cb-question-label';
    label.textContent = q.label; block.appendChild(label);

    var emojisDiv = document.createElement('div'); emojisDiv.className = 'cb-emojis';

    q.emojis.forEach(function(em) {
      var btn = document.createElement('button');
      btn.type = 'button'; btn.className = 'cb-emoji-btn';
      btn.innerHTML = '<span class="e-icon">' + em.icon + '</span><span class="e-lbl">' + em.lbl + '</span>';
      if (reponses[q.id] === em.v) btn.classList.add('selected');

      btn.addEventListener('click', function() {
        emojisDiv.querySelectorAll('.cb-emoji-btn').forEach(function(b){ b.classList.remove('selected'); });
        btn.classList.add('selected');
        reponses[q.id] = em.v;
        document.getElementById('cbNextBtn').disabled = false;

        setTimeout(function() {
          if (currentStep < totalSteps - 1) {
            currentStep++;
            afficherQuestion(currentStep);
          } else {
            // Dernière question répondue → changer le bouton
            document.getElementById('cbNextBtn').textContent = 'Voir mes conseils 🌿';
            document.getElementById('cbNextBtn').disabled    = false;
          }
        }, 350);
      });
      emojisDiv.appendChild(btn);
    });

    block.appendChild(emojisDiv); zone.appendChild(block);
    var body = document.getElementById('cbBody');
    setTimeout(function(){ body.scrollTop = body.scrollHeight; }, 80);
  }

  /* ── Bouton Suivant ─────────────────────────────────────── */
  window.questionSuivante = function() {
    if (currentStep < totalSteps - 1) {
      currentStep++; afficherQuestion(currentStep);
    } else {
      envoyerBilan();
    }
  };

  /* ── Passer ─────────────────────────────────────────────── */
  window.passerQuestion = function() {
    if (currentStep < totalSteps - 1) {
      currentStep++; afficherQuestion(currentStep);
    } else {
      envoyerBilan();
    }
  };

  /* ── Envoyer au serveur ─────────────────────────────────── */
  function envoyerBilan() {
    document.getElementById('cbNextBtn').disabled  = true;
    document.getElementById('cbSkipBtn').disabled  = true;
    document.getElementById('cbNextBtn').innerHTML =
      '<div class="cb-loader"><span></span><span></span><span></span></div>';

    // Message "en cours"
    var body = document.getElementById('cbBody');
    var analyseMsg = document.createElement('div');
    analyseMsg.className = 'cb-msg'; analyseMsg.id = 'cbAnalyseMsg';
    analyseMsg.innerHTML =
      '<div class="cb-bot-icon">🤖</div>' +
      '<div class="cb-bubble">J\'analyse tes réponses pour préparer tes conseils personnalisés...' +
      '<br><div class="cb-loader" style="margin-top:8px"><span></span><span></span><span></span></div></div>';
    body.appendChild(analyseMsg);
    setTimeout(function(){ body.scrollTop = body.scrollHeight; }, 80);

    var formData = new FormData();
    formData.append('action', 'sauvegarder_bilan');
    for (var key in reponses) formData.append(key, reponses[key]);

    fetch('index.php', {method:'POST', body:formData})
      .then(function(r){ return r.json(); })
      .then(function(data) {
        var conseil = (data.ok && data.conseil && data.conseil.trim())
          ? data.conseil
          : genererConseilLocal(reponses);
        afficherConseils(conseil, reponses);
      })
      .catch(function() {
        afficherConseils(genererConseilLocal(reponses), reponses);
      });
  }

  /* ── Conseils JS (fallback si serveur KO) ───────────────── */
  function genererConseilLocal(rep) {
    var c = [];
    var f=rep.fatigue||3, h=rep.humeur||3, w=rep.hydratation||3, a=rep.appetit||3, s=rep.sommeil||3;
    if(f<=2)      c.push('💤 Tu sembles fatigué(e) — essaie de te coucher 30 min plus tôt ce soir et évite les écrans avant de dormir.');
    else if(f==3) c.push('☕ Énergie normale. Une courte pause de 10 min en milieu de journée peut booster ta productivité.');
    else          c.push('⚡ Super énergie aujourd\'hui ! C\'est le bon moment pour une activité physique douce.');
    if(h<=2)      c.push('🍫 Pour améliorer ton humeur, essaie quelques noix ou un carré de chocolat noir — riches en magnésium, excellents contre le stress.');
    else if(h>=4) c.push('😊 Bonne humeur = bonne journée ! Profites-en pour préparer un repas sain et équilibré.');
    if(w<=2)      c.push('💧 Attention à l\'hydratation ! Bois au moins 1,5L d\'eau aujourd\'hui. Pose une bouteille sur ton bureau pour t\'en rappeler.');
    else if(w<=3) c.push('🥤 Hydratation correcte. Pense à boire régulièrement tout au long de la journée, pas seulement quand tu as soif.');
    else          c.push('✅ Excellente hydratation ! Continue ainsi, ton corps te remerciera.');
    if(a<=2)      c.push('🥗 Peu d\'appétit ? Essaie des petits repas fractionnés toutes les 3h plutôt qu\'un grand repas.');
    else if(a>=4) c.push('🍽️ Bon appétit ! Pense à privilégier les protéines et les fibres pour te sentir rassasié(e) plus longtemps.');
    if(s<=2)      c.push('🌙 Mauvais sommeil détecté. Évite la caféine après 14h et les repas lourds le soir. Une tisane à la camomille peut aider.');
    else if(s>=4) c.push('🌟 Excellent sommeil ! Un bon repos aide ton corps à mieux assimiler les nutriments.');
    var score=(f+h+w+a+s)/5;
    if(score>=4)      c.push('🏆 Ton bilan du jour est excellent ! Continue sur cette lancée.');
    else if(score<=2) c.push('🤗 Ne t\'inquiète pas — chaque jour est une nouvelle chance. Ton nutritionniste est là pour t\'accompagner.');
    return c.join('\n');
  }

  /* ── Afficher les conseils ──────────────────────────────── */
  function afficherConseils(texte, rep) {
    document.getElementById('cbProgressFill').style.width    = '100%';
    document.getElementById('cbStepCount').textContent       = '✓ Terminé';
    document.getElementById('cbHeaderStatus').textContent    = '✓ Bilan terminé';

    // Supprimer loader "analyse"
    var am = document.getElementById('cbAnalyseMsg');
    if (am) am.remove();

    // Masquer footer et zone question
    document.getElementById('cbFooter').style.display    = 'none';
    document.getElementById('cbQuestionZone').innerHTML  = '';

    // Message de conclusion dans le chat
    var body = document.getElementById('cbBody');
    var msg = document.createElement('div'); msg.className = 'cb-msg';
    msg.innerHTML =
      '<div class="cb-bot-icon">🤖</div>' +
      '<div class="cb-bubble">Voici tes conseils personnalisés pour aujourd\'hui 👇' +
      '<br><strong>Prends soin de toi !</strong> 💚</div>';
    body.appendChild(msg);

    // Remplir les conseils
    var scoreZone    = document.getElementById('cbScoreZone');
    var conseilLines = document.getElementById('cbConseilLines');
    scoreZone.innerHTML = '';
    conseilLines.innerHTML = '';

    // Badge score
    if (rep && Object.keys(rep).length > 0) {
      var vals = Object.values(rep);
      var avg  = vals.reduce(function(a,b){return a+b;},0) / vals.length;
      var stars = avg>=4.5?'⭐⭐⭐⭐⭐':avg>=3.5?'⭐⭐⭐⭐':avg>=2.5?'⭐⭐⭐':avg>=1.5?'⭐⭐':'⭐';
      var sd = document.createElement('div'); sd.className = 'conseil-score';
      sd.innerHTML = '<span class="conseil-score-label">Score global du jour</span><span class="conseil-score-stars">' + stars + '</span>';
      scoreZone.appendChild(sd);
    }

    // Lignes de conseil (une par ligne)
    texte.split('\n').forEach(function(ligne) {
      if (!ligne.trim()) return;
      var div = document.createElement('div');
      div.className   = 'conseil-line';
      div.textContent = ligne.trim();
      conseilLines.appendChild(div);
    });

    // AFFICHER le bloc conseils
    var conseilDiv = document.getElementById('cbConseil');
    conseilDiv.style.display = 'block';

    // Scroll
    setTimeout(function() {
      body.scrollTop = body.scrollHeight;
    }, 150);
  }

  /* ── Vérifier bilan du jour ─────────────────────────────── */
  window.verifierBilanDuJour = function(prenom) {
    var fd = new FormData(); fd.append('action','verifier_bilan');
    fetch('index.php', {method:'POST',body:fd})
      .then(function(r){return r.json();})
      .then(function(d){
        if(d.ok && !d.bilan_fait) setTimeout(function(){ouvrirChatbot(prenom);}, 900);
      })
      .catch(function(){setTimeout(function(){ouvrirChatbot(prenom);}, 900);});
  };

})();
</script>
