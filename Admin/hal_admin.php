<?php
// JANGAN panggil session_start() di sini — sudah ada di header.php
include "template/header.php"; // header.php sudah handle session + $koneksi
include "template/menu.php";
// $koneksi sudah tersedia dari header.php

// ── Stats ─────────────────────────────────────────────────────
$total_berita  = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM berita"))['c'];
$total_galeri  = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM galeri"))['c'];
$total_guru    = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM guru"))['c'];
$total_kontak  = (int)mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) c FROM kontak"))['c'];

// ── Profil sekolah ────────────────────────────────────────────
$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil_sekolah LIMIT 1")) ?: [];

// ── Berita terbaru (5) ────────────────────────────────────────
$berita_recent = [];
$r = mysqli_query($koneksi, "SELECT judul, penulis, tanggal FROM berita ORDER BY tanggal DESC LIMIT 5");
while ($d = mysqli_fetch_assoc($r)) $berita_recent[] = $d;

// ── Guru per mapel (untuk chart donut) ───────────────────────
$guru_mapel = [];
$r = mysqli_query($koneksi, "SELECT mapel, COUNT(*) c FROM guru GROUP BY mapel ORDER BY c DESC LIMIT 6");
while ($d = mysqli_fetch_assoc($r)) $guru_mapel[] = $d;

// ── Kontak terbaru ────────────────────────────────────────────
$kontak_recent = [];
$r = mysqli_query($koneksi, "SELECT nama, email, tanggal_kirim FROM kontak ORDER BY tanggal_kirim DESC LIMIT 5");
while ($d = mysqli_fetch_assoc($r)) $kontak_recent[] = $d;

// ── Berita per bulan (chart) ──────────────────────────────────
$chart_labels = [];
$chart_data   = [];
$r = mysqli_query($koneksi, "SELECT DATE_FORMAT(tanggal,'%b %Y') lbl, COUNT(*) c FROM berita GROUP BY DATE_FORMAT(tanggal,'%Y-%m') ORDER BY tanggal DESC LIMIT 6");
$rows_chart = [];
while ($d = mysqli_fetch_assoc($r)) $rows_chart[] = $d;
$rows_chart = array_reverse($rows_chart);
foreach ($rows_chart as $rc) {
    $chart_labels[] = $rc['lbl'];
    $chart_data[]   = (int)$rc['c'];
}
?>
<main class="app-main">

  <!-- ── Breadcrumb ── -->
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0" style="font-family:'Outfit',sans-serif;font-weight:800;color:#0F172A;">
            Dashboard
          </h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="hal_admin.php">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <!-- ════════════════════
           STAT CARDS
      ════════════════════ -->
      <div class="row g-3 mb-4">

        <!-- Berita -->
        <div class="col-sm-6 col-xl-3">
          <a href="data_berita.php" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
              <div class="card-body" style="position:relative;padding:20px;">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Total Berita</p>
                    <h2 class="fw-800 mb-0" style="font-size:38px;font-weight:800;color:#0F172A;line-height:1;"><?= $total_berita ?></h2>
                  </div>
                  <div style="width:50px;height:50px;background:#EFF6FF;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-newspaper" style="font-size:22px;color:#3B82F6;"></i>
                  </div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-1" style="color:#3B82F6;font-size:12px;font-weight:600;">
                  <i class="bi bi-arrow-right-short"></i> Lihat semua berita
                </div>
                <div style="position:absolute;bottom:-15px;right:-15px;width:80px;height:80px;border-radius:50%;background:#EFF6FF;opacity:.6;"></div>
              </div>
            </div>
          </a>
        </div>

        <!-- Galeri -->
        <div class="col-sm-6 col-xl-3">
          <a href="data_galeri.php" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
              <div class="card-body" style="position:relative;padding:20px;">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Total Galeri</p>
                    <h2 class="fw-800 mb-0" style="font-size:38px;font-weight:800;color:#0F172A;line-height:1;"><?= $total_galeri ?></h2>
                  </div>
                  <div style="width:50px;height:50px;background:#F5F3FF;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-images" style="font-size:22px;color:#8B5CF6;"></i>
                  </div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-1" style="color:#8B5CF6;font-size:12px;font-weight:600;">
                  <i class="bi bi-arrow-right-short"></i> Lihat semua galeri
                </div>
                <div style="position:absolute;bottom:-15px;right:-15px;width:80px;height:80px;border-radius:50%;background:#F5F3FF;opacity:.6;"></div>
              </div>
            </div>
          </a>
        </div>

        <!-- Guru -->
        <div class="col-sm-6 col-xl-3">
          <a href="data_guru.php" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
              <div class="card-body" style="position:relative;padding:20px;">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Total Guru</p>
                    <h2 class="fw-800 mb-0" style="font-size:38px;font-weight:800;color:#0F172A;line-height:1;"><?= $total_guru ?></h2>
                  </div>
                  <div style="width:50px;height:50px;background:#F0FDF4;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-mortarboard-fill" style="font-size:22px;color:#22C55E;"></i>
                  </div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-1" style="color:#22C55E;font-size:12px;font-weight:600;">
                  <i class="bi bi-arrow-right-short"></i> Lihat data guru
                </div>
                <div style="position:absolute;bottom:-15px;right:-15px;width:80px;height:80px;border-radius:50%;background:#F0FDF4;opacity:.6;"></div>
              </div>
            </div>
          </a>
        </div>

        <!-- Kontak -->
        <div class="col-sm-6 col-xl-3">
          <a href="data_kontak.php" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100" style="border-radius:14px;overflow:hidden;">
              <div class="card-body" style="position:relative;padding:20px;">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;">Pesan Masuk</p>
                    <h2 class="fw-800 mb-0" style="font-size:38px;font-weight:800;color:#0F172A;line-height:1;"><?= $total_kontak ?></h2>
                  </div>
                  <div style="width:50px;height:50px;background:#FFF7ED;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-envelope-fill" style="font-size:22px;color:#F97316;"></i>
                  </div>
                </div>
                <div class="mt-3 d-flex align-items-center gap-1" style="color:#F97316;font-size:12px;font-weight:600;">
                  <i class="bi bi-arrow-right-short"></i> Lihat semua pesan
                </div>
                <div style="position:absolute;bottom:-15px;right:-15px;width:80px;height:80px;border-radius:50%;background:#FFF7ED;opacity:.6;"></div>
              </div>
            </div>
          </a>
        </div>

      </div>
      <!-- ══ END STAT CARDS ══ -->


      <!-- ════════════════════
           ROW 2: Chart + Profil
      ════════════════════ -->
      <div class="row g-3 mb-4">

        <!-- Chart Berita per Bulan -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header border-0 pt-3 px-4 pb-0 bg-white" style="border-radius:14px 14px 0 0;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mb-0 fw-bold" style="font-family:'Outfit',sans-serif;">Berita per Bulan</h5>
                  <small class="text-muted">Jumlah artikel yang dipublish</small>
                </div>
                <a href="input_berita.php" class="btn btn-sm btn-primary" style="border-radius:8px;">
                  <i class="bi bi-plus me-1"></i>Tambah
                </a>
              </div>
            </div>
            <div class="card-body px-4 pb-4">
              <div id="chart-berita"></div>
            </div>
          </div>
        </div>

        <!-- Profil Sekolah -->
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm h-100" style="border-radius:14px;">
            <div class="card-header border-0 pt-3 px-4 pb-0 bg-white" style="border-radius:14px 14px 0 0;">
              <h5 class="mb-1 fw-bold" style="font-family:'Outfit',sans-serif;">Profil Sekolah</h5>
              <small class="text-muted">Informasi identitas sekolah</small>
            </div>
            <div class="card-body px-4">
              <?php if ($profil): ?>
                <?php
                $rows_profil = [
                  ['bi bi-building-fill text-primary', 'Nama Sekolah', $profil['nama_sekolah']],
                  ['bi bi-hash text-warning',           'NPSN',         $profil['npsn']],
                  ['bi bi-geo-alt-fill text-danger',    'Kabupaten',    $profil['kabupaten']],
                  ['bi bi-person-badge-fill text-info', 'Kepala Sekolah', $profil['kepala_sekolah']],
                  ['bi bi-globe text-success',          'Website',      $profil['website']],
                  ['bi bi-envelope-fill text-warning',  'Email',        $profil['email']],
                ];
                foreach ($rows_profil as [$icon, $lbl, $val]):
                ?>
                <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                  <div style="width:32px;height:32px;background:#F8FAFC;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="<?= $icon ?>" style="font-size:14px;"></i>
                  </div>
                  <div>
                    <div style="font-size:11px;color:#94A3B8;font-weight:500;"><?= $lbl ?></div>
                    <div style="font-size:13px;font-weight:600;color:#1E293B;"><?= htmlspecialchars($val ?? '-') ?></div>
                  </div>
                </div>
                <?php endforeach; ?>
                <div class="mt-3">
                  <a href="data_sekolah.php" class="btn btn-sm btn-outline-primary w-100" style="border-radius:8px;">
                    <i class="bi bi-pencil me-1"></i>Edit Profil
                  </a>
                </div>
              <?php else: ?>
                <div class="text-center py-4 text-muted">
                  <i class="bi bi-building-x fs-2 d-block mb-2 opacity-25"></i>
                  <p style="font-size:13px;">Profil sekolah belum diisi.</p>
                  <a href="input_sekolah.php" class="btn btn-sm btn-primary" style="border-radius:8px;">
                    <i class="bi bi-plus me-1"></i>Input Sekarang
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
      <!-- ══ END ROW 2 ══ -->


      <!-- ════════════════════
           ROW 3: Recent Activity
      ════════════════════ -->
      <div class="row g-3">

        <!-- Berita Terbaru -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header border-0 pt-3 px-4 pb-0 bg-white d-flex justify-content-between align-items-center" style="border-radius:14px 14px 0 0;">
              <div>
                <h5 class="mb-0 fw-bold" style="font-family:'Outfit',sans-serif;">Berita Terbaru</h5>
              </div>
              <a href="data_berita.php" class="text-primary text-decoration-none" style="font-size:12px;font-weight:600;">Lihat semua →</a>
            </div>
            <div class="card-body px-4 pb-4">
              <?php if (empty($berita_recent)): ?>
                <div class="text-center py-4 text-muted" style="font-size:13px;">Belum ada berita.</div>
              <?php else: ?>
                <?php foreach ($berita_recent as $i => $b): ?>
                <div class="d-flex align-items-center gap-3 py-2 <?= $i < count($berita_recent)-1 ? 'border-bottom' : '' ?>">
                  <div style="width:8px;height:8px;border-radius:50%;background:#3B82F6;flex-shrink:0;"></div>
                  <div style="flex:1;overflow:hidden;">
                    <div class="fw-semibold text-truncate" style="font-size:13px;max-width:280px;"><?= htmlspecialchars($b['judul']) ?></div>
                    <div style="font-size:11px;color:#94A3B8;"><?= htmlspecialchars($b['penulis']) ?> · <?= $b['tanggal'] ?></div>
                  </div>
                  <a href="edit_berita.php?id=0" class="btn btn-sm" style="padding:3px 8px;border-radius:6px;background:#F1F5F9;color:#475569;font-size:12px;">
                    <i class="bi bi-pencil"></i>
                  </a>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Kontak Terbaru -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-header border-0 pt-3 px-4 pb-0 bg-white d-flex justify-content-between align-items-center" style="border-radius:14px 14px 0 0;">
              <div>
                <h5 class="mb-0 fw-bold" style="font-family:'Outfit',sans-serif;">Pesan Kontak Terbaru</h5>
              </div>
              <a href="data_kontak.php" class="text-primary text-decoration-none" style="font-size:12px;font-weight:600;">Lihat semua →</a>
            </div>
            <div class="card-body px-4 pb-4">
              <?php if (empty($kontak_recent)): ?>
                <div class="text-center py-4 text-muted" style="font-size:13px;">Belum ada pesan masuk.</div>
              <?php else: ?>
                <?php foreach ($kontak_recent as $i => $k): ?>
                <div class="d-flex align-items-center gap-3 py-2 <?= $i < count($kontak_recent)-1 ? 'border-bottom' : '' ?>">
                  <?= render_avatar($k['nama'], 36) ?>
                  <div style="flex:1;overflow:hidden;">
                    <div class="fw-semibold" style="font-size:13px;"><?= htmlspecialchars($k['nama']) ?></div>
                    <div style="font-size:11px;color:#3B82F6;"><?= htmlspecialchars($k['email']) ?></div>
                    <div style="font-size:11px;color:#94A3B8;"><?= $k['tanggal_kirim'] ?></div>
                  </div>
                  <span style="width:8px;height:8px;border-radius:50%;background:#F97316;flex-shrink:0;"></span>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
      <!-- ══ END ROW 3 ══ -->

    </div>
  </div>
</main>

<!-- Chart data inject -->
<script>
  window.chartBeritaData   = <?= json_encode($chart_data) ?>;
  window.chartBeritaLabels = <?= json_encode($chart_labels) ?>;
</script>

<?php include "template/footer.php"; ?>