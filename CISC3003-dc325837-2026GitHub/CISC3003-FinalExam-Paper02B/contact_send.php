<?php
/**
 * POST / redirect / GET: process contact form then redirect to index.php with flash.
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/lib/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$cfg = require __DIR__ . '/mail_config.php';

$filters = [
    'name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'email' => FILTER_SANITIZE_EMAIL,
    'subject' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'message' => FILTER_UNSAFE_RAW,
];
$in = filter_input_array(INPUT_POST, $filters) ?: [];

$name = trim((string)($in['name'] ?? ''));
$email = filter_var(trim((string)($in['email'] ?? '')), FILTER_VALIDATE_EMAIL);
$subject = trim((string)($in['subject'] ?? ''));
$message = trim((string)($in['message'] ?? ''));

$errors = [];
if ($name === '' || mb_strlen($name) < 2) {
    $errors[] = 'Name is required.';
}
if ($email === false || $email === null) {
    $errors[] = 'Valid email is required.';
}
if ($subject === '') {
    $errors[] = 'Subject is required.';
}
if ($message === '' || mb_strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters.';
}

$mailDebug = '';
$sent = false;

if (empty($errors)) {
    if ($cfg['smtp_user'] === '' || $cfg['smtp_pass'] === '' || $cfg['to_email'] === '' || $cfg['from_email'] === '') {
        $mailDebug = 'SMTP not configured: fill mail_config.php (smtp_user, smtp_pass, from_email, to_email).';
        $_SESSION['flash_type'] = 'error';
        $_SESSION['flash_msg'] = $mailDebug;
    } else {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $cfg['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $cfg['smtp_user'];
            $mail->Password = $cfg['smtp_pass'];
            $mail->SMTPSecure = $cfg['smtp_secure'];
            $mail->Port = (int)$cfg['smtp_port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($cfg['from_email'], $cfg['from_name']);
            $mail->addAddress($cfg['to_email']);
            $mail->addReplyTo($email, $name);

            $mail->isHTML(true);
            $mail->Subject = '[Contact] ' . $subject;
            $mail->Body = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));
            $mail->AltBody = $message;

            $mail->send();
            $sent = true;
            $_SESSION['flash_type'] = 'success';
            $_SESSION['flash_msg'] = 'Message sent. Thank you.';
        } catch (Exception $e) {
            $mailDebug = $mail->ErrorInfo . ' | ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
            $_SESSION['flash_msg'] = 'Mail error (see server log / DB debug column): ' . htmlspecialchars($mail->ErrorInfo, ENT_QUOTES, 'UTF-8');
        }
    }

    $stmt = $mysqli->prepare(
        'INSERT INTO contact_messages (name, email, subject, body, mail_debug) VALUES (?, ?, ?, ?, ?)'
    );
    if ($stmt) {
        $dbg = $mailDebug;
        $stmt->bind_param('sssss', $name, $email, $subject, $message, $dbg);
        $stmt->execute();
        $stmt->close();
    }
} else {
    $_SESSION['flash_type'] = 'error';
    $_SESSION['flash_msg'] = implode(' ', $errors);
}

header('Location: index.php#contact');
exit;
