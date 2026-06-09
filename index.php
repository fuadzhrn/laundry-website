<?php
session_start();

// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit;
}

$baseUrl   = '';
$pageTitle = 'Beranda';
$bodyClass = 'landing-body';   // class body untuk halaman landing
include 'template/header.php';
?>

<!-- ==================== NAVBAR LANDING ==================== -->
<nav class="landing-nav">
    <div class="nav-inner">
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fa-solid fa-shirt"></i></div>
            LaundryKu
        </a>
        <div class="nav-links">
            <a href="login.php"    class="btn btn-outline btn-sm">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </a>
            <a href="register.php" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-user-plus"></i> Daftar
            </a>
        </div>
    </div>
</nav>

<!-- ==================== HERO ==================== -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">
            <i class="fa-solid fa-star"></i> Layanan Laundry Terpercaya
        </div>
        <h1>Laundry Bersih, <span>Hidup Lebih</span> Nyaman</h1>
        <p>Sistem manajemen laundry modern yang memudahkan Anda memantau pesanan,
           pembayaran, dan layanan secara real-time.</p>
        <div class="hero-cta">
            <a href="register.php" class="btn-hero-primary">
                <i class="fa-solid fa-arrow-right"></i> Mulai Sekarang
            </a>
            <a href="login.php" class="btn-hero-outline">
                Sudah punya akun?
            </a>
        </div>
    </div>
</section>

<!-- ==================== FEATURES ==================== -->
<section class="features">
    <div class="section-header">
        <h2>Kenapa Pilih LaundryKu?</h2>
        <p>Fitur lengkap yang memudahkan pengelolaan laundry Anda sehari-hari.</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-clipboard-list"></i></div>
            <h3>Kelola Pesanan</h3>
            <p>Pantau status pesanan pelanggan dari masuk hingga selesai secara mudah.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-wallet"></i></div>
            <h3>Pembayaran Praktis</h3>
            <p>Catat dan verifikasi pembayaran dengan berbagai metode: cash, transfer, dan QRIS.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-users"></i></div>
            <h3>Manajemen Pelanggan</h3>
            <p>Data pelanggan tersimpan rapi sehingga mudah diakses kapan pun dibutuhkan.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
            <h3>Laporan Lengkap</h3>
            <p>Lihat ringkasan transaksi dan statistik bisnis laundry Anda dengan mudah.</p>
        </div>
    </div>
</section>

<!-- ==================== HOW IT WORKS ==================== -->
<section class="how-it-works">
    <div class="how-it-works-inner">
        <div class="section-header">
            <h2>Cara Menggunakan</h2>
            <p>Hanya butuh beberapa langkah untuk memulai.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Daftar Akun</h3>
                <p>Buat akun pelanggan secara gratis hanya dalam 1 menit.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>Pilih Layanan</h3>
                <p>Lihat daftar layanan dan harga yang tersedia.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Buat Pesanan</h3>
                <p>Isi formulir pesanan dengan detail cucian Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-number">4</div>
                <h3>Pantau Status</h3>
                <p>Lihat perkembangan laundry Anda secara real-time.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== FOOTER ==================== -->
<footer class="landing-footer">
    <p>&copy; <?= date('Y') ?> <strong>LaundryKu</strong> &mdash; Sistem Manajemen Laundry Berbasis Web</p>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>
