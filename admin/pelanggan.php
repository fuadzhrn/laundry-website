<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Data Pelanggan';

include '../config/koneksi.php';
include '../template/header.php';

$result = mysqli_query($koneksi,
    "SELECT u.*,
            COUNT(p.id) AS total_pesanan,
            COALESCE(SUM(p.total_harga), 0) AS total_belanja
     FROM users u
     LEFT JOIN pesanan p ON u.id = p.user_id
     WHERE u.role = 'user'
     GROUP BY u.id
     ORDER BY u.created_at DESC");
?>

<div class="wrapper">
    <?php include '../template/sidebar_admin.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Pelanggan</span>
            </div>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-users"></i> Data Pelanggan</h2>
                    <p>Daftar seluruh pelanggan terdaftar.</p>
                </div>
            </div>

            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Total Pesanan</th>
                                <th>Total Belanja</th>
                                <th>Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                              $no = 1;
                              while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:.5rem;">
                                        <div class="stat-icon blue"
                                             style="width:32px;height:32px;border-radius:50%;font-size:.75rem;flex-shrink:0;">
                                            <?= strtoupper(substr($row['nama'], 0, 2)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa-solid fa-at" style="color:var(--gray-400);"></i>
                                    <?= htmlspecialchars($row['username']) ?>
                                </td>
                                <td>
                                    <i class="fa-solid fa-envelope" style="color:var(--gray-400);"></i>
                                    <?= htmlspecialchars($row['email']) ?>
                                </td>
                                <td>
                                    <?php if ($row['no_telp']): ?>
                                        <i class="fa-solid fa-phone" style="color:var(--gray-400);"></i>
                                        <?= htmlspecialchars($row['no_telp']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                        <?= $row['total_pesanan'] ?> pesanan
                                    </span>
                                </td>
                                <td>Rp <?= number_format($row['total_belanja'], 0, ',', '.') ?></td>
                                <td>
                                    <i class="fa-regular fa-calendar" style="color:var(--gray-400);"></i>
                                    <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-users" style="font-size:3rem;opacity:.3;display:block;margin-bottom:.75rem;"></i>
                                        <h3>Belum ada pelanggan terdaftar</h3>
                                        <p>Pelanggan akan muncul setelah mendaftar.</p>
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
