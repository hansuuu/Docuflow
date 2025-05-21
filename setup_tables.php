<?php
// Direct database setup without Laravel
$host = '127.0.0.1';
$dbname = 'laravel';
$username = 'laravel';
$password = 'secret';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!\n";
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `docuflow_users` (
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
          UNIQUE KEY `docuflow_users_email_unique` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "Users table created!\n";
    
    // Create folders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `docuflow_folders` (
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
    
    // Create files table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `docuflow_files` (
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
    
    // Insert a test user
    $stmt = $pdo->prepare("
        INSERT INTO docuflow_users (name, email, password, total_storage, used_storage, used_storage_percentage, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmt->execute(['TestUser', 'test@example.com', password_hash('password123', PASSWORD_DEFAULT), 256, 0, 0]);
    $userId = $pdo->lastInsertId();
    echo "Test user created with ID: $userId\n";
    
    // Insert a root folder for the test user
    $stmt = $pdo->prepare("
        INSERT INTO docuflow_folders (name, user_id, parent_id, is_starred, is_trashed, created_at, updated_at)
        VALUES (?, ?, NULL, 0, 0, NOW(), NOW())
    ");
    $stmt->execute(['Root', $userId]);
    $folderId = $pdo->lastInsertId();
    echo "Root folder created with ID: $folderId\n";
    
    echo "All tables created and test data inserted successfully!\n";
    
} catch(PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}