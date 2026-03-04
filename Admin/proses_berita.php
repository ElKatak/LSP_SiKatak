<?php
session_start();
include "../koneksi.php";
include "template/notif_helper.php"; // ← notifikasi helper

$judul   = $_POST['judul'];
$isi     = $_POST['isi'];
$penulis = $_POST['penulis'];
$tanggal = date('Y-m-d');

$folder = "upload/";
if (!is_dir($folder)) mkdir($folder, 0777, true);

$nama_gambar = $_FILES['gambar']['name'];
$tmp_gambar  = $_FILES['gambar']['tmp_name'];
$ext         = pathinfo($nama_gambar, PATHINFO_EXTENSION);
$nama_baru   = time() . "_" . rand(100, 999) . "." . $ext;

$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($ext), $allowed)) {
    echo "Format gambar tidak diizinkan!";
    exit;
}

if (move_uploaded_file($tmp_gambar, $folder . $nama_baru)) {
    $judul_safe   = mysqli_real_escape_string($koneksi, $judul);
    $isi_safe     = mysqli_real_escape_string($koneksi, $isi);
    $penulis_safe = mysqli_real_escape_string($koneksi, $penulis);

    $sql = "INSERT INTO berita (judul, isi, gambar, tanggal, penulis)
            VALUES ('$judul_safe','$isi_safe','$nama_baru','$tanggal','$penulis_safe')";

    if (mysqli_query($koneksi, $sql)) {
        // ── Tambah notifikasi ──
        tambah_notif('berita', 'Berita baru dipublikasikan', '"' . $judul . '" oleh ' . $penulis);

        echo "<script>alert('Berita berhasil disimpan');window.location='input_berita.php';</script>";
    } else {
        echo "Gagal menyimpan data";
    }
} else {
    echo "Upload gambar gagal";
}