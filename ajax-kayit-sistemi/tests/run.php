<?php
declare(strict_types=1);

$failed = 0;
$passed = 0;

function assert_true(bool $condition, string $message): void
{
    global $failed, $passed;
    if ($condition) {
        $passed++;
        echo "  OK  {$message}\n";
        return;
    }
    $failed++;
    echo "  FAIL  {$message}\n";
}

function assert_equals(mixed $expected, mixed $actual, string $message): void
{
    assert_true($expected === $actual, $message . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
}

$root = dirname(__DIR__);
require_once $root . '/includes/config.php';
require_once $root . '/includes/security.php';
require_once $root . '/includes/functions.php';
require_once $root . '/includes/auth.php';
require_once $root . '/db.php';

$_SERVER['REMOTE_ADDR'] = '203.0.113.10';
$_SERVER['REQUEST_METHOD'] = 'GET';
init_app();

echo "Unit tests\n";

assert_true(validate_name('Ferat'), 'Geçerli ad kabul edilir');
assert_true(validate_name("O'Connor"), 'Kesme işaretli ad kabul edilir');
assert_true(!validate_name(''), 'Boş ad reddedilir');
assert_true(!validate_name('<script>alert(1)</script>'), 'HTML içeren ad reddedilir');
assert_true(!validate_name(str_repeat('a', 51)), '51 karakterlik ad reddedilir');

assert_true(validate_email_address('info@deneme.com.tr'), 'Geçerli e-posta kabul edilir');
assert_true(!validate_email_address('not-an-email'), 'Geçersiz e-posta reddedilir');
assert_true(!validate_email_address(str_repeat('a', 90) . '@x.com'), 'Aşırı uzun e-posta reddedilir');

assert_equals('Şifre en az 8 karakter olmalıdır.', validate_password('123456'), 'Zayıf şifre reddedilir');
assert_equals(null, validate_password('ChangeMe!123'), 'Güçlü şifre kabul edilir');
assert_true(validate_password(str_repeat('a', 73)) !== null, '72 karakterden uzun şifre reddedilir');

assert_equals(1, normalize_aktif('1'), 'aktif=1');
assert_equals(0, normalize_aktif('0'), 'aktif=0');
assert_equals(0, normalize_aktif(null), 'aktif null -> 0');

assert_true(is_allowed_role('admin'), 'admin rolü geçerli');
assert_true(!is_allowed_role('superadmin'), 'bilinmeyen rol geçersiz');

assert_equals('&lt;script&gt;', e('<script>'), 'HTML kaçış çalışır');

$token = csrf_token();
assert_true(strlen($token) === 64, 'CSRF token 64 hex karakter');
assert_true(verify_csrf($token), 'Aynı CSRF token doğrulanır');
assert_true(!verify_csrf('deadbeef'), 'Yanlış CSRF token reddedilir');
assert_true(!verify_csrf(''), 'Boş CSRF token reddedilir');

$_POST['_csrf'] = $token;
assert_true(verify_csrf(), 'POST CSRF token doğrulanır');
unset($_POST['_csrf']);
$_SERVER['HTTP_X_CSRF_TOKEN'] = $token;
assert_true(verify_csrf(), 'Header CSRF token doğrulanır');
unset($_SERVER['HTTP_X_CSRF_TOKEN']);

$actorAdmin = ['id' => 1, 'rol' => 'admin'];
$targetSelf = ['id' => 1, 'rol' => 'user'];
$targetAdmin = ['id' => 2, 'rol' => 'admin'];
$targetUser = ['id' => 3, 'rol' => 'user'];
assert_equals('Kendi hesabınızı silemezsiniz.', can_delete_user($actorAdmin, $targetSelf), 'Kendini silme engellenir');
assert_equals('Admin kullanıcıları silemezsiniz.', can_delete_user($actorAdmin, $targetAdmin), 'Admin silme engellenir');
assert_equals(true, can_delete_user($actorAdmin, $targetUser), 'Normal kullanıcı silinebilir');

$sqlite = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$sqlite->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, rol TEXT)');
$sqlite->exec("INSERT INTO users (id, rol) VALUES (1, 'admin')");
$sqlite->exec("INSERT INTO users (id, rol) VALUES (3, 'user')");

assert_equals(1, count_admins($sqlite), 'Tek admin sayılır');
assert_equals('Sistemde en az bir admin kalmalıdır.', can_update_user($sqlite, $actorAdmin, ['id' => 1, 'rol' => 'admin'], 'user', 1), 'Son admin düşürülemez');
assert_equals('Kendi hesabınızı pasife alamazsınız.', can_update_user($sqlite, $actorAdmin, ['id' => 1, 'rol' => 'admin'], 'admin', 0), 'Kendi hesabı pasife alınamaz');
assert_equals(true, can_update_user($sqlite, $actorAdmin, ['id' => 3, 'rol' => 'user'], 'editor', 1), 'Normal kullanıcı güncellenebilir');
assert_equals('Geçersiz rol.', can_update_user($sqlite, $actorAdmin, $targetUser, 'root', 1), 'Geçersiz rol reddedilir');

$demoHash = '$2y$10$.W/JxRgozp9RYmICA0LY6uaVirRJJ1JwY7Xz/1gRP130oQkSgauha';
assert_true(password_verify('ChangeMe!123', $demoHash), 'Demo admin şifre özeti doğrulanır');

$action = 'test_' . bin2hex(random_bytes(4));
assert_true(!rate_limit_is_blocked($action, 3, 60), 'İlk istekte rate limit açık');
rate_limit_hit($action, 60);
rate_limit_hit($action, 60);
rate_limit_hit($action, 60);
assert_true(rate_limit_is_blocked($action, 3, 60), 'Limit dolunca istekler bloklanır');
rate_limit_clear($action);
assert_true(!rate_limit_is_blocked($action, 3, 60), 'Rate limit temizlenince açılır');

echo "\nPHP lint\n";
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
foreach ($iterator as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    if (str_contains($path, '/src/phpmailer/')) {
        continue;
    }
    $out = [];
    $code = 0;
    exec('php -l ' . escapeshellarg($path) . ' 2>&1', $out, $code);
    assert_true($code === 0, 'php -l ' . substr($path, strlen($root) + 1));
}

echo "\nHTTP integration tests\n";

if (!function_exists('curl_init')) {
    echo "  SKIP  curl yok, HTTP testleri atlandı\n";
} else {
    $dbFile = sys_get_temp_dir() . '/ajax_kayit_http_' . getmypid() . '.sqlite';
    if (is_file($dbFile)) {
        unlink($dbFile);
    }
    $httpDb = new PDO('sqlite:' . $dbFile, null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $httpDb->exec('CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ad TEXT NOT NULL,
        soyad TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        sifre TEXT NOT NULL,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        dogrulama_kodu TEXT,
        dogrulama_expires_at TEXT,
        aktif INTEGER NOT NULL DEFAULT 0,
        rol TEXT NOT NULL DEFAULT \'user\'
    )');
    $adminHash = password_hash('ChangeMe!123', PASSWORD_DEFAULT);
    $userHash = password_hash('UserPass!123', PASSWORD_DEFAULT);
    $ins = $httpDb->prepare('INSERT INTO users (ad, soyad, email, sifre, aktif, rol) VALUES (?, ?, ?, ?, ?, ?)');
    $ins->execute(['Ferat', 'Ucmaz', 'info@deneme.com.tr', $adminHash, 1, 'admin']);
    $ins->execute(['Ali', 'Editor', 'editor@deneme.com.tr', $userHash, 1, 'editor']);
    $ins->execute(['Veli', 'User', 'user@deneme.com.tr', $userHash, 1, 'user']);
    $httpDb = null;

    $port = 18080;
    $docRoot = $root;
    $env = getenv();
    if (!is_array($env)) {
        $env = [];
    }
    $env['TEST_DB_DSN'] = 'sqlite:' . $dbFile;
    $env['TEST_MAIL_DRIVER'] = 'log';

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['file', sys_get_temp_dir() . '/ajax_kayit_server.log', 'w'],
        2 => ['file', sys_get_temp_dir() . '/ajax_kayit_server.err', 'w'],
    ];
    $cmd = sprintf('php -S 127.0.0.1:%d -t %s', $port, escapeshellarg($docRoot));
    $proc = proc_open($cmd, $descriptors, $pipes, $docRoot, $env);
    if (!is_resource($proc)) {
        assert_true(false, 'PHP built-in server başlatılamadı');
    } else {
        $ready = false;
        for ($i = 0; $i < 25; $i++) {
            $fp = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.2);
            if (is_resource($fp)) {
                fclose($fp);
                $ready = true;
                break;
            }
            usleep(200000);
        }
        assert_true($ready, 'PHP built-in server dinliyor');

        if ($ready) {
            $cookie = tempnam(sys_get_temp_dir(), 'cj');
            $base = 'http://127.0.0.1:' . $port;

            $http = function (string $method, string $path, array $fields = [], bool $follow = false) use ($base, $cookie): array {
                $ch = curl_init($base . $path);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => true,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_COOKIEJAR => $cookie,
                    CURLOPT_COOKIEFILE => $cookie,
                    CURLOPT_FOLLOWLOCATION => $follow,
                    CURLOPT_TIMEOUT => 5,
                ]);
                if ($method === 'POST') {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-Requested-With: XMLHttpRequest']);
                }
                $raw = (string) curl_exec($ch);
                $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                curl_close($ch);
                $headers = substr($raw, 0, $headerSize);
                $body = substr($raw, $headerSize);
                return ['status' => $status, 'headers' => $headers, 'body' => $body];
            };

            $index = $http('GET', '/index.php');
            assert_equals(200, $index['status'], 'index.php 200 döner');
            assert_true(str_contains($index['body'], 'login.php'), 'Kayıt sayfası girişe yönlendirir');
            preg_match('/name="csrf-token" content="([^"]+)"/', $index['body'], $csrfMatch);
            $csrf = $csrfMatch[1] ?? '';
            assert_true($csrf !== '', 'CSRF token HTML içinde yer alır');
            assert_true(str_contains($index['headers'], 'X-Frame-Options: DENY'), 'X-Frame-Options gönderilir');
            assert_true(str_contains($index['headers'], 'Content-Security-Policy:'), 'CSP başlığı gönderilir');

            $getApi = $http('GET', '/kaydet.php');
            assert_equals(405, $getApi['status'], 'GET kaydet.php 405 döner');

            $noCsrf = $http('POST', '/kaydet.php', ['ad' => 'Ayşe', 'soyad' => 'Yılmaz', 'email' => 'ayse@example.com', 'sifre' => 'StrongPass1']);
            assert_equals(403, $noCsrf['status'], 'CSRF olmadan kayıt 403 döner');

            $register = $http('POST', '/kaydet.php', [
                '_csrf' => $csrf,
                'ad' => 'Ayşe',
                'soyad' => 'Yılmaz',
                'email' => 'ayse@example.com',
                'sifre' => 'StrongPass1',
            ]);
            $regJson = json_decode($register['body'], true);
            assert_equals(200, $register['status'], 'Kayıt isteği 200 döner');
            assert_equals('success', $regJson['status'] ?? null, 'Kayıt başarılı JSON döner');

            $dbCheck = new PDO('sqlite:' . $dbFile);
            $row = $dbCheck->query("SELECT aktif, dogrulama_kodu FROM users WHERE email = 'ayse@example.com'")->fetch(PDO::FETCH_ASSOC);
            assert_equals(0, (int) ($row['aktif'] ?? -1), 'Yeni kullanıcı pasif kaydedilir');
            $kod = (string) ($row['dogrulama_kodu'] ?? '');
            assert_true(preg_match('/^[a-f0-9]{64}$/', $kod) === 1, 'Doğrulama kodu 64 hex karakter');

            $verify = $http('GET', '/dogrula.php?kod=' . $kod);
            assert_equals(200, $verify['status'], 'Doğrulama sayfası 200 döner');
            assert_true(str_contains($verify['body'], 'E-posta başarıyla doğrulandı'), 'E-posta doğrulanır');

            $loginPage = $http('GET', '/login.php');
            preg_match('/name="csrf-token" content="([^"]+)"/', $loginPage['body'], $loginCsrf);
            $csrf = $loginCsrf[1] ?? $csrf;

            $badLogin = $http('POST', '/giris_kontrol.php', ['_csrf' => $csrf, 'email' => 'info@deneme.com.tr', 'sifre' => 'wrong-pass']);
            $badJson = json_decode($badLogin['body'], true);
            assert_equals('error', $badJson['status'] ?? null, 'Yanlış şifre reddedilir');

            $goodLogin = $http('POST', '/giris_kontrol.php', ['_csrf' => $csrf, 'email' => 'info@deneme.com.tr', 'sifre' => 'ChangeMe!123']);
            $goodJson = json_decode($goodLogin['body'], true);
            assert_equals('success', $goodJson['status'] ?? null, 'Admin girişi başarılı');

            $panel = $http('GET', '/panel.php');
            assert_equals(200, $panel['status'], 'Giriş sonrası panel açılır');
            assert_true(str_contains($panel['body'], 'Ferat'), 'Panel kullanıcı adını gösterir');

            $list = $http('GET', '/kullanici_listesi.php');
            assert_equals(200, $list['status'], 'Admin kullanıcı listesini görür');

            preg_match('/name="csrf-token" content="([^"]+)"/', $list['body'], $listCsrf);
            $csrf = $listCsrf[1] ?? $csrf;

            $deleteAdmin = $http('POST', '/kullanici_sil.php', ['_csrf' => $csrf, 'id' => 1]);
            $delAdminJson = json_decode($deleteAdmin['body'], true);
            assert_equals('error', $delAdminJson['status'] ?? null, 'Admin silinemez');

            $deleteUser = $http('POST', '/kullanici_sil.php', ['_csrf' => $csrf, 'id' => 3]);
            $delUserJson = json_decode($deleteUser['body'], true);
            assert_equals('success', $delUserJson['status'] ?? null, 'Normal kullanıcı silinir');

            $editPage = $http('GET', '/kullanici_duzenle.php?id=2');
            assert_equals(200, $editPage['status'], 'Admin düzenleme sayfasını açar');

            $logout = $http('GET', '/logout.php', [], false);
            assert_true(in_array($logout['status'], [302, 303], true), 'Çıkış yönlendirmesi yapılır');

            $anonEdit = $http('GET', '/kullanici_duzenle.php?id=2');
            assert_true(in_array($anonEdit['status'], [302, 303], true), 'Anonim kullanıcı düzenleme sayfasına alınmaz');
            assert_true(str_contains($anonEdit['headers'], 'login.php'), 'Düzenleme sayfası login.php yönlendirir');

            $anonList = $http('GET', '/kullanici_listesi.php');
            assert_true(in_array($anonList['status'], [302, 303], true), 'Anonim kullanıcı listeye alınmaz');
        }

        if (isset($pipes[0]) && is_resource($pipes[0])) {
            fclose($pipes[0]);
        }
        $status = proc_get_status($proc);
        if (!empty($status['pid'])) {
            proc_terminate($proc);
        }
        proc_close($proc);
        @unlink($cookie ?? '');
    }
    @unlink($dbFile);
}

echo "\n{$passed} passed, {$failed} failed\n";
exit($failed > 0 ? 1 : 0);
