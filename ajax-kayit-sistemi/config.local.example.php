<?php
declare(strict_types=1);

/**
 * Bu dosyayı config.local.php olarak kopyalayın ve değerleri doldurun.
 * config.local.php sürüme eklenmez.
 */
return [
    'debug' => false,
    'app_url' => 'http://localhost/ajax-kayit-sistemi',
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => 'ajax_kayit',
    'db_user' => 'root',
    'db_pass' => '',
    'mail_driver' => 'smtp',
    'mail_host' => 'mail.example.com',
    'mail_port' => 587,
    'mail_username' => 'info@example.com',
    'mail_password' => 'CHANGE_ME',
    'mail_encryption' => 'tls',
    'mail_from' => 'info@example.com',
    'mail_from_name' => 'Kayıt Sistemi',
    'mail_verify_ssl' => true,
];
