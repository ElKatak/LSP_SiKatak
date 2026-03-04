<?php
include "template/header.php";
include "template/menu.php";
include "../koneksi.php";
/* =========================
 PROSES HAPUS DATA
========================= */
if (isset($_GET['hapus'])) {
 $id = (int)$_GET['hapus'];
 // ambil gambar
 $q = mysqli_query($koneksi, "SELECT gambar FROM galeri WHERE id='$id'");
 $d = mysqli_fetch_assoc($q);
 if ($d) {
 if (!empty($d['gambar']) && file_exists("upload/".$d['gambar'])) {
 unlink("upload/".$d['gambar']);
 }
 mysqli_query($koneksi, "DELETE FROM galeri WHERE id='$id'");
 }
 echo "<script>alert('Data berhasil dihapus');window.location='data_galeri.php';</script>";
 exit;
}
/* =========================
 PAGINATION
========================= */
$limit = 5;
$halaman = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$halaman = ($halaman < 1) ? 1 : $halaman;
$offset = ($halaman - 1) * $limit;
$totalData = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM galeri"));
$totalHalaman = ceil($totalData / $limit);
$data = mysqli_query(
 $koneksi,
 "SELECT * FROM galeri ORDER BY id DESC LIMIT $limit OFFSET $offset"
);
?>
<main class="app-main">
 <!-- Header -->
 <div class="app-content-header">
 <div class="container-fluid">
 <div class="row">
 <div class="col-sm-6">
 <h3 class="mb-0">Data Galeri</h3>
 </div>
 <div class="col-sm-6">
 <ol class="breadcrumb float-sm-end">
 <li class="breadcrumb-item"><a href="#">Home</a></li>
 <li class="breadcrumb-item active">Data Galeri</li>
 </ol>
 </div>
 </div>
 </div>
 </div>
 <!-- Content -->
 <div class="app-content">
 <div class="container-fluid">
 <div class="card">
 <div class="card-header">
 <h3 class="card-title">Daftar Galeri</h3>
 </div>
 <div class="card-body table-responsive">
 <table class="table table-bordered table-striped align-middle">
 <thead>
 <tr>
 <th width="5%">No</th>
 <th>Judul</th>
 <th>Deskripsi</th>
 <th width="120">Gambar</th>
 <th width="130">Tanggal Upload</th>
 <th width="100" class="text-center">Aksi</th>
 </tr>
 </thead>
 <tbody>
 <?php
 $no = $offset + 1;
 if (mysqli_num_rows($data) > 0) {
 while ($d = mysqli_fetch_array($data)) {
 ?>
 <tr>
 <td><?php echo $no++; ?></td>
 <td><?php echo htmlspecialchars($d['judul']); ?></td>
 <td><?php echo htmlspecialchars($d['deskripsi']); ?></td>
 <td>
 <?php if (!empty($d['gambar'])) { ?>
 <img src="upload/<?php echo $d['gambar']; ?>" width="90" class="imgthumbnail">
 <?php } else { echo "-"; } ?>
 </td>
 <td><?php echo $d['tanggal_upload']; ?></td>
 <td class="text-center">
<a href="edit_galeri.php?id=<?= $d['id']; ?>" class="btn btn-sm btn-warning me-1"
title="Edit"> <i class="bi bi-pencil-square"></i></a>
 <a href="?hapus=<?php echo $d['id']; ?>"
 class="btn btn-sm btn-danger"
 onclick="return confirm('Yakin ingin menghapus data ini?')">
 <i class="bi bi-trash"></i>
 </a>
 </td>
 </tr>
 <?php
 }
 } else {
 echo "<tr><td colspan='6' class='text-center'>Data kosong</td></tr>";
 }
 ?>
 </tbody>
 </table>
 </div>
 <!-- PAGINATION -->
 <div class="card-footer clearfix">
 <ul class="pagination pagination-sm m-0 float-end">
 <?php if ($halaman > 1) { ?>
 <li class="page-item">
 <a class="page-link" href="?page=<?php echo $halaman-1; ?>">&laquo;</a>
 </li>
 <?php } ?>
 <?php
 for ($i = 1; $i <= $totalHalaman; $i++) {
 $active = ($i == $halaman) ? "active" : "";
 ?>
 <li class="page-item <?php echo $active; ?>">
 <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
 </li>
 <?php } ?>
 <?php if ($halaman < $totalHalaman) { ?>
 <li class="page-item">
 <a class="page-link" href="?page=<?php echo $halaman+1; ?>">&raquo;</a>
 </li>
 <?php } ?>
 </ul>
 </div>
 </div>
 </div>
 </div>
</main>
<?php include "template/footer.php"; ?>