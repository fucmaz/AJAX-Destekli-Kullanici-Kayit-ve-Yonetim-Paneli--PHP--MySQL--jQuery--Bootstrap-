<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Yetkiniz yok.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz kullanıcı ID.']);
    exit;
}

// Kendisini silmesini engelle
if ($_SESSION['user_id'] == $id) {
    echo json_encode(['status' => 'error', 'message' => 'Kendi hesabınızı silemezsiniz.']);
    exit;
}

// Silinmek istenen kişinin rolünü al
$sorgu = $db->prepare("SELECT rol FROM users WHERE id = ?");
$sorgu->execute([$id]);
$hedef = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$hedef) {
    echo json_encode(['status' => 'error', 'message' => 'Kullanıcı bulunamadı.']);
    exit;
}

// Admin silinmesin
if ($hedef['rol'] === 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Admin kullanıcıları silemezsiniz.']);
    exit;
}

$sil = $db->prepare("DELETE FROM users WHERE id = ?");
if ($sil->execute([$id])) {
    echo json_encode(['status' => 'success', 'message' => 'Kullanıcı başarıyla silindi.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Kullanıcı silinemedi.']);
}
