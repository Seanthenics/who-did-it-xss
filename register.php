<?php
/**
 * register.php — Create a new account.
 *
 * SECURITY FEATURES IN THIS FILE:
 *  1. XSS prevention  -> every value printed back to the page is passed
 *                        through htmlspecialchars() (see the e() helper).
 *  2. Password policy -> minimum 8 characters, AND at least one uppercase,
 *                        one lowercase, and one number (validated with preg_match).
 *  3. Passwords are stored with password_hash(), never in plain text.
 *  4. SQL injection prevention -> PDO prepared statements.
 */

require 'db.php';
session_start();

// Helper: escape output so injected HTML/JS is shown as text, not executed.
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- Username checks ---
    if ($username === '') {
        $errors[] = 'Username is required.';
    }

    // --- PASSWORD SECURITY RULES (Part 4 of the activity) ---
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one UPPERCASE letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one number.';
    }

    // --- Make sure the username is not already taken ---
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'That username is already taken.';
        }
    }

    // --- Create the account ---
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
        $stmt->execute([$username, $hash]);
        $success = 'Account created! You can now log in.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Who Did It</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h1>Create an account</h1>

    <?php foreach ($errors as $err): ?>
        <p class="error"><?= e($err) ?></p>
    <?php endforeach; ?>

    <?php if ($success): ?>
        <p class="success"><?= e($success) ?></p>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <label>Username
            <!-- value is escaped with e() so a script in the username cannot run -->
            <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>">
        </label>
        <label>Password
            <input type="password" name="password">
        </label>
        <p class="hint">At least 8 characters, with an uppercase letter, a lowercase letter, and a number.</p>
        <button type="submit">Register</button>
    </form>

    <p class="muted">Already have an account? <a href="login.php">Log in</a></p>
</div>
</body>
</html>
