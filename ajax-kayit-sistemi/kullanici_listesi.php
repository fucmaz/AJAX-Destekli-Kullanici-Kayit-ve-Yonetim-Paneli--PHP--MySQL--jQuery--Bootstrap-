<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireRole(['admin']);

$db = db();
$users = $db->query('SELECT id, ad, soyad, email, aktif, rol, created_at FROM users ORDER BY id DESC')->fetchAll();

$pageTitle = 'Kullanıcı Listesi';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-light">

<div class="container mt-4">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <h3>Kullanıcı Listesi</h3>
  <table class="table table-bordered table-hover mt-3 bg-white">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Ad Soyad</th>
        <th>Email</th>
        <th>Durum</th>
        <th>Rol</th>
        <th>Kayıt Tarihi</th>
        <th>İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr id="user-<?= (int) $user['id'] ?>">
          <td><?= (int) $user['id'] ?></td>
          <td><?= e((string) $user['ad'] . ' ' . (string) $user['soyad']) ?></td>
          <td><?= e((string) $user['email']) ?></td>
          <td><?= (int) $user['aktif'] === 1 ? 'Aktif' : 'Pasif' ?></td>
          <td><?= e((string) $user['rol']) ?></td>
          <td><?= e((string) $user['created_at']) ?></td>
          <td>
            <a href="kullanici_duzenle.php?id=<?= (int) $user['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
            <button type="button" class="btn btn-sm btn-danger js-user-delete" data-user-id="<?= (int) $user['id'] ?>">Sil</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>
<script nonce="<?= e(csp_nonce()) ?>" src="js/kullanici.js"></script>
</body>
</html>
