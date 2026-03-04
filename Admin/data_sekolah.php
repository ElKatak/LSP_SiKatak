<?php
// JANGAN panggil session_start() di sini — sudah ada di header.php
include "template/header.php"; // header.php sudah handle session + $koneksi
include "template/menu.php";
// $koneksi sudah tersedia dari header.php
include "template/notif_helper.php";

/* ── HAPUS ── */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $q = mysqli_query($koneksi, "SELECT logo FROM profil_sekolah WHERE id='$id'");
    $d = mysqli_fetch_assoc($q);
    if ($d) {
        if (!empty($d['logo']) && file_exists("upload/".$d['logo'])) unlink("upload/".$d['logo']);
        mysqli_query($koneksi, "DELETE FROM profil_sekolah WHERE id='$id'");
    }
    echo "<script>alert('Data berhasil dihapus');window.location='data_sekolah.php';</script>";
    exit;
}

/* ── UPDATE ── */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $fields = ['nama_sekolah','npsn','alamat','desa','kecamatan','kabupaten','provinsi','email','telepon','website','kepala_sekolah','visi','misi','deskripsi'];
    foreach ($fields as $f) $$f = $_POST[$f];
    $logo_lama = $_POST['logo_lama'];

    if (!empty($_FILES['logo']['name'])) {
        $logo_baru = time().'_'.$_FILES['logo']['name'];
        move_uploaded_file($_FILES['logo']['tmp_name'], "upload/".$logo_baru);
        if ($logo_lama && file_exists("upload/".$logo_lama)) unlink("upload/".$logo_lama);
    } else {
        $logo_baru = $logo_lama;
    }

    $ns = mysqli_real_escape_string($koneksi, $nama_sekolah);
    mysqli_query($koneksi, "UPDATE profil_sekolah SET
        nama_sekolah='".mysqli_real_escape_string($koneksi,$nama_sekolah)."',
        npsn='".mysqli_real_escape_string($koneksi,$npsn)."',
        alamat='".mysqli_real_escape_string($koneksi,$alamat)."',
        desa='".mysqli_real_escape_string($koneksi,$desa)."',
        kecamatan='".mysqli_real_escape_string($koneksi,$kecamatan)."',
        kabupaten='".mysqli_real_escape_string($koneksi,$kabupaten)."',
        provinsi='".mysqli_real_escape_string($koneksi,$provinsi)."',
        email='".mysqli_real_escape_string($koneksi,$email)."',
        telepon='".mysqli_real_escape_string($koneksi,$telepon)."',
        website='".mysqli_real_escape_string($koneksi,$website)."',
        kepala_sekolah='".mysqli_real_escape_string($koneksi,$kepala_sekolah)."',
        logo='".mysqli_real_escape_string($koneksi,$logo_baru)."',
        visi='".mysqli_real_escape_string($koneksi,$visi)."',
        misi='".mysqli_real_escape_string($koneksi,$misi)."',
        deskripsi='".mysqli_real_escape_string($koneksi,$deskripsi)."'
        WHERE id='$id'");

    // ── Notifikasi ──
    tambah_notif('profil', 'Profil sekolah diperbarui', 'Data ' . $nama_sekolah . ' telah diperbarui');

    echo "<script>alert('Profil berhasil diperbarui');window.location='data_sekolah.php';</script>";
    exit;
}

/* ── EDIT FORM ── */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil_sekolah WHERE id='$id'"));
}

/* ── PAGINATION ── */
$limit = 5;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM profil_sekolah"));
$totalPage = ceil($total / $limit);
$data = mysqli_query($koneksi, "SELECT * FROM profil_sekolah ORDER BY id DESC LIMIT $limit OFFSET $offset");
?>
<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Profil Sekolah</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="hal_admin.php">Home</a></li>
            <li class="breadcrumb-item active">Profil Sekolah</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <?php if ($edit): ?>
      <!-- FORM EDIT -->
      <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;">
        <div class="card-header border-0 pt-3 px-4">
          <h5 class="fw-bold mb-0">Edit Profil Sekolah</h5>
        </div>
        <div class="card-body px-4">
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit['id'] ?>">
            <input type="hidden" name="logo_lama" value="<?= $edit['logo'] ?>">
            <div class="row">
              <div class="col-md-6 mb-3"><label class="fw-semibold">Nama Sekolah</label><input type="text" name="nama_sekolah" class="form-control" value="<?= htmlspecialchars($edit['nama_sekolah']) ?>"></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">NPSN</label><input type="text" name="npsn" class="form-control" value="<?= htmlspecialchars($edit['npsn']) ?>"></div>
              <div class="col-md-12 mb-3"><label class="fw-semibold">Alamat</label><input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($edit['alamat']) ?>"></div>
              <div class="col-md-4 mb-3"><label class="fw-semibold">Desa</label><input type="text" name="desa" class="form-control" value="<?= htmlspecialchars($edit['desa']) ?>"></div>
              <div class="col-md-4 mb-3"><label class="fw-semibold">Kecamatan</label><input type="text" name="kecamatan" class="form-control" value="<?= htmlspecialchars($edit['kecamatan']) ?>"></div>
              <div class="col-md-4 mb-3"><label class="fw-semibold">Kabupaten</label><input type="text" name="kabupaten" class="form-control" value="<?= htmlspecialchars($edit['kabupaten']) ?>"></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Provinsi</label><input type="text" name="provinsi" class="form-control" value="<?= htmlspecialchars($edit['provinsi']) ?>"></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Kepala Sekolah</label><input type="text" name="kepala_sekolah" class="form-control" value="<?= htmlspecialchars($edit['kepala_sekolah']) ?>"></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($edit['email']) ?>"></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Telepon</label><input type="text" name="telepon" class="form-control" value="<?= htmlspecialchars($edit['telepon']) ?>"></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Website</label><input type="text" name="website" class="form-control" value="<?= htmlspecialchars($edit['website']) ?>"></div>
              <div class="col-md-6 mb-3">
                <label class="fw-semibold">Logo</label><br>
                <?php if ($edit['logo']): ?><img src="upload/<?= $edit['logo'] ?>" width="80" class="mb-2 rounded"><br><?php endif; ?>
                <input type="file" name="logo" class="form-control">
              </div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Visi</label><textarea name="visi" class="form-control" rows="4"><?= htmlspecialchars($edit['visi']) ?></textarea></div>
              <div class="col-md-6 mb-3"><label class="fw-semibold">Misi</label><textarea name="misi" class="form-control" rows="4"><?= htmlspecialchars($edit['misi']) ?></textarea></div>
              <div class="col-12 mb-3"><label class="fw-semibold">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($edit['deskripsi']) ?></textarea></div>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update</button>
              <a href="data_sekolah.php" class="btn btn-secondary">Batal</a>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>

      <!-- DATA TABLE -->
      <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-header border-0 pt-3 px-4 d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0">Data Profil Sekolah</h5>
          <a href="input_sekolah.php" class="btn btn-sm btn-primary" style="border-radius:8px;"><i class="bi bi-plus me-1"></i>Tambah</a>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered align-middle">
            <thead><tr><th>No</th><th>Nama Sekolah</th><th>NPSN</th><th>Logo</th><th class="text-center">Aksi</th></tr></thead>
            <tbody>
              <?php $no=$offset+1; while($d=mysqli_fetch_assoc($data)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td class="fw-semibold"><?= htmlspecialchars($d['nama_sekolah']) ?></td>
                <td><?= $d['npsn'] ?></td>
                <td><?php if($d['logo']): ?><img src="upload/<?= $d['logo'] ?>" width="60" class="rounded"><?php else: ?>-<?php endif; ?></td>
                <td class="text-center">
                  <a href="?edit=<?= $d['id'] ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil"></i></a>
                  <a href="?hapus=<?= $d['id'] ?>" onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer clearfix">
          <ul class="pagination pagination-sm m-0 float-end">
            <?php for($i=1;$i<=$totalPage;$i++): ?>
              <li class="page-item <?= ($i==$page?'active':'') ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
          </ul>
        </div>
      </div>

    </div>
  </div>
</main>
<?php include "template/footer.php"; ?>