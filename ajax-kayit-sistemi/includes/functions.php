<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_response(array $data, int $status = 200): void
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function is_ajax_request(): bool
{
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    return (is_string($requestedWith) && strcasecmp($requestedWith, 'XMLHttpRequest') === 0)
        || (is_string($accept) && str_contains($accept, 'application/json'));
}

function allowed_roles(): array
{
    return ['admin', 'editor', 'user'];
}

function is_allowed_role(string $role): bool
{
    return in_array($role, allowed_roles(), true);
}

function validate_name(string $value): bool
{
    $value = trim($value);
    if ($value === '' || mb_strlen($value) > 50) {
        return false;
    }
    return (bool) preg_match('/^[\p{L}\p{M}\s\'.-]{1,50}$/u', $value);
}

function validate_email_address(string $email): bool
{
    if ($email === '' || mb_strlen($email) > 100) {
        return false;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_password(string $password): ?string
{
    $min = (int) (app_config()['password_min_length'] ?? 8);
    $len = strlen($password);
    if ($len < $min) {
        return 'Şifre en az ' . $min . ' karakter olmalıdır.';
    }
    if ($len > 72) {
        return 'Şifre en fazla 72 karakter olabilir.';
    }
    return null;
}

function normalize_aktif(mixed $value): int
{
    return ((string) $value === '1' || $value === 1 || $value === true) ? 1 : 0;
}

function count_admins(PDO $db, ?int $exceptId = null): int
{
    if ($exceptId !== null) {
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE rol = ? AND id != ?');
        $stmt->execute(['admin', $exceptId]);
    } else {
        $stmt = $db->prepare('SELECT COUNT(*) FROM users WHERE rol = ?');
        $stmt->execute(['admin']);
    }
    return (int) $stmt->fetchColumn();
}

function can_delete_user(array $actor, array $target): string|true
{
    if ((int) ($actor['id'] ?? 0) === (int) ($target['id'] ?? 0)) {
        return 'Kendi hesabınızı silemezsiniz.';
    }
    if (($target['rol'] ?? '') === 'admin') {
        return 'Admin kullanıcıları silemezsiniz.';
    }
    return true;
}

function can_update_user(PDO $db, array $actor, array $target, string $newRole, int $newAktif): string|true
{
    if (!is_allowed_role($newRole)) {
        return 'Geçersiz rol.';
    }

    $actorId = (int) ($actor['id'] ?? 0);
    $targetId = (int) ($target['id'] ?? 0);
    $targetIsAdmin = ($target['rol'] ?? '') === 'admin';

    if ($actorId === $targetId && $newAktif !== 1) {
        return 'Kendi hesabınızı pasife alamazsınız.';
    }

    if ($actorId === $targetId && $newRole !== 'admin' && $targetIsAdmin && count_admins($db) <= 1) {
        return 'Son admin hesabının rolü değiştirilemez.';
    }

    if ($targetIsAdmin && $newRole !== 'admin' && count_admins($db) <= 1) {
        return 'Sistemde en az bir admin kalmalıdır.';
    }

    return true;
}

function establish_user_session(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = (string) ($user['ad'] ?? '');
    $_SESSION['user_role'] = is_allowed_role((string) ($user['rol'] ?? '')) ? (string) $user['rol'] : 'user';
    $_SESSION['last_activity'] = time();
}

function app_url(string $path = ''): string
{
    $base = app_config()['app_url'];
    $path = ltrim($path, '/');
    return $path === '' ? $base : $base . '/' . $path;
}
