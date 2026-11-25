-- schema.sql — Run this once in phpMyAdmin (XAMPP) or MySQL to set up the database.

CREATE DATABASE IF NOT EXISTS who_did_it
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE who_did_it;

-- Users table: stores accounts for registration/login
CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,          -- stores a password_hash(), never plain text
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity log table: this is the "WHO DID IT" feed.
-- Every post is shown to everyone, so it is the stored-XSS injection point.
CREATE TABLE IF NOT EXISTS activity_log (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(50)  NOT NULL,
    action     TEXT         NOT NULL,          -- the message the user left
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
