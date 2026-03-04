<?php
// JANGAN panggil session_start() di sini — sudah ada di header.php
include "template/header.php"; // header.php sudah handle session + $koneksi
include "template/menu.php";
include "template/notif_helper.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul          = $_POST['judul'];
    $deskripsi      = $_POST['deskripsi'];
    $tanggal_upload = $_POST['tanggal_upload'];

    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . '_' . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], 'upload/' . $gambar);
    }

    $j = mysqli_real_escape_string($koneksi, $judul);
    $d = mysqli_real_escape_string($koneksi, $deskripsi);
    $g = mysqli_real_escape_string($koneksi, $gambar);
    $t = mysqli_real_escape_string($koneksi, $tanggal_upload);

    mysqli_query($koneksi, "INSERT INTO galeri (judul, deskripsi, gambar, tanggal_upload) VALUES ('$j','$d','$g','$t')");

    // ── Notifikasi ──
    tambah_notif('galeri', 'Foto baru ditambahkan ke galeri', '"' . $judul . '"');

    echo "<script>alert('Galeri berhasil disimpan');window.location='input_galeri.php';</script>";
}
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Input Galeri</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="hal_admin.php">Home</a></li>
            <li class="breadcrumb-item active">Input Galeri</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
          <div class="card-header border-0 pt-3 px-4"><h5 class="fw-bold mb-0">Form Galeri</h5></div>
          <div class="card-body px-4">
            <form method="post" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label fw-semibold">Judul Galeri</label>
                <input type="text" name="judul" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" id="editor" rows="5" class="form-control"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Gambar</label>
                <input type="file" name="gambar" class="form-control" accept="image/*" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Tanggal Upload</label>
                <input type="date" name="tanggal_upload" class="form-control" required value="<?= date('Y-m-d') ?>">
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="data_galeri.php" class="btn btn-secondary">Kembali</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include "template/footer.php"; ?>
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>CKEDITOR.replace('editor');</script>