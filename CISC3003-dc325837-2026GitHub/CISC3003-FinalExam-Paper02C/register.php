<?php
session_start();
require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/helpers.php';

if (!empty($_SESSION['c_user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$mailNote = '';
$mailOk = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string)filter_input(INPUT_POST, 'full_name', FILTER_UNSAFE_RAW));
    $emailIn = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var(trim((string)$emailIn), FILTER_VALIDATE_EMAIL);
    $password = (string)filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $password2 = (string)filter_input(INPUT_POST, 'password2', FILTER_UNSAFE_RAW);

    if ($fullName === '' || mb_strlen($fullName) < 2) {
        $errors[] = 'Full name is required.';
    }
    if ($email === false) {
        $errors[] = 'Valid email is required.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors) && $email) {
        $chk = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        if ($chk) {
            $chk->bind_param('s', $email);
            $chk->execute();
            if ($chk->get_result()->fetch_assoc()) {
                $errors[] = 'That email is already registered.';
            }
            $chk->close();
        }
    }

    if (empty($errors) && $email) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));
        $expires = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');

        $stmt = $mysqli->prepare(
            'INSERT INTO users (email, password_hash, full_name, email_verified, verify_token, verify_expires)
             VALUES (?, ?, ?, 0, ?, ?)'
        );
        if ($stmt) {
            $stmt->bind_param('sssss', $email, $hash, $fullName, $token, $expires);
            if ($stmt->execute()) {
                $base = c_site_base_url();
                $link = $base . '/verify_email.php?token=' . rawurlencode($token);
                $subj = 'Verify your account';
                $html = '<p>Click to verify your email:</p><p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">Verify</a></p>';
                $alt = "Verify your email: {$link}";
                $sent = c_send_mail($email, $subj, $html, $alt);
                if (!$sent['ok']) {
                    $mailOk = false;
                    $mailNote = 'Account created, but email could not be sent: ' . htmlspecialchars($sent['error'] ?? 'unknown', ENT_QUOTES, 'UTF-8')
                        . '. Configure mail_config.php or ask your instructor.';
                } else {
                    $mailNote = 'Check your inbox for the verification link.';
                }
            } else {
                $errors[] = 'Could not register.';
            }
            $stmt->close();
        } else {
            $errors[] = 'Server error.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up — Scenario C</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Sign up</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <?php foreach ($errors as $e): ?>
                <p class="alert alert-error"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <?php if ($mailNote !== ''): ?>
                <p class="alert <?php echo $mailOk ? 'alert-success' : 'alert-error'; ?>"><?php echo $mailNote; ?></p>
            <?php endif; ?>

            <form id="signup-form" method="post" action="register.php" novalidate autocomplete="on">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" required minlength="2" maxlength="120"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars((string)$_POST['full_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="255"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars((string)$_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                <p id="email-ajax-msg" class="hint" hidden></p>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">

                <label for="password2">Confirm password</label>
                <input type="password" id="password2" name="password2" required minlength="8" autocomplete="new-password">

                <button type="submit">Create account</button>
            </form>
            <p class="hint">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
