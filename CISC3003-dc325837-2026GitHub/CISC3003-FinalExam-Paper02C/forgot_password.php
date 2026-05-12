<?php
session_start();
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/helpers.php';

$msg = '';
$showBanner = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $showBanner = true;
    $msg = 'If an account exists for that email, a reset link has been sent.';

    $emailIn = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var(trim((string)$emailIn), FILTER_VALIDATE_EMAIL);

    if ($email !== false) {
        $stmt = $mysqli->prepare('SELECT id, full_name FROM users WHERE email = ? AND email_verified = 1 LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                $token = bin2hex(random_bytes(32));
                $expires = (new DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');
                $u = $mysqli->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
                if ($u) {
                    $uid = (int)$row['id'];
                    $u->bind_param('ssi', $token, $expires, $uid);
                    $u->execute();
                    $u->close();
                }
                $base = c_site_base_url();
                $link = $base . '/reset_password.php?token=' . rawurlencode($token);
                $subj = 'Password reset';
                $html = '<p>Hi ' . htmlspecialchars((string)$row['full_name'], ENT_QUOTES, 'UTF-8') . ',</p>';
                $html .= '<p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">Reset your password</a> (expires in 1 hour).</p>';
                $alt = "Reset password: {$link}";
                c_send_mail($email, $subj, $html, $alt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Reset password</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <?php if ($showBanner): ?>
                <p class="alert alert-success"><?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <form method="post" action="forgot_password.php">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="255">
                <button type="submit">Send reset link</button>
            </form>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
