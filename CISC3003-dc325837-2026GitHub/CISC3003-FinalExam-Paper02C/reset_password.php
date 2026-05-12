<?php
session_start();
require_once __DIR__ . '/connect.php';

$token = isset($_GET['token']) ? trim((string)$_GET['token']) : (isset($_POST['token']) ? trim((string)$_POST['token']) : '');
$errors = [];
$done = false;

if ($token === '') {
    $errors[] = 'Missing reset token.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $p1 = (string)filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $p2 = (string)filter_input(INPUT_POST, 'password2', FILTER_UNSAFE_RAW);
    if (strlen($p1) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($p1 !== $p2) {
        $errors[] = 'Passwords do not match.';
    }
    if (empty($errors)) {
        $stmt = $mysqli->prepare(
            'SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW() LIMIT 1'
        );
        if ($stmt) {
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                $hash = password_hash($p1, PASSWORD_DEFAULT);
                $id = (int)$row['id'];
                $u = $mysqli->prepare(
                    'UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?'
                );
                if ($u) {
                    $u->bind_param('si', $hash, $id);
                    $u->execute();
                    $u->close();
                }
                $done = true;
            } else {
                $errors[] = 'Invalid or expired token.';
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
    <title>Set new password</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>New password</h1>
        <nav>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <?php if ($done): ?>
                <p class="alert alert-success">Password updated. <a href="login.php">Log in</a>.</p>
            <?php else: ?>
                <?php foreach ($errors as $e): ?>
                    <p class="alert alert-error"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endforeach; ?>
                <?php if ($token !== ''): ?>
                    <form method="post" action="reset_password.php">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                        <label for="password">New password</label>
                        <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
                        <label for="password2">Confirm</label>
                        <input type="password" id="password2" name="password2" required minlength="8" autocomplete="new-password">
                        <button type="submit">Update password</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
