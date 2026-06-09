<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Dashboard Saya';
$userId    = (int)$_SESSION['user_id'];

include '../config/koneksi.php';
include '../template/header.php';

$totalPesanan   = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM pesanan WHERE user_id=$userId"))[0] ?? 0;
$pesananProses  = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM pesanan WHERE user_id=$userId AND status IN ('pending','diproses')"))[0] ?? 0;
$pesananSelesai = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM pesanan WHERE user_id=$userId AND status='selesai'"))[0] ?? 0;
$totalBelanja   = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COALESCE(SUM(total_harga),0) FROM pesanan WHERE user_id=$userId"))[0] ?? 0;

$hasilPesanan = mysqli_query($koneksi,
    "SELECT p.*, l.nama_layanan
     FROM pesanan p
     JOIN layanan l ON p.layanan_id = l.id
     WHERE p.user_id = $userId
     ORDER BY p.tanggal_masuk DESC LIMIT 5");
?>

<div class="wrapper">
    <?php include '../template/sidebar_user.php'; ?>

    <div class="main-content">
        <?php include '../template/navbar.php'; ?>

        <div class="page-content">

            <div class="breadcrumb">
                <i class="fa-solid fa-house"></i>
                <span class="breadcrumb-sep">/</span>
                <span>Dashboard</span>
            </div>

            <!-- Sapaan -->
            <div class="card" style="margin-bottom:1.5rem;background:linear-gradient(135deg,#2563eb,#0ea5e9);border:none;">
                <div class="card-body" style="color:white;">
                    <h2 style="font-size:1.4rem;margin-bottom:.3rem;">
                        <i class="fa-solid fa-star"></i>
                        Halo, <?= htmlspecialchars($_SESSION['nama']) ?>!
                    </h2>
                    <p style="opacity:.85;font-size:.9rem;">
                        Selamat datang di LaundryKu. Pantau pesanan Anda di sini.
                    </p>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div class="stat-info">
                        <div class="label">Total Pesanan</div>
                        <div class="value"><?= $totalPesanan ?></div>
                        <div class="sub">sepanjang waktu</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div class="stat-info">
                        <div class="label">Sedang Diproses</div>
                        <div class="value"><?= $pesananProses ?></div>
                        <div class="sub">pesanan aktif</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                    <div class="stat-info">
                        <div class="label">Selesai</div>
                        <div class="value"><?= $pesananSelesai ?></div>
                        <div class="sub">pesanan selesai</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cyan"><i class="fa-solid fa-coins"></i></div>
                    <div class="stat-info">
                        <div class="label">Total Belanja</div>
                        <div class="value" style="font-size:1.3rem;">
                            Rp <?= number_format($totalBelanja, 0, ',', '.') ?>
                        </div>
                        <div class="sub">semua pesanan</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div style="display:flex;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                <a href="buat_pesanan.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Buat Pesanan Baru
                </a>
                <a href="layanan.php" class="btn btn-outline">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Lihat Layanan
                </a>
                <a href="riwayat.php" class="btn btn-secondary">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pesanan
                </a>
            </div>

            <!-- Pesanan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa-solid fa-clipboard-list"></i> Pesanan Terbaru Saya</h3>
                    <a href="riwayat.php" class="btn btn-outline btn-sm">Lihat Semua</a>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>Layanan</th>
                                <th>Berat</th>
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
                                <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                                <td><?= $row['berat'] ?> kg</td>
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
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-basket-shopping" style="font-size:3rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                                        <h3>Belum ada pesanan</h3>
                                        <p>Yuk, buat pesanan laundry pertama Anda!</p>
                                        <a href="buat_pesanan.php" class="btn btn-primary" style="margin-top:.75rem;">
                                            <i class="fa-solid fa-plus"></i> Buat Pesanan
                                        </a>
                                    </div>
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
