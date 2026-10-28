<?php
// Halaman Login & Logout (?aksi=logout)
require_once __DIR__ . '/includes/init.php';

// ---------- LOGOUT ----------
if (($_GET['aksi'] ?? '') === 'logout') {
    $lang = $_SESSION['lang'];          // pertahankan pilihan bahasa
    $_SESSION = [];
    session_destroy();
    session_start();
    $_SESSION['lang'] = $lang;
    header('Location: login_dan_logout.php');
    exit;
}

// Sudah login → ke dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// ---------- LOGIN ----------
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!csrf_valid()) {
        $error = t('err_csrf');
    } elseif ($username === '' || $password === '') {
        $error = t('login_required');
    } else {
        $stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = :u');
        $stmt->execute([':u' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        }
        $error = t('login_failed');
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="card card-narrow">
  <h2><?= e(t('login')) ?></h2>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="POST" action="login_dan_logout.php">
    <?= csrf_field() ?>
    <label><?= e(t('username')) ?></label>
    <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>" autofocus>
    <label><?= e(t('password')) ?></label>
    <input type="password" name="password">
    <button class="btn" type="submit"><?= e(t('login')) ?></button>
  </form>
  <p class="hint">Demo: admin / admin123</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
