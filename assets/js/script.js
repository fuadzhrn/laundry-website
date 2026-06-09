// LaundryKu - Main JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // --- Auto-hide alert setelah 4 detik ---
    const alerts = document.querySelectorAll('.alert[data-auto-hide]');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity .4s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });

    // --- Konfirmasi hapus data ---
    const deleteForms = document.querySelectorAll('form[data-confirm]');
    deleteForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const msg = form.dataset.confirm || 'Apakah Anda yakin ingin menghapus data ini?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // --- Konfirmasi hapus via tombol/link ---
    const deleteLinks = document.querySelectorAll('[data-confirm-link]');
    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            const msg = link.dataset.confirmLink || 'Apakah Anda yakin?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // --- Hitung total harga pesanan secara real-time ---
    const beratInput  = document.getElementById('berat');
    const hargaInput  = document.getElementById('harga_per_satuan');
    const totalOutput = document.getElementById('total_harga_display');

    if (beratInput && hargaInput && totalOutput) {
        function hitungTotal() {
            const berat = parseFloat(beratInput.value) || 0;
            const harga = parseFloat(hargaInput.value) || 0;
            const total = berat * harga;
            totalOutput.textContent = formatRupiah(total);
        }
        beratInput.addEventListener('input', hitungTotal);
        hargaInput.addEventListener('input', hitungTotal);
    }

    // --- Format angka ke Rupiah ---
    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    // --- Mobile sidebar toggle ---
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
        });
        // Tutup sidebar saat klik di luar
        document.addEventListener('click', function (e) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        });
    }

    // --- Highlight baris tabel aktif ---
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(function (row) {
        row.addEventListener('click', function () {
            tableRows.forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');
        });
    });

    // --- Preview gambar upload ---
    const imgInput   = document.getElementById('foto_input');
    const imgPreview = document.getElementById('foto_preview');
    if (imgInput && imgPreview) {
        imgInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => { imgPreview.src = e.target.result; imgPreview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
});
