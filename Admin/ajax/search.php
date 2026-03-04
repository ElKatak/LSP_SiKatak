<?php
/**
 * AJAX SEARCH — ajax/search.php
 * Returns JSON array of search results.
 */
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo json_encode([]);
    exit;
}

include '../../koneksi.php';

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit; }

$q_safe = mysqli_real_escape_string($koneksi, $q);
$results = [];

// ── Berita ──
$r = mysqli_query($koneksi, "SELECT id, judul, penulis FROM berita WHERE judul LIKE '%$q_safe%' OR isi LIKE '%$q_safe%' LIMIT 5");
while ($d = mysqli_fetch_assoc($r)) {
    $results[] = [
        'type'  => 'Berita',
        'label' => $d['judul'],
        'sub'   => 'Penulis: ' . $d['penulis'],
        'url'   => '../data_berita.php',
    ];
}

// ── Guru ──
$r = mysqli_query($koneksi, "SELECT id, nama_guru, mapel FROM guru WHERE nama_guru LIKE '%$q_safe%' OR mapel LIKE '%$q_safe%' LIMIT 5");
while ($d = mysqli_fetch_assoc($r)) {
    $results[] = [
        'type'  => 'Guru',
        'label' => $d['nama_guru'],
        'sub'   => 'Mapel: ' . $d['mapel'],
        'url'   => '../data_guru.php',
    ];
}

// ── Galeri ──
$r = mysqli_query($koneksi, "SELECT id, judul, deskripsi FROM galeri WHERE judul LIKE '%$q_safe%' LIMIT 5");
while ($d = mysqli_fetch_assoc($r)) {
    $results[] = [
        'type'  => 'Galeri',
        'label' => $d['judul'],
        'sub'   => $d['deskripsi'] ?? '',
        'url'   => '../data_galeri.php',
    ];
}

// ── Kontak ──
$r = mysqli_query($koneksi, "SELECT id, nama, pesan FROM kontak WHERE nama LIKE '%$q_safe%' OR pesan LIKE '%$q_safe%' LIMIT 5");
while ($d = mysqli_fetch_assoc($r)) {
    $results[] = [
        'type'  => 'Kontak',
        'label' => $d['nama'],
        'sub'   => $d['pesan'] ?? '',
        'url'   => '../data_kontak.php',
    ];
}

// ── Profil Sekolah ──
$r = mysqli_query($koneksi, "SELECT id, nama_sekolah FROM profil_sekolah WHERE nama_sekolah LIKE '%$q_safe%' LIMIT 3");
while ($d = mysqli_fetch_assoc($r)) {
    $results[] = [
        'type'  => 'Profil',
        'label' => $d['nama_sekolah'],
        'sub'   => '',
        'url'   => '../data_sekolah.php',
    ];
}

header('Content-Type: application/json');
echo json_encode(array_slice($results, 0, 15));