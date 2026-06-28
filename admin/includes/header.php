<?php
// Hitung notif
$notifBooking = $conn->query("SELECT COUNT(*) as c FROM booking WHERE status='pending'")->fetch_assoc()['c'];
$notifKontak  = $conn->query("SELECT COUNT(*) as c FROM kontak WHERE status='belum_dibaca'")->fetch_assoc()['c'];
$currentPage  = basename($_SERVER['PHP_SELF'], '.php');

function sideActive(string $page, string $current): string {
    return strpos($current, $page) !== false ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= $pageTitle ?? 'Admin Panel' ?> | Dealer Mitsubishi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    :root{--primary:#dc2626;--primary-dark:#b91c1c;--sidebar-bg:#0f172a;--sidebar-w:260px;--header-h:68px;--dark:#111827;--gray:#6b7280;--lighter:#f3f4f6;--shadow:0 4px 20px rgba(0,0,0,.08);--shadow-lg:0 10px 40px rgba(0,0,0,.14);--radius:16px}
    *{margin:0;padding:0;box-sizing:border-box}
    body{font-family:'Poppins',sans-serif;background:#f1f5f9;color:var(--dark);display:flex;min-height:100vh}
    /* SIDEBAR */
    .sidebar{width:var(--sidebar-w);background:var(--sidebar-bg);position:fixed;top:0;left:0;height:100vh;display:flex;flex-direction:column;z-index:100;transition:.3s;overflow-y:auto}
    .sidebar-brand{padding:22px 22px 18px;border-bottom:1px solid rgba(255,255,255,.07)}
    .sidebar-brand .logo{display:flex;align-items:center;gap:12px;text-decoration:none}
    .logo-icon{width:44px;height:44px;background:linear-gradient(135deg,#dc2626,#ef4444);border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:18px;flex-shrink:0}
    .logo-text h2{font-size:15px;color:white;font-weight:700;line-height:1.2}
    .logo-text p{font-size:10px;color:rgba(255,255,255,.4);margin-top:1px}
    .sidebar-menu{flex:1;padding:18px 14px}
    .menu-label{font-size:10px;text-transform:uppercase;letter-spacing:1.2px;color:rgba(255,255,255,.25);padding:14px 10px 8px;font-weight:600}
    .menu-item{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:12px;text-decoration:none;color:rgba(255,255,255,.55);font-size:13.5px;font-weight:500;transition:.25s;margin-bottom:3px;position:relative}
    .menu-item:hover{background:rgba(255,255,255,.07);color:white}
    .menu-item.active{background:linear-gradient(135deg,rgba(220,38,38,.25),rgba(239,68,68,.15));color:white;border:1px solid rgba(220,38,38,.2)}
    .menu-item i{width:20px;text-align:center;font-size:15px}
    .menu-badge{margin-left:auto;background:#ef4444;color:white;font-size:10px;font-weight:700;padding:2px 7px;border-radius:50px;min-width:20px;text-align:center}
    .sidebar-footer{padding:16px 14px;border-top:1px solid rgba(255,255,255,.07)}
    .admin-profile{display:flex;align-items:center;gap:12px;padding:12px;border-radius:12px;background:rgba(255,255,255,.06)}
    .admin-avatar{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-size:15px;font-weight:700;flex-shrink:0}
    .admin-info h4{font-size:13px;color:white;font-weight:600}
    .admin-info span{font-size:11px;color:rgba(255,255,255,.4)}
    .logout-btn{display:flex;align-items:center;gap:8px;padding:11px 14px;border-radius:12px;color:rgba(255,255,255,.45);font-size:13px;text-decoration:none;transition:.25s;margin-top:8px;background:none;border:none;cursor:pointer;font-family:'Poppins',sans-serif;width:100%}
    .logout-btn:hover{background:rgba(220,38,38,.15);color:#fca5a5}
    /* MAIN */
    .main-wrap{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh}
    .topbar{height:var(--header-h);background:white;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:50;box-shadow:0 1px 8px rgba(0,0,0,.06)}
    .topbar-left{display:flex;align-items:center;gap:14px}
    .page-title-bar h1{font-size:18px;font-weight:700;color:var(--dark)}
    .page-title-bar p{font-size:12px;color:var(--gray)}
    .topbar-right{display:flex;align-items:center;gap:16px}
    .topbar-btn{width:38px;height:38px;border-radius:10px;background:var(--lighter);border:none;cursor:pointer;color:var(--gray);display:flex;align-items:center;justify-content:center;transition:.25s;text-decoration:none;position:relative}
    .topbar-btn:hover{background:rgba(220,38,38,.1);color:var(--primary)}
    .notif-dot{position:absolute;top:6px;right:6px;width:8px;height:8px;background:#ef4444;border-radius:50%;border:2px solid white}
    .topbar-admin{display:flex;align-items:center;gap:10px;padding:6px 14px;background:var(--lighter);border-radius:12px;cursor:pointer}
    .topbar-admin img,.topbar-admin-av{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#dc2626,#ef4444);display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:700}
    .topbar-admin span{font-size:13px;font-weight:600;color:var(--dark)}
    /* CONTENT */
    .content{padding:28px;flex:1}
    /* CARDS */
    .stat-card{background:white;border-radius:var(--radius);padding:22px;box-shadow:var(--shadow);display:flex;align-items:center;gap:18px;transition:.3s;border-left:4px solid transparent}
    .stat-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-lg)}
    .stat-icon{width:54px;height:54px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;color:white;flex-shrink:0}
    .stat-info h3{font-size:28px;font-weight:800;color:var(--dark)}
    .stat-info p{font-size:13px;color:var(--gray);margin-top:2px}
    /* TABLE */
    .card{background:white;border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}
    .card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
    .card-header h3{font-size:16px;font-weight:700;color:var(--dark);display:flex;align-items:center;gap:10px}
    .card-header h3 i{color:var(--primary)}
    .card-body{padding:0}
    table{width:100%;border-collapse:collapse}
    thead th{background:#f8fafc;padding:12px 18px;text-align:left;font-size:12px;font-weight:600;color:var(--gray);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
    tbody td{padding:14px 18px;font-size:13.5px;color:var(--dark);border-bottom:1px solid #f0f0f0;vertical-align:middle}
    tbody tr:last-child td{border:none}
    tbody tr:hover{background:#fafbfc}
    .badge{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:50px;font-size:11px;font-weight:600}
    .badge-success{background:rgba(22,163,74,.1);color:#16a34a}
    .badge-warning{background:rgba(217,119,6,.1);color:#b45309}
    .badge-danger{background:rgba(220,38,38,.1);color:#dc2626}
    .badge-info{background:rgba(37,99,235,.1);color:#2563eb}
    .badge-secondary{background:#f1f5f9;color:#64748b}
    .btn-admin{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;transition:.25s;text-decoration:none;border:none;font-family:'Poppins',sans-serif}
    .btn-primary-a{background:linear-gradient(135deg,#dc2626,#ef4444);color:white}
    .btn-primary-a:hover{opacity:.88;transform:translateY(-1px)}
    .btn-outline{background:white;color:var(--gray);border:1.5px solid #e5e7eb}
    .btn-outline:hover{border-color:var(--primary);color:var(--primary)}
    .btn-danger{background:rgba(220,38,38,.1);color:#dc2626}
    .btn-danger:hover{background:#dc2626;color:white}
    .btn-success{background:rgba(22,163,74,.1);color:#16a34a}
    .btn-success:hover{background:#16a34a;color:white}
    .btn-sm{padding:6px 12px;font-size:12px}
    /* FORM */
    .form-card{background:white;border-radius:var(--radius);padding:28px;box-shadow:var(--shadow)}
    .form-group-a{margin-bottom:20px}
    .form-group-a label{display:block;font-size:13px;font-weight:600;color:var(--dark);margin-bottom:8px}
    .form-group-a input,.form-group-a select,.form-group-a textarea{width:100%;padding:11px 14px;border:1.5px solid #e5e7eb;border-radius:12px;font-size:14px;font-family:'Poppins',sans-serif;color:var(--dark);background:#f9fafb;transition:.3s;box-sizing:border-box}
    .form-group-a input:focus,.form-group-a select:focus,.form-group-a textarea:focus{outline:none;border-color:var(--primary);background:white;box-shadow:0 0 0 3px rgba(220,38,38,.1)}
    .form-group-a textarea{resize:vertical;min-height:100px}
    .form-row-a{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .alert-a{padding:13px 18px;border-radius:12px;font-size:13px;display:flex;align-items:center;gap:10px;margin-bottom:20px}
    .alert-success-a{background:rgba(22,163,74,.1);border:1px solid rgba(22,163,74,.2);color:#166534}
    .alert-error-a{background:rgba(220,38,38,.1);border:1px solid rgba(220,38,38,.2);color:#dc2626}
    /* MOBILE */
    .mobile-toggle-admin{display:none;background:none;border:none;cursor:pointer;color:var(--gray);font-size:20px}
    @media(max-width:900px){
      .sidebar{transform:translateX(-100%)}
      .sidebar.open{transform:translateX(0)}
      .main-wrap{margin-left:0}
      .mobile-toggle-admin{display:block}
      .form-row-a{grid-template-columns:1fr}
    }
    .overlay-sidebar{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99}
    .overlay-sidebar.show{display:block}
  </style>
</head>
<body>
<div class="overlay-sidebar" id="sideOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <a href="/mitsubishi/admin/dashboard.php" class="logo">
      <div class="logo-icon"><i class="fa-solid fa-car-side"></i></div>
      <div class="logo-text"><h2>Dealer Mitsubishi</h2><p>Panel Administrasi</p></div>
    </a>
  </div>
  <nav class="sidebar-menu">
    <div class="menu-label">Menu Utama</div>
    <a href="/mitsubishi/admin/dashboard.php" class="menu-item <?= sideActive('dashboard', $currentPage) ?>">
      <i class="fa-solid fa-chart-pie"></i> Dashboard
    </a>

    <div class="menu-label">Manajemen</div>
    <a href="/mitsubishi/admin/mobil.php" class="menu-item <?= sideActive('mobil', $currentPage) ?>">
      <i class="fa-solid fa-car-side"></i> Data Mitsubishi
    </a>
    <a href="/mitsubishi/admin/booking.php" class="menu-item <?= sideActive('booking', $currentPage) ?>">
      <i class="fa-solid fa-calendar-check"></i> Data Booking
      <?php if ($notifBooking > 0): ?><span class="menu-badge"><?= $notifBooking ?></span><?php endif; ?>
    </a>
    <a href="/mitsubishi/admin/kontak.php" class="menu-item <?= sideActive('kontak', $currentPage) ?>">
      <i class="fa-solid fa-envelope"></i> Pesan Masuk
      <?php if ($notifKontak > 0): ?><span class="menu-badge"><?= $notifKontak ?></span><?php endif; ?>
    </a>
    <a href="/mitsubishi/admin/testimonial.php" class="menu-item <?= sideActive('testimonial', $currentPage) ?>">
      <i class="fa-solid fa-star"></i> Testimonial
    </a>

    <div class="menu-label">Pengaturan</div>
    <a href="/mitsubishi/admin/pengaturan.php" class="menu-item <?= sideActive('pengaturan', $currentPage) ?>">
      <i class="fa-solid fa-gear"></i> Pengaturan Web
    </a>
    <a href="/mitsubishi/admin/password.php" class="menu-item <?= sideActive('password', $currentPage) ?>">
      <i class="fa-solid fa-key"></i> Ubah Password
    </a>
    <a href="/mitsubishi/index.php" class="menu-item" target="_blank">
      <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website
    </a>
  </nav>
  <div class="sidebar-footer">
    <div class="admin-profile">
      <div class="admin-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
      <div class="admin-info">
        <h4><?= sanitize($_SESSION['admin_name'] ?? 'Admin') ?></h4>
        <span>Administrator</span>
      </div>
    </div>
    <a href="/mitsubishi/admin/logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
  </div>
</aside>

<!-- MAIN -->
<div class="main-wrap">
  <header class="topbar">
    <div class="topbar-left">
      <button class="mobile-toggle-admin" onclick="openSidebar()"><i class="fa-solid fa-bars"></i></button>
      <div class="page-title-bar">
        <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
        <p><?= $pageSubtitle ?? 'Panel Admin Dealer Mitsubishi' ?></p>
      </div>
    </div>
    <div class="topbar-right">
      <a href="/mitsubishi/admin/booking.php" class="topbar-btn" title="Booking Pending">
        <i class="fa-solid fa-calendar-check"></i>
        <?php if ($notifBooking > 0): ?><span class="notif-dot"></span><?php endif; ?>
      </a>
      <a href="/mitsubishi/admin/kontak.php" class="topbar-btn" title="Pesan Baru">
        <i class="fa-solid fa-bell"></i>
        <?php if ($notifKontak > 0): ?><span class="notif-dot"></span><?php endif; ?>
      </a>
      <div class="topbar-admin">
        <div class="topbar-admin-av"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
        <span><?= sanitize($_SESSION['admin_name'] ?? 'Admin') ?></span>
      </div>
    </div>
  </header>
  <div class="content">
