<?php
declare(strict_types=1);

function set_db_override(?PDO $pdo): void
{
    $GLOBALS['__db_override'] = $pdo;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (isset($GLOBALS['__db_override']) && $GLOBALS['__db_override'] instanceof PDO) {
        $pdo = $GLOBALS['__db_override'];
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $testDsn = getenv('TEST_DB_DSN');
    if (is_string($testDsn) && $testDsn !== '') {
        $pdo = new PDO($testDsn, null, null, $options);
        return $pdo;
    }

    $config = app_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['db_host'],
        $config['db_port'],
        $config['db_name']
    );

    try {
        $pdo = new PDO($dsn, (string) $config['db_user'], (string) $config['db_pass'], $options);
    } catch (PDOException $e) {
        error_log('Veritabanı bağlantı hatası: ' . $e->getMessage());
        if (PHP_SAPI === 'cli') {
            throw $e;
        }
        http_response_code(500);
        if (is_ajax_request()) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['status' => 'error', 'message' => 'Veritabanı bağlantısı kurulamadı.']);
            exit;
        }
        exit('Veritabanı bağlantısı kurulamadı.');
    }

    return $pdo;
}
