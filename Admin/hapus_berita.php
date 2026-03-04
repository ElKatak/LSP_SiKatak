<?php
// koneksi database
include '../koneksi.php';
// menangkap data id yang di kirim dari url
$id = $_GET['id'];
// menghapus data dari database
mysqli_query($koneksi,"delete from berita where id='$id'");
// mengalihkan halaman kembali ke data_kontak.php
header("location:data_berita.php");
?>
