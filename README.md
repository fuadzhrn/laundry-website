# LaundryKu — Sistem Manajemen Laundry Berbasis Web

Aplikasi web manajemen laundry menggunakan **PHP Native**, MySQL, HTML, CSS, dan JavaScript.

---

## Struktur Folder

```
Laundry/
├── index.php               ← Landing page utama
├── login.php               ← Halaman masuk
├── register.php            ← Halaman daftar pelanggan
├── logout.php              ← Handler keluar
├── db_laundry.sql          ← Skrip SQL database
│
├── config/
│   └── koneksi.php         ← Koneksi database MySQL
│
├── admin/
│   ├── dashboard.php       ← Ringkasan statistik admin
│   ├── layanan.php         ← Daftar layanan
│   ├── tambah_layanan.php  ← Form tambah layanan
│   ├── edit_layanan.php    ← Form edit layanan
│   ├── hapus_layanan.php   ← Handler hapus layanan
│   ├── pesanan.php         ← Daftar semua pesanan
│   ├── detail_pesanan.php  ← Detail + update status pesanan
│   ├── pembayaran.php      ← Konfirmasi pembayaran
│   └── pelanggan.php       ← Daftar semua pelanggan
│
├── user/
│   ├── dashboard.php       ← Dashboard pelanggan
│   ├── layanan.php         ← Tampilan layanan untuk pelanggan
│   ├── buat_pesanan.php    ← Form buat pesanan baru
│   ├── riwayat.php         ← Riwayat semua pesanan
│   └── detail_pesanan.php  ← Detail + status tracker pesanan
│
├── template/
│   ├── header.php          ← HTML head + buka body
│   ├── navbar.php          ← Topbar dashboard
│   ├── sidebar_admin.php   ← Sidebar menu admin
│   ├── sidebar_user.php    ← Sidebar menu user
│   └── footer.php          ← Footer + tutup HTML
│
└── assets/
    ├── css/style.css       ← CSS utama
    ├── js/script.js        ← JavaScript utama
    └── img/                ← Folder gambar
```

---

## Cara Menjalankan

### 1. Persiapan

- Pastikan **Laragon** atau **XAMPP** sudah berjalan
- MySQL dan Apache harus aktif
- Letakkan folder `Laundry` di dalam `laragon/www/` atau `htdocs/`

### 2. Buat Database

**Cara A — phpMyAdmin:**
1. Buka `http://localhost/phpmyadmin`
2. Klik **"New"** → beri nama `db_laundry` → klik **Create**
3. Pilih tab **SQL**, tempel isi file `db_laundry.sql`, klik **Go**

**Cara B — Terminal/CMD:**
```bash
mysql -u root -p < db_laundry.sql
```

### 3. Konfigurasi Database

Edit file `config/koneksi.php` jika perlu:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');        // isi password MySQL Anda
define('DB_NAME', 'db_laundry');
```

### 4. Akses Aplikasi

Buka browser dan kunjungi:
```
http://localhost/Laundry
```

---

## Akun Default

| Role  | Username | Password  |
|-------|----------|-----------|
| Admin | `admin`  | `admin123`|
| User  | `budi`   | `user123` |
fuasaauau

> Jika login gagal, generate ulang hash password via PHP:
> ```php
> echo password_hash('admin123', PASSWORD_DEFAULT);
> ```
> Lalu update kolom `password` di tabel `users`.

---

## Fitur Aplikasi

### Admin
- Dashboard dengan statistik (pelanggan, pesanan, pendapatan)
- Kelola layanan (tambah, edit, hapus)
- Kelola pesanan (lihat, update status)
- Konfirmasi pembayaran pelanggan
- Lihat data semua pelanggan

### Pelanggan (User)
- Dashboard pribadi dengan statistik pesanan
- Lihat daftar layanan dan harga
- Buat pesanan baru dengan kalkulasi harga otomatis
- Riwayat semua pesanan dengan filter status
- Detail pesanan + status tracker visual

---

## Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend  | PHP 7.4+ Native |
| Database | MySQL 5.7+ / MariaDB |
| Frontend | HTML5, CSS3, JavaScript (Vanilla) |
| Auth     | PHP Session |
| Server   | Apache (Laragon/XAMPP) |

---

## Keamanan

- Password di-hash menggunakan `password_hash()` (bcrypt)
- Proteksi halaman admin dan user via session role-check
- Input di-escape dengan `mysqli_real_escape_string()`
- Output di-sanitize dengan `htmlspecialchars()`
- Hapus data memakai POST (bukan GET) untuk cegah CSRF sederhana
