<?php $currentPage = $_GET['page'] ?? ''; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NutriSmart — <?= htmlspecialchars($pageTitle ?? 'Accueil') ?></title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

    :root {
      --primary: #16a34a;
      --primary-dark: #15803d;
      --primary-light: #dcfce7;
      --secondary: #7c3aed;
      --danger: #dc2626;
      --danger-light: #fee2e2;
      --warning: #d97706;
      --warning-light: #fef3c7;
      --text: #0f172a;
      --muted: #64748b;
      --border: #e2e8f0;
      --bg: #f8fafc;
      --surface: #ffffff;
      --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
      --shadow-lg: 0 4px 6px rgba(0,0,0,.05), 0 10px 40px rgba(0,0,0,.1);
      --radius: 12px;
      --radius-lg: 20px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
    a { text-decoration: none; color: inherit; }
    img, button, input, select, textarea { font: inherit; }

    /* ── Navbar ── */
    .navbar {
      position: sticky; top: 0; z-index: 999;
      background: rgba(255,255,255,.95);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
      padding: 0 40px;
      display: flex; align-items: center; justify-content: space-between;
      height: 66px;
      box-shadow: 0 1px 0 var(--border), var(--shadow);
    }
    .nav-brand { display: flex; align-items: center; gap: 12px; }
    .nav-logo {
      width: 42px; height: 42px; border-radius: 12px;
      background: linear-gradient(135deg, var(--primary), #059669);
      display: grid; place-items: center;
      color: #fff; font-weight: 800; font-size: 15px;
      box-shadow: 0 4px 12px rgba(22,163,74,.35);
    }
    .nav-brand-name { font-size: 20px; font-weight: 800; color: var(--text); }
    .nav-brand-tag { font-size: 10px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .1em; }
    .nav-links { display: flex; align-items: center; gap: 2px; }
    .nav-links a {
      padding: 8px 16px; border-radius: 8px;
      font-size: 14px; font-weight: 500; color: var(--muted);
      transition: all .15s;
    }
    .nav-links a:hover { background: #f1f5f9; color: var(--text); }
    .nav-links a.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
    .nav-links .nav-admin {
      background: var(--text); color: #fff;
      padding: 8px 18px; border-radius: 8px; font-weight: 600;
    }
    .nav-links .nav-admin:hover { background: #1e293b; color: #fff; }

    /* ── Hero ── */
    .hero-section {
      background: linear-gradient(135deg, #0f4c2a 0%, #1a6b3c 50%, #0d3b22 100%);
      padding: 80px 40px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .hero-section::before {
      content: '';
      position: absolute; inset: 0;
      background: radial-gradient(ellipse at top, rgba(22,163,74,.3), transparent 70%);
    }
    .hero-content { position: relative; z-index: 1; max-width: 700px; margin: 0 auto; }
    .hero-kicker {
      display: inline-block;
      background: rgba(255,255,255,.12);
      color: #86efac;
      padding: 6px 16px; border-radius: 999px;
      font-size: 13px; font-weight: 600;
      margin-bottom: 20px;
      border: 1px solid rgba(255,255,255,.15);
    }
    .hero-content h1 {
      font-size: clamp(32px, 5vw, 54px);
      font-weight: 900;
      color: #fff;
      line-height: 1.1;
      margin-bottom: 16px;
    }
    .hero-content h1 span { color: #4ade80; }
    .hero-sub { font-size: 17px; color: rgba(255,255,255,.75); line-height: 1.7; margin-bottom: 32px; }
    .hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }
    .btn-white {
      background: #fff; color: var(--primary);
      padding: 12px 28px; border-radius: 10px;
      font-weight: 700; font-size: 15px;
      box-shadow: 0 4px 14px rgba(0,0,0,.15);
      transition: all .2s;
    }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.2); }
    .btn-outline {
      background: rgba(255,255,255,.12);
      color: #fff; border: 1.5px solid rgba(255,255,255,.3);
      padding: 12px 28px; border-radius: 10px;
      font-weight: 600; font-size: 15px;
      transition: all .2s;
    }
    .btn-outline:hover { background: rgba(255,255,255,.2); }

    /* ── Features ── */
    .features-section { padding: 60px 40px; max-width: 1100px; margin: 0 auto; }
    .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
    .feature-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 32px;
      box-shadow: var(--shadow);
      transition: all .2s;
    }
    .feature-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
    .feature-icon { font-size: 36px; margin-bottom: 16px; }
    .feature-card h3 { font-size: 17px; font-weight: 700; margin-bottom: 10px; }
    .feature-card p { font-size: 14px; color: var(--muted); line-height: 1.7; margin-bottom: 20px; }
    .feature-link { font-size: 14px; font-weight: 600; color: var(--primary); }
    .feature-link:hover { text-decoration: underline; }

    /* ── Page header ── */
    .page-header {
      background: linear-gradient(135deg, #0f4c2a, #1a6b3c);
      padding: 48px 40px;
      color: #fff;
    }
    .page-header .ph-kicker {
      display: inline-block;
      background: rgba(255,255,255,.15);
      color: #86efac;
      padding: 4px 14px; border-radius: 999px;
      font-size: 12px; font-weight: 600;
      margin-bottom: 12px;
    }
    .page-header h1 { font-size: clamp(26px, 4vw, 38px); font-weight: 900; line-height: 1.2; }
    .page-header h1 span { color: #4ade80; }
    .page-header p { color: rgba(255,255,255,.7); margin-top: 8px; font-size: 15px; }

    /* ── Container ── */
    .container { max-width: 1100px; margin: 0 auto; padding: 36px 40px 60px; }

    /* ── Alerts ── */
    .alert {
      padding: 14px 18px; border-radius: 10px;
      font-size: 14px; font-weight: 500;
      margin-bottom: 20px;
      display: flex; align-items: center; gap: 10px;
    }
    .alert-success { background: var(--primary-light); color: #14532d; border: 1px solid #86efac; }
    .alert-warning { background: var(--warning-light); color: #78350f; border: 1px solid #fcd34d; }
    .alert-danger  { background: var(--danger-light);  color: #7f1d1d; border: 1px solid #fca5a5; }

    /* ── Stats ── */
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 24px;
      text-align: center;
      box-shadow: var(--shadow);
    }
    .stat-val { font-size: 40px; font-weight: 800; color: var(--primary); line-height: 1; }
    .stat-lbl { font-size: 12px; font-weight: 600; color: var(--muted); margin-top: 6px; text-transform: uppercase; letter-spacing: .06em; }

    /* ── Table card ── */
    .table-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      overflow: hidden;
    }
    .table-card-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
      background: #fafafa;
    }
    .table-card-header h2 { font-size: 17px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      padding: 12px 18px; text-align: left;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .07em;
      color: var(--muted);
      background: #f8fafc;
      border-bottom: 1px solid var(--border);
    }
    tbody td {
      padding: 14px 18px; font-size: 14px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
    }
    tbody tr:last-child td { border-bottom: none; }
    tbody tr:hover td { background: #f8fafc; }

    /* ── Badges ── */
    .badge {
      display: inline-block; padding: 4px 12px;
      border-radius: 999px; font-size: 12px; font-weight: 600;
    }
    .badge-green  { background: var(--primary-light); color: #14532d; }
    .badge-orange { background: var(--warning-light);  color: #78350f; }
    .badge-red    { background: var(--danger-light);   color: #7f1d1d; }
    .badge-blue   { background: #dbeafe; color: #1e3a8a; }
    .badge-purple { background: #ede9fe; color: #4c1d95; }

    /* ── Buttons ── */
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 10px 20px; border-radius: 9px;
      font-size: 14px; font-weight: 600;
      cursor: pointer; border: none; transition: all .15s;
      text-decoration: none;
    }
    .btn-primary { background: var(--primary); color: #fff; }
    .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
    .btn-secondary { background: #f1f5f9; color: var(--text); border: 1px solid var(--border); }
    .btn-secondary:hover { background: #e2e8f0; }
    .btn-sm { padding: 6px 14px; font-size: 13px; border-radius: 7px; }
    .btn-edit { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .btn-edit:hover { background: #dbeafe; }
    .btn-delete { background: var(--danger-light); color: var(--danger); border: 1px solid #fca5a5; }
    .btn-delete:hover { background: #fecaca; }

    /* ── Form ── */
    .form-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 36px;
    }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; }
    .form-group { display: flex; flex-direction: column; gap: 7px; }
    .form-group.full { grid-column: 1/-1; }
    .form-group label { font-size: 13px; font-weight: 600; color: var(--text); }
    .form-group input, .form-group select, .form-group textarea {
      padding: 11px 14px;
      border: 1.5px solid var(--border);
      border-radius: 9px;
      font-size: 14px; color: var(--text);
      background: #fff; outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .form-group input:focus, .form-group select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(22,163,74,.12);
    }
    .form-group input.is-invalid, .form-group select.is-invalid {
      border-color: var(--danger);
      box-shadow: 0 0 0 3px rgba(220,38,38,.1);
    }
    .error-msg { font-size: 12px; font-weight: 500; color: var(--danger); min-height: 16px; }
    .form-actions { grid-column: 1/-1; display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid var(--border); margin-top: 8px; }

    /* ── Empty state ── */
    .empty-state { text-align: center; padding: 64px 32px; color: var(--muted); }
    .empty-state .empty-icon { font-size: 56px; margin-bottom: 16px; }
    .empty-state h3 { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
    .empty-state p { margin-bottom: 24px; }

    /* ── Footer ── */
    .site-footer {
      text-align: center; padding: 28px;
      font-size: 13px; color: var(--muted);
      border-top: 1px solid var(--border);
    }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="nav-brand">
    <div class="nav-logo">NS</div>
    <div>
      <div class="nav-brand-name">NutriSmart</div>
      <div class="nav-brand-tag">Eat Smart Live Smart</div>
    </div>
  </div>
  <div class="nav-links">
    <a href="index.php" class="<?= $currentPage==='' ? 'active':'' ?>">🏠 Accueil</a>
    <a href="index.php?page=stock" class="<?= $currentPage==='stock' ? 'active':'' ?>">📦 Mes Stocks</a>
    <a href="index.php?page=liste_courses" class="<?= $currentPage==='liste_courses' ? 'active':'' ?>">🛒 Mes Courses</a>
    <a href="../backoffice/index.php" class="nav-admin">⚙ Admin</a>
  </div>
</nav>
