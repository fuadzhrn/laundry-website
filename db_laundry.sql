-- ================================================
-- Database: db_laundry
-- Sistem Manajemen Laundry Berbasis Web
-- ================================================

CREATE DATABASE IF NOT EXISTS db_laundry
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_laundry;

-- ------------------------------------------------
-- Tabel: users
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT PRIMARY KEY AUTO_INCREMENT,
    nama       VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    email      VARCHAR(100) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    no_telp    VARCHAR(20)  DEFAULT NULL,
    role       ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------
-- Tabel: layanan
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS layanan (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    nama_layanan VARCHAR(100)    NOT NULL,
    deskripsi    TEXT            DEFAULT NULL,
    harga        DECIMAL(10, 2)  NOT NULL,
    satuan       VARCHAR(20)     DEFAULT 'kg',
    status       ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------
-- Tabel: pesanan
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS pesanan (
    id             INT PRIMARY KEY AUTO_INCREMENT,
    kode_pesanan   VARCHAR(20)    NOT NULL UNIQUE,
    user_id        INT            NOT NULL,
    layanan_id     INT            NOT NULL,
    berat          DECIMAL(6, 2)  DEFAULT 0,
    total_harga    DECIMAL(10, 2) NOT NULL,
    catatan        TEXT           DEFAULT NULL,
    status         ENUM('pending', 'diproses', 'selesai', 'diambil') DEFAULT 'pending',
    tanggal_masuk  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_selesai TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id)    REFERENCES users(id)   ON DELETE CASCADE,
    FOREIGN KEY (layanan_id) REFERENCES layanan(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------
-- Tabel: pembayaran
-- ------------------------------------------------
CREATE TABLE IF NOT EXISTS pembayaran (
    id           INT PRIMARY KEY AUTO_INCREMENT,
    pesanan_id   INT            NOT NULL,
    jumlah       DECIMAL(10, 2) NOT NULL,
    metode       ENUM('cash', 'transfer', 'qris') DEFAULT 'cash',
    status       ENUM('belum_bayar', 'lunas') DEFAULT 'belum_bayar',
    tanggal_bayar TIMESTAMP NULL DEFAULT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================
-- Data Awal (Seed)
-- ================================================

-- Akun Admin (password: admin123)
INSERT INTO users (nama, username, email, password, role) VALUES
('Administrator', 'admin', 'admin@laundryku.com',
 '$2y$10$TzMAvlnE9YsLlOjZ5yUCfOVMhFXGN.SB7hQxqz5UD1DiwjxaIJ/Iu',
 'admin');

-- Akun Pelanggan Demo (password: user123)
INSERT INTO users (nama, username, email, password, no_telp, role) VALUES
('Budi Santoso', 'budi', 'budi@email.com',
 '$2y$10$P9nxqWKn7j1Z7VNR2RJkluEoZl3LKIF4dHEGqXbVJ7yqg5B8F4Poi',
 '081234567890', 'user');

-- Layanan Laundry
INSERT INTO layanan (nama_layanan, deskripsi, harga, satuan) VALUES
('Cuci Setrika Regular', 'Cuci dan setrika pakaian biasa. Proses 2-3 hari kerja.', 7000.00, 'kg'),
('Cuci Kering (Dry Clean)', 'Untuk pakaian bahan khusus. Bebas dari noda membandel.', 15000.00, 'pcs'),
('Cuci Express (1 Hari)', 'Layanan kilat selesai dalam 1 hari kerja.', 12000.00, 'kg'),
('Cuci Selimut/Bed Cover', 'Cuci dan setrika selimut dan bed cover besar.', 25000.00, 'pcs'),
('Cuci Sepatu', 'Cuci sepatu sneakers, kulit, atau kanvas.', 30000.00, 'pcs');

-- ================================================
-- Catatan:
-- Hash password di atas dibuat dengan PHP:
--   admin123 → password_hash('admin123', PASSWORD_DEFAULT)
--   user123  → password_hash('user123', PASSWORD_DEFAULT)
--
-- Jika hash tidak cocok, generate ulang di PHP:
--   echo password_hash('admin123', PASSWORD_DEFAULT);
-- ================================================
