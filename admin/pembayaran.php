<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Kelola Pembayaran';

include '../config/koneksi.php';
include '../template/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_id'])) {
    $pid = (int)$_POST['konfirmasi_id'];
    mysqli_query($koneksi, "UPDATE pembayaran SET status='lunas', tanggal_bayar=NOW() WHERE id=$pid");
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Pembayaran berhasil dikonfirmasi.'];
    header('Location: pembayaran.php'); exit;
}

$result = mysqli_query($koneksi,
    "SELECT pb.*, p.kode_pesanan, p.total_harga, u.nama AS nama_pelanggan
     FROM pembayaran pb
     JOIN pesanan p ON pb.pesanan_id = p.id
     JOIN users u   ON p.user_id = u.id
     ORDER BY pb.created_at DESC");
?>

<div class="wrapper">
    <?php include '../template/sidebar_admin.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Pembayaran</span>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>" data-auto-hide>
                    <i class="fa-solid fa-circle-check"></i> <?= $flash['msg'] ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-wallet"></i> Kelola Pembayaran</h2>
                    <p>Konfirmasi dan pantau status pembayaran pelanggan.</p>
                </div>
            </div>

            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Pesanan</th>
                                <th>Pelanggan</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Tanggal Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                              $no = 1;
                              while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="detail_pesanan.php?id=<?= $row['pesanan_id'] ?>">
                                        <strong><?= htmlspecialchars($row['kode_pesanan']) ?></strong>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['nama_pelanggan']) ?></td>
                                <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-primary">
                                        <i class="fa-solid fa-credit-card"></i> <?= strtoupper($row['metode']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $row['status'] === 'lunas' ? 'badge-success' : 'badge-warning' ?>">
                                        <?= $row['status'] === 'lunas'
                                            ? '<i class="fa-solid fa-circle-check"></i> Lunas'
                                            : '<i class="fa-solid fa-clock"></i> Belum Bayar' ?>
                                    </span>
                                </td>
                                <td><?= $row['tanggal_bayar'] ? date('d/m/Y H:i', strtotime($row['tanggal_bayar'])) : '-' ?></td>
                                <td>
                                    <?php if ($row['status'] === 'belum_bayar'): ?>
                                    <form method="POST" action=""
                                          data-confirm="Konfirmasi pembayaran <?= htmlspecialchars($row['kode_pesanan']) ?>?">
                                        <input type="hidden" name="konfirmasi_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa-solid fa-check"></i> Konfirmasi
                                        </button>
                                    </form>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:.8rem;">
                                            <i class="fa-solid fa-circle-check"></i> Lunas
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-wallet" style="font-size:3rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                                        <h3>Belum ada data pembayaran</h3>
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
