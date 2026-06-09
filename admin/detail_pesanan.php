<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

include '../config/koneksi.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: pesanan.php'); exit; }

$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT p.*, u.nama AS nama_pelanggan, u.no_telp, u.email,
            l.nama_layanan, l.harga AS harga_satuan, l.satuan
     FROM pesanan p
     JOIN users u   ON p.user_id   = u.id
     JOIN layanan l ON p.layanan_id = l.id
     WHERE p.id = $id LIMIT 1"));
if (!$pesanan) { header('Location: pesanan.php'); exit; }

$pembayaran = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT * FROM pembayaran WHERE pesanan_id = $id LIMIT 1"));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status_baru     = mysqli_real_escape_string($koneksi, $_POST['status_baru']);
    $tanggal_selesai = ($status_baru === 'selesai') ? ", tanggal_selesai = NOW()" : '';
    mysqli_query($koneksi, "UPDATE pesanan SET status='$status_baru' $tanggal_selesai WHERE id=$id");
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Status pesanan berhasil diperbarui.'];
    header("Location: detail_pesanan.php?id=$id"); exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$baseUrl   = '../';
$pageTitle = 'Detail Pesanan';
include '../template/header.php';

$statusBadge = ['pending'=>'warning','diproses'=>'info','selesai'=>'success','diambil'=>'gray'];
?>

<div class="wrapper">
    <?php include '../template/sidebar_admin.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <a href="pesanan.php">Pesanan</a>
                <span class="breadcrumb-sep">/</span>
                <span><?= htmlspecialchars($pesanan['kode_pesanan']) ?></span>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>" data-auto-hide>
                    <i class="fa-solid fa-circle-check"></i> <?= $flash['msg'] ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-file-lines"></i> Detail Pesanan</h2>
                    <p>Kode: <strong><?= htmlspecialchars($pesanan['kode_pesanan']) ?></strong></p>
                </div>
                <a href="pesanan.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">

                <!-- Info Pesanan -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-clipboard-list"></i> Info Pesanan</h3>
                    </div>
                    <div class="card-body">
                        <div class="detail-list">
                            <div class="detail-item"><span class="label">Kode Pesanan</span><span class="value"><?= htmlspecialchars($pesanan['kode_pesanan']) ?></span></div>
                            <div class="detail-item"><span class="label">Layanan</span><span class="value"><?= htmlspecialchars($pesanan['nama_layanan']) ?></span></div>
                            <div class="detail-item"><span class="label">Berat</span><span class="value"><?= $pesanan['berat'] ?> <?= $pesanan['satuan'] ?></span></div>
                            <div class="detail-item"><span class="label">Harga Satuan</span><span class="value">Rp <?= number_format($pesanan['harga_satuan'], 0, ',', '.') ?></span></div>
                            <div class="detail-item"><span class="label">Total Harga</span><span class="value fw-bold">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></span></div>
                            <div class="detail-item"><span class="label">Catatan</span><span class="value"><?= htmlspecialchars($pesanan['catatan'] ?: '-') ?></span></div>
                            <div class="detail-item">
                                <span class="label">Status</span>
                                <span class="value">
                                    <span class="badge badge-<?= $statusBadge[$pesanan['status']] ?? 'gray' ?>">
                                        <?= $pesanan['status'] ?>
                                    </span>
                                </span>
                            </div>
                            <div class="detail-item"><span class="label">Tanggal Masuk</span><span class="value"><?= date('d/m/Y H:i', strtotime($pesanan['tanggal_masuk'])) ?></span></div>
                            <?php if ($pesanan['tanggal_selesai']): ?>
                            <div class="detail-item"><span class="label">Tanggal Selesai</span><span class="value"><?= date('d/m/Y H:i', strtotime($pesanan['tanggal_selesai'])) ?></span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:1.25rem;">

                    <!-- Info Pelanggan -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-user"></i> Info Pelanggan</h3>
                        </div>
                        <div class="card-body">
                            <div class="detail-list">
                                <div class="detail-item"><span class="label">Nama</span><span class="value"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?></span></div>
                                <div class="detail-item"><span class="label">Email</span><span class="value"><?= htmlspecialchars($pesanan['email']) ?></span></div>
                                <div class="detail-item"><span class="label">Telepon</span><span class="value"><?= htmlspecialchars($pesanan['no_telp'] ?: '-') ?></span></div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Status -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-rotate"></i> Update Status</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label class="form-label">Status Pesanan</label>
                                    <select name="status_baru" class="form-control">
                                        <?php foreach (['pending','diproses','selesai','diambil'] as $s): ?>
                                        <option value="<?= $s ?>" <?= $pesanan['status'] === $s ? 'selected' : '' ?>>
                                            <?= ucfirst($s) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary">
                                    <i class="fa-solid fa-check"></i> Perbarui Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Pembayaran -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-wallet"></i> Pembayaran</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($pembayaran): ?>
                                <div class="detail-list">
                                    <div class="detail-item"><span class="label">Metode</span><span class="value"><?= strtoupper($pembayaran['metode']) ?></span></div>
                                    <div class="detail-item"><span class="label">Jumlah</span><span class="value">Rp <?= number_format($pembayaran['jumlah'], 0, ',', '.') ?></span></div>
                                    <div class="detail-item">
                                        <span class="label">Status</span>
                                        <span class="value">
                                            <span class="badge <?= $pembayaran['status'] === 'lunas' ? 'badge-success' : 'badge-warning' ?>">
                                                <?= $pembayaran['status'] ?>
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Belum ada data pembayaran.</p>
                                <a href="pembayaran.php" class="btn btn-outline btn-sm" style="margin-top:.75rem;">
                                    <i class="fa-solid fa-wallet"></i> Kelola Pembayaran
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        <?php include '../template/footer.php'; ?>
    </div>
</div>
