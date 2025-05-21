<?php
// Save as create_tables.php

// Basic connection
$host = '127.0.0.1';
$dbname = 'laravel';
$username = 'laravel';
$password = 'secret';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!\n";
    
    // Create users table without prefix (to match your code)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
          `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `email` varchar(255) NOT NULL,
          `email_verified_at` timestamp NULL DEFAULT NULL,
          `password` varchar(255) NOT NULL,
          `total_storage` float DEFAULT '256',
          `used_storage` float DEFAULT '0',
          `used_storage_percentage` float DEFAULT '0',
          `remember_token` varchar(100) DEFAULT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `users_email_unique` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Users table created!\n";
    
    // Create folders table without prefix
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `folders` (
          `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `user_id` bigint(20) UNSIGNED NOT NULL,
          `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
          `is_starred` tinyint(1) NOT NULL DEFAULT '0',
          `is_trashed` tinyint(1) NOT NULL DEFAULT '0',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Folders table created!\n";
    
    // Create files table without prefix
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `files` (
          `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `name` varchar(255) NOT NULL,
          `path` varchar(255) NOT NULL,
          `mime_type` varchar(255) DEFAULT NULL,
          `size` bigint(20) NOT NULL DEFAULT '0',
          `user_id` bigint(20) UNSIGNED NOT NULL,
          `folder_id` bigint(20) UNSIGNED DEFAULT NULL,
          `is_starred` tinyint(1) NOT NULL DEFAULT '0',
          `is_trashed` tinyint(1) NOT NULL DEFAULT '0',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Files table created!\n";
    
    echo "All tables created successfully!\n";
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}