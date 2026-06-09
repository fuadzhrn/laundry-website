<?php
// Sidebar Admin — menggunakan Font Awesome 6 icons
$currentFile = basename($_SERVER['PHP_SELF']);
$namaAdmin   = $_SESSION['nama'] ?? 'Admin';
$inisial     = strtoupper(substr($namaAdmin, 0, 2));

function isActive($file) {
    global $currentFile;
    return $currentFile === $file ? 'active' : '';
}
?>
<aside class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-shirt"></i></div>
        <div>
            <div class="brand-text">LaundryKu</div>
            <div class="brand-sub">Panel Admin</div>
        </div>
    </div>

    <!-- Menu Navigasi -->
    <nav class="sidebar-menu">

        <div class="sidebar-section-label">Menu Utama</div>

        <div class="sidebar-item">
            <a href="dashboard.php" class="sidebar-link <?= isActive('dashboard.php') ?>">
                <span class="icon"><i class="fa-solid fa-house"></i></span>
                Dashboard
            </a>
        </div>

        <div class="sidebar-section-label">Manajemen</div>

        <div class="sidebar-item">
            <a href="layanan.php" class="sidebar-link <?= isActive('layanan.php') ?>">
                <span class="icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                Layanan
            </a>
        </div>

        <div class="sidebar-item">
            <a href="pesanan.php" class="sidebar-link <?= isActive('pesanan.php') ?>">
                <span class="icon"><i class="fa-solid fa-clipboard-list"></i></span>
                Pesanan
            </a>
        </div>

        <div class="sidebar-item">
            <a href="pembayaran.php" class="sidebar-link <?= isActive('pembayaran.php') ?>">
                <span class="icon"><i class="fa-solid fa-wallet"></i></span>
                Pembayaran
            </a>
        </div>

        <div class="sidebar-item">
            <a href="pelanggan.php" class="sidebar-link <?= isActive('pelanggan.php') ?>">
                <span class="icon"><i class="fa-solid fa-users"></i></span>
                Pelanggan
            </a>
        </div>

        <div class="sidebar-section-label">Lainnya</div>

        <div class="sidebar-item">
            <a href="../index.php" class="sidebar-link">
                <span class="icon"><i class="fa-solid fa-globe"></i></span>
                Halaman Utama
            </a>
        </div>

        <div class="sidebar-item">
            <a href="../logout.php"
               class="sidebar-link"
               onclick="return confirm('Yakin ingin keluar?')">
                <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                Keluar
            </a>
        </div>

    </nav>

    <!-- Info User di Bawah Sidebar -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar"><?= $inisial ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($namaAdmin) ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>

</aside>
