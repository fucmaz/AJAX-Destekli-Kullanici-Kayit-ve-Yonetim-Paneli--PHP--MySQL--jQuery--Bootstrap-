$(document).ready(function () {
  $('#duzenleFormu').on('submit', function (e) {
    e.preventDefault();
    $.post('kullanici_guncelle.php', $(this).serialize(), function (response) {
      if (response.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Güncellendi!',
          text: response.message,
          showConfirmButton: false,
          timer: 1500
        }).then(function () {
          window.location.href = 'kullanici_listesi.php';
        });
      } else {
        Swal.fire('Hata!', response.message, 'error');
      }
    }, 'json').fail(function (xhr) {
      const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Güncelleme başarısız.';
      Swal.fire('Hata!', message, 'error');
    });
  });

  $(document).on('click', '.js-user-delete', function () {
    const id = $(this).data('user-id');
    Swal.fire({
      title: 'Emin misiniz?',
      text: 'Bu kullanıcı silinecek!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Evet, sil!',
      cancelButtonText: 'İptal'
    }).then(function (result) {
      if (!result.isConfirmed) {
        return;
      }
      $.post('kullanici_sil.php', { id: id }, function (response) {
        if (response.status === 'success') {
          $('#user-' + id).remove();
          Swal.fire('Silindi!', response.message, 'success');
        } else {
          Swal.fire('Hata!', response.message, 'error');
        }
      }, 'json').fail(function (xhr) {
        const message = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Silme başarısız.';
        Swal.fire('Hata!', message, 'error');
      });
    });
  });
});
