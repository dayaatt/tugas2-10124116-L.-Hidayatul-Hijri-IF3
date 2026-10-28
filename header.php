<!DOCTYPE html>
<html lang="<?= e($_SESSION['lang']) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(t('app_title')) ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="index.php">📚 <?= e(t('app_title')) ?></a>
  <div class="nav-right">
    <span class="lang">
      <?= e(t('language')) ?>:
      <a href="<?= e(lang_url('id')) ?>" class="<?= $_SESSION['lang']==='id'?'active':'' ?>">ID</a> |
      <a href="<?= e(lang_url('en')) ?>" class="<?= $_SESSION['lang']==='en'?'active':'' ?>">EN</a>
    </span>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <span><?= e(t('welcome')) ?>, <b><?= e($_SESSION['username']) ?></b></span>
      <a class="btn btn-sm btn-gray" href="login_dan_logout.php?aksi=logout"><?= e(t('logout')) ?></a>
    <?php endif; ?>
  </div>
</nav>
<main class="container">
