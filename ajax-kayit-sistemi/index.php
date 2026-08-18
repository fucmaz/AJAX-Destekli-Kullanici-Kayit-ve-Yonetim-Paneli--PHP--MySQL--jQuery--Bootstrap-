<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pageTitle = 'Kayıt Formu (AJAX)';
$loggedIn = current_user_id() !== null;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-light">

<div class="container mt-5">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">AJAX Kayıt</a>
      <div class="navbar-nav ms-auto">
        <?php if ($loggedIn): ?>
          <a class="nav-link" href="panel.php">Panele Git</a>
        <?php else: ?>
          <a class="nav-link" href="login.php">Kayıtlıysan Giriş Yap</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header text-center bg-primary text-white">
          <h4>AJAX Kayıt Formu</h4>
        </div>
        <div class="card-body">
          <form id="kayitFormu" autocomplete="off">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label for="ad" class="form-label">Ad</label>
              <input id="ad" type="text" name="ad" class="form-control" maxlength="50" required>
            </div>
            <div class="mb-3">
              <label for="soyad" class="form-label">Soyad</label>
              <input id="soyad" type="text" name="soyad" class="form-control" maxlength="50" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">E-posta</label>
              <input id="email" type="email" name="email" class="form-control" maxlength="100" required>
            </div>
            <div class="mb-3">
              <label for="sifre" class="form-label">Şifre</label>
              <input id="sifre" type="password" name="sifre" class="form-control" minlength="8" maxlength="72" required>
              <div class="form-text">En az 8 karakter.</div>
            </div>
            <button type="submit" class="btn btn-success w-100">Kaydol</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>
<script nonce="<?= e(csp_nonce()) ?>" src="js/main.js"></script>
</body>
</html>
