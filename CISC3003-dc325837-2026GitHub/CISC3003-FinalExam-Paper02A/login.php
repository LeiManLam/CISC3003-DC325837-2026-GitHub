<?php
session_start();
require_once __DIR__ . '/connect.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $password = (string)filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

    if ($email === false || $email === null) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare('SELECT id, full_name, password_hash, created_at FROM users WHERE email = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $stmt->close();
            if ($row && password_verify($password, $row['password_hash'])) {
                $_SESSION['user_id'] = (int)$row['id'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['user_since'] = $row['created_at'];
                header('Location: dashboard.php');
                exit;
            }
        }
        $errors[] = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario A — Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Login</h1>
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
            <form method="post" action="login.php" autocomplete="on">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="255"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars((string)$_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">

                <button type="submit">Sign in</button>
            </form>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
