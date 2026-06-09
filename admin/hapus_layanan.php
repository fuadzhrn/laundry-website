<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

// Hanya proses request POST untuk keamanan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: layanan.php'); exit;
}

include '../config/koneksi.php';

$id = (int)($_POST['id'] ?? 0);

if ($id > 0) {
    // Cek apakah layanan masih dipakai di pesanan
    $cek = mysqli_fetch_row(
        mysqli_query($koneksi, "SELECT COUNT(*) FROM pesanan WHERE layanan_id = $id")
    )[0];

    if ($cek > 0) {
        $_SESSION['flash'] = [
            'type' => 'danger',
            'msg'  => '&#x26A0; Layanan tidak bisa dihapus karena masih terkait dengan pesanan.'
        ];
    } else {
        mysqli_query($koneksi, "DELETE FROM layanan WHERE id = $id");
        $_SESSION['flash'] = [
            'type' => 'success',
            'msg'  => '&#x2714; Layanan berhasil dihapus.'
        ];
    }
}

header('Location: layanan.php');
exit;
