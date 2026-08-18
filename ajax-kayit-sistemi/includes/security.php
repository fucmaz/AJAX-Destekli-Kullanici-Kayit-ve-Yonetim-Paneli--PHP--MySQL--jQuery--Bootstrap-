<?php
declare(strict_types=1);

function init_app(): void
{
    configure_error_handling();
    start_secure_session();
    enforce_idle_timeout();
    if (PHP_SAPI !== 'cli') {
        send_security_headers();
    }
    csrf_token();
}

function configure_error_handling(): void
{
    $debug = (bool) (app_config()['debug'] ?? false);
    ini_set('display_errors', $debug ? '1' : '0');
    ini_set('display_startup_errors', $debug ? '1' : '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = is_https();
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $secure ? '1' : '0');
    ini_set('session.gc_maxlifetime', (string) (int) (app_config()['session_idle_seconds'] ?? 3600));

    session_name('AJAXKAYITSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    return isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443;
}

function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    $nonce = csp_nonce();
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header('X-XSS-Protection: 0');
    header("Content-Security-Policy: default-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'; object-src 'none'; script-src 'self' 'nonce-{$nonce}' https://code.jquery.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net; connect-src 'self'");
    header('Cache-Control: no-store, no-cache, must-revalidate');
}

function csp_nonce(): string
{
    static $nonce = null;
    if (!is_string($nonce) || $nonce === '') {
        $nonce = rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');
    }
    return $nonce;
}

function enforce_idle_timeout(): void
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }
    $idle = (int) (app_config()['session_idle_seconds'] ?? 3600);
    $last = (int) ($_SESSION['last_activity'] ?? 0);
    if ($last > 0 && (time() - $last) > $idle) {
        unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role'], $_SESSION['last_activity']);
        session_regenerate_id(true);
        return;
    }
    $_SESSION['last_activity'] = time();
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf']) || !is_string($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token = null): bool
{
    $session = $_SESSION['_csrf'] ?? '';
    if (!is_string($session) || $session === '') {
        return false;
    }

    if ($token === null) {
        $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $post = $_POST['_csrf'] ?? '';
        $token = is_string($header) && $header !== '' ? $header : (is_string($post) ? $post : '');
    }

    if (!is_string($token) || $token === '') {
        return false;
    }

    return hash_equals($session, $token);
}

function require_csrf(): void
{
    if (!verify_csrf()) {
        json_response(['status' => 'error', 'message' => 'Güvenlik doğrulaması başarısız. Sayfayı yenileyip tekrar deneyin.'], 403);
    }
}

function request_method(): string
{
    return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
}

function require_post(): void
{
    if (request_method() !== 'POST') {
        json_response(['status' => 'error', 'message' => 'Geçersiz istek yöntemi.'], 405);
    }
}

function require_json_api(): void
{
    require_post();
    require_csrf();
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return is_string($ip) && $ip !== '' ? $ip : '0.0.0.0';
}

function rate_limit_key(string $action): string
{
    return hash('sha256', $action . '|' . client_ip());
}

function rate_limit_storage_dir(): string
{
    $dir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'ajax_kayit_rl';
    if (!is_dir($dir)) {
        mkdir($dir, 0700, true);
    }
    return $dir;
}

function rate_limit_is_blocked(string $action, int $maxAttempts, int $windowSeconds): bool
{
    $file = rate_limit_storage_dir() . DIRECTORY_SEPARATOR . rate_limit_key($action) . '.json';
    $now = time();
    $hits = [];

    if (is_file($file)) {
        $raw = file_get_contents($file);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if (is_array($decoded)) {
            foreach ($decoded as $ts) {
                if (is_int($ts) && ($now - $ts) < $windowSeconds) {
                    $hits[] = $ts;
                }
            }
        }
    }

    return count($hits) >= $maxAttempts;
}

function rate_limit_hit(string $action, int $windowSeconds): void
{
    $file = rate_limit_storage_dir() . DIRECTORY_SEPARATOR . rate_limit_key($action) . '.json';
    $now = time();
    $hits = [];

    if (is_file($file)) {
        $raw = file_get_contents($file);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if (is_array($decoded)) {
            foreach ($decoded as $ts) {
                if (is_int($ts) && ($now - $ts) < $windowSeconds) {
                    $hits[] = $ts;
                }
            }
        }
    }

    $hits[] = $now;
    file_put_contents($file, json_encode($hits), LOCK_EX);
}

function rate_limit_clear(string $action): void
{
    $file = rate_limit_storage_dir() . DIRECTORY_SEPARATOR . rate_limit_key($action) . '.json';
    if (is_file($file)) {
        unlink($file);
    }
}

function logout_session(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        start_secure_session();
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'] ?? '/',
            'domain' => $params['domain'] ?? '',
            'secure' => (bool) ($params['secure'] ?? false),
            'httponly' => (bool) ($params['httponly'] ?? true),
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }
    session_regenerate_id(true);
    session_destroy();
    session_write_close();
}
