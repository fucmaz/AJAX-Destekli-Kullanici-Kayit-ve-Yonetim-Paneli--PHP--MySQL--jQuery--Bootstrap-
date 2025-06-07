<?php
session_start();
require 'db.php';




$id = intval($_GET['id'] ?? 0);

$sorgu = $db->prepare("SELECT * FROM users WHERE id = ?");
$sorgu->execute([$id]);
$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$kullanici) {
    die("Kullanıcı bulunamadı.");
}
?>


<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kullanıcı Düzenle</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

<div class="container mt-5">
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


  <div class="card shadow">
    <div class="card-header bg-warning text-white">
      <?php if (isset($_SESSION['user_role'])): ?>
  <div class="alert alert-info mt-3">
    Rolünüz: <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>
  </div>
<?php endif; ?>

      <h4>Kullanıcıyı Düzenle</h4>
    </div>
    <div class="card-body">
      <form id="duzenleFormu">
        <input type="hidden" name="id" value="<?= $kullanici['id'] ?>">
        <div class="mb-3">
          <label>Ad</label>
          <input type="text" name="ad" class="form-control" value="<?= htmlspecialchars($kullanici['ad']) ?>" required>
        </div>
        <div class="mb-3">
          <label>Soyad</label>
          <input type="text" name="soyad" class="form-control" value="<?= htmlspecialchars($kullanici['soyad']) ?>" required>
        </div>
        <div class="mb-3">
          <label>E-posta</label>
          <input type="email" class="form-control" value="<?= htmlspecialchars($kullanici['email']) ?>" disabled>
        </div>
        <div class="mb-3">
          <label>Durum</label>
          <select name="aktif" class="form-select">
            <option value="1" <?= $kullanici['aktif'] == 1 ? 'selected' : '' ?>>Aktif</option>
            <option value="0" <?= $kullanici['aktif'] == 0 ? 'selected' : '' ?>>Pasif</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Rol</label>
          <select name="rol" class="form-select">
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

<script>
$('#duzenleFormu').on('submit', function(e){
  e.preventDefault();
  $.post('kullanici_guncelle.php', $(this).serialize(), function(response){
    if (response.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'Güncellendi!',
        text: response.message,
        showConfirmButton: false,
        timer: 1500
      }).then(() => {
        window.location.href = 'kullanici_listesi.php';
      });
    } else {
      Swal.fire('Hata!', response.message, 'error');
    }
  }, 'json');
});
</script>

</body>
</html>
