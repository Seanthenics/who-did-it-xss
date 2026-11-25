<?php
/**
 * dashboard.php — The "WHO DID IT" page.
 *
 * Logged-in users can post a message/action. Every post is shown to everyone,
 * together with WHO did it. Because posts are stored and shown to all users,
 * this is a STORED (persistent) XSS injection point.
 *
 * THIS IS THE SECURED VERSION:
 *   The e() helper runs every stored value through htmlspecialchars() before
 *   it is printed, so an injected <script> is displayed as harmless text
 *   instead of being executed by the browser.
 *
 *   >>> To DEMONSTRATE the defacement attack for the activity, see the two
 *       commented lines marked "VULNERABLE VERSION" below. Temporarily swap
 *       them in, reload, take your screenshots, then switch back to secure. <<<
 */

require 'db.php';
session_start();

// Only logged-in users may view this page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Handle a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = trim($_POST['action']);
    if ($action !== '') {
        $stmt = $pdo->prepare('INSERT INTO activity_log (username, action) VALUES (?, ?)');
        $stmt->execute([$_SESSION['username'], $action]);
    }
    header('Location: dashboard.php');
    exit;
}

// Load the feed, newest first
$feed = $pdo->query('SELECT username, action, created_at FROM activity_log ORDER BY id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Who Did It — Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <span>Logged in as <strong><?= e($_SESSION['username']) ?></strong></span>
        <a class="btn" href="logout.php">Logout</a>
    </div>

    <h1>Who Did It</h1>

    <form method="post" class="post-form">
        <input type="text" name="action" placeholder="Leave a message / log an action..." required>
        <button type="submit">Post</button>
    </form>

    <table class="feed">
        <thead>
            <tr><th>Who</th><th>Did what</th><th>When</th></tr>
        </thead>
        <tbody>
        <?php foreach ($feed as $row): ?>
            <tr>
                
                <td><?= e($row['username']) ?></td>
                <td><?= e($row['action']) ?></td>

                
                <td><?php // echo $row['username']; ?></td>
                <td><?php // echo $row['action']; ?></td>
        
                <td><?= e($row['created_at']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
