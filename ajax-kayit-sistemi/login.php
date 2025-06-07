<?php include 'db.php'; session_start(); 
// Kullanıcı zaten giriş yaptıysa panel'e yönlendir
if (isset($_SESSION['user_id'])) {
    header("Location: panel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Giriş Yap</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
          <form id="girisFormu">
            <div class="mb-3">
              <label>E-posta</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Şifre</label>
              <input type="password" name="sifre" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$('#girisFormu').on('submit', function(e){
  e.preventDefault();
  const formData = $(this).serialize();

  $.ajax({
    type: 'POST',
    url: 'giris_kontrol.php',
    data: formData,
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Başarılı!',
          text: response.message,
          showConfirmButton: false,
          timer: 1500
        }).then(() => {
          window.location.href = 'panel.php';
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Hata!',
          text: response.message
        });
      }
    }
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
