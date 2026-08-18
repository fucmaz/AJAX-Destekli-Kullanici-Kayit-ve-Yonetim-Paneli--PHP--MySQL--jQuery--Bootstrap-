<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_json_api();
requireRole(['admin']);

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    json_response(['status' => 'error', 'message' => 'Geçersiz kullanıcı ID.']);
}

$db = db();
$sorgu = $db->prepare('SELECT id, rol FROM users WHERE id = ? LIMIT 1');
$sorgu->execute([$id]);
$hedef = $sorgu->fetch();

if (!$hedef) {
    json_response(['status' => 'error', 'message' => 'Kullanıcı bulunamadı.']);
}

$actor = [
    'id' => (int) current_user_id(),
    'rol' => (string) current_user_role(),
];

$izin = can_delete_user($actor, $hedef);
if ($izin !== true) {
    json_response(['status' => 'error', 'message' => $izin]);
}

$sil = $db->prepare('DELETE FROM users WHERE id = ? AND rol != ?');
$sil->execute([$id, 'admin']);

if ($sil->rowCount() !== 1) {
    json_response(['status' => 'error', 'message' => 'Kullanıcı silinemedi.']);
}

json_response(['status' => 'success', 'message' => 'Kullanıcı başarıyla silindi.']);
