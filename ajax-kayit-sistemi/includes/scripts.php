<?php
declare(strict_types=1);
$nonce = e(csp_nonce());
?>
<script nonce="<?= $nonce ?>" src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script nonce="<?= $nonce ?>" src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
<script nonce="<?= $nonce ?>" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script nonce="<?= $nonce ?>" src="js/app.js"></script>
