<?php
declare(strict_types=1);

// Database Configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'stageone');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('SITE_URL', $_ENV['SITE_URL'] ?? 'http://localhost/stageone');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// Security Configuration
define('CSRF_TOKEN_LIFETIME', 3600); // 1 hour
define('SESSION_LIFETIME', 3600); // 1 hour
define('RECAPTCHA_SECRET_KEY', $_ENV['RECAPTCHA_SECRET_KEY'] ?? '');
define('RECAPTCHA_SITE_KEY', $_ENV['RECAPTCHA_SITE_KEY'] ?? '');

// Image processing
define('THUMBNAIL_WIDTH', 300);
define('THUMBNAIL_HEIGHT', 300);
define('COVER_WIDTH', 1200);
define('COVER_HEIGHT', 800);

// Pagination
define('ITEMS_PER_PAGE', 12);