<?php
session_start();
require_once __DIR__ . '/connect.php';

$errors = [];
$ok = isset($_GET['ok']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if ($email === false) {
        $errors[] = 'Valid email is required.';
    } else {
        $stmt = $mysqli->prepare('INSERT INTO newsletter_users (email) VALUES (?)');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            if (!$stmt->execute()) {
                if ($mysqli->errno === 1062) {
                    $errors[] = 'That email is already registered.';
                } else {
                    $errors[] = 'Could not register.';
                }
            } else {
                header('Location: register.php?ok=1');
                exit;
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
    <title>Scenario B — Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Newsletter signup</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <?php if ($ok): ?>
                <p class="alert alert-success">Thanks — you can <a href="login.php">sign in</a> with this email (demo password below).</p>
            <?php endif; ?>
            <?php foreach ($errors as $e): ?>
                <p class="alert alert-error"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <form method="post" action="register.php">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="255"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars((string)$_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
                <button type="submit">Sign up</button>
            </form>
            <p class="hint">Demo login password (after signup): <code>contact2026</code></p>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
