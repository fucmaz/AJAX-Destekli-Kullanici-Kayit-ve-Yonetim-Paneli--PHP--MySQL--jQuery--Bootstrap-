<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$kod = (string) ($_GET['kod'] ?? '');
$durum = '';
$mesaj = '';

if ($kod === '' || !preg_match('/^[a-f0-9]{64}$/', $kod)) {
    $durum = 'danger';
    $mesaj = 'Geçersiz bağlantı.';
} else {
    $db = db();
    $sorgu = $db->prepare('SELECT id, dogrulama_expires_at FROM users WHERE dogrulama_kodu = ? AND aktif = 0 LIMIT 1');
    $sorgu->execute([$kod]);
    $kullanici = $sorgu->fetch();

    if (!$kullanici) {
        $durum = 'danger';
        $mesaj = 'Geçersiz veya zaten doğrulanmış bağlantı.';
    } else {
        $expiresAt = $kullanici['dogrulama_expires_at'] ?? null;
        if (is_string($expiresAt) && $expiresAt !== '' && strtotime($expiresAt) < time()) {
            $durum = 'danger';
            $mesaj = 'Doğrulama bağlantısının süresi dolmuş. Lütfen yeniden kayıt olun.';
        } else {
            $guncelle = $db->prepare('UPDATE users SET aktif = 1, dogrulama_kodu = NULL, dogrulama_expires_at = NULL WHERE id = ?');
            $guncelle->execute([(int) $kullanici['id']]);
            $durum = 'success';
            $mesaj = 'E-posta başarıyla doğrulandı! Artık giriş yapabilirsiniz.';
        }
    }
}

$pageTitle = 'E-posta Doğrulama';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <?php include __DIR__ . '/includes/head.php'; ?>
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow p-4 text-center" style="max-width: 500px;">
    <h3 class="mb-4">E-posta Doğrulama</h3>
    <div class="alert alert-<?= e($durum) ?>" role="alert">
      <?= e($mesaj) ?>
    </div>
    <?php if ($durum === 'success'): ?>
      <a href="login.php" class="btn btn-primary">Giriş Yap</a>
    <?php else: ?>
      <a href="index.php" class="btn btn-outline-secondary">Kayıt Formuna Dön</a>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/includes/scripts.php'; ?>
</body>
</html>
