<?php
// Koneksi database menggunakan PDO (MySQL/MariaDB)
$db_host = 'localhost';
$db_name = 'katalog_buku';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $ex) {
    die('Koneksi database gagal. Periksa config/database.php dan pastikan database sudah di-import.');
}
