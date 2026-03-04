<?php
/**
 * AJAX CHAT — ajax/pesan_handler.php
 * Handles: kirim pesan, ambil pesan, mark as read
 */
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'login') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include '../../koneksi.php';
include '../template/notif_helper.php';

$action       = $_POST['action'] ?? $_GET['action'] ?? '';
$id_pengirim  = (int)($_SESSION['id_admin'] ?? 0);

// Fallback: ambil id dari username session
if (!$id_pengirim) {
    $u = mysqli_real_escape_string($koneksi, $_SESSION['username']);
    $r = mysqli_query($koneksi, "SELECT id FROM admin WHERE username='$u' LIMIT 1");
    $d = mysqli_fetch_assoc($r);
    $id_pengirim = (int)($d['id'] ?? 0);
    $_SESSION['id_admin'] = $id_pengirim;
}

header('Content-Type: application/json');

switch ($action) {

    // ── Kirim pesan ──────────────────────────────
    case 'kirim':
        $penerima_id = (int)($_POST['penerima_id'] ?? 0);
        $pesan       = trim($_POST['pesan'] ?? '');
        if (!$penerima_id || !$pesan) { echo json_encode(['ok'=>false]); exit; }

        $pesan_safe = mysqli_real_escape_string($koneksi, $pesan);
        mysqli_query($koneksi,
            "INSERT INTO pesan_admin (pengirim_id, penerima_id, pesan, dibaca, created_at)
             VALUES ($id_pengirim, $penerima_id, '$pesan_safe', 0, NOW())"
        );
        echo json_encode(['ok' => true, 'id' => mysqli_insert_id($koneksi)]);
        break;

    // ── Ambil pesan (polling) ─────────────────────
    case 'ambil':
        $partner_id  = (int)($_GET['partner_id'] ?? 0);
        $last_id     = (int)($_GET['last_id'] ?? 0);
        if (!$partner_id) { echo json_encode([]); exit; }

        // Mark as read
        mysqli_query($koneksi,
            "UPDATE pesan_admin SET dibaca=1
             WHERE penerima_id=$id_pengirim AND pengirim_id=$partner_id AND dibaca=0"
        );

        $r = mysqli_query($koneksi,
            "SELECT p.*, a.nama_admin AS nama_pengirim
             FROM pesan_admin p
             JOIN admin a ON a.id = p.pengirim_id
             WHERE ((p.pengirim_id=$id_pengirim AND p.penerima_id=$partner_id)
                 OR (p.pengirim_id=$partner_id AND p.penerima_id=$id_pengirim))
               AND p.id > $last_id
             ORDER BY p.created_at ASC
             LIMIT 50"
        );
        $rows = [];
        while ($d = mysqli_fetch_assoc($r)) $rows[] = $d;
        echo json_encode($rows);
        break;

    // ── Semua pesan (load awal) ───────────────────
    case 'load':
        $partner_id = (int)($_GET['partner_id'] ?? 0);
        if (!$partner_id) { echo json_encode([]); exit; }

        mysqli_query($koneksi,
            "UPDATE pesan_admin SET dibaca=1
             WHERE penerima_id=$id_pengirim AND pengirim_id=$partner_id AND dibaca=0"
        );

        $r = mysqli_query($koneksi,
            "SELECT p.*, a.nama_admin AS nama_pengirim
             FROM pesan_admin p
             JOIN admin a ON a.id = p.pengirim_id
             WHERE (p.pengirim_id=$id_pengirim AND p.penerima_id=$partner_id)
                OR (p.pengirim_id=$partner_id AND p.penerima_id=$id_pengirim)
             ORDER BY p.created_at ASC
             LIMIT 100"
        );
        $rows = [];
        while ($d = mysqli_fetch_assoc($r)) $rows[] = $d;
        echo json_encode($rows);
        break;

    default:
        echo json_encode(['error' => 'Unknown action']);
}