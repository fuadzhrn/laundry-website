<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Kelola Layanan';

include '../config/koneksi.php';
include '../template/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$result = mysqli_query($koneksi, "SELECT * FROM layanan ORDER BY id DESC");
?>

<div class="wrapper">
    <?php include '../template/sidebar_admin.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Layanan</span>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>" data-auto-hide>
                    <i class="fa-solid fa-circle-check"></i> <?= $flash['msg'] ?>
                </div>
            <?php endif; ?>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-wand-magic-sparkles"></i> Kelola Layanan</h2>
                    <p>Daftar semua jenis layanan laundry.</p>
                </div>
                <a href="tambah_layanan.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Layanan
                </a>
            </div>

            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Layanan</th>
                                <th>Deskripsi</th>
                                <th>Harga</th>
                                <th>Satuan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                              $no = 1;
                              while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_layanan']) ?></strong></td>
                                <td><?= htmlspecialchars($row['deskripsi'] ?: '-') ?></td>
                                <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['satuan']) ?></td>
                                <td>
                                    <span class="badge <?= $row['status'] === 'aktif' ? 'badge-success' : 'badge-gray' ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_layanan.php?id=<?= $row['id'] ?>"
                                           class="btn btn-warning btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                        </a>
                                        <form method="POST" action="hapus_layanan.php"
                                              data-confirm="Hapus layanan '<?= htmlspecialchars($row['nama_layanan']) ?>'?">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="fa-solid fa-wand-magic-sparkles icon" style="font-size:3rem;opacity:.3;"></i>
                                        <h3>Belum ada layanan</h3>
                                        <p>Tambahkan layanan laundry pertama Anda.</p>
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
