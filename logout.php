<?php
/**
 * logout.php — End the session and return to the login page.
 */

require 'db.php';
session_start();

// Record the logout in the WHO DID IT feed before destroying the session
if (isset($_SESSION['username'])) {
    $log = $pdo->prepare('INSERT INTO activity_log (username, action) VALUES (?, ?)');
    $log->execute([$_SESSION['username'], 'logged out']);
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
