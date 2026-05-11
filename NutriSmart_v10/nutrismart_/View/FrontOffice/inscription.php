<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Inscription</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="public/css/style.css" />
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4fbf7; margin: 0; }

    .auth-wrapper {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    /* ── PANNEAU GAUCHE ── */
    .auth-hero {
      background: linear-gradient(145deg, #0a3d22 0%, #0f6c42 45%, #1fa463 100%);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 52px 56px;
      position: relative;
      overflow: hidden;
    }
    .auth-hero::before {
      content: '';
      position: absolute; inset: 0;
      background:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,.07) 0%, transparent 50%),
        radial-gradient(circle at 80% 15%, rgba(255,255,255,.05) 0%, transparent 40%);
    }
    .deco-ring { position: absolute; border-radius: 50%; border: 56px solid rgba(255,255,255,.05); }
    .deco-ring-1 { width: 380px; height: 380px; right: -90px; top: 50%; transform: translateY(-50%); }
    .deco-ring-2 { width: 260px; height: 260px; left: -80px; bottom: 60px; border-width: 40px; }

    .hero-brand { position: relative; z-index: 1; }
    .hero-logo  { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: -.02em; }
    .hero-tagline { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.55); letter-spacing: .18em; text-transform: uppercase; margin-top: 3px; }

    .hero-body { position: relative; z-index: 1; }
    .hero-body h2 { font-size: clamp(1.9rem, 2.8vw, 2.8rem); font-weight: 900; color: #fff; line-height: 1.15; margin: 0 0 18px; }
    .hero-body p  { color: rgba(255,255,255,.72); font-size: 15px; line-height: 1.75; max-width: 360px; margin: 0 0 32px; }

    .steps { display: flex; flex-direction: column; gap: 14px; }
    .step  { display: flex; align-items: flex-start; gap: 12px; }
    .step-num {
      width: 28px; height: 28px; flex-shrink: 0;
      background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
      border-radius: 50%; display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 800; color: #fff;
    }
    .step-txt strong { display: block; font-size: 13px; font-weight: 700; color: #fff; }
    .step-txt span   { font-size: 12px; color: rgba(255,255,255,.55); }

    .hero-pills { position: relative; z-index: 1; display: flex; gap: 10px; flex-wrap: wrap; }
    .pill {
      display: flex; align-items: center; gap: 6px;
      background: rgba(255,255,255,.11); border: 1px solid rgba(255,255,255,.18);
      backdrop-filter: blur(6px); border-radius: 999px;
      padding: 8px 16px; font-size: 12px; font-weight: 600; color: #fff;
    }

    /* ── PANNEAU DROIT ── */
    .auth-panel {
      display: flex; flex-direction: column; justify-content: center;
      padding: 48px 64px;
      background: #fafdfb;
      position: relative;
      overflow-y: auto;
    }
    .auth-panel::before {
      content: ''; position: absolute; top: 0; left: 0;
      width: 1px; height: 100%;
      background: linear-gradient(180deg, transparent, rgba(31,164,99,.2), transparent);
    }

    .back-link {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 13px; font-weight: 600; color: #638070;
      text-decoration: none; margin-bottom: 28px;
      transition: color .2s;
    }
    .back-link:hover { color: #1fa463; }

    .auth-title { font-size: 1.9rem; font-weight: 900; color: #10281b; margin: 0 0 6px; letter-spacing: -.02em; }
    .auth-sub   { color: #638070; font-size: 14px; margin: 0 0 28px; }

    /* Alert */
    .alert {
      display: flex; align-items: center; gap: 10px;
      padding: 13px 16px; border-radius: 12px;
      font-size: 13px; font-weight: 600; margin-bottom: 20px;
    }
    .alert-err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    .alert-ok  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

    /* Form */
    .auth-form { display: flex; flex-direction: column; gap: 18px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }

    .f-group { display: flex; flex-direction: column; gap: 5px; }
    .f-group label { font-size: 13px; font-weight: 700; color: #10281b; }
    .star { color: #ef4444; }

    .f-wrap { position: relative; }
    .f-icon {
      position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
      font-size: 15px; pointer-events: none; opacity: .45;
    }
    .f-group input,
    .f-group select {
      width: 100%; padding: 13px 14px 13px 42px;
      border: 1.5px solid #ddeee5; border-radius: 14px;
      background: #fff; color: #10281b; font-size: 14px;
      font-family: inherit; transition: border-color .2s, box-shadow .2s;
      box-sizing: border-box;
      -webkit-appearance: none; appearance: none;
    }
    .f-group input:focus,
    .f-group select:focus { outline: none; border-color: #1fa463; box-shadow: 0 0 0 4px rgba(31,164,99,.1); }
    .f-group input.invalid,
    .f-group select.invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 4px rgba(239,68,68,.08) !important; }
    .f-group input.valid,
    .f-group select.valid   { border-color: #16a34a !important; box-shadow: 0 0 0 4px rgba(22,163,74,.08) !important; }

    /* Select arrow */
    .select-wrap::after {
      content: '▾'; position: absolute; right: 14px; top: 50%;
      transform: translateY(-50%); pointer-events: none;
      color: #638070; font-size: 14px;
    }

    /* Password eye */
    .f-group .has-eye input { padding-right: 46px; }
    .eye-btn {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      font-size: 16px; color: #638070; padding: 4px; transition: color .2s;
    }
    .eye-btn:hover { color: #1fa463; }

    /* Password strength bar */
    .pwd-strength-bar {
      height: 4px; border-radius: 4px; background: #e3ede8;
      margin-top: 4px; overflow: hidden;
    }
    .pwd-strength-fill {
      height: 100%; border-radius: 4px;
      transition: width .3s, background .3s;
    }

    .fb { font-size: 12px; font-weight: 600; min-height: 16px; }
    .fb.err { color: #dc2626; }
    .fb.ok  { color: #16a34a; }
    .fb.warn { color: #d97706; }

    /* Role cards */
    .role-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .role-card {
      border: 2px solid #ddeee5; border-radius: 14px;
      padding: 16px; cursor: pointer; transition: border-color .2s, background .2s;
      display: flex; flex-direction: column; gap: 6px;
      background: #fff;
    }
    .role-card:hover { border-color: #1fa463; background: #f0fdf7; }
    .role-card.selected { border-color: #1fa463; background: #f0fdf7; box-shadow: 0 0 0 4px rgba(31,164,99,.1); }
    .role-card .role-icon { font-size: 24px; }
    .role-card .role-name { font-size: 14px; font-weight: 700; color: #10281b; }
    .role-card .role-desc { font-size: 11px; color: #638070; line-height: 1.5; }
    .role-radio { display: none; }

    .btn-submit {
      background: linear-gradient(135deg, #1fa463, #0f6c42);
      color: #fff; border: none; border-radius: 14px;
      padding: 15px 28px; font-size: 15px; font-weight: 800;
      font-family: inherit; cursor: pointer; width: 100%;
      box-shadow: 0 12px 28px rgba(31,164,99,.25);
      transition: transform .25s, box-shadow .25s; margin-top: 4px;
    }
    .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 18px 36px rgba(31,164,99,.32); }
    .btn-submit:active { transform: translateY(0); }

    .auth-sep {
      display: flex; align-items: center; gap: 12px;
      color: #b8d4c4; font-size: 12px; font-weight: 600; letter-spacing: .06em;
      margin-top: 8px;
    }
    .auth-sep::before, .auth-sep::after { content: ''; flex: 1; height: 1px; background: #e3ede8; }

    .auth-foot { text-align: center; font-size: 13px; color: #638070; margin-top: 16px; }
    .auth-foot a { color: #1fa463; font-weight: 700; text-decoration: none; transition: color .2s; }
    .auth-foot a:hover { color: #0f6c42; }

    @media (max-width: 900px) {
      .auth-wrapper { grid-template-columns: 1fr; }
      .auth-hero { display: none; }
      .auth-panel { padding: 36px 20px; }
    }

    /* ── FACE ID ── */
    .face-box {
      background: #fff; border: 1.5px solid #ddeee5; border-radius: 20px;
      padding: 24px; display: flex; flex-direction: column; gap: 16px;
    }
    .face-header { display: flex; align-items: center; gap: 14px; }
    .face-icon-big { font-size: 36px; }
    .face-title { font-size: 16px; font-weight: 800; color: #10281b; }
    .face-hint  { font-size: 12px; color: #638070; margin-top: 2px; }

    .video-container {
      position: relative; width: 100%; max-width: 380px;
      margin: 0 auto; border-radius: 16px; overflow: hidden;
      background: #0a1a10; aspect-ratio: 4/3;
    }
    .video-container video,
    .video-container canvas {
      position: absolute; top: 0; left: 0;
      width: 100%; height: 100%; object-fit: cover;
    }
    .video-container canvas { z-index: 2; }
    .face-overlay {
      position: absolute; inset: 0; z-index: 3;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center; gap: 12px;
      pointer-events: none;
    }
    .face-ring {
      width: 160px; height: 160px; border-radius: 50%;
      border: 3px solid rgba(31,164,99,.7);
      box-shadow: 0 0 0 4px rgba(31,164,99,.15);
      transition: border-color .3s, box-shadow .3s;
    }
    .face-ring.detected {
      border-color: #1fa463;
      box-shadow: 0 0 0 6px rgba(31,164,99,.3), 0 0 20px rgba(31,164,99,.4);
    }
    .face-ring.error { border-color: #ef4444; box-shadow: 0 0 0 6px rgba(239,68,68,.2); }
    .face-status {
      background: rgba(0,0,0,.55); backdrop-filter: blur(4px);
      color: #fff; font-size: 12px; font-weight: 600;
      padding: 6px 14px; border-radius: 999px;
    }

    .face-msg {
      font-size: 13px; font-weight: 600; text-align: center;
      min-height: 20px; padding: 4px 0;
    }
    .face-msg.ok   { color: #16a34a; }
    .face-msg.err  { color: #dc2626; }
    .face-msg.info { color: #0369a1; }

    .face-actions { display: flex; flex-direction: column; gap: 10px; align-items: center; }
    .btn-face {
      background: linear-gradient(135deg, #1fa463, #0f6c42);
      color: #fff; border: none; border-radius: 14px;
      padding: 13px 28px; font-size: 14px; font-weight: 800;
      font-family: inherit; cursor: pointer; width: 100%;
      box-shadow: 0 8px 20px rgba(31,164,99,.25);
      transition: transform .2s, box-shadow .2s, opacity .2s;
    }
    .btn-face:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(31,164,99,.32); }
    .btn-face:disabled { opacity: .45; cursor: not-allowed; }
    .btn-skip {
      font-size: 12px; font-weight: 600; color: #638070;
      text-decoration: none; transition: color .2s;
    }
    .btn-skip:hover { color: #1fa463; }
  </style>
  <script src="public/js/lang.js"></script>
</head>
<body data-page-title="page_title_signup">
<div class="auth-wrapper">

  <!-- PANNEAU GAUCHE -->
  <div class="auth-hero">
    <div class="deco-ring deco-ring-1"></div>
    <div class="deco-ring deco-ring-2"></div>

    <div class="hero-brand">
      <div class="hero-logo">🥗 NutriSmart</div>
      <div class="hero-tagline">Eat Smart · Live Smart</div>
    </div>

    <div class="hero-body">
      <h2><span data-i18n="signup_hero_title">Rejoignez la<br>communauté.</span></h2>
      <p data-i18n="signup_hero_sub">Créez votre compte en quelques secondes et commencez votre parcours vers une meilleure santé nutritionnelle.</p>
      <div class="steps">
        <div class="step">
          <div class="step-num">1</div>
          <div class="step-txt">
            <strong data-i18n="step1_title">Créez votre profil</strong>
            <span data-i18n="step1_sub">Remplissez vos informations de base</span>
          </div>
        </div>
        <div class="step">
          <div class="step-num">2</div>
          <div class="step-txt">
            <strong data-i18n="step2_title">Choisissez votre rôle</strong>
            <span data-i18n="step2_sub">Client ou nutritionniste</span>
          </div>
        </div>
        <div class="step">
          <div class="step-num">3</div>
          <div class="step-txt">
            <strong data-i18n="step3_title">Accédez à votre espace</strong>
            <span data-i18n="step3_sub">Suivi nutrition personnalisé</span>
          </div>
        </div>
      </div>
    </div>

    <div class="hero-pills">
      <div class="pill" data-i18n="pill_secure2">🔒 100% Sécurisé</div>
      <div class="pill" data-i18n="pill_free">⚡ Gratuit</div>
    </div>
  </div>

  <!-- PANNEAU DROIT -->
  <div class="auth-panel">
    <a href="index.php?page=accueil" class="back-link" data-i18n="back_home">← Retour à l'accueil</a>
    <h1 class="auth-title" data-i18n="signup_title">Créer un compte ✨</h1>
    <p class="auth-sub" data-i18n="signup_sub">Rejoignez NutriSmart et prenez soin de votre santé.</p>

    <?php if (isset($_GET['succes']) && isset($_GET['user_id'])): ?>
      <!-- Étape Face ID après inscription réussie -->
      <div id="faceIdSetup">
        <div class="alert alert-ok" style="margin-bottom:16px;">✅ Compte créé ! Enregistrez maintenant votre visage pour activer la connexion Face ID.</div>
        <div class="face-box">
          <div class="face-header">
            <span class="face-icon-big">🪪</span>
            <div>
              <div class="face-title">Enregistrement Face ID</div>
              <div class="face-hint">Placez votre visage dans le cadre et cliquez sur "Scanner"</div>
            </div>
          </div>
          <div class="video-container">
            <video id="faceVideo" autoplay muted playsinline></video>
            <canvas id="faceCanvas"></canvas>
            <div id="faceOverlay" class="face-overlay">
              <div class="face-ring" id="faceRing"></div>
              <div class="face-status" id="faceStatus">Initialisation de la caméra…</div>
            </div>
          </div>
          <div id="faceMsg" class="face-msg"></div>
          <div class="face-actions">
            <button type="button" id="btnScanFace" class="btn-face" disabled>📸 Scanner mon visage</button>
            <a href="index.php?page=login" class="btn-skip">Passer cette étape →</a>
          </div>
        </div>
      </div>
    <?php elseif (isset($_GET['succes'])): ?>
      <div class="alert alert-ok"><span data-i18n="alert_success">✅ Compte créé avec succès !</span> <a href="index.php?page=login" style="color:#166534;font-weight:800;" data-i18n="alert_login_link">Se connecter</a></div>
    <?php endif; ?>
    <?php if (isset($_GET['erreur'])): ?>
      <?php if ($_GET['erreur'] === 'champs_vides'): ?>
        <div class="alert alert-err"><span data-i18n="err_empty_fields">⚠️ Veuillez remplir tous les champs obligatoires.</span></div>
      <?php elseif ($_GET['erreur'] === 'email_existe'): ?>
        <div class="alert alert-err"><span data-i18n="err_email_exists">❌ Cet email est déjà utilisé.</span> <a href="index.php?page=login" style="color:#991b1b;font-weight:800;" data-i18n="err_email_login">Se connecter ?</a></div>
      <?php endif; ?>
    <?php endif; ?>

    <form id="formInscription" class="auth-form" action="index.php" method="POST" novalidate>
      <input type="hidden" name="action" value="ajouter" />
      <!-- Champ role caché, mis à jour par les cartes -->
      <input type="hidden" name="role" id="roleHidden" value="" />

      <!-- Nom / Prénom -->
      <div class="form-row">
        <div class="f-group">
          <label for="nom"><span data-i18n="label_nom">Nom</span> <span class="star">*</span></label>
          <div class="f-wrap">
            <span class="f-icon">👤</span>
            <input id="nom" type="text" name="nom" placeholder="Ex : Ben Ali" data-i18n-placeholder="placeholder_nom" autocomplete="family-name" />
          </div>
          <div id="fbNom" class="fb"></div>
        </div>
        <div class="f-group">
          <label for="prenom"><span data-i18n="label_prenom">Prénom</span> <span class="star">*</span></label>
          <div class="f-wrap">
            <span class="f-icon">👤</span>
            <input id="prenom" type="text" name="prenom" placeholder="Ex : Mohamed" data-i18n-placeholder="placeholder_prenom" autocomplete="given-name" />
          </div>
          <div id="fbPrenom" class="fb"></div>
        </div>
      </div>

      <!-- Email -->
      <div class="f-group">
        <label for="email"><span data-i18n="label_email2">Adresse email</span> <span class="star">*</span></label>
        <div class="f-wrap">
          <span class="f-icon">📧</span>
          <input id="email" type="text" name="email" placeholder="mohamedbenali@gmail.com" data-i18n-placeholder="placeholder_email2" autocomplete="email" />
        </div>
        <div id="fbEmail" class="fb"></div>
      </div>

      <!-- Mot de passe -->
      <div class="f-group">
        <label for="mot_de_passe"><span data-i18n="label_password2">Mot de passe</span> <span class="star">*</span></label>
        <div class="f-wrap has-eye">
          <span class="f-icon">🔑</span>
          <input id="mot_de_passe" type="password" name="mot_de_passe" placeholder="Choisissez un mot de passe" data-i18n-placeholder="placeholder_password2" autocomplete="new-password" />
          <button type="button" class="eye-btn" id="eyeBtn">👁</button>
        </div>
        <div class="pwd-strength-bar"><div class="pwd-strength-fill" id="pwdBar" style="width:0%;background:#ef4444;"></div></div>
        <div id="fbMdp" class="fb"></div>
      </div>

      <!-- Rôle (cartes visuelles) -->
      <div class="f-group">
        <label><span data-i18n="label_role">Rôle</span> <span class="star">*</span></label>
        <div class="role-cards">
          <div class="role-card" id="cardClient" onclick="selectRole('client')">
            <div class="role-icon">🧑‍💼</div>
            <div class="role-name" data-i18n="role_client_name">Client</div>
            <div class="role-desc" data-i18n="role_client_desc">Suivez votre alimentation et recevez des conseils personnalisés.</div>
          </div>
          <div class="role-card" id="cardNutri" onclick="selectRole('nutritionniste')">
            <div class="role-icon">👩‍⚕️</div>
            <div class="role-name" data-i18n="role_nutri_name">Nutritionniste</div>
            <div class="role-desc" data-i18n="role_nutri_desc">Accompagnez vos patients dans leur parcours nutritionnel.</div>
          </div>
        </div>
        <div id="fbRole" class="fb"></div>
      </div>

      <button type="submit" class="btn-submit" data-i18n="btn_signup_submit">Créer mon compte →</button>
    </form>

    <div class="auth-sep"><span data-i18n="signup_sep">OU</span></div>
    <p class="auth-foot"><span data-i18n="signup_have_account">Déjà un compte ?</span> <a href="index.php?page=login" data-i18n="signup_login_link">Se connecter</a></p>
  </div>
</div>

<script>
  const RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function fb(id, inp, cls, msg) {
    const el = document.getElementById(id);
    el.textContent = msg;
    el.className = 'fb ' + cls;
    if (inp) {
      inp.classList.toggle('valid',   cls === 'ok');
      inp.classList.toggle('invalid', cls === 'err');
    }
  }

  // Nom
  const nomInp = document.getElementById('nom');
  function chkNom(force) {
    if (!nomInp.value && !force) return true;
    const ok = nomInp.value.trim().length >= 2;
    fb('fbNom', nomInp, ok ? 'ok' : 'err', ok ? t('val_nom_ok') : t('val_nom_short'));
    return ok;
  }
  nomInp.addEventListener('blur',  () => chkNom(true));
  nomInp.addEventListener('input', () => { if (nomInp.classList.contains('invalid')) chkNom(false); });

  // Prénom
  const prenomInp = document.getElementById('prenom');
  function chkPrenom(force) {
    if (!prenomInp.value && !force) return true;
    const ok = prenomInp.value.trim().length >= 2;
    fb('fbPrenom', prenomInp, ok ? 'ok' : 'err', ok ? t('val_prenom_ok') : t('val_prenom_short'));
    return ok;
  }
  prenomInp.addEventListener('blur',  () => chkPrenom(true));
  prenomInp.addEventListener('input', () => { if (prenomInp.classList.contains('invalid')) chkPrenom(false); });

  // Email
  const emlInp = document.getElementById('email');
  function chkEmail(force) {
    if (!emlInp.value && !force) return true;
    const ok = RE.test(emlInp.value.trim());
    fb('fbEmail', emlInp, ok ? 'ok' : 'err', ok ? t('val_email_ok2') : t('val_email_invalid2'));
    return ok;
  }
  emlInp.addEventListener('blur',  () => chkEmail(true));
  emlInp.addEventListener('input', () => { if (emlInp.classList.contains('invalid')) chkEmail(false); });

  // Password
  const mdpInp = document.getElementById('mot_de_passe');
  const bar    = document.getElementById('pwdBar');
  function pwdStrength(v) {
    let s = 0;
    if (v.length >= 4) s++;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    return s;
  }
  function chkMdp(force) {
    if (!mdpInp.value && !force) return true;
    const v   = mdpInp.value;
    const str = pwdStrength(v);
    const pct = Math.min(str * 20, 100);
    const col = str <= 1 ? '#ef4444' : str <= 2 ? '#f59e0b' : str <= 3 ? '#84cc16' : '#16a34a';
    bar.style.width = pct + '%';
    bar.style.background = col;
    const ok = v.length >= 4;
    const msgs = ['', t('val_pwd_weak'), t('val_pwd_weak'), t('val_pwd_medium'), t('val_pwd_strong'), t('val_pwd_strong')];
    const cls  = ok ? (str >= 3 ? 'ok' : 'warn') : 'err';
    fb('fbMdp', mdpInp, cls, ok ? (msgs[str] || t('val_pwd_strong')) : t('val_pwd_weak'));
    return ok;
  }
  mdpInp.addEventListener('input', () => chkMdp(true));

  // Eye toggle
  document.getElementById('eyeBtn').addEventListener('click', function() {
    const show = mdpInp.type === 'password';
    mdpInp.type = show ? 'text' : 'password';
    this.textContent = show ? '🙈' : '👁';
  });

  // Rôle
  let selectedRole = '';
  function selectRole(role) {
    selectedRole = role;
    document.getElementById('roleHidden').value = role;
    document.getElementById('cardClient').classList.toggle('selected', role === 'client');
    document.getElementById('cardNutri').classList.toggle('selected', role === 'nutritionniste');
    const el = document.getElementById('fbRole');
    el.textContent = '✓ ' + (role === 'client' ? t('role_client_name') : t('role_nutri_name'));
    el.className = 'fb ok';
  }

  // Submit
  document.getElementById('formInscription').addEventListener('submit', function(e) {
    const okNom    = chkNom(true);
    const okPrenom = chkPrenom(true);
    const okEmail  = chkEmail(true);
    const okMdp    = chkMdp(true);
    let okRole = true;
    if (!selectedRole) {
      fb('fbRole', null, 'err', t('val_role_required'));
      okRole = false;
    }
    if (!okNom || !okPrenom || !okEmail || !okMdp || !okRole) e.preventDefault();
  });
</script>

<?php if (isset($_GET['succes']) && isset($_GET['user_id'])): ?>
<!-- face-api.js local -->
<script src="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/public/js/face-api.min.js"></script>
<script>
(function() {
  const USER_ID   = <?= (int) $_GET['user_id'] ?>;
  const MODEL_URL = '<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/public/models';

  const video    = document.getElementById('faceVideo');
  const canvas   = document.getElementById('faceCanvas');
  const ring     = document.getElementById('faceRing');
  const statusEl = document.getElementById('faceStatus');
  const msgEl    = document.getElementById('faceMsg');
  const btnScan  = document.getElementById('btnScanFace');

  let modelsLoaded = false;
  let stream       = null;
  let detectLoop   = null;
  let lastDescriptor = null;

  function setMsg(text, cls) {
    msgEl.textContent = text;
    msgEl.className   = 'face-msg ' + (cls || '');
  }

  async function init() {
    statusEl.textContent = 'Chargement des modèles IA…';
    try {
      await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
        faceapi.nets.faceLandmark68TinyNet.loadFromUri(MODEL_URL),
        faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
      ]);
      modelsLoaded = true;
      statusEl.textContent = 'Démarrage de la caméra…';
      await startCamera();
    } catch (err) {
      statusEl.textContent = 'Erreur de chargement';
      setMsg('❌ Impossible de charger les modèles : ' + err.message, 'err');
    }
  }

  async function startCamera() {
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
      video.srcObject = stream;
      video.onloadedmetadata = () => {
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        statusEl.textContent = 'Placez votre visage dans le cadre';
        btnScan.disabled = false;
        startDetectLoop();
      };
    } catch (err) {
      statusEl.textContent = 'Caméra inaccessible';
      setMsg('❌ Accès à la caméra refusé. Autorisez la caméra dans votre navigateur.', 'err');
    }
  }

  function startDetectLoop() {
    const opts = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 });
    detectLoop = setInterval(async () => {
      if (!modelsLoaded || video.paused || video.ended) return;
      const result = await faceapi
        .detectSingleFace(video, opts)
        .withFaceLandmarks(true)
        .withFaceDescriptor();

      const ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      if (result) {
        faceapi.draw.drawDetections(canvas, [result]);
        ring.classList.add('detected');
        ring.classList.remove('error');
        statusEl.textContent = '✅ Visage détecté';
        lastDescriptor = result.descriptor;
      } else {
        ring.classList.remove('detected', 'error');
        statusEl.textContent = 'Placez votre visage dans le cadre';
        lastDescriptor = null;
      }
    }, 300);
  }

  btnScan.addEventListener('click', async () => {
    if (!lastDescriptor) {
      setMsg('⚠️ Aucun visage détecté. Positionnez-vous face à la caméra.', 'err');
      ring.classList.add('error');
      return;
    }

    btnScan.disabled = true;
    btnScan.textContent = '⏳ Enregistrement…';
    setMsg('', '');

    const descriptorArray = Array.from(lastDescriptor);

    try {
      const fd = new FormData();
      fd.append('action',     'face_register');
      fd.append('id_user',    USER_ID);
      fd.append('descriptor', JSON.stringify(descriptorArray));

      const resp = await fetch('index.php', { method: 'POST', body: fd });
      const data = await resp.json();

      if (data.success) {
        clearInterval(detectLoop);
        if (stream) stream.getTracks().forEach(t => t.stop());
        ring.classList.add('detected');
        statusEl.textContent = '✅ Face ID enregistré !';
        setMsg('🎉 Votre Face ID a été enregistré avec succès !', 'ok');
        btnScan.textContent = '✅ Enregistré';
        setTimeout(() => { window.location.href = 'index.php?page=login'; }, 2000);
      } else {
        setMsg('❌ ' + (data.message || 'Erreur lors de l\'enregistrement.'), 'err');
        btnScan.disabled = false;
        btnScan.textContent = '📸 Réessayer';
      }
    } catch (err) {
      setMsg('❌ Erreur réseau : ' + err.message, 'err');
      btnScan.disabled = false;
      btnScan.textContent = '📸 Réessayer';
    }
  });

  // Démarrer automatiquement
  init();
})();
</script>
<?php endif; ?>
</body>
</html>
