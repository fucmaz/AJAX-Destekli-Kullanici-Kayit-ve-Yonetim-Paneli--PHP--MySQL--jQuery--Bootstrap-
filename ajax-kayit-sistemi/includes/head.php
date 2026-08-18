<?php
declare(strict_types=1);
$pageTitle = $pageTitle ?? 'AJAX Kayıt Sistemi';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="<?= e(csrf_token()) ?>">
<meta name="referrer" content="strict-origin-when-cross-origin">
<title><?= e($pageTitle) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
