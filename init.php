<?php
// Bootstrap bersama: session, koneksi, bahasa, helper
session_start();
require_once __DIR__ . '/../config/database.php';

// ---------- Dual bahasa (disimpan di $_SESSION['lang']) ----------
$bahasa_tersedia = ['id', 'en'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $bahasa_tersedia, true)) {
    $_SESSION['lang'] = $_GET['lang'];
}
if (empty($_SESSION['lang']) || !in_array($_SESSION['lang'], $bahasa_tersedia, true)) {
    $_SESSION['lang'] = 'id';
}
$kamus = require __DIR__ . '/../languages/' . $_SESSION['lang'] . '.php';

function t(string $key, ...$args): string {
    global $kamus;
    $teks = $kamus[$key] ?? $key;
    return $args ? vsprintf($teks, $args) : $teks;
}

// URL halaman ini dengan bahasa lain (query lain dipertahankan)
function lang_url(string $lang): string {
    $q = $_GET;
    $q['lang'] = $lang;
    return basename($_SERVER['SCRIPT_NAME']) . '?' . http_build_query($q);
}

// ---------- Helper ----------
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">'; }
function csrf_valid(): bool { return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']); }

function require_login(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login_dan_logout.php');
        exit;
    }
}
