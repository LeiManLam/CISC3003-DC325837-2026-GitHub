<?php
session_start();
require_once __DIR__ . '/connect.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filters = [
        'full_name' => [
            'filter' => FILTER_UNSAFE_RAW,
            'flags'  => FILTER_FLAG_STRIP_LOW,
        ],
        'email' => FILTER_SANITIZE_EMAIL,
        'phone' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
        'country' => FILTER_UNSAFE_RAW,
        'gender' => FILTER_UNSAFE_RAW,
        'comments' => FILTER_UNSAFE_RAW,
        'password' => FILTER_UNSAFE_RAW,
        'password2' => FILTER_UNSAFE_RAW,
    ];
    $in = filter_input_array(INPUT_POST, $filters) ?: [];

    $fullName = trim((string)($in['full_name'] ?? ''));
    $emailRaw = (string)($in['email'] ?? '');
    $phone = trim((string)($in['phone'] ?? ''));
    $country = trim((string)($in['country'] ?? ''));
    $gender = trim((string)($in['gender'] ?? ''));
    $comments = trim((string)($in['comments'] ?? ''));
    $password = (string)($in['password'] ?? '');
    $password2 = (string)($in['password2'] ?? '');

    $allowedCountries = ['HK', 'MO', 'CN', 'US', 'OTHER'];
    $allowedGender = ['m', 'f', 'x'];
    $allowedInterest = ['web', 'db', 'mobile'];

    $interests = [];
    if (!empty($_POST['interests']) && is_array($_POST['interests'])) {
        foreach ($_POST['interests'] as $item) {
            $item = (string)$item;
            if (in_array($item, $allowedInterest, true)) {
                $interests[] = $item;
            }
        }
    }
    $interestsStr = implode(',', $interests);

    if ($fullName === '' || mb_strlen($fullName) < 2) {
        $errors[] = 'Full name is required (at least 2 characters).';
    }

    $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
    if ($email === false) {
        $errors[] = 'A valid email address is required.';
    }

    if ($phone !== '' && !preg_match('/^[\d\s\-+]{6,20}$/', $phone)) {
        $errors[] = 'Phone must be 6–20 digits or spaces / - / + only.';
    }

    if (!in_array($country, $allowedCountries, true)) {
        $errors[] = 'Please choose a valid country.';
    }

    if (!in_array($gender, $allowedGender, true)) {
        $errors[] = 'Please select a gender option.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors) && $email) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO users (full_name, email, phone, country, gender, interests, comments, password_hash)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            $errors[] = 'Could not prepare statement.';
        } else {
            $stmt->bind_param(
                'ssssssss',
                $fullName,
                $email,
                $phone,
                $country,
                $gender,
                $interestsStr,
                $comments,
                $hash
            );
            if (!$stmt->execute()) {
                if ($mysqli->errno === 1062) {
                    $errors[] = 'That email is already registered.';
                } else {
                    $errors[] = 'Could not save registration.';
                }
            } else {
                $success = true;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scenario A — Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Register</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="register.php">Register</a>
            <a href="login.php">Login</a>
        </nav>
    </header>
    <main>
        <div class="card">
            <?php if ($success): ?>
                <p class="alert alert-success">Registration saved. You can <a href="login.php">log in</a> now.</p>
            <?php endif; ?>
            <?php foreach ($errors as $e): ?>
                <p class="alert alert-error"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>

            <form method="post" action="register.php" novalidate autocomplete="on">
                <label for="full_name">Full name</label>
                <input type="text" id="full_name" name="full_name" required maxlength="120"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars((string)$_POST['full_name'], ENT_QUOTES, 'UTF-8') : ''; ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required maxlength="255"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars((string)$_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">

                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" maxlength="40"
                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars((string)$_POST['phone'], ENT_QUOTES, 'UTF-8') : ''; ?>">

                <label for="country">Country / region</label>
                <select id="country" name="country" required>
                    <?php
                    $opts = ['' => '— Select —', 'HK' => 'Hong Kong', 'MO' => 'Macau', 'CN' => 'China', 'US' => 'United States', 'OTHER' => 'Other'];
                    $sel = $_POST['country'] ?? '';
                    foreach ($opts as $val => $label) {
                        $v = htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
                        $l = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
                        $c = ((string)$sel === (string)$val) ? ' selected' : '';
                        echo "<option value=\"{$v}\"{$c}>{$l}</option>";
                    }
                    ?>
                </select>

                <fieldset>
                    <legend>Gender</legend>
                    <?php
                    $g = $_POST['gender'] ?? '';
                    foreach (['m' => 'Male', 'f' => 'Female', 'x' => 'Prefer not to say'] as $val => $label) {
                        $id = 'gender_' . $val;
                        $chk = ((string)$g === $val) ? ' checked' : '';
                        echo '<div class="radio-row"><input type="radio" name="gender" id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"' . $chk . ' required> ';
                        echo '<label for="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label></div>';
                    }
                    ?>
                </fieldset>

                <fieldset>
                    <legend>Interests (checkboxes)</legend>
                    <?php
                    $posted = isset($_POST['interests']) && is_array($_POST['interests']) ? $_POST['interests'] : [];
                    foreach (['web' => 'Web', 'db' => 'Database', 'mobile' => 'Mobile'] as $val => $label) {
                        $id = 'int_' . $val;
                        $chk = in_array($val, $posted, true) ? ' checked' : '';
                        echo '<div class="check-row"><input type="checkbox" name="interests[]" id="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . '"' . $chk . '> ';
                        echo '<label for="' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label></div>';
                    }
                    ?>
                </fieldset>

                <label for="comments">Comments (textarea)</label>
                <textarea id="comments" name="comments" maxlength="2000"><?php echo isset($_POST['comments']) ? htmlspecialchars((string)$_POST['comments'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">

                <label for="password2">Confirm password</label>
                <input type="password" id="password2" name="password2" required minlength="8" autocomplete="new-password">

                <button type="submit">Submit registration</button>
            </form>
        </div>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
