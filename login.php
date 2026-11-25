<?php
/**
 * login.php — Log a user in.
 * Uses PDO prepared statements (SQL-injection safe) and password_verify().
 */

require 'db.php';
session_start();

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login OK — save the user in the session
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Record the login in the WHO DID IT feed
        $log = $pdo->prepare('INSERT INTO activity_log (username, action) VALUES (?, ?)');
        $log->execute([$user['username'], 'logged in']);

        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Who Did It</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="card">
    <h1>Log in</h1>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" autocomplete="off">
        <label>Username
            <input type="text" name="username" value="<?= e($_POST['username'] ?? '') ?>">
        </label>
        <label>Password
            <input type="password" name="password">
        </label>
        <button type="submit">Login</button>
    </form>

    <p class="muted">No account yet? <a href="register.php">Register</a></p>
</div>
</body>
</html>
