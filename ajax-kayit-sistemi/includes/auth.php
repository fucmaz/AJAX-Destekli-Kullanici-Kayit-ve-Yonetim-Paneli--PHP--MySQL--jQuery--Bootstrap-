<?php
session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($roles = []) {
    requireLogin();
    if (!in_array($_SESSION['user_role'], $roles)) {
        die('<div style="padding:20px;"><b>Erişim reddedildi:</b> Bu sayfayı görüntüleme yetkiniz yok.</div>');
    }
}
