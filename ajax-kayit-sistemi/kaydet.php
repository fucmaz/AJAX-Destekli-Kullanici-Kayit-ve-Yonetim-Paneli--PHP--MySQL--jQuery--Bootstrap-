<?php
header("Content-Type: application/json");
require 'db.php';

// PHPMailer dosyalarını dahil et
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'src/phpmailer/PHPMailer.php';
require 'src/phpmailer/SMTP.php';
require 'src/phpmailer/Exception.php';

// POST verilerini al
$ad = trim($_POST['ad'] ?? '');
$soyad = trim($_POST['soyad'] ?? '');
$email = trim($_POST['email'] ?? '');
$sifre = $_POST['sifre'] ?? '';

// Basit doğrulama
if (!$ad || !$soyad || !$email || !$sifre) {
    echo json_encode(['status' => 'error', 'message' => 'Tüm alanları doldurunuz.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Geçerli bir e-posta giriniz.']);
    exit;
}
$kontrol = $db->prepare("SELECT id FROM users WHERE email = ?");
$kontrol->execute([$email]);
if ($kontrol->rowCount() > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Bu e-posta adresi zaten kayıtlı.']);
    exit;
}

// Doğrulama kodu ve şifre oluştur
$dogrulama_kodu = bin2hex(random_bytes(20));
$hashedPassword = password_hash($sifre, PASSWORD_DEFAULT);

// Kullanıcıyı aktif olmayan şekilde kaydet
$ekle = $db->prepare("INSERT INTO users (ad, soyad, email, sifre, dogrulama_kodu, aktif) VALUES (?, ?, ?, ?, ?, 0)");
$sonuc = $ekle->execute([$ad, $soyad, $email, $hashedPassword, $dogrulama_kodu]);

if ($sonuc) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.xxxxx.com.tr'; // kendi SMTP adresin
        $mail->SMTPAuth = true;
        $mail->Username = 'info@xxxx.com.tr'; // kendi kullanıcı adın
        $mail->Password = 'xxxxxx'; // kendi şifren
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

          // UTF-8 ayarları
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

        $mail->SMTPOptions = [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true,
    ],
];


        $mail->setFrom('info@scriptal.com.tr', 'Kayıt Sistemi');
        $mail->addAddress($email, $ad . ' ' . $soyad);
        $mail->isHTML(true);
        $mail->Subject = 'E-posta Doğrulama';
        $dogrulama_linki = "http://localhost/ajax-kayit-sistemi/dogrula.php?kod=$dogrulama_kodu";
        $mail->Body = "
  <div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; border-radius: 10px; color: #333;'>
    <div style='background: #0d6efd; color: #fff; padding: 10px 20px; border-radius: 8px 8px 0 0; text-align: center;'>
      <h2 style='margin: 0;'>Kayıt Onayı</h2>
    </div>
    <div style='padding: 20px;'>
      <p><strong>Merhaba $ad $soyad,</strong></p>
      <p>Kayıt işleminizi tamamlamak için aşağıdaki bağlantıya tıklayın:</p>
      <p style='text-align: center; margin: 30px 0;'>
        <a href='$dogrulama_linki' style='background-color: #198754; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
          Hesabımı Doğrula
        </a>
      </p>
      <hr>
      <p style='font-size: 12px; color: #888;'>Bu e-posta size sistem tarafından otomatik olarak gönderilmiştir.</p>
    </div>
  </div>
";


        $mail->send();

        echo json_encode(['status' => 'success', 'message' => 'Kayıt başarılı! Lütfen e-postanızı doğrulayın.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Kayıt yapıldı ama e-posta gönderilemedi.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Kayıt yapılamadı.']);
}
