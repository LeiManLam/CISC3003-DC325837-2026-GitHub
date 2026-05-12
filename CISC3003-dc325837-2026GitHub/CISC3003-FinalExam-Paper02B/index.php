<?php
session_start();
$flashType = $_SESSION['flash_type'] ?? null;
$flashMsg = $_SESSION['flash_msg'] ?? null;
unset($_SESSION['flash_type'], $_SESSION['flash_msg']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario B — Contact (PHPMailer)</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Scenario B — Contact form &amp; PHPMailer</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
            <?php if (!empty($_SESSION['b_user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>
        <?php if ($flashMsg): ?>
            <div class="alert <?php echo $flashType === 'success' ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($flashMsg, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="card" id="contact">
            <h2>Contact us</h2>
            <p>Client-side validation runs in <code>script.js</code>. Submitting uses POST →
                <code>contact_send.php</code> → redirect back here (PRG).</p>
            <form id="contact-form" method="post" action="contact_send.php" novalidate>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required minlength="2" maxlength="120">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="255">

                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required maxlength="200">

                <label for="message">Message</label>
                <textarea id="message" name="message" required minlength="10" maxlength="4000"></textarea>

                <button type="submit">Send</button>
            </form>
        </div>

        <div class="card">
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
