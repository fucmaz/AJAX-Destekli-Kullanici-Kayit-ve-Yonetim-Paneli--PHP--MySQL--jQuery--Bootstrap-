<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

require_json_api();

$config = app_config();
if (rate_limit_is_blocked('login', (int) $config['login_max_attempts'], (int) $config['login_window_seconds'])) {
    json_response(['status' => 'error', 'message' => 'Çok fazla başarısız deneme. Lütfen daha sonra tekrar deneyin.'], 429);
}

$email = trim((string) ($_POST['email'] ?? ''));
$sifre = (string) ($_POST['sifre'] ?? '');

if ($email === '' || $sifre === '') {
    json_response(['status' => 'error', 'message' => 'Boş alan bırakmayın.']);
}

$db = db();
$sorgu = $db->prepare('SELECT id, ad, email, sifre, rol, aktif FROM users WHERE email = ? LIMIT 1');
$sorgu->execute([$email]);
$kullanici = $sorgu->fetch();

$valid = is_array($kullanici)
    && (int) $kullanici['aktif'] === 1
    && is_string($kullanici['sifre'] ?? null)
    && password_verify($sifre, $kullanici['sifre']);

if (!$valid) {
    rate_limit_hit('login', (int) $config['login_window_seconds']);
    json_response(['status' => 'error', 'message' => 'Geçersiz e-posta veya şifre.']);
}

if (password_needs_rehash($kullanici['sifre'], PASSWORD_DEFAULT)) {
    $yeniHash = password_hash($sifre, PASSWORD_DEFAULT);
    $guncelle = $db->prepare('UPDATE users SET sifre = ? WHERE id = ?');
    $guncelle->execute([$yeniHash, $kullanici['id']]);
}

rate_limit_clear('login');
establish_user_session($kullanici);

json_response(['status' => 'success', 'message' => 'Giriş başarılı.']);
