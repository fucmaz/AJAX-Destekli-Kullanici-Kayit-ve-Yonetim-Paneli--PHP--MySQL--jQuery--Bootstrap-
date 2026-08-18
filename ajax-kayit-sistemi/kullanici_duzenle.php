<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireRole(['admin']);

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('Geçersiz kullanıcı.');
}

$db = db();
$sorgu = $db->prepare('SELECT id, ad, soyad, email, aktif, rol FROM users WHERE id = ? LIMIT 1');
$sorgu->execute([$id]);
$kullanici = $sorgu->fetch();

if (!$kullanici) {
    http_response_code(404);
    exit('Kullanıcı bulunamadı.');
}

$pageTitle = 'Kullanıcı Düzenle';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-light">

<div class="container mt-4">
  <?php include __DIR__ . '/includes/header.php'; ?>

  <div class="card shadow">
    <div class="card-header bg-warning">
      <div class="alert alert-info mt-3">
        Rolünüz: <strong><?= e((string) current_user_role()) ?></strong>
      </div>
      <h4>Kullanıcıyı Düzenle</h4>
    </div>
    <div class="card-body">
      <form id="duzenleFormu">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $kullanici['id'] ?>">
        <div class="mb-3">
          <label for="ad" class="form-label">Ad</label>
          <input id="ad" type="text" name="ad" class="form-control" maxlength="50" value="<?= e((string) $kullanici['ad']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="soyad" class="form-label">Soyad</label>
          <input id="soyad" type="text" name="soyad" class="form-control" maxlength="50" value="<?= e((string) $kullanici['soyad']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">E-posta</label>
          <input type="email" class="form-control" value="<?= e((string) $kullanici['email']) ?>" disabled>
        </div>
        <div class="mb-3">
          <label for="aktif" class="form-label">Durum</label>
          <select id="aktif" name="aktif" class="form-select">
            <option value="1" <?= (int) $kullanici['aktif'] === 1 ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= (int) $kullanici['aktif'] === 0 ? 'selected' : '' ?>>Pasif</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="rol" class="form-label">Rol</label>
          <select id="rol" name="rol" class="form-select">
            <option value="admin" <?= $kullanici['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="editor" <?= $kullanici['rol'] === 'editor' ? 'selected' : '' ?>>Editör</option>
            <option value="user" <?= $kullanici['rol'] === 'user' ? 'selected' : '' ?>>Kullanıcı</option>
          </select>
        </div>
        <button type="submit" class="btn btn-success w-100">Güncelle</button>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>
<script nonce="<?= e(csp_nonce()) ?>" src="js/kullanici.js"></script>
</body>
</html>
