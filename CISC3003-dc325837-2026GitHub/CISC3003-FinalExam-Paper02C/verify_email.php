<?php
session_start();
require_once __DIR__ . '/connect.php';

$token = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
$msg = '';
$ok = false;

if ($token === '' || strlen($token) < 20) {
    $msg = 'Invalid verification link.';
} else {
    $stmt = $mysqli->prepare(
        'SELECT id FROM users WHERE verify_token = ? AND verify_expires > NOW() LIMIT 1'
    );
    if ($stmt) {
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            $id = (int)$row['id'];
            $u = $mysqli->prepare(
                'UPDATE users SET email_verified = 1, verify_token = NULL, verify_expires = NULL WHERE id = ?'
            );
            if ($u) {
                $u->bind_param('i', $id);
                $u->execute();
                $u->close();
            }
            $ok = true;
            $msg = 'Your email is verified. You can log in now.';
        } else {
            $msg = 'This link is invalid or has expired.';
        }
    } else {
        $msg = 'Server error.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify email</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Email verification</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <p class="alert <?php echo $ok ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <?php if ($ok): ?>
                <p><a class="btn-link" href="login.php">Go to login</a></p>
            <?php endif; ?>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
