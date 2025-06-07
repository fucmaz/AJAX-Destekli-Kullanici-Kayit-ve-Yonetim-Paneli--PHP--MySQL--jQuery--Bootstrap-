<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo "Bu sayfaya erişim yetkiniz yok.";
    exit;
}

$users = $db->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kullanıcı Listesi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="panel.php">Kontrol Paneli</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
   
        <?php if (in_array($_SESSION['user_role'], ['admin', 'editor'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="kullanici_listesi.php">Kullanıcı Listesi</a>
          </li>
        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Çıkış Yap</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<h3>Kullanıcı Listesi</h3>
<table class="table table-bordered table-hover mt-3">
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
   <tr id="user-<?= $user['id'] ?>">
  <td><?= $user['id'] ?></td>
  <td><?= htmlspecialchars($user['ad'] . ' ' . $user['soyad']) ?></td>
  <td><?= htmlspecialchars($user['email']) ?></td>
  <td><?= $user['aktif'] == 1 ? 'Aktif' : 'Pasif' ?></td>
  <td><?= $user['rol'] ?></td>
  <td><?= $user['created_at'] ?></td>
  <td>
    <a href="kullanici_duzenle.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-warning">Düzenle</a>
    <button class="btn btn-sm btn-danger" onclick="kullaniciSil(<?= $user['id'] ?>)">Sil</button>
  </td>
</tr>

    <?php endforeach; ?>
  </tbody>
</table>

<!-- HEAD veya BODY içine ekle -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
function kullaniciSil(id) {
  Swal.fire({
    title: 'Emin misiniz?',
    text: "Bu kullanıcı silinecek!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Evet, sil!',
    cancelButtonText: 'İptal',
  }).then((result) => {
    if (result.isConfirmed) {
      $.post('kullanici_sil.php', { id: id }, function (response) {
        if (response.status === 'success') {
          $('#user-' + id).remove();
          Swal.fire('Silindi!', response.message, 'success');
        } else {
          Swal.fire('Hata!', response.message, 'error');
        }
      }, 'json');
    }
  });
}
</script>
</body>
</html>
