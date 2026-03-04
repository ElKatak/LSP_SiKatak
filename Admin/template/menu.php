<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">

  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand" style="background:#0F172A !important;">
    <a href="hal_admin.php" class="brand-link d-flex align-items-center gap-2" style="text-decoration:none;padding:12px 16px;">
      <!-- Logo icon -->
      <div style="width:36px;height:36px;background:linear-gradient(135deg,#2563EB,#06B6D4);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 3px 8px rgba(37,99,235,.4);">
        <i class="bi bi-mortarboard-fill" style="color:#fff;font-size:18px;"></i>
      </div>
      <!-- Brand text -->
      <div>
        <div class="brand-text" style="font-family:'Outfit',sans-serif;font-weight:800;font-size:15px;color:#F8FAFC;letter-spacing:-0.3px;line-height:1.1;">
          XTM Ganakat
        </div>
        <div style="font-size:10px;color:#475569;font-weight:500;letter-spacing:0.3px;">
          Admin Panel
        </div>
      </div>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column"
          data-lte-toggle="treeview"
          role="navigation"
          aria-label="Main navigation"
          data-accordion="false"
          id="navigation">

        <!-- Dashboard -->
        <li class="nav-item">
          <a href="hal_admin.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='hal_admin.php')?'active':'' ?>">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- Berita -->
        <li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['input_berita.php','data_berita.php','edit_berita.php'])?'menu-open':'' ?>">
          <a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['input_berita.php','data_berita.php','edit_berita.php'])?'active':'' ?>">
            <i class="nav-icon bi bi-newspaper"></i>
            <p>
              Berita
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="input_berita.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='input_berita.php')?'active':'' ?>">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>Input Berita</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="data_berita.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='data_berita.php')?'active':'' ?>">
                <i class="nav-icon bi bi-list-ul"></i>
                <p>Data Berita</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Galeri -->
        <li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['input_galeri.php','data_galeri.php','edit_galeri.php'])?'menu-open':'' ?>">
          <a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['input_galeri.php','data_galeri.php','edit_galeri.php'])?'active':'' ?>">
            <i class="nav-icon bi bi-images"></i>
            <p>
              Galeri
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="input_galeri.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='input_galeri.php')?'active':'' ?>">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>Input Galeri</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="data_galeri.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='data_galeri.php')?'active':'' ?>">
                <i class="nav-icon bi bi-grid-3x3-gap"></i>
                <p>Data Galeri</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Guru -->
        <li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['input_guru.php','data_guru.php'])?'menu-open':'' ?>">
          <a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['input_guru.php','data_guru.php'])?'active':'' ?>">
            <i class="nav-icon bi bi-mortarboard-fill"></i>
            <p>
              Guru
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="input_guru.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='input_guru.php')?'active':'' ?>">
                <i class="nav-icon bi bi-person-plus-fill"></i>
                <p>Input Guru</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="data_guru.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='data_guru.php')?'active':'' ?>">
                <i class="nav-icon bi bi-people-fill"></i>
                <p>Data Guru</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Kontak -->
        <li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['input_kontak.php','data_kontak.php'])?'menu-open':'' ?>">
          <a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['input_kontak.php','data_kontak.php'])?'active':'' ?>">
            <i class="nav-icon bi bi-envelope-fill"></i>
            <p>
              Kontak
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="input_kontak.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='input_kontak.php')?'active':'' ?>">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>Input Kontak</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="data_kontak.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='data_kontak.php')?'active':'' ?>">
                <i class="nav-icon bi bi-inbox-fill"></i>
                <p>Data Kontak</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Profil Sekolah -->
        <li class="nav-item <?= in_array(basename($_SERVER['PHP_SELF']),['input_sekolah.php','data_sekolah.php'])?'menu-open':'' ?>">
          <a href="#" class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']),['input_sekolah.php','data_sekolah.php'])?'active':'' ?>">
            <i class="nav-icon bi bi-building-fill"></i>
            <p>
              Profil Sekolah
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="input_sekolah.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='input_sekolah.php')?'active':'' ?>">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>Input Profil</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="data_sekolah.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='data_sekolah.php')?'active':'' ?>">
                <i class="nav-icon bi bi-card-list"></i>
                <p>Data Profil</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- ── PESAN (new) ── -->
        <li class="nav-item">
          <a href="pesan.php" class="nav-link <?= (basename($_SERVER['PHP_SELF'])==='pesan.php')?'active':'' ?>">
            <i class="nav-icon bi bi-chat-dots-fill"></i>
            <p>
              Pesan
              <?php
                // Show unread badge
                $tbl_check2 = mysqli_query($koneksi, "SHOW TABLES LIKE 'pesan_admin'");
                if (mysqli_num_rows($tbl_check2) > 0) {
                  $q_unread = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM pesan_admin WHERE penerima_id=$id_admin_login AND dibaca=0");
                  $cnt_unread = (int)(mysqli_fetch_assoc($q_unread)['c'] ?? 0);
                  if ($cnt_unread > 0) echo '<span class="badge text-bg-danger ms-auto">'.$cnt_unread.'</span>';
                }
              ?>
            </p>
          </a>
        </li>

        <li class="nav-item"><hr style="border-color:#1E293B;margin:8px 16px;"></li>

        <!-- Logout -->
        <li class="nav-item">
          <a href="logout.php" class="nav-link text-danger"
             onclick="return confirm('Yakin ingin logout?')">
            <i class="nav-icon bi bi-box-arrow-right text-danger"></i>
            <p style="color:#EF4444;">Logout</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->

</aside>
<!--end::Sidebar-->