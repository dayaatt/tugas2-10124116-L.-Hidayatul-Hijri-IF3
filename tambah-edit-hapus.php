<?php
// Modul CREATE, UPDATE, DELETE + validasi form
//   tambah-edit-hapus.php?aksi=tambah
//   tambah-edit-hapus.php?aksi=edit&id=1
//   tambah-edit-hapus.php?aksi=hapus   (POST)
require_once __DIR__ . '/includes/init.php';
require_login();

$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'], true)) {
    $aksi = 'tambah';
}

// ---------- DELETE ----------
if ($aksi === 'hapus') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare('DELETE FROM buku WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $_SESSION['flash'] = $stmt->rowCount() > 0 ? t('deleted') : t('not_found');
        }
    }
    header('Location: index.php');
    exit;
}

// ---------- Validasi & sanitasi ----------
function validasi_buku(array $in): array {
    $errors = [];
    // Sanitasi: trim + buang tag HTML (output tetap di-escape dengan htmlspecialchars via e())
    $data = [
        'judul'    => trim(strip_tags($in['judul'] ?? '')),
        'penulis'  => trim(strip_tags($in['penulis'] ?? '')),
        'penerbit' => trim(strip_tags($in['penerbit'] ?? '')),
        'tahun'    => trim($in['tahun'] ?? ''),
        'isbn'     => trim($in['isbn'] ?? ''),
        'harga'    => trim($in['harga'] ?? ''),
    ];

    // Percabangan if-else: kelengkapan data
    if ($data['judul'] === '' || $data['penulis'] === '' || $data['penerbit'] === ''
        || $data['tahun'] === '' || $data['isbn'] === '' || $data['harga'] === '') {
        $errors[] = t('err_required');
        return [$data, $errors];
    }

    // filter_var: validasi angka
    $tahun = filter_var($data['tahun'], FILTER_VALIDATE_INT,
             ['options' => ['min_range' => 1000, 'max_range' => (int)date('Y')]]);
    if ($tahun === false) $errors[] = t('err_year', (int)date('Y'));

    $harga = filter_var($data['harga'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    if ($harga === false) $errors[] = t('err_price');

    if (!preg_match('/^[0-9\-]{10,17}$/', $data['isbn'])) $errors[] = t('err_isbn');

    $data['tahun'] = (int)$tahun;
    $data['harga'] = (int)$harga;
    return [$data, $errors];
}

// ---------- Siapkan data awal ----------
$id = null;
$data = ['judul' => '', 'penulis' => '', 'penerbit' => '', 'tahun' => '', 'isbn' => '', 'harga' => ''];
$errors = [];

if ($aksi === 'edit') {
    $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
    $buku = false;
    if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM buku WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $buku = $stmt->fetch();
    }
    if (!$buku) {
        $_SESSION['flash_err'] = t('not_found');
        header('Location: index.php');
        exit;
    }
    $data = $buku;
}

// ---------- Proses form (CREATE / UPDATE) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $errors[] = t('err_csrf');
    } else {
        [$data, $errors] = validasi_buku($_POST);
        if (!$errors) {
            try {
                if ($aksi === 'tambah') {
                    $stmt = $pdo->prepare('INSERT INTO buku (judul, penulis, penerbit, tahun, isbn, harga)
                                           VALUES (:judul, :penulis, :penerbit, :tahun, :isbn, :harga)');
                    $stmt->execute($data);
                    $_SESSION['flash'] = t('saved');
                } else {
                    $stmt = $pdo->prepare('UPDATE buku SET judul=:judul, penulis=:penulis, penerbit=:penerbit,
                                           tahun=:tahun, isbn=:isbn, harga=:harga WHERE id=:id');
                    $stmt->execute($data + [':id' => $id]);
                    $_SESSION['flash'] = t('updated');
                }
                header('Location: index.php');
                exit;
            } catch (PDOException $ex) {
                $errors[] = ($ex->getCode() === '23000') ? t('err_dup') : t('err_db');
            }
        }
    }
}

$judulHalaman = ($aksi === 'tambah') ? t('add_book') : t('edit_book') . ' #' . (int)$id;
$action = 'tambah-edit-hapus.php?aksi=' . $aksi . ($aksi === 'edit' ? '&id=' . (int)$id : '');

require __DIR__ . '/includes/header.php';
?>
<div class="card">
  <h2><?= e($judulHalaman) ?></h2>
  <?php foreach ($errors as $err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endforeach; ?>

  <form method="POST" action="<?= e($action) ?>">
    <?= csrf_field() ?>
    <label><?= e(t('title')) ?></label>
    <input type="text" name="judul" maxlength="150" value="<?= e($data['judul']) ?>">
    <label><?= e(t('author')) ?></label>
    <input type="text" name="penulis" maxlength="100" value="<?= e($data['penulis']) ?>">
    <label><?= e(t('publisher')) ?></label>
    <input type="text" name="penerbit" maxlength="100" value="<?= e($data['penerbit']) ?>">
    <div class="row">
      <div><label><?= e(t('year')) ?></label>
        <input type="number" name="tahun" value="<?= e($data['tahun']) ?>"></div>
      <div><label><?= e(t('isbn')) ?></label>
        <input type="text" name="isbn" maxlength="17" value="<?= e($data['isbn']) ?>"></div>
      <div><label><?= e(t('price')) ?></label>
        <input type="number" name="harga" min="0" value="<?= e($data['harga']) ?>"></div>
    </div>
    <button class="btn" type="submit"><?= e($aksi === 'tambah' ? t('save') : t('update')) ?></button>
    <a class="btn btn-gray" href="index.php"><?= e(t('cancel')) ?></a>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
