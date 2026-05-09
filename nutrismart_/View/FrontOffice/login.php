<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NutriSmart — Connexion</title>
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
      position: absolute;
      inset: 0;
      background:
        radial-gradient(circle at 20% 80%, rgba(255,255,255,.07) 0%, transparent 50%),
        radial-gradient(circle at 80% 15%, rgba(255,255,255,.05) 0%, transparent 40%);
    }
    .deco-ring {
      position: absolute;
      border-radius: 50%;
      border: 56px solid rgba(255,255,255,.05);
    }
    .deco-ring-1 { width: 380px; height: 380px; right: -90px; top: 50%; transform: translateY(-50%); }
    .deco-ring-2 { width: 260px; height: 260px; left: -80px; bottom: 60px; border-width: 40px; }

    .hero-brand { position: relative; z-index: 1; }
    .hero-logo  { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: -.02em; }
    .hero-tagline { font-size: 10px; font-weight: 700; color: rgba(255,255,255,.55); letter-spacing: .18em; text-transform: uppercase; margin-top: 3px; }

    .hero-body { position: relative; z-index: 1; }
    .hero-body h2 { font-size: clamp(1.9rem, 2.8vw, 2.8rem); font-weight: 900; color: #fff; line-height: 1.15; margin: 0 0 18px; }
    .hero-body p  { color: rgba(255,255,255,.72); font-size: 15px; line-height: 1.75; max-width: 360px; margin: 0 0 36px; }

    .hero-stats { display: flex; gap: 32px; }
    .stat-num   { font-size: 26px; font-weight: 900; color: #fff; }
    .stat-lbl   { font-size: 11px; color: rgba(255,255,255,.5); font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }

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
      padding: 64px 72px;
      background: #fafdfb;
      position: relative;
    }
    .auth-panel::before {
      content: ''; position: absolute; top: 0; left: 0;
      width: 1px; height: 100%;
      background: linear-gradient(180deg, transparent, rgba(31,164,99,.2), transparent);
    }

    .back-link {
      display: inline-flex; align-items: center; gap: 6px;
      font-size: 13px; font-weight: 600; color: #638070;
      text-decoration: none; margin-bottom: 36px;
      transition: color .2s;
    }
    .back-link:hover { color: #1fa463; }

    .auth-title { font-size: 2rem; font-weight: 900; color: #10281b; margin: 0 0 8px; letter-spacing: -.02em; }
    .auth-sub   { color: #638070; font-size: 14px; margin: 0 0 32px; }

    /* Alert */
    .alert {
      display: flex; align-items: center; gap: 10px;
      padding: 13px 16px; border-radius: 12px;
      font-size: 13px; font-weight: 600; margin-bottom: 24px;
    }
    .alert-err { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

    /* Form */
    .auth-form { display: flex; flex-direction: column; gap: 20px; }

    .f-group { display: flex; flex-direction: column; gap: 6px; }
    .f-group label { font-size: 13px; font-weight: 700; color: #10281b; }

    .f-wrap { position: relative; }
    .f-icon {
      position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
      font-size: 15px; pointer-events: none; opacity: .45;
    }
    .f-group input {
      width: 100%; padding: 13px 14px 13px 42px;
      border: 1.5px solid #ddeee5; border-radius: 14px;
      background: #fff; color: #10281b; font-size: 14px;
      font-family: inherit; transition: border-color .2s, box-shadow .2s;
      box-sizing: border-box;
    }
    .f-group input:focus { outline: none; border-color: #1fa463; box-shadow: 0 0 0 4px rgba(31,164,99,.1); }
    .f-group input.invalid { border-color: #ef4444 !important; box-shadow: 0 0 0 4px rgba(239,68,68,.08) !important; }
    .f-group input.valid   { border-color: #16a34a !important; box-shadow: 0 0 0 4px rgba(22,163,74,.08) !important; }

    .f-group .has-eye input { padding-right: 46px; }
    .eye-btn {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      font-size: 16px; color: #638070; padding: 4px; transition: color .2s;
    }
    .eye-btn:hover { color: #1fa463; }

    .fb { font-size: 12px; font-weight: 600; min-height: 16px; }
    .fb.err { color: #dc2626; }
    .fb.ok  { color: #16a34a; }

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

    .auth-foot { text-align: center; font-size: 13px; color: #638070; margin-top: 20px; }
    .auth-foot a { color: #1fa463; font-weight: 700; text-decoration: none; transition: color .2s; }
    .auth-foot a:hover { color: #0f6c42; }

    @media (max-width: 900px) {
      .auth-wrapper { grid-template-columns: 1fr; }
      .auth-hero { display: none; }
      .auth-panel { padding: 40px 24px; }
    }
  </style>
  <script src="public/js/lang.js"></script>
</head>
<body data-page-title="page_title_login">
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
      <h2><span data-i18n="login_hero_title">Votre santé,<br>notre priorité.</span></h2>
      <p data-i18n="login_hero_sub">Accédez à votre espace personnalisé pour suivre votre nutrition, consulter vos nutritionnistes et atteindre vos objectifs bien-être.</p>
      <div class="hero-stats">
        <div>
          <div class="stat-num">500+</div>
          <div class="stat-lbl" data-i18n="stat_users_lbl">Utilisateurs</div>
        </div>
        <div>
          <div class="stat-num">50+</div>
          <div class="stat-lbl" data-i18n="stat_nutri_lbl">Nutritionnistes</div>
        </div>
        <div>
          <div class="stat-num">98%</div>
          <div class="stat-lbl" data-i18n="stat_sat_lbl">Satisfaction</div>
        </div>
      </div>
    </div>

    <div class="hero-pills">
      <div class="pill" data-i18n="pill_secure">🔒 Données sécurisées</div>
      <div class="pill" data-i18n="pill_certified">✅ Certifié</div>
      <div class="pill" data-i18n="pill_instant">⚡ Accès instantané</div>
    </div>
  </div>

  <!-- PANNEAU DROIT -->
  <div class="auth-panel">
    <a href="index.php?page=accueil" class="back-link" data-i18n="back_home">← Retour à l'accueil</a>
    <h1 class="auth-title" data-i18n="login_title">Bon retour 👋</h1>
    <p class="auth-sub" data-i18n="login_sub">Connectez-vous pour accéder à votre espace NutriSmart.</p>

    <?php if (isset($erreur) && $erreur === 'acces'): ?>
      <div class="alert alert-err"><span data-i18n="err_access">⚠️ Accès refusé. Veuillez vous connecter.</span></div>
    <?php elseif (isset($erreur) && $erreur === 'identifiants'): ?>
      <div class="alert alert-err"><span data-i18n="err_credentials">❌ Email ou mot de passe incorrect.</span></div>
    <?php elseif (isset($erreur) && $erreur === 'champs_vides'): ?>
      <div class="alert alert-err"><span data-i18n="err_fields">⚠️ Veuillez remplir tous les champs.</span></div>
    <?php endif; ?>

    <form id="formLogin" class="auth-form" action="index.php" method="POST" novalidate>
      <input type="hidden" name="action" value="login" />

      <div class="f-group">
        <label for="email" data-i18n="label_email">Adresse email</label>
        <div class="f-wrap">
          <span class="f-icon">📧</span>
          <input id="email" type="email" name="email" placeholder="exemple@mail.com" data-i18n-placeholder="placeholder_email" autocomplete="email" />
        </div>
        <div id="fbEmail" class="fb"></div>
      </div>

      <div class="f-group">
        <label for="mot_de_passe" data-i18n="label_password">Mot de passe</label>
        <div class="f-wrap has-eye">
          <span class="f-icon">🔑</span>
          <input id="mot_de_passe" type="password" name="mot_de_passe" placeholder="Votre mot de passe" data-i18n-placeholder="placeholder_password" autocomplete="current-password" />
          <button type="button" class="eye-btn" id="eyeBtn">👁</button>
        </div>
        <div id="fbMdp" class="fb"></div>
      </div>

      <button type="submit" class="btn-submit" data-i18n="btn_login_submit">Se connecter →</button>
    </form>

    <div class="auth-sep"><span data-i18n="login_sep">OU</span></div>
    <p class="auth-foot"><span data-i18n="login_no_account">Pas encore de compte ?</span> <a href="index.php?page=inscription" data-i18n="login_create">Créer un compte gratuitement</a></p>
  </div>
</div>

<script>
  const RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const eml  = document.getElementById('email');
  const mdp  = document.getElementById('mot_de_passe');
  const fbe  = document.getElementById('fbEmail');
  const fbm  = document.getElementById('fbMdp');
  const eye  = document.getElementById('eyeBtn');

  function fb(el, inp, ok, msg) {
    el.textContent = msg;
    el.className   = 'fb ' + (ok ? 'ok' : 'err');
    inp.classList.toggle('valid',   ok);
    inp.classList.toggle('invalid', !ok);
  }

  function chkEmail(force) {
    if (!eml.value && !force) return true;
    const ok = RE.test(eml.value.trim());
    fb(fbe, eml, ok, ok ? t('val_email_ok') : t('val_email_invalid'));
    return ok;
  }

  function chkMdp(force) {
    if (!mdp.value && !force) return true;
    const ok = mdp.value.length >= 4;
    fb(fbm, mdp, ok, ok ? t('val_pwd_ok') : t('val_pwd_short'));
    return ok;
  }

  eml.addEventListener('blur',  () => chkEmail(true));
  eml.addEventListener('input', () => { if (eml.classList.contains('invalid')) chkEmail(false); });
  mdp.addEventListener('input', () => chkMdp(true));

  eye.addEventListener('click', () => {
    const show = mdp.type === 'password';
    mdp.type = show ? 'text' : 'password';
    eye.textContent = show ? '🙈' : '👁';
  });

  document.getElementById('formLogin').addEventListener('submit', function(e) {
    const ok = chkEmail(true) & chkMdp(true);
    if (!ok) e.preventDefault();
  });
</script>
</body>
</html>
