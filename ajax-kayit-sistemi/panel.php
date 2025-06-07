<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">


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


<div class="container py-5">
  <div class="text-center">
    <h2 class="mb-3">👋 Hoş geldin, <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>!</h2>
    <p class="lead">Bu senin kişisel yönetim panelindir. Sol üstten menüleri gezebilirsin.</p>
    
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
      <a href="kullanici_listesi.php" class="btn btn-success mt-3">Kullanıcı Listesini Gör</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
