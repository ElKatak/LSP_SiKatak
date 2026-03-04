<?php
include "template/header.php";
include "template/menu.php";
include "../koneksi.php";
/* =========================
 PROSES HAPUS DATA
========================= */
if (isset($_GET['hapus'])) {
 $id = (int)$_GET['hapus'];
 $q = mysqli_query($koneksi, "SELECT logo FROM profil_sekolah WHERE id='$id'");
 $d = mysqli_fetch_assoc($q);
 if ($d) {
 if (!empty($d['logo']) && file_exists("upload/".$d['logo'])) {
 unlink("upload/".$d['logo']);
 }
 mysqli_query($koneksi, "DELETE FROM profil_sekolah WHERE id='$id'");
 }
 echo "<script>alert('Data berhasil dihapus');window.location='data_sekolah.php';</script>";
 exit;
}
/* =========================
 PROSES EDIT DATA
========================= */
if (isset($_POST['update'])) {
 $id = $_POST['id'];
 $fields = [
 'nama_sekolah','npsn','alamat','desa','kecamatan','kabupaten','provinsi',
 'email','telepon','website','kepala_sekolah','visi','misi','deskripsi'
 ];
 foreach ($fields as $f) {
 $$f = $_POST[$f];
 }
 $logo_lama = $_POST['logo_lama'];
 if (!empty($_FILES['logo']['name'])) {
 $logo_baru = time().'_'.$_FILES['logo']['name'];
 move_uploaded_file($_FILES['logo']['tmp_name'], "upload/".$logo_baru);
 if ($logo_lama && file_exists("upload/".$logo_lama)) {
 unlink("upload/".$logo_lama);
 }
 } else {
 $logo_baru = $logo_lama;
 }
 mysqli_query($koneksi, "UPDATE profil_sekolah SET
 nama_sekolah='$nama_sekolah',
 npsn='$npsn',
 alamat='$alamat',
 desa='$desa',
 kecamatan='$kecamatan',
 kabupaten='$kabupaten',
 provinsi='$provinsi',
 email='$email',
 telepon='$telepon',
 website='$website',
 kepala_sekolah='$kepala_sekolah',
 logo='$logo_baru',
 visi='$visi',
 misi='$misi',
 deskripsi='$deskripsi'
 WHERE id='$id'
 ");
 echo "<script>alert('Profil berhasil
diperbarui');window.location='data_sekolah.php';</script>";
 exit;
}
/* =========================
 DATA EDIT
========================= */
$edit = null;
if (isset($_GET['edit'])) {
 $id = (int)$_GET['edit'];
 $edit = mysqli_fetch_assoc(
 mysqli_query($koneksi, "SELECT * FROM profil_sekolah WHERE id='$id'")
 );
}
/* =========================
 PAGINATION
========================= */
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;
$total = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM profil_sekolah"));
$totalPage = ceil($total / $limit);
$data = mysqli_query($koneksi,
 "SELECT * FROM profil_sekolah ORDER BY id DESC LIMIT $limit OFFSET $offset"
);
?>
<main class="app-main">
<div class="app-content">
<div class="container-fluid">
<?php if ($edit) { ?>
<!-- ================= FORM EDIT ================= -->
<div class="card mb-4">
<div class="card-header"><h3 class="card-title">Edit Profil Sekolah</h3></div>
<div class="card-body">
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="<?= $edit['id']; ?>">
<input type="hidden" name="logo_lama" value="<?= $edit['logo']; ?>">
<div class="row">
<div class="col-md-6 mb-3"><label>Nama Sekolah</label><input type="text"
name="nama_sekolah" class="form-control" value="<?= $edit['nama_sekolah']; ?>"></div>
<div class="col-md-6 mb-3"><label>NPSN</label><input type="text" name="npsn"
class="form-control" value="<?= $edit['npsn']; ?>"></div>
<div class="col-md-12 mb-3"><label>Alamat</label><input type="text" name="alamat"
class="form-control" value="<?= $edit['alamat']; ?>"></div>
<div class="col-md-4 mb-3"><label>Desa</label><input type="text" name="desa"
class="form-control" value="<?= $edit['desa']; ?>"></div>
<div class="col-md-4 mb-3"><label>Kecamatan</label><input type="text" name="kecamatan"
class="form-control" value="<?= $edit['kecamatan']; ?>"></div>
<div class="col-md-4 mb-3"><label>Kabupaten</label><input type="text" name="kabupaten"
class="form-control" value="<?= $edit['kabupaten']; ?>"></div>
<div class="col-md-6 mb-3"><label>Provinsi</label><input type="text" name="provinsi"
class="form-control" value="<?= $edit['provinsi']; ?>"></div>
<div class="col-md-6 mb-3"><label>Kepala Sekolah</label><input type="text"
name="kepala_sekolah" class="form-control" value="<?= $edit['kepala_sekolah']; ?>"></div>
<div class="col-md-6 mb-3"><label>Email</label><input type="email" name="email"
class="form-control" value="<?= $edit['email']; ?>"></div>
<div class="col-md-6 mb-3"><label>Telepon</label><input type="text" name="telepon"
class="form-control" value="<?= $edit['telepon']; ?>"></div>
<div class="col-md-6 mb-3"><label>Website</label><input type="text" name="website"
class="form-control" value="<?= $edit['website']; ?>"></div>
<div class="col-md-6 mb-3">
<label>Logo</label><br>
<img src="upload/<?= $edit['logo']; ?>" width="120" class="mb-2"><br>
<input type="file" name="logo" class="form-control">
</div>
<div class="col-md-12 mb-3"><label>Visi</label><textarea name="visi" class="formcontrol"><?= $edit['visi']; ?></textarea></div>
<div class="col-md-12 mb-3"><label>Misi</label><textarea name="misi" class="formcontrol"><?= $edit['misi']; ?></textarea></div>
<div class="col-md-12 mb-3"><label>Deskripsi</label><textarea name="deskripsi"
class="form-control"><?= $edit['deskripsi']; ?></textarea></div>
</div>
<div class="card-footer">
<button type="submit" name="update" class="btn btn-primary">Update</button>
<a href="data_profil_sekolah.php" class="btn btn-secondary">Batal</a>
</div>
</form>
</div>
</div>
<?php } ?>
<!-- ================= DATA TABLE ================= -->
<div class="card">
<div class="card-header"><h3 class="card-title">Data Profil Sekolah</h3></div>
<div class="card-body table-responsive">
<table class="table table-bordered">
<tr><th>No</th><th>Nama Sekolah</th><th>NPSN</th><th>Logo</th><th>Aksi</th></tr>
<?php $no=$offset+1; while($d=mysqli_fetch_assoc($data)){ ?>
<tr>
<td><?= $no++; ?></td>
<td><?= $d['nama_sekolah']; ?></td>
<td><?= $d['npsn']; ?></td>
<td><?php if($d['logo']){ ?><img src="upload/<?= $d['logo']; ?>" width="70"><?php }
?></td>
<td>
<a href="?edit=<?= $d['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
<a href="?hapus=<?= $d['id']; ?>" onclick="return confirm('Hapus data?')" class="btn btndanger btn-sm">Hapus</a>
</td>
</tr>
<?php } ?>
</table>
</div>
<!-- PAGINATION -->
<div class="card-footer">
<ul class="pagination pagination-sm float-end">
<?php for($i=1;$i<=$totalPage;$i++){ ?>
<li class="page-item <?= ($i==$page?'active':''); ?>">
<a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
</li>
<?php } ?>
</ul>
</div>
</div>
</div>
</div>
</main>
<?php include "template/footer.php"; ?>