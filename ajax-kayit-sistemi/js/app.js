(function () {
  const tokenMeta = document.querySelector('meta[name="csrf-token"]');
  const token = tokenMeta ? tokenMeta.getAttribute('content') : '';

  if (window.jQuery) {
    $.ajaxSetup({
      headers: {
        'X-CSRF-Token': token,
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
  }
})();
