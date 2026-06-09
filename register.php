<?php
session_start();

if (isset($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';
$old   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'config/koneksi.php';

    $nama     = trim($_POST['nama']     ?? '');
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $no_telp  = trim($_POST['no_telp']  ?? '');
    $password = $_POST['password']      ?? '';
    $konfirm  = $_POST['konfirmasi']    ?? '';

    $old = compact('nama', 'username', 'email', 'no_telp');

    if (empty($nama) || empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field wajib diisi (kecuali No. Telepon).';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $user_safe  = mysqli_real_escape_string($koneksi, $username);
        $email_safe = mysqli_real_escape_string($koneksi, $email);

        $cek = mysqli_query($koneksi,
            "SELECT id FROM users WHERE username = '$user_safe' OR email = '$email_safe' LIMIT 1");

        if (mysqli_num_rows($cek) > 0) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            $hash   = password_hash($password, PASSWORD_DEFAULT);
            $nama_s = mysqli_real_escape_string($koneksi, $nama);
            $telp_s = mysqli_real_escape_string($koneksi, $no_telp);

            $insert = mysqli_query($koneksi,
                "INSERT INTO users (nama, username, email, password, no_telp, role)
                 VALUES ('$nama_s', '$user_safe', '$email_safe', '$hash', '$telp_s', 'user')");

            if ($insert) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Gagal mendaftar. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | LaundryKu</title>
    <!-- Google Fonts: Montserrat + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

<div class="auth-container" style="max-width:480px;">

    <!-- Logo -->
    <div class="auth-logo">
        <div class="logo-icon"><i class="fa-solid fa-shirt"></i></div>
        <h1>LaundryKu</h1>
        <p>Daftar sebagai pelanggan baru</p>
    </div>

    <!-- Card Register -->
    <div class="auth-card">
        <h2>Buat Akun Baru</h2>
        <p class="subtitle">Isi data diri Anda untuk mendaftar.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" data-auto-hide>
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label class="form-label">
                    <i class="fa-solid fa-id-card"></i> Nama Lengkap <span style="color:var(--danger)">*</span>
                </label>
                <input type="text"
                       name="nama"
                       class="form-control"
                       placeholder="Contoh: Budi Santoso"
                       value="<?= htmlspecialchars($old['nama'] ?? '') ?>"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-at"></i> Username <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           placeholder="username_anda"
                           value="<?= htmlspecialchars($old['username'] ?? '') ?>"
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-phone"></i> No. Telepon
                    </label>
                    <input type="tel"
                           name="no_telp"
                           class="form-control"
                           placeholder="08xxxxxxxxxx"
                           value="<?= htmlspecialchars($old['no_telp'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fa-solid fa-envelope"></i> Email <span style="color:var(--danger)">*</span>
                </label>
                <input type="email"
                       name="email"
                       class="form-control"
                       placeholder="email@contoh.com"
                       value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-lock"></i> Password <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Min. 6 karakter"
                           required>
                </div>
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-lock"></i> Konfirmasi <span style="color:var(--danger)">*</span>
                    </label>
                    <input type="password"
                           name="konfirmasi"
                           class="form-control"
                           placeholder="Ulangi password"
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:.5rem;">
                <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
            </button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php"><strong>Masuk di sini</strong></a>
        </div>
        <div class="auth-footer" style="margin-top:.4rem;">
            <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
