-- Ekspor database: Katalog Buku
CREATE DATABASE IF NOT EXISTS katalog_buku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE katalog_buku;

DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE buku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  penulis VARCHAR(100) NOT NULL,
  penerbit VARCHAR(100) NOT NULL,
  tahun SMALLINT NOT NULL,
  isbn VARCHAR(17) NOT NULL UNIQUE,
  harga INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Akun demo: admin / admin123 (password di-hash dengan bcrypt)
INSERT INTO users (username, password) VALUES ('admin', '$2y$10$6vT5wMIDIy54VvNMhX08geMSjR2WZi9RgOpW3.kkQbqnkm3cfnzJS');

INSERT INTO buku (judul, penulis, penerbit, tahun, isbn, harga) VALUES
('Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, '9789793062792', 89000),
('Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', 1980, '9789799731234', 110000),
('Pemrograman PHP & MySQL', 'Budi Raharjo', 'Informatika', 2020, '9786026231001', 95000);
