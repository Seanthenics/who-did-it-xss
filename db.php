<?php
/**
 * db.php — Database connection (PDO)
 * Works with XAMPP/MySQL. Create the database first using schema.sql.
 */

$DB_HOST = '127.0.0.1';
$DB_NAME = 'who_did_it';
$DB_USER = 'root';        // default XAMPP user
$DB_PASS = '';            // default XAMPP password is empty

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
