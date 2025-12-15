<?php
declare(strict_types=1);

/**
 * Get user by ID
 */
function getUserById(int $userId): ?array
{
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, email, role, avatar, bio, specialization, location, equipment, instagram, website, is_online, last_active, created_at FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}

/**
 * Get user by username
 */
function getUserByUsername(string $username): ?array
{
    $db = getDB();
    $stmt = $db->prepare("SELECT id, username, email, role, avatar, bio, specialization, location, equipment, instagram, website, is_online, last_active, created_at FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch() ?: null;
}

/**
 * Check if user is following another user
 */
function isFollowing(int $followerId, int $followedId): bool
{
    $db = getDB();
    $stmt = $db->prepare("SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = ? AND status = 'active'");
    $stmt->execute([$followerId, $followedId]);
    return $stmt->fetch() !== false;
}

/**
 * Get follow counts for a user
 */
function getFollowCounts(int $userId): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT 
            (SELECT COUNT(*) FROM followers WHERE follower_id = ? AND status = 'active') AS following_count,
            (SELECT COUNT(*) FROM followers WHERE followed_id = ? AND status = 'active') AS followers_count
    ");
    $stmt->execute([$userId, $userId]);
    return $stmt->fetch() ?: ['following_count' => 0, 'followers_count' => 0];
}

/**
 * Get user statistics
 */
function getUserStats(int $userId): array
{
    $db = getDB();
    
    $stmt = $db->prepare("
        SELECT 
            (SELECT COUNT(*) FROM sessions WHERE user_id = ? AND status = 'public') AS session_count,
            (SELECT COUNT(*) FROM images WHERE user_id = ?) AS image_count,
            (SELECT COALESCE(AVG(rating), 0) FROM image_ratings WHERE image_id IN (SELECT id FROM images WHERE user_id = ?)) AS avg_rating,
            (SELECT COUNT(*) FROM followers WHERE followed_id = ? AND status = 'active') AS followers_count
    ");
    $stmt->execute([$userId, $userId, $userId, $userId]);
    
    $stats = $stmt->fetch();
    $stats['avg_rating'] = round((float)$stats['avg_rating'], 2);
    
    return $stats;
}

/**
 * Get user's public sessions
 */
function getUserSessions(int $userId, int $limit = ITEMS_PER_PAGE, int $offset = 0): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT s.*, u.username, u.avatar,
               (SELECT COUNT(*) FROM session_likes WHERE session_id = s.id) AS likes_count,
               (SELECT COUNT(*) FROM session_comments WHERE session_id = s.id AND status = 'approved') AS comments_count
        FROM sessions s
        JOIN users u ON s.user_id = u.id
        WHERE s.user_id = ? AND s.status = 'public'
        ORDER BY s.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$userId, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get user's images
 */
function getUserImages(int $userId, int $limit = ITEMS_PER_PAGE, int $offset = 0): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, s.title as session_title,
               (SELECT COUNT(*) FROM image_likes WHERE image_id = i.id) AS likes_count
        FROM images i
        LEFT JOIN sessions s ON i.session_id = s.id
        WHERE i.user_id = ?
        ORDER BY i.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$userId, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get user's references
 */
function getUserReferences(int $userId): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT r.*, u.username, u.avatar, r.created_at as reference_date
        FROM references r
        JOIN users u ON r.from_user_id = u.id
        WHERE r.to_user_id = ? AND r.status = 'approved'
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get user's followers
 */
function getUserFollowers(int $userId, int $limit = ITEMS_PER_PAGE, int $offset = 0): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.id, u.username, u.avatar, u.specialization, u.location, u.last_active, 
               (SELECT COUNT(*) FROM sessions WHERE user_id = u.id AND status = 'public') AS session_count
        FROM followers f
        JOIN users u ON f.follower_id = u.id
        WHERE f.followed_id = ? AND f.status = 'active'
        ORDER BY f.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$userId, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Get users that user is following
 */
function getUserFollowing(int $userId, int $limit = ITEMS_PER_PAGE, int $offset = 0): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.id, u.username, u.avatar, u.specialization, u.location, u.last_active,
               (SELECT COUNT(*) FROM sessions WHERE user_id = u.id AND status = 'public') AS session_count
        FROM followers f
        JOIN users u ON f.followed_id = u.id
        WHERE f.follower_id = ? AND f.status = 'active'
        ORDER BY f.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$userId, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Update user's last activity
 */
function updateLastActivity(int $userId): void
{
    if ($userId <= 0) return;
    
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET last_active = NOW(), is_online = 1 WHERE id = ?");
    $stmt->execute([$userId]);
}

/**
 * Mark user as offline if inactive for more than 5 minutes
 */
function updateUserOnlineStatus(int $userId): void
{
    if ($userId <= 0) return;
    
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET is_online = 0 WHERE id = ? AND last_active < DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
    $stmt->execute([$userId]);
}

/**
 * Check if user has permission
 */
function userHasPermission(int $userId, string $permission): bool
{
    $db = getDB();
    
    // Check if user is admin
    $stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) return false;
    
    if ($user['role'] === 'admin') return true;
    
    // Check moderator permissions
    if ($user['role'] === 'moderator') {
        $stmt = $db->prepare("SELECT 1 FROM moderator_permissions WHERE user_id = ? AND permission = ?");
        $stmt->execute([$userId, $permission]);
        return $stmt->fetch() !== false;
    }
    
    return false;
}