<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Kayıt Formu (AJAX)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/main.js"></script>
</head>
<body class="bg-light">

<div class="container mt-5">

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand btn btn-success" href="panel.php">Kayıtlıysan Giriş Yap</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
   
  
      </ul>
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
          <form id="kayitFormu">
            <div class="mb-3">
              <label>Ad</label>
              <input type="text" name="ad" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Soyad</label>
              <input type="text" name="soyad" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>E-posta</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Şifre</label>
              <input type="password" name="sifre" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Kaydol</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
