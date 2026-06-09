<?php
// Sidebar User/Pelanggan — menggunakan Font Awesome 6 icons
$currentFile = basename($_SERVER['PHP_SELF']);
$namaUser    = $_SESSION['nama'] ?? 'Pelanggan';
$inisial     = strtoupper(substr($namaUser, 0, 2));

function isActiveUser($file) {
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
            <div class="brand-sub">Area Pelanggan</div>
        </div>
    </div>

    <!-- Menu Navigasi -->
    <nav class="sidebar-menu">

        <div class="sidebar-section-label">Menu</div>

        <div class="sidebar-item">
            <a href="dashboard.php" class="sidebar-link <?= isActiveUser('dashboard.php') ?>">
                <span class="icon"><i class="fa-solid fa-house"></i></span>
                Dashboard
            </a>
        </div>

        <div class="sidebar-item">
            <a href="layanan.php" class="sidebar-link <?= isActiveUser('layanan.php') ?>">
                <span class="icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                Lihat Layanan
            </a>
        </div>

        <div class="sidebar-item">
            <a href="buat_pesanan.php" class="sidebar-link <?= isActiveUser('buat_pesanan.php') ?>">
                <span class="icon"><i class="fa-solid fa-plus"></i></span>
                Buat Pesanan
            </a>
        </div>

        <div class="sidebar-item">
            <a href="riwayat.php" class="sidebar-link <?= isActiveUser('riwayat.php') ?>">
                <span class="icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                Riwayat Pesanan
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
                <div class="user-name"><?= htmlspecialchars($namaUser) ?></div>
                <div class="user-role">Pelanggan</div>
            </div>
        </div>
    </div>

</aside>
