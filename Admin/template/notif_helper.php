<?php
/**
 * NOTIF HELPER
 * Include this di setiap proses_*.php untuk otomatis tambah notifikasi ke session.
 * Pastikan session_start() sudah dipanggil sebelumnya (atau di header.php).
 */

function tambah_notif(string $type, string $judul, string $body): void {
    if (!isset($_SESSION['notifikasi'])) {
        $_SESSION['notifikasi'] = [];
    }
    array_unshift($_SESSION['notifikasi'], [
        'id'     => uniqid('n_', true),
        'type'   => $type,   // berita | galeri | guru | kontak | profil
        'judul'  => $judul,
        'body'   => $body,
        'waktu'  => date('Y-m-d H:i:s'),
        'dibaca' => false,
    ]);
    // simpan maks 30 notif terbaru
    $_SESSION['notifikasi'] = array_slice($_SESSION['notifikasi'], 0, 30);
}

function hitung_notif_belum_dibaca(): int {
    if (!isset($_SESSION['notifikasi'])) return 0;
    return count(array_filter($_SESSION['notifikasi'], fn($n) => !$n['dibaca']));
}

function format_waktu_relatif(string $waktu): string {
    $diff = time() - strtotime($waktu);
    if ($diff < 60)      return 'Baru saja';
    if ($diff < 3600)    return floor($diff / 60)   . ' mnt lalu';
    if ($diff < 86400)   return floor($diff / 3600)  . ' jam lalu';
    return floor($diff / 86400) . ' hari lalu';
}