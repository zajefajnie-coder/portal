<?php
declare(strict_types=1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'stageone_db');
define('DB_USER', 'stageone_user');
define('DB_PASS', 'stageone_password');

// Site configuration
define('SITE_URL', 'http://localhost/stageone');
define('SITE_NAME', 'StageOne - Modeling Portal');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('AVATAR_PATH', __DIR__ . '/../avatars/');
define('SESSION_PATH', __DIR__ . '/../sessions/');

// Security
define('CSRF_TOKEN_LENGTH', 32);
define('RECAPTCHA_SITE_KEY', '');
define('RECAPTCHA_SECRET_KEY', '');

// File uploads
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');