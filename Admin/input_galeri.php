<?php
include "template/header.php";
include "template/menu.php";
include "../koneksi.php"; // pastikan file koneksi ada
// ================= PROSES SIMPAN =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
 $judul = $_POST['judul'];
 $deskripsi = $_POST['deskripsi'];
 $tanggal_upload = $_POST['tanggal_upload'];
 // Upload gambar
 $gambar = '';
 if (!empty($_FILES['gambar']['name'])) {
 $gambar = time() . '_' . $_FILES['gambar']['name'];
 move_uploaded_file($_FILES['gambar']['tmp_name'], 'upload/' . $gambar);
 }
 // Simpan ke database
 mysqli_query($koneksi, "INSERT INTO galeri
 (judul, deskripsi, gambar, tanggal_upload)
 VALUES
 ('$judul', '$deskripsi', '$gambar', '$tanggal_upload')
 ");
 echo "<script>alert('Galeri berhasil disimpan');window.location='input_galeri.php';</script>";
}
?>
<main class="app-main">
 <!-- App Content Header -->
 <div class="app-content-header">
 <div class="container-fluid">
 <div class="row">
 <div class="col-sm-6">
 <h3 class="mb-0">Input Galeri</h3>
 </div>
 <div class="col-sm-6">
 <ol class="breadcrumb float-sm-end">
 <li class="breadcrumb-item"><a href="#">Home</a></li>
 <li class="breadcrumb-item active">Input Galeri</li>
 </ol>
 </div>
 </div>
 </div>
 </div>
 <!-- App Content -->
 <div class="app-content">
 <div class="container-fluid">
 <div class="row">
 <div class="col-12">
 <div class="card">
 <div class="card-header">
 <h3 class="card-title">Form Galeri</h3>
 </div>
 <div class="card-body">
 <form method="post" enctype="multipart/form-data">
 <!-- Judul -->
 <div class="mb-3">
 <label class="form-label">Judul Galeri</label>
 <input type="text" name="judul" class="form-control" required>
 </div>
 <!-- Deskripsi -->
 <div class="mb-3">
 <label class="form-label">Deskripsi</label>
 <textarea name="deskripsi" id="editor" rows="6" class="form-control"
required></textarea>
 </div>
 <!-- Gambar -->
 <div class="mb-3">
 <label class="form-label">Gambar</label>
 <input type="file" name="gambar" class="form-control" accept="image/*"
required>
 </div>
 <!-- Tanggal Upload -->
 <div class="mb-3">
 <label class="form-label">Tanggal Upload</label>
 <input type="date" name="tanggal_upload" class="form-control" required>
 </div>
 <div class="card-footer">
 <button type="submit" class="btn btn-primary">Simpan</button>
 </div>
 </form>
 </div>
 </div>
 </div>
 </div>
 </div>
 </div>
</main>
<?php include "template/footer.php"; ?>
<!-- CKEditor -->
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
 CKEDITOR.replace('editor');
</script>
