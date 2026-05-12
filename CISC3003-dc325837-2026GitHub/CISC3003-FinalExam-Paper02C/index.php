<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario C — Secure signup &amp; login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Scenario C — Sign up / Sign in</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
            <?php if (!empty($_SESSION['c_user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <div class="card">
            <p>Server-side validation, <code>password_hash</code>, email verification before login,
                password reset by email, Ajax email availability check, and a post-login dashboard.</p>
            <p>
                <button type="button" id="btn-signup">Sign up</button>
                <button type="button" id="btn-signin">Sign in</button>
            </p>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>
