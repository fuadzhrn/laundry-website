<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Riwayat Pesanan';
$userId    = (int)$_SESSION['user_id'];

include '../config/koneksi.php';
include '../template/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$filterStatus = $_GET['status'] ?? '';
$where = "WHERE p.user_id = $userId";
if ($filterStatus) {
    $st = mysqli_real_escape_string($koneksi, $filterStatus);
    $where .= " AND p.status = '$st'";
}

$result = mysqli_query($koneksi,
    "SELECT p.*, l.nama_layanan, pb.status AS status_bayar, pb.metode
     FROM pesanan p
     JOIN layanan l ON p.layanan_id = l.id
     LEFT JOIN pembayaran pb ON pb.pesanan_id = p.id
     $where
     ORDER BY p.tanggal_masuk DESC");
?>

<div class="wrapper">
    <?php include '../template/sidebar_user.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Riwayat Pesanan</span>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>" data-auto-hide>
                    <i class="fa-solid fa-circle-check"></i> <?= $flash['msg'] ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Pesanan Saya</h2>
                    <p>Semua pesanan yang pernah Anda buat.</p>
                </div>
                <a href="buat_pesanan.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Pesanan Baru
                </a>
            </div>

            <!-- Filter Status -->
            <div style="display:flex;gap:.5rem;margin-bottom:1.25rem;flex-wrap:wrap;">
                <a href="riwayat.php" class="btn <?= !$filterStatus ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                    <i class="fa-solid fa-list"></i> Semua
                </a>
                <?php foreach (['pending'=>'clock','diproses'=>'rotate','selesai'=>'circle-check','diambil'=>'box-archive'] as $st => $ico): ?>
                <a href="riwayat.php?status=<?= $st ?>"
                   class="btn <?= $filterStatus === $st ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                    <i class="fa-solid fa-<?= $ico ?>"></i> <?= ucfirst($st) ?>
                </a>
                <?php endforeach; ?>
            </div>

            <div class="card">
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
                                <th>Pembayaran</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                              $no = 1;
                              while ($row = mysqli_fetch_assoc($result)): ?>
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
                                <td>
                                    <span class="badge <?= ($row['status_bayar'] ?? '') === 'lunas' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= $row['status_bayar'] === 'lunas'
                                            ? '<i class="fa-solid fa-circle-check"></i> Lunas'
                                            : '<i class="fa-solid fa-clock"></i> Belum Bayar' ?>
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
                                <td colspan="9">
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
