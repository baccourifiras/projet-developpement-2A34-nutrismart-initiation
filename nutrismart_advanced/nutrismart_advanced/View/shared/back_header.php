<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>NutriSmart - Backoffice</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,900;1,600;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
<?php /* same style as front to preserve template family */ ?>
:root {
  --forest:#0d3b1f;--green:#1a6b3c;--lime:#4ade80;--lime-soft:#bbf7d0;--cream:#f7f3ed;--ivory:#fdfcf9;--sand:#e8e0d4;--ink:#0f1a12;--muted:#6b7c72;--white:#ffffff;--danger:#b91c1c;--shadow-sm:0 1px 3px rgba(13,59,31,.08),0 1px 2px rgba(13,59,31,.05);--shadow-md:0 4px 16px rgba(13,59,31,.10),0 2px 6px rgba(13,59,31,.06);--radius:14px;--radius-lg:22px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}html{scroll-behavior:smooth;}body{font-family:'DM Sans',sans-serif;background:var(--cream);color:var(--ink);min-height:100vh;display:flex;-webkit-font-smoothing:antialiased;}a{text-decoration:none;color:inherit;}
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
.msg-ok{display:flex;align-items:center;gap:10px;background:linear-gradient(135deg,#d1fae5,#ecfdf5);color:#065f46;border:1px solid #6ee7b7;padding:14px 18px;border-radius:var(--radius);font-size:14px;font-weight:500;margin-bottom:20px;box-shadow:var(--shadow-sm);}
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
.sidebar{width:220px;min-height:100vh;background:var(--forest);color:#fff;padding:24px 14px;display:flex;flex-direction:column;gap:2px;position:fixed;top:0;left:0;bottom:0;}
.sidebar-logo{font-family:'Playfair Display',serif;font-size:17px;font-weight:700;color:var(--lime);padding:0 8px 16px;border-bottom:1px solid rgba(255,255,255,.1);margin-bottom:10px;}
.sidebar-section{font-size:10px;font-weight:bold;text-transform:uppercase;color:rgba(255,255,255,.35);padding:12px 8px 4px;letter-spacing:1px;}
.sidebar a{display:block;padding:9px 12px;border-radius:9px;font-size:14px;color:rgba(255,255,255,.65);transition:all .15s;margin-bottom:1px;font-family:'DM Sans',sans-serif;}
.sidebar a:hover{background:rgba(255,255,255,.08);color:#fff;}
.sidebar a.actif{background:rgba(74,222,128,.15);color:var(--lime);font-weight:bold;}
.sidebar-footer{margin-top:auto;padding-top:14px;border-top:1px solid rgba(255,255,255,.1);}
.sidebar-footer a{display:block;padding:9px 12px;border-radius:9px;background:rgba(74,222,128,.1);color:var(--lime);font-size:14px;font-weight:bold;}
.main{margin-left:220px;padding:30px 32px;flex:1;display:flex;flex-direction:column;gap:20px;}
  </style>
</head>
<body>
<?php $pg = $page ?? ($_GET['page'] ?? 'stock'); $ac = $_GET['action'] ?? 'list'; ?>
<aside class="sidebar">
  <div class="sidebar-logo">NutriSmart<br><small style="font-size:11px;color:rgba(255,255,255,.4);font-weight:normal">Backoffice Admin</small></div>
  <div class="sidebar-section">Stocks</div>
  <a href="index.php?space=back&page=stock" class="<?= $pg === 'stock' && $ac === 'list' ? 'actif' : '' ?>">Liste des stocks</a>
  <a href="index.php?space=back&page=stock&action=add" class="<?= $pg === 'stock' && $ac === 'add' ? 'actif' : '' ?>">Ajouter un stock</a>
  <div class="sidebar-section">Listes de Courses</div>
  <a href="index.php?space=back&page=liste_courses" class="<?= $pg === 'liste_courses' && $ac === 'list' ? 'actif' : '' ?>">Liste des courses</a>
  <a href="index.php?space=back&page=liste_courses&action=add" class="<?= $pg === 'liste_courses' && $ac === 'add' ? 'actif' : '' ?>">Ajouter une liste</a>
  <div class="sidebar-footer">
    <a href="index.php">Front Office</a>
  </div>
</aside>
<div class="main">
