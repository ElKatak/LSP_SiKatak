<?php
// JANGAN panggil session_start() di sini — sudah ada di header.php
include "template/header.php"; // header.php sudah handle session + $koneksi
include "template/menu.php";
// $koneksi sudah tersedia dari header.php
include "template/notif_helper.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_guru    = $_POST['nama_guru'];
    $nip          = $_POST['nip'];
    $jenis_kelamin= $_POST['jenis_kelamin'];
    $mapel        = $_POST['mapel'];
    $email        = $_POST['email'];
    $no_hp        = $_POST['no_hp'];
    $foto = '';
    if (!empty($_FILES['foto']['name'])) {
        $foto = time().'_'.$_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], 'upload/'.$foto);
    }
    $ng = mysqli_real_escape_string($koneksi, $nama_guru);
    $np = mysqli_real_escape_string($koneksi, $nip);
    $jk = mysqli_real_escape_string($koneksi, $jenis_kelamin);
    $mp = mysqli_real_escape_string($koneksi, $mapel);
    $em = mysqli_real_escape_string($koneksi, $email);
    $hp = mysqli_real_escape_string($koneksi, $no_hp);
    $ft = mysqli_real_escape_string($koneksi, $foto);

    mysqli_query($koneksi, "INSERT INTO guru (nama_guru,nip,jenis_kelamin,mapel,foto,email,no_hp) VALUES ('$ng','$np','$jk','$mp','$ft','$em','$hp')");

    // ── Notifikasi ──
    tambah_notif('guru', 'Data guru baru ditambahkan', $nama_guru . ' — Mapel: ' . $mapel);

    echo "<script>alert('Data guru berhasil disimpan');window.location='data_guru.php';</script>";
}
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Input Guru</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="hal_admin.php">Home</a></li>
            <li class="breadcrumb-item active">Input Guru</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
          <div class="card-header border-0 pt-3 px-4"><h5 class="fw-bold mb-0">Form Input Guru</h5></div>
          <div class="card-body px-4">
            <form method="post" enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Nama Guru</label>
                  <input type="text" name="nama_guru" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">NIP</label>
                  <input type="text" name="nip" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Jenis Kelamin</label>
                  <select name="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="Laki-Laki">Laki-Laki</option>
                    <option value="Perempuan">Perempuan</option>
                  </select>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Mata Pelajaran</label>
                  <input type="text" name="mapel" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">Email</label>
                  <input type="email" name="email" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-semibold">No HP</label>
                  <input type="text" name="no_hp" class="form-control">
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label fw-semibold">Foto Guru</label>
                  <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="data_guru.php" class="btn btn-secondary">Kembali</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include "template/footer.php"; ?>