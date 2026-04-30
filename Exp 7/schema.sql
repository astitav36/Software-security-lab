CREATE DATABASE IF NOT EXISTS security_lab_hashing;
USE security_lab_hashing;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash CHAR(64) NOT NULL,
    salt CHAR(32) NULL,
    method ENUM('hash_only', 'hash_with_salt') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
