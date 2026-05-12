<?php
session_start();
if (empty($_SESSION['c_user_id'])) {
    header('Location: login.php');
    exit;
}
$name = $_SESSION['c_name'] ?? 'User';
$since = $_SESSION['c_since'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Scenario C</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header>
        <h1>Your dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <p class="welcome">Welcome, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>.</p>
            <p class="meta">Member since: <strong><?php echo htmlspecialchars((string)$since, ENT_QUOTES, 'UTF-8'); ?></strong></p>

            <h2>Services</h2>
            <div class="dashboard-grid">
                <div class="tile">
                    <h3>Profile</h3>
                    <p>View your account status (email verified).</p>
                </div>
                <div class="tile">
                    <h3>Security</h3>
                    <p>Passwords stored with <code>password_hash</code>; reset via email token.</p>
                </div>
                <div class="tile">
                    <h3>Preferences</h3>
                    <p>Placeholder — extend with your own settings.</p>
                </div>
                <div class="tile">
                    <h3>Support</h3>
                    <p>Use <a href="forgot_password.php">password reset</a> if needed.</p>
                </div>
            </div>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
