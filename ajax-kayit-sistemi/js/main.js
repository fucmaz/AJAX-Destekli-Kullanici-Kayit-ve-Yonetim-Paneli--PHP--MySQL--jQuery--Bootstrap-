$(document).ready(function () {
  $('#kayitFormu').on('submit', function (e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
      type: 'POST',
      url: 'kaydet.php',
      data: formData,
      dataType: 'json',
      success: function (response) {
        if (response.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Başarılı!',
            text: response.message,
            timer: 2000,
            showConfirmButton: false
          });

          $('#kayitFormu')[0].reset();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Hata!',
            text: response.message
          });
        }
      },
      error: function () {
        Swal.fire({
          icon: 'error',
          title: 'Sunucu Hatası',
          text: 'İşlem sırasında bir hata oluştu.'
        });
      }
    });
  });
});
