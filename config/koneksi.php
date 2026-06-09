<?php
// Konfigurasi koneksi database MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_laundry');

$koneksi = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$koneksi) {
    die('
    <div style="padding:20px;background:#fee2e2;border:1px solid #fca5a5;color:#dc2626;
                font-family:system-ui,sans-serif;border-radius:8px;margin:20px;">
        <strong>Koneksi Database Gagal!</strong><br>
        Error: ' . mysqli_connect_error() . '<br><br>
        Pastikan MySQL berjalan dan database <strong>db_laundry</strong> sudah dibuat.
    </div>');
}

// Set charset ke UTF-8 agar mendukung karakter Indonesia
mysqli_set_charset($koneksi, 'utf8');
