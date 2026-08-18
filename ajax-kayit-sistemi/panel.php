<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireLogin();

$pageTitle = 'Panel';
$role = current_user_role();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-light">

<?php include __DIR__ . '/includes/header.php'; ?>

<div class="container py-5">
  <div class="text-center">
    <h2 class="mb-3">Hoş geldin, <strong><?= e(current_user_name()) ?></strong>!</h2>
    <p class="lead">Bu senin kişisel yönetim panelindir.</p>
    <p>Rolünüz: <strong><?= e((string) $role) ?></strong></p>

    <?php if ($role === 'admin'): ?>
      <a href="kullanici_listesi.php" class="btn btn-success mt-3">Kullanıcı Listesini Gör</a>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
