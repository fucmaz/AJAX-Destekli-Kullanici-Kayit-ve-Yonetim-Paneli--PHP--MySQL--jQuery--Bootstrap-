<?php
session_start();
require 'db.php';

$email = $_POST['email'] ?? '';
$sifre = $_POST['sifre'] ?? '';

if (!$email || !$sifre) {
    echo json_encode(['status' => 'error', 'message' => 'Boş alan bırakmayın.']);
    exit;
}

$sorgu = $db->prepare("SELECT * FROM users WHERE email = ? AND aktif = 1");
$sorgu->execute([$email]);
$kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
    $_SESSION['user_id'] = $kullanici['id'];
    $_SESSION['user_name'] = $kullanici['ad'];
    $_SESSION['user_role'] = $kullanici['rol'] ?? 'user'; // ileride yetkilendirme için

    echo json_encode(['status' => 'success', 'message' => 'Giriş başarılı.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz e-posta veya şifre.']);
}
