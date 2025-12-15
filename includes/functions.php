<?php
declare(strict_types=1);

require_once 'config.php';

/**
 * Generate CSRF token
 */
function generateCSRFToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCSRFToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}

/**
 * Check if user is admin
 */
function isAdmin(): bool
{
    $user = getCurrentUser();
    return $user && $user['role'] === 'admin';
}

/**
 * Check if user is moderator
 */
function isModerator(): bool
{
    $user = getCurrentUser();
    return $user && ($user['role'] === 'admin' || $user['role'] === 'moderator');
}

/**
 * Sanitize output
 */
function sanitizeOutput(string $data): string
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate image file
 */
function validateImageFile(array $file): array
{
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Error uploading file.';
        return $errors;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File too large.';
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_TYPES)) {
        $errors[] = 'Invalid file type.';
    }
    
    return $errors;
}

/**
 * Generate random string
 */
function generateRandomString(int $length = 32): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Create notification
 */
function createNotification(int $userId, string $type, string $message, ?int $relatedId = null): void
{
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, type, message, related_id, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$userId, $type, $message, $relatedId]);
}

/**
 * Format date
 */
function formatDate(string $date): string
{
    $timestamp = strtotime($date);
    return date('M j, Y', $timestamp);
}

/**
 * Truncate text
 */
function truncateText(string $text, int $limit = 100): string
{
    if (strlen($text) <= $limit) {
        return $text;
    }
    
    return substr($text, 0, $limit) . '...';
}

/**
 * Get user avatar URL
 */
function getUserAvatar(int $userId, string $avatarPath = ''): string
{
    if (!empty($avatarPath) && file_exists(AVATAR_PATH . $avatarPath)) {
        return '/avatars/' . $avatarPath;
    }
    
    return '/assets/default-avatar.png';
}

/**
 * Check if user is following another user
 */
function isFollowing(int $followerId, int $followedId): bool
{
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM user_follows 
        WHERE follower_id = ? AND followed_id = ? AND status = 'active'
    ");
    $stmt->execute([$followerId, $followedId]);
    
    return $stmt->fetchColumn() > 0;
}

/**
 * Get follow counts
 */
function getFollowCounts(int $userId): array
{
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            (SELECT COUNT(*) FROM user_follows WHERE follower_id = ? AND status = 'active') AS following_count,
            (SELECT COUNT(*) FROM user_follows WHERE followed_id = ? AND status = 'active') AS followers_count
    ");
    $stmt->execute([$userId, $userId]);
    
    return $stmt->fetch();
}

/**
 * Update user's last active time
 */
function updateLastActive(int $userId): void
{
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE users 
        SET last_active = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
}