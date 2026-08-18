<?php
declare(strict_types=1);
$role = current_user_role();
$name = current_user_name();
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="panel.php">Kontrol Paneli</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Menü">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto">
        <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" href="kullanici_listesi.php">Kullanıcı Listesi</a>
          </li>
        <?php endif; ?>
        <?php if ($name !== ''): ?>
          <li class="nav-item">
            <span class="navbar-text text-white-50 me-3"><?= e($name) ?></span>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Çıkış Yap</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
