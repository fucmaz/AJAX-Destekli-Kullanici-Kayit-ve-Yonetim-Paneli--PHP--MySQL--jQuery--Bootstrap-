<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/includes/mailer.php';

require_json_api();

$config = app_config();
if (rate_limit_is_blocked('register', (int) $config['register_max_attempts'], (int) $config['register_window_seconds'])) {
    json_response(['status' => 'error', 'message' => 'Çok fazla deneme yaptınız. Lütfen daha sonra tekrar deneyin.'], 429);
}

$ad = trim((string) ($_POST['ad'] ?? ''));
$soyad = trim((string) ($_POST['soyad'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$sifre = (string) ($_POST['sifre'] ?? '');

if (!validate_name($ad) || !validate_name($soyad)) {
    json_response(['status' => 'error', 'message' => 'Ad ve soyad yalnızca harf içermeli ve 50 karakteri geçmemelidir.']);
}
if (!validate_email_address($email)) {
    json_response(['status' => 'error', 'message' => 'Geçerli bir e-posta giriniz.']);
}
$passwordError = validate_password($sifre);
if ($passwordError !== null) {
    json_response(['status' => 'error', 'message' => $passwordError]);
}

rate_limit_hit('register', (int) $config['register_window_seconds']);

$db = db();

$kontrol = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$kontrol->execute([$email]);
if ($kontrol->fetch()) {
    json_response(['status' => 'error', 'message' => 'Bu e-posta adresi zaten kayıtlı.']);
}

$dogrulamaKodu = bin2hex(random_bytes(32));
$hashedPassword = password_hash($sifre, PASSWORD_DEFAULT);
$expiresAt = (new DateTimeImmutable('now'))->modify('+' . (int) $config['verify_expire_hours'] . ' hours')->format('Y-m-d H:i:s');

try {
    $ekle = $db->prepare('INSERT INTO users (ad, soyad, email, sifre, dogrulama_kodu, dogrulama_expires_at, aktif) VALUES (?, ?, ?, ?, ?, ?, 0)');
    $ekle->execute([$ad, $soyad, $email, $hashedPassword, $dogrulamaKodu, $expiresAt]);
} catch (PDOException $e) {
    error_log('Kayıt hatası: ' . $e->getMessage());
    json_response(['status' => 'error', 'message' => 'Kayıt yapılamadı.'], 500);
}

$mailOk = send_verification_email($email, $ad, $soyad, $dogrulamaKodu);
if ($mailOk) {
    json_response(['status' => 'success', 'message' => 'Kayıt başarılı! Lütfen e-postanızı doğrulayın.']);
}

json_response(['status' => 'error', 'message' => 'Kayıt yapıldı ama e-posta gönderilemedi. Yöneticinize başvurun.']);
