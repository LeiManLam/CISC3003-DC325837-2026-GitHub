<?php

declare(strict_types=1);

function c_site_base_url(): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');
    return $scheme . '://' . $host . $dir;
}

/**
 * @return array{ok:bool,error?:string}
 */
function c_send_mail(string $to, string $subject, string $htmlBody, string $altBody): array
{
    $cfg = require __DIR__ . '/mail_config.php';
    if ($cfg['smtp_user'] === '' || $cfg['smtp_pass'] === '' || $cfg['from_email'] === '') {
        return ['ok' => false, 'error' => 'Mail not configured (mail_config.php).'];
    }

    require_once __DIR__ . '/lib/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/lib/PHPMailer/src/SMTP.php';

    $mail = null;
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $cfg['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $cfg['smtp_user'];
        $mail->Password = $cfg['smtp_pass'];
        $mail->SMTPSecure = $cfg['smtp_secure'];
        $mail->Port = (int)$cfg['smtp_port'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($cfg['from_email'], $cfg['from_name']);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = $altBody;
        $mail->send();
        return ['ok' => true];
    } catch (Throwable $e) {
        $info = ($mail instanceof PHPMailer\PHPMailer\PHPMailer) ? $mail->ErrorInfo : $e->getMessage();
        return ['ok' => false, 'error' => $info];
    }
}
