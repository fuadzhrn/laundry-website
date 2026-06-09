<?php
/**
 * Script setup password untuk akun demo LaundryKu.
 * Jalankan SEKALI via browser, lalu HAPUS file ini.
 * URL: http://localhost/Laundry/setup_password.php
 */

include 'config/koneksi.php';

// Generate hash yang benar menggunakan PHP
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$user_hash  = password_hash('user123',  PASSWORD_DEFAULT);

$log = [];

// ---- Admin ----
$cek = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE username='admin'"))[0];
if ($cek > 0) {
    $ok = mysqli_query($koneksi, "UPDATE users SET password='$admin_hash' WHERE username='admin'");
    $log[] = ['ok' => $ok, 'msg' => "Password akun <strong>admin</strong> berhasil diperbarui."];
} else {
    $ok = mysqli_query($koneksi,
        "INSERT INTO users (nama, username, email, password, role)
         VALUES ('Administrator', 'admin', 'admin@laundryku.com', '$admin_hash', 'admin')");
    $log[] = ['ok' => $ok, 'msg' => "Akun <strong>admin</strong> berhasil dibuat."];
}

// ---- User Demo ----
$cek = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE username='budi'"))[0];
if ($cek > 0) {
    $ok = mysqli_query($koneksi, "UPDATE users SET password='$user_hash' WHERE username='budi'");
    $log[] = ['ok' => $ok, 'msg' => "Password akun <strong>budi</strong> berhasil diperbarui."];
} else {
    $ok = mysqli_query($koneksi,
        "INSERT INTO users (nama, username, email, password, no_telp, role)
         VALUES ('Budi Santoso', 'budi', 'budi@email.com', '$user_hash', '081234567890', 'user')");
    $log[] = ['ok' => $ok, 'msg' => "Akun <strong>budi</strong> berhasil dibuat."];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Password | LaundryKu</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #f1f5f9; display: flex;
               align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .box { background: #fff; border-radius: 12px; padding: 2rem 2.5rem;
               box-shadow: 0 4px 20px rgba(0,0,0,.1); max-width: 480px; width: 100%; }
        h2  { color: #1e293b; margin-bottom: 1.5rem; font-size: 1.3rem; }
        .item { display: flex; align-items: center; gap: .6rem; padding: .6rem 0;
                border-bottom: 1px solid #f1f5f9; font-size: .9rem; }
        .ok   { color: #166534; background: #dcfce7; border-radius: 50%; width: 22px; height: 22px;
                display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; flex-shrink:0; }
        .err  { color: #991b1b; background: #fee2e2; border-radius: 50%; width: 22px; height: 22px;
                display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; flex-shrink:0; }
        .creds { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
                 padding: 1rem 1.25rem; margin: 1.5rem 0; font-size: .9rem; line-height: 1.8; }
        .creds strong { color: #2563eb; }
        .btn { display: inline-block; background: #2563eb; color: #fff; padding: .65rem 1.5rem;
               border-radius: 8px; text-decoration: none; font-weight: 600; margin-right: .5rem; font-size: .9rem; }
        .warn { background: #fef3c7; border: 1px solid #fcd34d; border-radius: 8px;
                padding: .75rem 1rem; font-size: .8rem; color: #92400e; margin-top: 1.5rem; }
    </style>
</head>
<body>
<div class="box">
    <h2>&#9881; Setup Password LaundryKu</h2>

    <?php foreach ($log as $item): ?>
    <div class="item">
        <span class="<?= $item['ok'] ? 'ok' : 'err' ?>"><?= $item['ok'] ? '&#10003;' : '&#10007;' ?></span>
        <?= $item['msg'] ?>
    </div>
    <?php endforeach; ?>

    <div class="creds">
        <strong>Akun Admin</strong><br>
        Username: <strong>admin</strong> &nbsp;|&nbsp; Password: <strong>admin123</strong><br><br>
        <strong>Akun Pelanggan Demo</strong><br>
        Username: <strong>budi</strong> &nbsp;|&nbsp; Password: <strong>user123</strong>
    </div>

    <a href="login.php" class="btn">&#8594; Coba Login Sekarang</a>

    <div class="warn">
        <strong>&#9888; Penting:</strong> Hapus file <code>setup_password.php</code> setelah berhasil login
        agar tidak bisa diakses orang lain.
    </div>
</div>
</body>
</html>
