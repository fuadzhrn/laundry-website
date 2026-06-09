<?php
// Topbar untuk halaman dashboard (admin & user) — Font Awesome 6
$navTitle = $pageTitle ?? 'Dashboard';
$namaUser = $_SESSION['nama'] ?? $_SESSION['username'] ?? 'Pengguna';
$roleUser = $_SESSION['role'] ?? 'user';
$baseUrl  = $baseUrl ?? '';
$inisial  = strtoupper(substr($namaUser, 0, 2));
?>
<div class="topbar">
    <div class="topbar-title">
        <h1><?= htmlspecialchars($navTitle) ?></h1>
        <p><?= date('l, d F Y') ?></p>
    </div>

    <div class="topbar-actions">
        <div class="topbar-user">
            <div class="stat-icon blue" style="width:34px;height:34px;border-radius:50%;font-size:.85rem;flex-shrink:0;">
                <?= $inisial ?>
            </div>
            <div>
                <div class="name"><?= htmlspecialchars($namaUser) ?></div>
                <div class="role"><?= htmlspecialchars($roleUser) ?></div>
            </div>
        </div>

        <a href="<?= $baseUrl ?>logout.php"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Yakin ingin keluar?')">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </a>
    </div>
</div>
