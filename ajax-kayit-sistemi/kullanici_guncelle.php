<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Yetkiniz yok.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
$ad = trim($_POST['ad'] ?? '');
$soyad = trim($_POST['soyad'] ?? '');
$aktif = $_POST['aktif'] === '1' ? 1 : 0;
$rol = $_POST['rol'] ?? 'user';

if (!$id || !$ad || !$soyad || !in_array($rol, ['admin','editor','user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Eksik veya geçersiz bilgi.']);
    exit;
}

$guncelle = $db->prepare("UPDATE users SET ad = ?, soyad = ?, aktif = ?, rol = ? WHERE id = ?");
$sonuc = $guncelle->execute([$ad, $soyad, $aktif, $rol, $id]);

if ($sonuc) {
    echo json_encode(['status' => 'success', 'message' => 'Kullanıcı bilgileri güncellendi.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Güncelleme başarısız.']);
}
