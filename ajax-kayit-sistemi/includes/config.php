<?php
declare(strict_types=1);

/**
 * Uygulama yapılandırması.
 * Gizli bilgileri config.local.php dosyasına koyun (örnek: config.local.example.php).
 */
function app_config(): array
{
    static $config = null;
    if (is_array($config)) {
        return $config;
    }

    $config = [
        'debug' => false,
        'app_url' => getenv('APP_URL') ?: 'http://localhost/ajax-kayit-sistemi',
        'app_name' => 'Kayıt Sistemi',
        'db_host' => getenv('DB_HOST') ?: 'localhost',
        'db_port' => getenv('DB_PORT') ?: '3306',
        'db_name' => getenv('DB_NAME') ?: 'ajax_kayit',
        'db_user' => getenv('DB_USER') ?: 'root',
        'db_pass' => getenv('DB_PASS') !== false ? (string) getenv('DB_PASS') : '',
        'mail_host' => getenv('MAIL_HOST') ?: 'localhost',
        'mail_port' => (int) (getenv('MAIL_PORT') ?: 587),
        'mail_username' => getenv('MAIL_USERNAME') ?: '',
        'mail_password' => getenv('MAIL_PASSWORD') ?: '',
        'mail_encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',
        'mail_from' => getenv('MAIL_FROM') ?: 'noreply@localhost',
        'mail_from_name' => getenv('MAIL_FROM_NAME') ?: 'Kayıt Sistemi',
        'mail_verify_ssl' => true,
        'verify_expire_hours' => 24,
        'session_idle_seconds' => 3600,
        'login_max_attempts' => 5,
        'login_window_seconds' => 900,
        'register_max_attempts' => 8,
        'register_window_seconds' => 3600,
        'password_min_length' => 8,
    ];

    $localFile = dirname(__DIR__) . '/config.local.php';
    if (is_file($localFile)) {
        $local = require $localFile;
        if (is_array($local)) {
            $config = array_merge($config, $local);
        }
    }

    $config['app_url'] = rtrim((string) $config['app_url'], '/');

    return $config;
}
