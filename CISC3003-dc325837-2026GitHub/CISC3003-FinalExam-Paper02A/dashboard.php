<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$name = $_SESSION['user_name'] ?? 'User';
$since = $_SESSION['user_since'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario A — Dashboard</title>
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
            <p class="welcome">Welcome, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>.</p>
            <p class="meta">You became a user on:
                <strong><?php echo htmlspecialchars((string)$since, ENT_QUOTES, 'UTF-8'); ?></strong>
                (server timestamp from registration).</p>

            <div class="dashboard-grid">
                <div class="tile">
                    <h3>Profile</h3>
                    <p>Your account is active for Scenario A.</p>
                </div>
                <div class="tile">
                    <h3>Forms lab</h3>
                    <p>Registration used prepared statements and filters.</p>
                </div>
                <div class="tile">
                    <h3>Security</h3>
                    <p>No raw SQL concatenation for user input.</p>
                </div>
            </div>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
