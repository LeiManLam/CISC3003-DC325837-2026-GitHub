<?php
session_start();
if (empty($_SESSION['b_user_id'])) {
    header('Location: login.php');
    exit;
}
$email = $_SESSION['b_email'] ?? '';
$since = $_SESSION['b_since'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario B — Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <p class="welcome">Welcome, <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>.</p>
            <p class="meta">Newsletter member since: <strong><?php echo htmlspecialchars((string)$since, ENT_QUOTES, 'UTF-8'); ?></strong></p>

            <div class="dashboard-grid">
                <div class="tile">
                    <h3>Contact</h3>
                    <p>PHPMailer + PRG on the home page.</p>
                </div>
                <div class="tile">
                    <h3>Debugging mail</h3>
                    <p>Failed sends store <code>ErrorInfo</code> in <code>contact_messages.mail_debug</code>.</p>
                </div>
                <div class="tile">
                    <h3>SMTP</h3>
                    <p>Edit <code>mail_config.php</code> for Gmail SMTP.</p>
                </div>
            </div>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
