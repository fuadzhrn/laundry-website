<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

include '../config/koneksi.php';
/** @var \mysqli $koneksi */

$id    = (int)($_GET['id'] ?? 0);
$error = '';

if ($id <= 0) { header('Location: layanan.php'); exit; }

$result  = mysqli_query($koneksi, "SELECT * FROM layanan WHERE id = $id LIMIT 1");
$layanan = mysqli_fetch_assoc($result);
if (!$layanan) { header('Location: layanan.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_layanan = trim($_POST['nama_layanan'] ?? '');
    $deskripsi    = trim($_POST['deskripsi']    ?? '');
    $harga        = $_POST['harga']  ?? 0;
    $satuan       = trim($_POST['satuan'] ?? 'kg');
    $status       = $_POST['status'] ?? 'aktif';

    if (empty($nama_layanan) || empty($harga)) {
        $error = 'Nama layanan dan harga wajib diisi.';
    } else {
        $nama_s  = mysqli_real_escape_string($koneksi, $nama_layanan);
        $desk_s  = mysqli_real_escape_string($koneksi, $deskripsi);
        $sat_s   = mysqli_real_escape_string($koneksi, $satuan);
        $stat_s  = mysqli_real_escape_string($koneksi, $status);
        $harga_f = (float) $harga;

        if (mysqli_query($koneksi,
            "UPDATE layanan SET nama_layanan='$nama_s', deskripsi='$desk_s',
             harga=$harga_f, satuan='$sat_s', status='$stat_s' WHERE id=$id")) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Layanan berhasil diperbarui.'];
            header('Location: layanan.php'); exit;
        } else {
            $error = 'Gagal memperbarui layanan.';
        }
    }
    $layanan = array_merge($layanan, $_POST);
}

$baseUrl   = '../';
$pageTitle = 'Edit Layanan';
include '../template/header.php';
?>

<div class="wrapper">
    <?php include '../template/sidebar_admin.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <a href="layanan.php">Layanan</a>
                <span class="breadcrumb-sep">/</span>
                <span>Edit</span>
            </div>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-pen-to-square"></i> Edit Layanan</h2>
                    <p>Perbarui data: <strong><?= htmlspecialchars($layanan['nama_layanan']) ?></strong></p>
                </div>
                <a href="layanan.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="card" style="max-width:600px;">
                <div class="card-body">

                    <?php if ($error): ?>
                        <div class="alert alert-danger" data-auto-hide>
                            <i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">

                        <div class="form-group">
                            <label class="form-label">Nama Layanan <span style="color:var(--danger)">*</span></label>
                            <input type="text" name="nama_layanan" class="form-control"
                                   value="<?= htmlspecialchars($layanan['nama_layanan']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control"><?= htmlspecialchars($layanan['deskripsi']) ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Harga (Rp) <span style="color:var(--danger)">*</span></label>
                                <input type="number" name="harga" class="form-control"
                                       value="<?= htmlspecialchars($layanan['harga']) ?>" min="0" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Satuan</label>
                                <select name="satuan" class="form-control">
                                    <option value="kg"    <?= $layanan['satuan'] === 'kg'    ? 'selected' : '' ?>>Kilogram (kg)</option>
                                    <option value="pcs"   <?= $layanan['satuan'] === 'pcs'   ? 'selected' : '' ?>>Per Buah (pcs)</option>
                                    <option value="lusin" <?= $layanan['satuan'] === 'lusin' ? 'selected' : '' ?>>Lusin</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif"    <?= $layanan['status'] === 'aktif'    ? 'selected' : '' ?>>Aktif</option>
                                <option value="nonaktif" <?= $layanan['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <hr class="divider">
                        <div style="display:flex;gap:.75rem;">
                            <button type="submit" class="btn btn-warning">
                                <i class="fa-solid fa-floppy-disk"></i> Update Layanan
                            </button>
                            <a href="layanan.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <?php include '../template/footer.php'; ?>
    </div>
</div>
