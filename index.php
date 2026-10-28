<?php
// Dashboard / READ data (dilindungi login)
require_once __DIR__ . '/includes/init.php';
require_login();

$flash    = $_SESSION['flash'] ?? '';     unset($_SESSION['flash']);
$flashErr = $_SESSION['flash_err'] ?? ''; unset($_SESSION['flash_err']);

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM buku WHERE judul LIKE :q1 OR penulis LIKE :q2 ORDER BY id DESC');
    $stmt->execute([':q1' => "%$q%", ':q2' => "%$q%"]);
} else {
    $stmt = $pdo->query('SELECT * FROM buku ORDER BY id DESC');
}
$rows = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<div class="card">
  <div class="card-head">
    <h2><?= e(t('book_list')) ?></h2>
    <a class="btn" href="tambah-edit-hapus.php?aksi=tambah">+ <?= e(t('add_book')) ?></a>
  </div>

  <?php if ($flash): ?><div class="alert alert-ok"><?= e($flash) ?></div><?php endif; ?>
  <?php if ($flashErr): ?><div class="alert alert-error"><?= e($flashErr) ?></div><?php endif; ?>

  <form method="GET" class="search">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="<?= e(t('search')) ?>">
    <button class="btn btn-gray" type="submit">🔍</button>
  </form>
  <p class="hint left"><?= e(t('total')) ?>: <b><?= count($rows) ?></b></p>

  <table>
    <thead>
      <tr>
        <th><?= e(t('no')) ?></th><th><?= e(t('title')) ?></th><th><?= e(t('author')) ?></th>
        <th><?= e(t('publisher')) ?></th><th><?= e(t('year')) ?></th><th><?= e(t('isbn')) ?></th>
        <th><?= e(t('price')) ?></th><th><?= e(t('action')) ?></th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$rows): ?>
      <tr><td colspan="8" class="center"><?= e(t('empty')) ?></td></tr>
    <?php endif; ?>
    <?php foreach ($rows as $i => $r): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= e($r['judul']) ?></td>
        <td><?= e($r['penulis']) ?></td>
        <td><?= e($r['penerbit']) ?></td>
        <td><?= (int)$r['tahun'] ?></td>
        <td><?= e($r['isbn']) ?></td>
        <td class="right"><?= number_format((int)$r['harga'], 0, ',', '.') ?></td>
        <td class="actions">
          <a class="btn btn-sm btn-warn" href="tambah-edit-hapus.php?aksi=edit&id=<?= (int)$r['id'] ?>"><?= e(t('edit')) ?></a>
          <form method="POST" action="tambah-edit-hapus.php?aksi=hapus"
                onsubmit="return confirm('<?= e(t('confirm_delete')) ?>')">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
            <button class="btn btn-sm btn-danger" type="submit"><?= e(t('delete')) ?></button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
