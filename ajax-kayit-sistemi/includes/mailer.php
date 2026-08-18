<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send_verification_email(string $email, string $ad, string $soyad, string $code): bool
{
    $config = app_config();
    $driver = getenv('TEST_MAIL_DRIVER') ?: (string) ($config['mail_driver'] ?? 'smtp');
    if ($driver === 'log') {
        error_log('Doğrulama e-postası (log): ' . $email);
        return true;
    }

    require_once dirname(__DIR__) . '/src/phpmailer/PHPMailer.php';
    require_once dirname(__DIR__) . '/src/phpmailer/SMTP.php';
    require_once dirname(__DIR__) . '/src/phpmailer/Exception.php';

    $link = app_url('dogrula.php?kod=' . urlencode($code));
    $safeAd = e($ad);
    $safeSoyad = e($soyad);
    $safeLink = e($link);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = (string) $config['mail_host'];
        $mail->SMTPAuth = ($config['mail_username'] ?? '') !== '';
        $mail->Username = (string) $config['mail_username'];
        $mail->Password = (string) $config['mail_password'];
        $mail->Port = (int) $config['mail_port'];
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $encryption = strtolower((string) ($config['mail_encryption'] ?? 'tls'));
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        if (!(bool) $config['mail_verify_ssl']) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $mail->setFrom((string) $config['mail_from'], (string) $config['mail_from_name']);
        $mail->addAddress($email, $ad . ' ' . $soyad);
        $mail->isHTML(true);
        $mail->Subject = 'E-posta Doğrulama';
        $mail->Body = "
  <div style='font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; border-radius: 10px; color: #333;'>
    <div style='background: #0d6efd; color: #fff; padding: 10px 20px; border-radius: 8px 8px 0 0; text-align: center;'>
      <h2 style='margin: 0;'>Kayıt Onayı</h2>
    </div>
    <div style='padding: 20px;'>
      <p><strong>Merhaba {$safeAd} {$safeSoyad},</strong></p>
      <p>Kayıt işleminizi tamamlamak için aşağıdaki bağlantıya tıklayın:</p>
      <p style='text-align: center; margin: 30px 0;'>
        <a href='{$safeLink}' style='background-color: #198754; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
          Hesabımı Doğrula
        </a>
      </p>
      <p>Bağlantı {$config['verify_expire_hours']} saat geçerlidir.</p>
      <hr>
      <p style='font-size: 12px; color: #888;'>Bu e-posta size sistem tarafından otomatik olarak gönderilmiştir.</p>
    </div>
  </div>
";
        $mail->AltBody = "Merhaba {$ad} {$soyad},\nHesabınızı doğrulamak için bu bağlantıyı açın: {$link}";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail gönderilemedi: ' . $e->getMessage());
        return false;
    }
}
