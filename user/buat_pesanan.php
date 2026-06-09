<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header('Location: ../login.php'); exit;
}

$baseUrl   = '../';
$pageTitle = 'Buat Pesanan';
$userId    = (int)$_SESSION['user_id'];
$error     = '';

include '../config/koneksi.php';

$layananResult = mysqli_query($koneksi, "SELECT * FROM layanan WHERE status='aktif' ORDER BY nama_layanan");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $layanan_id   = (int)($_POST['layanan_id']   ?? 0);
    $berat        = (float)($_POST['berat']       ?? 0);
    $catatan      = trim($_POST['catatan']        ?? '');
    $metode_bayar = $_POST['metode_bayar']        ?? 'cash';

    if ($layanan_id <= 0 || $berat <= 0) {
        $error = 'Pilih layanan dan masukkan berat yang valid.';
    } else {
        $layananRow = mysqli_fetch_assoc(mysqli_query($koneksi,
            "SELECT * FROM layanan WHERE id=$layanan_id AND status='aktif' LIMIT 1"));

        if (!$layananRow) {
            $error = 'Layanan tidak ditemukan atau tidak aktif.';
        } else {
            $total_harga = $layananRow['harga'] * $berat;
            $kode        = 'LDR-' . strtoupper(substr(md5(uniqid()), 0, 8));
            $catatan_s   = mysqli_real_escape_string($koneksi, $catatan);
            $metode_s    = mysqli_real_escape_string($koneksi, $metode_bayar);

            if (mysqli_query($koneksi,
                "INSERT INTO pesanan (kode_pesanan, user_id, layanan_id, berat, total_harga, catatan, status)
                 VALUES ('$kode', $userId, $layanan_id, $berat, $total_harga, '$catatan_s', 'pending')")) {
                $pesanan_id = mysqli_insert_id($koneksi);
                mysqli_query($koneksi,
                    "INSERT INTO pembayaran (pesanan_id, jumlah, metode, status)
                     VALUES ($pesanan_id, $total_harga, '$metode_s', 'belum_bayar')");
                $_SESSION['flash'] = ['type' => 'success', 'msg' => "Pesanan <strong>$kode</strong> berhasil dibuat!"];
                header('Location: riwayat.php'); exit;
            } else {
                $error = 'Gagal membuat pesanan. Silakan coba lagi.';
            }
        }
    }
}

$preSelected = (int)($_GET['layanan_id'] ?? 0);
include '../template/header.php';
?>

<div class="wrapper">
    <?php include '../template/sidebar_user.php'; ?>
    <div class="main-content">
        <?php include '../template/navbar.php'; ?>
        <div class="page-content">

            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fa-solid fa-house"></i></a>
                <span class="breadcrumb-sep">/</span>
                <span>Buat Pesanan</span>
            </div>

            <div class="page-header">
                <div>
                    <h2><i class="fa-solid fa-plus"></i> Buat Pesanan Baru</h2>
                    <p>Isi formulir di bawah untuk membuat pesanan laundry.</p>
                </div>
                <a href="layanan.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Lihat Layanan
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
                            <label class="form-label">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                Pilih Layanan <span style="color:var(--danger)">*</span>
                            </label>
                            <select name="layanan_id" id="layanan_id" class="form-control" required>
                                <option value="">-- Pilih Layanan --</option>
                                <?php
                                mysqli_data_seek($layananResult, 0);
                                while ($l = mysqli_fetch_assoc($layananResult)):
                                    $sel = ($l['id'] == $preSelected || $l['id'] == ($_POST['layanan_id'] ?? 0)) ? 'selected' : '';
                                ?>
                                <option value="<?= $l['id'] ?>"
                                        data-harga="<?= $l['harga'] ?>"
                                        data-satuan="<?= $l['satuan'] ?>"
                                        <?= $sel ?>>
                                    <?= htmlspecialchars($l['nama_layanan']) ?> —
                                    Rp <?= number_format($l['harga'], 0, ',', '.') ?>/<?= $l['satuan'] ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-weight-hanging"></i>
                                    Berat (kg) <span style="color:var(--danger)">*</span>
                                </label>
                                <input type="number" name="berat" id="berat" class="form-control"
                                       placeholder="Contoh: 2.5" step="0.1" min="0.1"
                                       value="<?= htmlspecialchars($_POST['berat'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fa-solid fa-calculator"></i> Estimasi Total
                                </label>
                                <div style="padding:.6rem .9rem;background:var(--gray-100);
                                            border:1.5px solid var(--gray-200);border-radius:var(--radius-sm);
                                            font-weight:700;color:var(--primary);font-size:1rem;">
                                    <span id="total_harga_display">Rp 0</span>
                                </div>
                                <input type="hidden" id="harga_per_satuan" value="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-credit-card"></i> Metode Pembayaran
                            </label>
                            <select name="metode_bayar" class="form-control">
                                <option value="cash"     <?= ($_POST['metode_bayar'] ?? 'cash') === 'cash'     ? 'selected' : '' ?>>
                                    <i class="fa-solid fa-money-bill"></i> Cash (Tunai)
                                </option>
                                <option value="transfer" <?= ($_POST['metode_bayar'] ?? '')     === 'transfer' ? 'selected' : '' ?>>Transfer Bank</option>
                                <option value="qris"     <?= ($_POST['metode_bayar'] ?? '')     === 'qris'     ? 'selected' : '' ?>>QRIS</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fa-solid fa-note-sticky"></i> Catatan (Opsional)
                            </label>
                            <textarea name="catatan" class="form-control"
                                      placeholder="Contoh: Ada pakaian putih, harap dipisah."><?= htmlspecialchars($_POST['catatan'] ?? '') ?></textarea>
                        </div>

                        <hr class="divider">
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fa-solid fa-basket-shopping"></i> Konfirmasi Pesanan
                        </button>
                    </form>
                </div>
            </div>

        </div>
        <?php include '../template/footer.php'; ?>
    </div>
</div>

<script>
document.getElementById('layanan_id').addEventListener('change', function () {
    const opt   = this.options[this.selectedIndex];
    const harga = opt.getAttribute('data-harga') || 0;
    document.getElementById('harga_per_satuan').value = harga;
    document.getElementById('berat').dispatchEvent(new Event('input'));
});
window.addEventListener('DOMContentLoaded', function () {
    document.getElementById('layanan_id').dispatchEvent(new Event('change'));
});
</script>
