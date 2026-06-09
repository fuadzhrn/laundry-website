<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Daftar Layanan';

include '../config/koneksi.php';
include '../template/header.php';

$result = mysqli_query($koneksi,
    "SELECT * FROM layanan WHERE status = 'aktif' ORDER BY harga ASC");
?>

<div class="wrapper">
    <?php include '../template/sidebar_user.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Layanan</span>
            </div>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-wand-magic-sparkles"></i> Layanan Kami</h2>
                    <p>Pilih layanan yang sesuai kebutuhan Anda.</p>
                </div>
                <a href="buat_pesanan.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Buat Pesanan
                </a>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="features-grid">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="feature-card" style="text-align:left;">
                        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                            <div class="feature-icon" style="margin:0;width:48px;height:48px;flex-shrink:0;">
                                <i class="fa-solid fa-shirt"></i>
                            </div>
                            <div>
                                <h3 style="margin-bottom:.15rem;"><?= htmlspecialchars($row['nama_layanan']) ?></h3>
                                <span class="badge badge-success">
                                    <i class="fa-solid fa-circle-check"></i> <?= $row['status'] ?>
                                </span>
                            </div>
                        </div>
                        <p style="margin-bottom:.75rem;color:var(--gray-600);font-size:.875rem;">
                            <?= htmlspecialchars($row['deskripsi'] ?: 'Layanan laundry berkualitas tinggi.') ?>
                        </p>
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div>
                                <span style="font-size:1.4rem;font-weight:800;color:var(--primary);">
                                    Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                                </span>
                                <span class="text-muted">/ <?= $row['satuan'] ?></span>
                            </div>
                            <a href="buat_pesanan.php?layanan_id=<?= $row['id'] ?>"
                               class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-basket-shopping"></i> Pesan
                            </a>
                        </div>
                    </div>
                <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fa-solid fa-wand-magic-sparkles" style="font-size:3rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                            <h3>Belum ada layanan tersedia</h3>
                            <p>Silakan hubungi admin untuk informasi lebih lanjut.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
        <?php include '../template/footer.php'; ?>
    </div>
</div>
