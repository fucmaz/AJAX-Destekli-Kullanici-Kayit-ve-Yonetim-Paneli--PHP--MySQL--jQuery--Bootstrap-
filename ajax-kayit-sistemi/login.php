<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

if (current_user_id() !== null) {
    header('Location: panel.php');
    exit;
}

$pageTitle = 'Giriş Yap';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-header text-center bg-secondary text-white">
          <h4>Giriş Yap</h4>
        </div>
        <div class="card-body">
          <form id="girisFormu" autocomplete="on">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label for="email" class="form-label">E-posta</label>
              <input id="email" type="email" name="email" class="form-control" maxlength="100" required>
            </div>
            <div class="mb-3">
              <label for="sifre" class="form-label">Şifre</label>
              <input id="sifre" type="password" name="sifre" class="form-control" maxlength="72" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
          </form>
          <div class="text-center mt-3">
            <a href="index.php">Hesabın yok mu? Kayıt ol</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>
<script nonce="<?= e(csp_nonce()) ?>" src="js/login.js"></script>
</body>
</html>
