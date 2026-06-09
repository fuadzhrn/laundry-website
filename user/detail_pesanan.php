<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php'); exit;
}

$userId = (int)$_SESSION['user_id'];
$id     = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: riwayat.php'); exit; }

include '../config/koneksi.php';

$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT p.*, u.nama AS nama_pelanggan, u.email, u.no_telp,
            l.nama_layanan, l.harga AS harga_satuan, l.satuan
     FROM pesanan p
     JOIN users u   ON p.user_id   = u.id
     JOIN layanan l ON p.layanan_id = l.id
     WHERE p.id = $id AND p.user_id = $userId LIMIT 1"));

if (!$pesanan) { header('Location: riwayat.php'); exit; }

$pembayaran = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM pembayaran WHERE pesanan_id = $id LIMIT 1"));

$baseUrl   = '../';
$pageTitle = 'Detail Pesanan';
include '../template/header.php';

$statusBadge = ['pending'=>'warning','diproses'=>'info','selesai'=>'success','diambil'=>'gray'];

// Urutan dan icon untuk status tracker
$steps = [
    'pending'  => ['icon' => 'fa-clock',        'label' => 'Pesanan Diterima'],
    'diproses' => ['icon' => 'fa-rotate',        'label' => 'Sedang Diproses'],
    'selesai'  => ['icon' => 'fa-wand-magic-sparkles', 'label' => 'Selesai Dicuci'],
    'diambil'  => ['icon' => 'fa-circle-check',  'label' => 'Sudah Diambil'],
];
$statusOrder = array_keys($steps);
$currentIdx  = array_search($pesanan['status'], $statusOrder);
?>

<div class="wrapper">
    <?php include '../template/sidebar_user.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <a href="riwayat.php">Riwayat</a>
                <span class="breadcrumb-sep">/</span>
                <span><?= htmlspecialchars($pesanan['kode_pesanan']) ?></span>
            </div>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-file-lines"></i> Detail Pesanan</h2>
                    <p>Kode: <strong><?= htmlspecialchars($pesanan['kode_pesanan']) ?></strong></p>
                </div>
                <a href="riwayat.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

                <!-- Info Pesanan -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-basket-shopping"></i> Info Pesanan</h3>
                    </div>
                    <div class="card-body">
                        <div class="detail-list">
                            <div class="detail-item">
                                <span class="label">Kode</span>
                                <span class="value fw-bold"><?= htmlspecialchars($pesanan['kode_pesanan']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Layanan</span>
                                <span class="value"><?= htmlspecialchars($pesanan['nama_layanan']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Berat</span>
                                <span class="value"><?= $pesanan['berat'] ?> <?= $pesanan['satuan'] ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Harga Satuan</span>
                                <span class="value">Rp <?= number_format($pesanan['harga_satuan'], 0, ',', '.') ?> / <?= $pesanan['satuan'] ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Total Harga</span>
                                <span class="value fw-bold" style="color:var(--primary);font-size:1.1rem;">
                                    Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Catatan</span>
                                <span class="value"><?= htmlspecialchars($pesanan['catatan'] ?: '-') ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Status</span>
                                <span class="value">
                                    <span class="badge badge-<?= $statusBadge[$pesanan['status']] ?? 'gray' ?>">
                                        <?= $pesanan['status'] ?>
                                    </span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="label">Masuk</span>
                                <span class="value"><?= date('d/m/Y H:i', strtotime($pesanan['tanggal_masuk'])) ?></span>
                            </div>
                            <?php if ($pesanan['tanggal_selesai']): ?>
                            <div class="detail-item">
                                <span class="label">Selesai</span>
                                <span class="value"><?= date('d/m/Y H:i', strtotime($pesanan['tanggal_selesai'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:1.25rem;">

                    <!-- Status Tracker -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-route"></i> Status Pesanan</h3>
                        </div>
                        <div class="card-body">
                            <div style="display:flex;flex-direction:column;gap:.85rem;">
                            <?php foreach ($steps as $key => $step):
                                  $idx  = array_search($key, $statusOrder);
                                  $done = $idx <= $currentIdx;
                                  $curr = $key === $pesanan['status'];
                            ?>
                                <div style="display:flex;align-items:center;gap:.75rem;">
                                    <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;
                                                display:flex;align-items:center;justify-content:center;font-size:1rem;
                                                background:<?= $done ? 'var(--success-light)' : 'var(--gray-100)' ?>;
                                                border:2px solid <?= $curr ? 'var(--success)' : ($done ? 'var(--success)' : 'var(--gray-200)') ?>;
                                                color:<?= $done ? 'var(--success)' : 'var(--gray-400)' ?>;">
                                        <i class="fa-solid <?= $step['icon'] ?>"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:<?= $curr ? '700' : '500' ?>;
                                                    color:<?= $done ? 'var(--dark)' : 'var(--gray-400)' ?>;
                                                    font-size:.9rem;">
                                            <?= $step['label'] ?>
                                        </div>
                                        <?php if ($curr): ?>
                                        <div style="font-size:.72rem;color:var(--success);font-weight:600;">
                                            <i class="fa-solid fa-circle-dot"></i> Status saat ini
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Info Pembayaran -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-wallet"></i> Info Pembayaran</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($pembayaran): ?>
                            <div class="detail-list">
                                <div class="detail-item">
                                    <span class="label">Jumlah</span>
                                    <span class="value">Rp <?= number_format($pembayaran['jumlah'], 0, ',', '.') ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Metode</span>
                                    <span class="value">
                                        <i class="fa-solid fa-credit-card"></i>
                                        <?= strtoupper($pembayaran['metode']) ?>
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <span class="label">Status</span>
                                    <span class="value">
                                        <span class="badge <?= $pembayaran['status'] === 'lunas' ? 'badge-success' : 'badge-warning' ?>">
                                            <?= $pembayaran['status'] === 'lunas'
                                                ? '<i class="fa-solid fa-circle-check"></i> Lunas'
                                                : '<i class="fa-solid fa-clock"></i> Belum Bayar' ?>
                                        </span>
                                    </span>
                                </div>
                                <?php if ($pembayaran['tanggal_bayar']): ?>
                                <div class="detail-item">
                                    <span class="label">Tgl. Bayar</span>
                                    <span class="value"><?= date('d/m/Y H:i', strtotime($pembayaran['tanggal_bayar'])) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($pembayaran['status'] === 'belum_bayar'): ?>
                                <div class="alert alert-warning" style="margin-top:1rem;">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Harap lakukan pembayaran sesuai metode yang dipilih dan konfirmasi kepada admin.
                                </div>
                            <?php endif; ?>
                            <?php else: ?>
                                <p class="text-muted">
                                    <i class="fa-solid fa-circle-info"></i> Belum ada data pembayaran.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <?php include '../template/footer.php'; ?>
    </div>
</div>
