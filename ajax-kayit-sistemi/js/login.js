$(document).ready(function () {
  $('#girisFormu').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      type: 'POST',
      url: 'giris_kontrol.php',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Başarılı!',
            text: response.message,
            showConfirmButton: false,
            timer: 1500
          }).then(function () {
            window.location.href = 'panel.php';
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: response.message
          });
        }
      },
      error: function (xhr) {
        let message = 'İşlem sırasında bir hata oluştu.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        }
        Swal.fire({
          icon: 'error',
          title: 'Sunucu Hatası',
          text: message
        });
      }
    });
  });
});
