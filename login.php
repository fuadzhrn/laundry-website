<?php
session_start();

if (isset($_SESSION['role'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'config/koneksi.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $username_safe = mysqli_real_escape_string($koneksi, $username);
        $sql = "SELECT * FROM users
                WHERE (username = '$username_safe' OR email = '$username_safe')
                LIMIT 1";
        $result = mysqli_query($koneksi, $sql);
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['role']     = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | LaundryKu</title>
    <!-- Google Fonts: Montserrat + Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">

<div class="auth-container">

    <!-- Logo -->
    <div class="auth-logo">
        <div class="logo-icon"><i class="fa-solid fa-shirt"></i></div>
        <h1>LaundryKu</h1>
        <p>Sistem Manajemen Laundry</p>
    </div>

    <!-- Card Login -->
    <div class="auth-card">
        <h2>Selamat Datang Kembali</h2>
        <p class="subtitle">Masuk ke akun Anda untuk melanjutkan.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" data-auto-hide>
                <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success" data-auto-hide>
                <i class="fa-solid fa-circle-check"></i> Akun berhasil dibuat! Silakan masuk.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['logout'])): ?>
            <div class="alert alert-info" data-auto-hide>
                <i class="fa-solid fa-circle-info"></i> Anda telah keluar dari sistem.
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="username">
                    <i class="fa-solid fa-user"></i> Username atau Email
                </label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-control"
                       placeholder="Masukkan username atau email"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="fa-solid fa-lock"></i> Password
                </label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Masukkan password"
                       required>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:.5rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="register.php"><strong>Daftar sekarang</strong></a>
        </div>
        <div class="auth-footer" style="margin-top:.4rem;">
            <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>

    <!-- Hint akun demo -->
    <div style="text-align:center;margin-top:1.25rem;font-size:.8rem;color:rgba(255,255,255,.7);">
        Demo admin: <strong style="color:white;">admin</strong> /
        <strong style="color:white;">admin123</strong>
    </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
