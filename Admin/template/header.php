<?php
/**
 * HEADER.PHP — XTM Ganakat Admin Panel
 * Fitur:
 *  - Profile user dari DB (session)
 *  - Google-style avatar (inisial + warna)
 *  - Notifikasi real-time (session-based)
 *  - Pesan (chat antar admin)
 *  - Search bar aktif
 *  - Home & Contact di topbar
 *  - Logout dari topbar
 */

if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect ke login jika belum login
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    header('Location: index.php');
    exit;
}

// ── Load helpers ──────────────────────────────────────────────
require_once __DIR__ . '/notif_helper.php';
require_once __DIR__ . '/avatar_helper.php';

// ── Koneksi DB (global, bisa dipakai halaman yang include header.php) ──
if (!isset($koneksi)) {
    require_once dirname(__DIR__) . '/../koneksi.php';
}
global $koneksi; // pastikan tersedia di scope global
$username_session = $_SESSION['username'];
$q_admin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '" . mysqli_real_escape_string($koneksi, $username_session) . "' LIMIT 1");
$current_admin    = mysqli_fetch_assoc($q_admin);
$nama_admin       = $current_admin['nama_admin'] ?? $username_session;
$id_admin_login   = $current_admin['id'] ?? 0;

// ── Hitung pesan belum dibaca ─────────────────────────────────
$unread_msg = 0;
$tbl_check = mysqli_query($koneksi, "SHOW TABLES LIKE 'pesan_admin'");
if (mysqli_num_rows($tbl_check) > 0) {
    $q_msg = mysqli_query($koneksi,
        "SELECT COUNT(*) as total FROM pesan_admin
         WHERE penerima_id = $id_admin_login AND dibaca = 0");
    $unread_msg = (int)(mysqli_fetch_assoc($q_msg)['total'] ?? 0);
}

// ── Notifikasi dari session ───────────────────────────────────
$notifikasi_list     = $_SESSION['notifikasi'] ?? [];
$unread_notif        = hitung_notif_belum_dibaca();

// ── Tandai notif dibaca (AJAX) ────────────────────────────────
if (isset($_GET['baca_notif'])) {
    $nid = $_GET['baca_notif'];
    if (isset($_SESSION['notifikasi'])) {
        foreach ($_SESSION['notifikasi'] as &$n) {
            if ($n['id'] === $nid) $n['dibaca'] = true;
        }
    }
    if ($_GET['baca_notif'] === 'semua') {
        foreach ($_SESSION['notifikasi'] as &$n) $n['dibaca'] = true;
    }
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'hal_admin.php'));
    exit;
}

// Icon per tipe notif
$notif_icons = [
    'berita' => 'bi-newspaper text-primary',
    'galeri' => 'bi-images text-purple',
    'guru'   => 'bi-mortarboard-fill text-success',
    'kontak' => 'bi-envelope-fill text-warning',
    'profil' => 'bi-building-fill text-info',
];

// File saat ini (untuk active state)
$current_file = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>XTM Ganakat | Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="assets/dist/css/adminlte.css">

  <!-- OverlayScrollbars -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" crossorigin="anonymous"/>

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous"/>

  <!-- ApexCharts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" crossorigin="anonymous"/>

  <!-- jsvectormap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" crossorigin="anonymous"/>

  <style>
    /* ── Base ─────────────────────────── */
    body, .app-wrapper { font-family: 'Outfit', sans-serif !important; }

    /* ── Google-style Avatar ────────── */
    .xtm-avatar {
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-family: 'Outfit', sans-serif;
      letter-spacing: 0.5px;
      flex-shrink: 0;
      user-select: none;
      line-height: 1;
    }

    /* ── Topbar tweaks ───────────────── */
    .app-header { box-shadow: 0 1px 4px rgba(0,0,0,.08) !important; }
    .app-header .nav-link { padding: 0.4rem 0.6rem; }

    /* ── Notif & Message dropdown ─── */
    .xtm-dropdown {
      border: 1px solid #F1F5F9;
      border-radius: 14px;
      box-shadow: 0 8px 30px rgba(0,0,0,.12);
      overflow: hidden;
      min-width: 340px;
    }
    .xtm-dropdown .dropdown-header-title {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 16px 10px;
      border-bottom: 1px solid #F1F5F9;
      font-weight: 700;
      font-size: 15px;
    }
    .xtm-notif-item {
      padding: 12px 16px;
      display: flex;
      gap: 12px;
      align-items: flex-start;
      border-bottom: 1px solid #F8FAFC;
      transition: background .12s;
      cursor: pointer;
      text-decoration: none;
      color: inherit;
    }
    .xtm-notif-item:hover { background: #F8FAFC; }
    .xtm-notif-item.unread { background: #EFF6FF; }
    .xtm-notif-icon {
      width: 36px; height: 36px; border-radius: 9px;
      background: #F1F5F9;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; font-size: 16px;
    }
    .notif-dot {
      width: 8px; height: 8px; border-radius: 50%;
      background: #3B82F6; flex-shrink: 0; margin-top: 4px;
    }

    /* ── Profile dropdown ────────────── */
    .xtm-profile-dropdown { min-width: 220px; }
    .xtm-profile-header {
      padding: 16px;
      border-bottom: 1px solid #F1F5F9;
      display: flex; align-items: center; gap: 10px;
    }

    /* ── Badge ───────────────────────── */
    .xtm-badge {
      position: absolute; top: 2px; right: 2px;
      min-width: 16px; height: 16px;
      background: #EF4444; color: #fff;
      border-radius: 8px; font-size: 9px; font-weight: 700;
      display: flex; align-items: center; justify-content: center;
      padding: 0 4px; border: 2px solid #fff;
    }

    /* ── Search overlay ──────────────── */
    #xtm-search-overlay {
      position: fixed; inset: 0;
      background: rgba(0,0,0,.45); backdrop-filter: blur(3px);
      z-index: 9999; display: none;
      align-items: flex-start; justify-content: center;
      padding-top: 80px;
    }
    #xtm-search-overlay.show { display: flex; }
    #xtm-search-box {
      background: #fff; border-radius: 16px;
      width: 100%; max-width: 580px;
      box-shadow: 0 20px 60px rgba(0,0,0,.18);
      overflow: hidden;
    }
    #xtm-search-input {
      border: none; outline: none;
      font-family: 'Outfit', sans-serif; font-size: 16px;
      flex: 1; color: #0F172A;
    }
    .xtm-search-result {
      padding: 11px 20px; display: flex; gap: 12px;
      align-items: center; cursor: pointer;
      border-bottom: 1px solid #F8FAFC;
      text-decoration: none; color: inherit;
      transition: background .1s;
    }
    .xtm-search-result:hover { background: #F8FAFC; }

    /* ── Sidebar brand ───────────────── */
    .brand-link:hover { text-decoration: none !important; }
    .sidebar-brand { background: #0F172A !important; }
    .brand-text { font-family: 'Outfit', sans-serif !important; font-weight: 800 !important; letter-spacing: -0.3px; }

    /* ── Tag badge ───────────────────── */
    .xtm-tag {
      display: inline-flex; align-items: center;
      padding: 2px 8px; border-radius: 20px;
      font-size: 11px; font-weight: 600;
    }

    /* ── text-purple ────────────────── */
    .text-purple { color: #8B5CF6 !important; }
  </style>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
<div class="app-wrapper">

  <!-- ════════════════════════════════════════
       TOPBAR / NAVBAR
  ════════════════════════════════════════ -->
  <nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">

      <!-- Left: toggle + Home + Contact -->
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
            <i class="bi bi-list fs-5"></i>
          </a>
        </li>
        <li class="nav-item d-none d-md-block">
          <a href="hal_admin.php" class="nav-link fw-semibold <?= ($current_file==='hal_admin.php')?'text-primary':'' ?>">
            <i class="bi bi-house-fill me-1"></i>Home
          </a>
        </li>
        <li class="nav-item d-none d-md-block">
          <a href="data_kontak.php" class="nav-link fw-semibold <?= ($current_file==='data_kontak.php')?'text-primary':'' ?>">
            <i class="bi bi-telephone-fill me-1"></i>Kontak
          </a>
        </li>
      </ul>

      <!-- Right: Search | Notif | Pesan | Profile -->
      <ul class="navbar-nav ms-auto align-items-center gap-1">

        <!-- ── SEARCH ── -->
        <li class="nav-item">
          <a class="nav-link" href="#" id="btn-open-search" title="Cari">
            <i class="bi bi-search fs-6"></i>
          </a>
        </li>

        <!-- ── NOTIFIKASI ── -->
        <li class="nav-item dropdown">
          <a class="nav-link position-relative" data-bs-toggle="dropdown" href="#" id="notif-toggle" title="Notifikasi">
            <i class="bi bi-bell-fill fs-6"></i>
            <?php if ($unread_notif > 0): ?>
              <span class="xtm-badge"><?= min($unread_notif, 99) ?></span>
            <?php endif; ?>
          </a>

          <div class="dropdown-menu dropdown-menu-end xtm-dropdown p-0" style="min-width:340px;">
            <div class="dropdown-header-title">
              <span>Notifikasi</span>
              <?php if ($unread_notif > 0): ?>
                <a href="?baca_notif=semua" class="text-primary text-decoration-none" style="font-size:12px;font-weight:600;">
                  Tandai semua dibaca
                </a>
              <?php endif; ?>
            </div>

            <div style="max-height:360px;overflow-y:auto;">
              <?php if (empty($notifikasi_list)): ?>
                <div class="p-4 text-center text-muted" style="font-size:13px;">
                  <i class="bi bi-bell-slash fs-4 d-block mb-2"></i>
                  Belum ada notifikasi
                </div>
              <?php else: ?>
                <?php foreach ($notifikasi_list as $notif):
                  $icon_class = $notif_icons[$notif['type']] ?? 'bi-info-circle text-secondary';
                ?>
                <a href="?baca_notif=<?= htmlspecialchars($notif['id']) ?>"
                   class="xtm-notif-item <?= !$notif['dibaca'] ? 'unread' : '' ?>" style="display:flex;">
                  <div class="xtm-notif-icon">
                    <i class="bi <?= $icon_class ?>"></i>
                  </div>
                  <div style="flex:1;">
                    <div style="font-weight:<?= !$notif['dibaca']?'700':'500' ?>;font-size:13px;color:#0F172A;">
                      <?= htmlspecialchars($notif['judul']) ?>
                    </div>
                    <div style="font-size:12px;color:#64748B;margin-top:2px;">
                      <?= htmlspecialchars($notif['body']) ?>
                    </div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:4px;">
                      <?= format_waktu_relatif($notif['waktu']) ?>
                    </div>
                  </div>
                  <?php if (!$notif['dibaca']): ?><div class="notif-dot ms-2 mt-1"></div><?php endif; ?>
                </a>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>

            <div style="padding:10px 16px;border-top:1px solid #F1F5F9;text-align:center;">
              <a href="?baca_notif=semua" class="text-decoration-none" style="font-size:12px;color:#64748B;">
                Lihat semua notifikasi
              </a>
            </div>
          </div>
        </li>

        <!-- ── PESAN ── -->
        <li class="nav-item">
          <a class="nav-link position-relative" href="pesan.php" title="Pesan">
            <i class="bi bi-chat-dots-fill fs-6"></i>
            <?php if ($unread_msg > 0): ?>
              <span class="xtm-badge"><?= min($unread_msg, 99) ?></span>
            <?php endif; ?>
          </a>
        </li>

        <!-- ── FULLSCREEN ── -->
        <li class="nav-item d-none d-md-block">
          <a class="nav-link" href="#" data-lte-toggle="fullscreen" title="Fullscreen">
            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display:none"></i>
          </a>
        </li>

        <!-- ── PROFILE DROPDOWN ── -->
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2 ps-1 pe-2"
             data-bs-toggle="dropdown" style="border-radius:10px;transition:background .15s;"
             onmouseenter="this.style.background='#F1F5F9'" onmouseleave="this.style.background='transparent'">
            <?= render_avatar($nama_admin, 34) ?>
            <div class="d-none d-md-block" style="line-height:1.2;">
              <div style="font-size:13px;font-weight:700;color:#1E293B;"><?= htmlspecialchars($nama_admin) ?></div>
              <div style="font-size:11px;color:#64748B;">Administrator</div>
            </div>
            <i class="bi bi-chevron-down ms-1" style="font-size:11px;color:#94A3B8;"></i>
          </a>

          <ul class="dropdown-menu dropdown-menu-end xtm-profile-dropdown p-0" style="border-radius:14px;border:1px solid #F1F5F9;box-shadow:0 8px 30px rgba(0,0,0,.12);">
            <!-- Header -->
            <li class="xtm-profile-header">
              <?= render_avatar($nama_admin, 42) ?>
              <div>
                <div style="font-weight:700;font-size:14px;color:#0F172A;"><?= htmlspecialchars($nama_admin) ?></div>
                <div style="font-size:12px;color:#64748B;">@<?= htmlspecialchars($username_session) ?></div>
                <div style="font-size:11px;color:#22C55E;font-weight:600;margin-top:2px;">● Online</div>
              </div>
            </li>

            <!-- Menu items -->
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="hal_admin.php">
              <i class="bi bi-speedometer2 text-primary"></i>Dashboard
            </a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="pesan.php">
              <i class="bi bi-chat-dots text-success"></i>Pesan
            </a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2" href="data_sekolah.php">
              <i class="bi bi-building text-info"></i>Profil Sekolah
            </a></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="logout.php">
              <i class="bi bi-box-arrow-right"></i>Logout
            </a></li>
          </ul>
        </li>

      </ul>
    </div>
  </nav>
  <!-- ══ END TOPBAR ══ -->


  <!-- ════════════════════════════════════════
       SEARCH OVERLAY
  ════════════════════════════════════════ -->
  <div id="xtm-search-overlay">
    <div id="xtm-search-box" class="fade-in">
      <!-- Input bar -->
      <div class="d-flex align-items-center px-3 py-2 border-bottom" style="gap:10px;">
        <i class="bi bi-search text-muted" style="font-size:18px;"></i>
        <input id="xtm-search-input" placeholder="Cari berita, guru, galeri, kontak..." autocomplete="off" />
        <button id="btn-close-search" class="btn btn-sm btn-light" style="border-radius:8px;">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <!-- Results -->
      <div id="xtm-search-results" style="max-height:400px;overflow-y:auto;">
        <div class="p-4 text-center text-muted" style="font-size:13px;">
          <i class="bi bi-search d-block fs-4 mb-2 opacity-25"></i>
          Ketik minimal 2 karakter untuk mencari…
        </div>
      </div>
      <!-- Footer hint -->
      <div class="px-4 py-2 border-top d-flex gap-3" style="font-size:11px;color:#94A3B8;">
        <span><kbd>↵</kbd> Buka</span>
        <span><kbd>ESC</kbd> Tutup</span>
      </div>
    </div>
  </div>
  <!-- ══ END SEARCH OVERLAY ══ -->