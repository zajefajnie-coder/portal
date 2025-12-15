<?php
declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
    'gc_maxlifetime' => SESSION_LIFETIME
]);

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

// Content Security Policy
$csp = "default-src 'self'; script-src 'self' 'unsafe-inline' https://www.google.com https://www.gstatic.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; object-src 'none'; frame-src https://www.google.com;";
header("Content-Security-Policy: {$csp}");

require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../functions/security_functions.php';
require_once __DIR__ . '/../functions/user_functions.php';
require_once __DIR__ . '/../functions/session_functions.php';
require_once __DIR__ . '/../functions/image_functions.php';

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$user_id = $logged_in ? (int)$_SESSION['user_id'] : 0;
$user_role = $logged_in ? $_SESSION['role'] ?? 'user' : 'guest';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_time'] + CSRF_TOKEN_LIFETIME) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Update last activity for online status
if ($logged_in) {
    updateLastActivity($user_id);
}

// Rate limiting helper
function checkRateLimit(string $key, int $maxRequests = 10, int $timeWindow = 300): bool 
{
    $cacheKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = ['count' => 0, 'time' => time()];
    }
    
    $limit = &$_SESSION[$cacheKey];
    
    if (time() - $limit['time'] > $timeWindow) {
        $limit = ['count' => 0, 'time' => time()];
    }
    
    if ($limit['count'] >= $maxRequests) {
        return false;
    }
    
    $limit['count']++;
    return true;
}