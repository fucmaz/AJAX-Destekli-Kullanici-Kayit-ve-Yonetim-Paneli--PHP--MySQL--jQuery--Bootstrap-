<?php
declare(strict_types=1);

function current_user_id(): ?int
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $id = (int) $_SESSION['user_id'];
    return $id > 0 ? $id : null;
}

function current_user_role(): ?string
{
    $role = $_SESSION['user_role'] ?? null;
    return is_string($role) && is_allowed_role($role) ? $role : null;
}

function current_user_name(): string
{
    return isset($_SESSION['user_name']) && is_string($_SESSION['user_name'])
        ? $_SESSION['user_name']
        : '';
}

function requireLogin(): void
{
    if (current_user_id() === null) {
        if (is_ajax_request()) {
            json_response(['status' => 'error', 'message' => 'Oturum gerekli.'], 401);
        }
        header('Location: login.php');
        exit;
    }
}

function requireRole(array $roles = []): void
{
    requireLogin();
    $role = current_user_role();
    if ($role === null || !in_array($role, $roles, true)) {
        if (is_ajax_request()) {
            json_response(['status' => 'error', 'message' => 'Yetkiniz yok.'], 403);
        }
        http_response_code(403);
        echo '<div style="padding:20px;"><b>Erişim reddedildi:</b> Bu sayfayı görüntüleme yetkiniz yok.</div>';
        exit;
    }
}
