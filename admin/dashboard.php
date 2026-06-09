<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Dashboard Admin';

include '../config/koneksi.php';
include '../template/header.php';

$totalPelanggan  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM users WHERE role='user'"))[0] ?? 0;
$totalLayanan    = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM layanan WHERE status='aktif'"))[0] ?? 0;
$totalPesanan    = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM pesanan"))[0] ?? 0;
$pesananProses   = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM pesanan WHERE status IN ('pending','diproses')"))[0] ?? 0;
$totalPendapatan = mysqli_fetch_row(mysqli_query($koneksi, "SELECT SUM(jumlah) FROM pembayaran WHERE status='lunas'"))[0] ?? 0;

$hasilPesanan = mysqli_query($koneksi,
    "SELECT p.*, u.nama AS nama_pelanggan, l.nama_layanan
     FROM pesanan p
     JOIN users u ON p.user_id = u.id
     JOIN layanan l ON p.layanan_id = l.id
     ORDER BY p.tanggal_masuk DESC LIMIT 5");
?>

<div class="wrapper">
    <?php include '../template/sidebar_admin.php'; ?>

    <div class="main-content">
        <?php include '../template/navbar.php'; ?>

        <div class="page-content">

            <div class="breadcrumb">
                <i class="fa-solid fa-house"></i>
                <span class="breadcrumb-sep">/</span>
                <span>Dashboard</span>
            </div>

            <!-- Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-users"></i></div>
                    <div class="stat-info">
                        <div class="label">Total Pelanggan</div>
                        <div class="value"><?= $totalPelanggan ?></div>
                        <div class="sub">pengguna terdaftar</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                    <div class="stat-info">
                        <div class="label">Layanan Aktif</div>
                        <div class="value"><?= $totalLayanan ?></div>
                        <div class="sub">jenis layanan tersedia</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div class="stat-info">
                        <div class="label">Total Pesanan</div>
                        <div class="value"><?= $totalPesanan ?></div>
                        <div class="sub"><?= $pesananProses ?> sedang diproses</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fa-solid fa-coins"></i></div>
                    <div class="stat-info">
                        <div class="label">Pendapatan</div>
                        <div class="value" style="font-size:1.3rem;">
                            Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                        </div>
                        <div class="sub">total pembayaran lunas</div>
                    </div>
                </div>
            </div>

            <!-- Pesanan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-clipboard-list"></i> Pesanan Terbaru</h3>
                    <a href="pesanan.php" class="btn btn-outline btn-sm">Lihat Semua</a>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Layanan</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($hasilPesanan) > 0):
                              $no = 1;
                              while ($row = mysqli_fetch_assoc($hasilPesanan)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['kode_pesanan']) ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td>
                                    <?php $badges = ['pending'=>'warning','diproses'=>'info','selesai'=>'success','diambil'=>'gray']; ?>
                                    <span class="badge badge-<?= $badges[$row['status']] ?? 'gray' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['tanggal_masuk'])) ?></td>
                                <td>
                                    <a href="detail_pesanan.php?id=<?= $row['id'] ?>"
                                       class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="8" class="no-data">
                                    <i class="fa-solid fa-clipboard-list" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem;"></i>
                                    Belum ada pesanan masuk.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <?php include '../template/footer.php'; ?>
    </div>
</div>
