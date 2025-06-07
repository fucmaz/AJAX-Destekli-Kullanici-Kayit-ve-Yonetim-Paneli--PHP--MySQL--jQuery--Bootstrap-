<?php
require 'db.php';

$kod = $_GET['kod'] ?? '';
$durum = '';
$mesaj = '';

if (!$kod) {
    $durum = 'danger';
    $mesaj = 'Geçersiz bağlantı.';
} else {
    $sorgu = $db->prepare("SELECT * FROM users WHERE dogrulama_kodu = ? AND aktif = 0");
    $sorgu->execute([$kod]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($kullanici) {
        $guncelle = $db->prepare("UPDATE users SET aktif = 1, dogrulama_kodu = NULL WHERE id = ?");
        $guncelle->execute([$kullanici['id']]);
        $durum = 'success';
        $mesaj = '✅ E-posta başarıyla doğrulandı! Artık giriş yapabilirsiniz.';
    } else {
        $durum = 'danger';
        $mesaj = '❌ Geçersiz veya zaten doğrulanmış bağlantı.';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>E-posta Doğrulama</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow p-4 text-center" style="max-width: 500px;">
    <h3 class="mb-4">E-posta Doğrulama</h3>
    <div class="alert alert-<?= $durum ?>" role="alert">
      <?= $mesaj ?>
    </div>
    <?php if ($durum === 'success'): ?>
      <a href="login.php" class="btn btn-primary">Giriş Yap</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
