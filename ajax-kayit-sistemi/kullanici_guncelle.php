<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_json_api();
requireRole(['admin']);

$id = (int) ($_POST['id'] ?? 0);
$ad = trim((string) ($_POST['ad'] ?? ''));
$soyad = trim((string) ($_POST['soyad'] ?? ''));
$aktif = normalize_aktif($_POST['aktif'] ?? '0');
$rol = (string) ($_POST['rol'] ?? 'user');

if ($id <= 0 || !validate_name($ad) || !validate_name($soyad) || !is_allowed_role($rol)) {
    json_response(['status' => 'error', 'message' => 'Eksik veya geçersiz bilgi.']);
}

$db = db();
$sorgu = $db->prepare('SELECT id, rol, aktif FROM users WHERE id = ? LIMIT 1');
$sorgu->execute([$id]);
$hedef = $sorgu->fetch();

if (!$hedef) {
    json_response(['status' => 'error', 'message' => 'Kullanıcı bulunamadı.']);
}

$actor = [
    'id' => (int) current_user_id(),
    'rol' => (string) current_user_role(),
];

$izin = can_update_user($db, $actor, $hedef, $rol, $aktif);
if ($izin !== true) {
    json_response(['status' => 'error', 'message' => $izin]);
}

$guncelle = $db->prepare('UPDATE users SET ad = ?, soyad = ?, aktif = ?, rol = ? WHERE id = ?');
$sonuc = $guncelle->execute([$ad, $soyad, $aktif, $rol, $id]);

if (!$sonuc) {
    json_response(['status' => 'error', 'message' => 'Güncelleme başarısız.']);
}

if ($id === current_user_id()) {
    $_SESSION['user_name'] = $ad;
    $_SESSION['user_role'] = $rol;
}

json_response(['status' => 'success', 'message' => 'Kullanıcı bilgileri güncellendi.']);
