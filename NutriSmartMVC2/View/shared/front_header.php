<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>NutriSmart - Eat Smart, Live Smart</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
:root {
  --forest:#0d3b1f;
  --green:#1a6b3c;
  --lime:#4ade80;
  --lime-soft:#bbf7d0;
  --cream:#f7f3ed;
  --ivory:#fdfcf9;
  --sand:#e8e0d4;
  --gold:#c9a84c;
  --ink:#0f1a12;
  --muted:#6b7c72;
  --white:#ffffff;
  --danger:#b91c1c;
  --shadow-sm:0 1px 3px rgba(13,59,31,.08),0 1px 2px rgba(13,59,31,.05);
  --shadow-md:0 4px 16px rgba(13,59,31,.10),0 2px 6px rgba(13,59,31,.06);
  --shadow-lg:0 20px 60px rgba(13,59,31,.15),0 8px 24px rgba(13,59,31,.08);
  --radius:14px;
  --radius-lg:22px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--ink);min-height:100vh;display:flex;flex-direction:column;-webkit-font-smoothing:antialiased;}
a{text-decoration:none;color:inherit;}
::-webkit-scrollbar{width:6px;}
::-webkit-scrollbar-track{background:var(--cream);}
::-webkit-scrollbar-thumb{background:var(--sand);border-radius:999px;}
.navbar{position:sticky;top:0;z-index:100;background:rgba(253,252,249,.94);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(13,59,31,.08);padding:0 40px;height:68px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 0 rgba(13,59,31,.06);}
.navbar-logo{font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:var(--forest);display:flex;align-items:center;gap:10px;letter-spacing:-0.5px;}
.navbar-logo .ns-badge{width:36px;height:36px;background:linear-gradient(135deg,var(--forest),var(--green));border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--lime);font-size:13px;font-family:'DM Sans',sans-serif;font-weight:700;box-shadow:0 2px 8px rgba(13,59,31,.25);}
.navbar-links{display:flex;align-items:center;gap:4px;}
.navbar-links a{display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:10px;font-size:14px;font-weight:500;color:var(--muted);transition:all .2s cubic-bezier(.16,1,.3,1);}
.navbar-links a:hover{background:rgba(26,107,60,.07);color:var(--green);transform:translateY(-1px);}
.navbar-links a.actif{background:linear-gradient(135deg,rgba(26,107,60,.12),rgba(74,222,128,.08));color:var(--green);font-weight:600;}
.navbar-links .lien-admin{background:var(--forest);color:var(--lime);padding:8px 18px;font-weight:600;margin-left:8px;border-radius:10px;}
.navbar-links .lien-admin:hover{background:var(--ink);color:var(--lime-soft);transform:translateY(-1px);box-shadow:0 4px 12px rgba(13,59,31,.3);}
.hero{position:relative;background:linear-gradient(135deg,var(--forest) 0%,#1a4a2a 40%,var(--green) 100%);color:var(--white);padding:56px 40px 52px;overflow:hidden;}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 80% 50%,rgba(74,222,128,.10) 0%,transparent 70%),radial-gradient(ellipse 40% 60% at 10% 80%,rgba(201,168,76,.07) 0%,transparent 60%);pointer-events:none;}
.hero-inner{position:relative;z-index:1;max-width:1100px;margin:0 auto;}
.hero-breadcrumb{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);padding:5px 12px;border-radius:999px;font-size:12px;color:rgba(255,255,255,.7);margin-bottom:20px;font-weight:500;}
.hero h1{font-family:'Playfair Display',serif;font-size:42px;font-weight:700;line-height:1.1;letter-spacing:-1px;margin-bottom:12px;}
.hero h1 span{color:var(--lime);font-style:italic;}
.hero p{font-size:15px;color:rgba(255,255,255,.65);max-width:460px;line-height:1.6;}
.container{max-width:1100px;margin:0 auto;padding:36px 40px 72px;flex:1;}
.msg-ok{display:flex;align-items:center;gap:10px;background:linear-gradient(135deg,#d1fae5,#ecfdf5);color:#065f46;border:1px solid #6ee7b7;padding:14px 18px;border-radius:var(--radius);font-size:14px;font-weight:500;margin-bottom:20px;box-shadow:var(--shadow-sm);}
.card{background:var(--ivory);border:1px solid var(--sand);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow-md);}
.card-header{padding:20px 28px;border-bottom:1px solid var(--sand);background:linear-gradient(135deg,var(--ivory),rgba(232,224,212,.3));display:flex;align-items:center;justify-content:space-between;gap:12px;}
.card-header h2{font-family:'Playfair Display',serif;font-size:18px;font-weight:600;color:var(--forest);display:flex;align-items:center;gap:10px;}
.count-badge{display:inline-flex;align-items:center;justify-content:center;background:var(--lime-soft);color:var(--forest);border-radius:999px;padding:2px 10px;font-size:12px;font-weight:600;font-family:'DM Sans',sans-serif;}
table{width:100%;border-collapse:collapse;}
thead th{padding:12px 24px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:1px;color:var(--muted);background:rgba(232,224,212,.25);border-bottom:1px solid var(--sand);}
tbody td{padding:16px 24px;font-size:14px;border-bottom:1px solid rgba(232,224,212,.5);vertical-align:middle;color:var(--ink);}
tbody tr:last-child td{border-bottom:none;}
.vide td{text-align:center;padding:56px;color:var(--muted);font-size:14px;}
.badge-budget{display:inline-flex;align-items:center;gap:4px;background:linear-gradient(135deg,var(--lime-soft),rgba(74,222,128,.15));color:var(--forest);border:1px solid rgba(74,222,128,.4);padding:5px 13px;border-radius:999px;font-size:13px;font-weight:600;}
.row-num{display:inline-flex;width:30px;height:30px;border-radius:8px;background:var(--sand);color:var(--muted);align-items:center;justify-content:center;font-size:12px;font-weight:600;}
.date-text{color:var(--muted);font-size:13px;}
.articles-text{max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.actions-group{display:flex;gap:6px;}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:all .2s cubic-bezier(.16,1,.3,1);white-space:nowrap;font-family:'DM Sans',sans-serif;}
.btn-vert{background:linear-gradient(135deg,var(--green),#15803d);color:#fff;box-shadow:0 3px 10px rgba(26,107,60,.35);}
.btn-bleu{background:rgba(37,99,235,.08);color:#1d4ed8;border:1px solid rgba(37,99,235,.2);}
.btn-rouge{background:rgba(185,28,28,.07);color:var(--danger);border:1px solid rgba(185,28,28,.15);}
.btn-gris{background:var(--sand);color:var(--muted);border:1px solid rgba(13,59,31,.08);}
.btn-sm{padding:7px 14px;font-size:12px;border-radius:8px;}
.form-page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px;}
.form-page-header h1{font-family:'Playfair Display',serif;font-size:28px;color:var(--forest);letter-spacing:-0.5px;}
.form-box{background:var(--ivory);border:1px solid var(--sand);border-radius:var(--radius-lg);padding:36px;max-width:580px;box-shadow:var(--shadow-md);}
.form-group{display:flex;flex-direction:column;gap:7px;margin-bottom:20px;}
.form-group label{font-size:13px;font-weight:600;color:var(--forest);display:flex;align-items:center;gap:6px;}
.form-group label small{color:var(--muted);font-weight:400;font-size:12px;}
.form-group input,.form-group select{padding:12px 16px;border:1.5px solid var(--sand);border-radius:10px;font-size:14px;font-family:'DM Sans',sans-serif;color:var(--ink);background:var(--white);outline:none;transition:all .2s;appearance:none;-webkit-appearance:none;}
.form-group input.champ-err{border-color:var(--danger);box-shadow:0 0 0 3px rgba(185,28,28,.08);}
.err-msg{font-size:12px;color:var(--danger);font-weight:500;min-height:18px;}
.form-actions{display:flex;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid var(--sand);}
.accueil-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px;}
.accueil-card{background:var(--ivory);border:1px solid var(--sand);border-radius:var(--radius-lg);padding:36px 32px;text-align:left;box-shadow:var(--shadow-md);}
.accueil-card .ico{width:58px;height:58px;background:linear-gradient(135deg,var(--forest),var(--green));border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:16px;margin-bottom:20px;color:#fff;box-shadow:0 4px 14px rgba(13,59,31,.25);}
.accueil-card h3{font-family:'Playfair Display',serif;font-size:21px;font-weight:700;color:var(--forest);margin-bottom:10px;}
.accueil-card p{font-size:14px;color:var(--muted);margin-bottom:24px;line-height:1.6;}
.accueil-card a{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--green),#15803d);color:#fff;padding:11px 22px;border-radius:10px;font-size:13px;font-weight:600;}
footer{text-align:center;padding:24px 40px;font-size:13px;color:var(--muted);border-top:1px solid var(--sand);background:var(--ivory);margin-top:auto;}
@media (max-width:768px){.navbar{padding:0 20px;}.hero{padding:32px 20px;}.hero h1{font-size:28px;}.container{padding:24px 20px 48px;}.accueil-grid{grid-template-columns:1fr;}.form-box{padding:24px;}}
  </style>
</head>
<body>
<?php $pg = $page ?? ($_GET['page'] ?? 'accueil'); ?>
<nav class="navbar">
  <a href="index.php" class="navbar-logo">
    <div class="ns-badge">NS</div>
    NutriSmart
  </a>
  <div class="navbar-links">
    <a href="index.php" class="<?= $pg === 'accueil' ? 'actif' : '' ?>">
      <span>Accueil</span>
    </a>
    <a href="index.php?page=stock" class="<?= $pg === 'stock' ? 'actif' : '' ?>">
      <span>Mes Stocks</span>
    </a>
    <a href="index.php?page=liste_courses" class="<?= $pg === 'liste_courses' ? 'actif' : '' ?>">
      <span>Mes Courses</span>
    </a>
    <a href="index.php?space=back&page=stock" class="lien-admin">
      Admin
    </a>
  </div>
</nav>
