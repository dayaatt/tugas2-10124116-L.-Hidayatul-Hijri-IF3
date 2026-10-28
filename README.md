# Katalog Buku – Sistem Informasi Manajemen Data Sederhana
PHP murni + MySQL/MariaDB (PDO). CRUD, validasi & sanitasi, login `$_SESSION`, dual bahasa (ID/EN).

## Struktur
```
katalog_buku/
├── config/database.php          # Koneksi PDO
├── languages/id.php, en.php     # Kamus bahasa
├── assets/style.css             # Styling
├── includes/                    # (tambahan) init session/bahasa/helper, header, footer
├── index.php                    # Dashboard / Read (protected)
├── login_dan_logout.php         # Login & logout (?aksi=logout)
├── tambah-edit-hapus.php        # Create, Update, Delete (?aksi=tambah|edit|hapus)
└── database/database.sql        # Ekspor database
```

## Cara Menjalankan
1. Jalankan Apache & MySQL (XAMPP/Laragon), salin folder ke `htdocs`.
2. phpMyAdmin → Import → `database/database.sql`.
3. Sesuaikan kredensial di `config/database.php` bila perlu.
4. Buka `http://localhost/katalog_buku/` → login **admin / admin123**.

## Keamanan
Prepared statement PDO, `htmlspecialchars()` pada output, `filter_var()` pada input angka,
`strip_tags()`+`trim()`, `password_hash/verify`, token CSRF, hapus via POST, `session_regenerate_id()`.
