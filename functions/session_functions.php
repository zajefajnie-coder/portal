<?php
declare(strict_types=1);

/**
 * Create a new session
 */
function createSession(int $userId, array $data): ?int
{
    $db = getDB();
    
    $stmt = $db->prepare("
        INSERT INTO sessions (user_id, title, description, location, session_date, cover_image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $userId,
        $data['title'],
        $data['description'] ?? '',
        $data['location'] ?? '',
        $data['session_date'] ?? date('Y-m-d'),
        $data['cover_image'] ?? null,
        $data['status'] ?? 'draft'
    ]);
    
    if ($result) {
        $sessionId = $db->lastInsertId();
        
        // Handle categories if provided
        if (isset($data['categories']) && is_array($data['categories'])) {
            assignSessionCategories($sessionId, $data['categories']);
        }
        
        return $sessionId;
    }
    
    return null;
}

/**
 * Update an existing session
 */
function updateSession(int $sessionId, int $userId, array $data): bool
{
    $db = getDB();
    
    $sql = "UPDATE sessions SET ";
    $params = [];
    $sets = [];
    
    foreach ($data as $field => $value) {
        if (in_array($field, ['title', 'description', 'location', 'session_date', 'cover_image', 'status'])) {
            $sets[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    if (empty($sets)) {
        return false;
    }
    
    $sql .= implode(', ', $sets);
    $sql .= " WHERE id = ? AND user_id = ?";
    $params[] = $sessionId;
    $params[] = $userId;
    
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Delete a session
 */
function deleteSession(int $sessionId, int $userId): bool
{
    $db = getDB();
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Delete related records first
        $stmt = $db->prepare("DELETE FROM images WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        $stmt = $db->prepare("DELETE FROM session_likes WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        $stmt = $db->prepare("DELETE FROM session_comments WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        $stmt = $db->prepare("DELETE FROM session_collaborators WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        // Remove from categories
        $stmt = $db->prepare("DELETE FROM session_categories WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        // Finally delete the session
        $stmt = $db->prepare("DELETE FROM sessions WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$sessionId, $userId]);
        
        if ($result) {
            $db->commit();
            return true;
        } else {
            $db->rollBack();
            return false;
        }
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error deleting session: " . $e->getMessage());
        return false;
    }
}

/**
 * Get session by ID
 */
function getSessionById(int $sessionId): ?array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT s.*, u.username, u.avatar,
               (SELECT COUNT(*) FROM session_likes WHERE session_id = s.id) AS likes_count,
               (SELECT COUNT(*) FROM session_comments WHERE session_id = s.id AND status = 'approved') AS comments_count
        FROM sessions s
        JOIN users u ON s.user_id = u.id
        WHERE s.id = ?
    ");
    $stmt->execute([$sessionId]);
    return $stmt->fetch() ?: null;
}

/**
 * Get all sessions with pagination and filtering
 */
function getAllSessions(string $status = 'public', int $limit = ITEMS_PER_PAGE, int $offset = 0, ?int $userId = null, ?array $categories = null): array
{
    $db = getDB();
    
    $sql = "
        SELECT s.*, u.username, u.avatar,
               (SELECT COUNT(*) FROM session_likes WHERE session_id = s.id) AS likes_count,
               (SELECT COUNT(*) FROM session_comments WHERE session_id = s.id AND status = 'approved') AS comments_count
        FROM sessions s
        JOIN users u ON s.user_id = u.id
        WHERE s.status = ?
    ";
    
    $params = [$status];
    
    if ($userId !== null) {
        $sql .= " AND s.user_id = ?";
        $params[] = $userId;
    }
    
    if ($categories !== null && !empty($categories)) {
        $placeholders = str_repeat('?,', count($categories) - 1) . '?';
        $sql .= " AND s.id IN (
            SELECT DISTINCT sc.session_id 
            FROM session_categories sc 
            WHERE sc.category_id IN ($placeholders)
        )";
        $params = array_merge($params, $categories);
    }
    
    $sql .= " ORDER BY s.created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Assign categories to a session
 */
function assignSessionCategories(int $sessionId, array $categoryIds): bool
{
    $db = getDB();
    
    try {
        // Remove existing categories
        $stmt = $db->prepare("DELETE FROM session_categories WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        
        // Add new categories
        if (!empty($categoryIds)) {
            $stmt = $db->prepare("INSERT INTO session_categories (session_id, category_id) VALUES (?, ?)");
            foreach ($categoryIds as $categoryId) {
                $stmt->execute([$sessionId, $categoryId]);
            }
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error assigning categories to session: " . $e->getMessage());
        return false;
    }
}

/**
 * Get categories for a session
 */
function getSessionCategories(int $sessionId): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT c.id, c.name
        FROM session_categories sc
        JOIN categories c ON sc.category_id = c.id
        WHERE sc.session_id = ?
    ");
    $stmt->execute([$sessionId]);
    return $stmt->fetchAll();
}

/**
 * Get session images
 */
function getSessionImages(int $sessionId, int $limit = 50, int $offset = 0): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT i.*, u.username,
               (SELECT COUNT(*) FROM image_likes WHERE image_id = i.id) AS likes_count,
               (SELECT AVG(rating) FROM image_ratings WHERE image_id = i.id) AS avg_rating
        FROM images i
        LEFT JOIN users u ON i.user_id = u.id
        WHERE i.session_id = ?
        ORDER BY i.sort_order ASC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$sessionId, $limit, $offset]);
    return $stmt->fetchAll();
}

/**
 * Toggle session like
 */
function toggleSessionLike(int $sessionId, int $userId): array
{
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id FROM session_likes WHERE session_id = ? AND user_id = ?");
    $stmt->execute([$sessionId, $userId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Unlike
        $stmt = $db->prepare("DELETE FROM session_likes WHERE session_id = ? AND user_id = ?");
        $stmt->execute([$sessionId, $userId]);
        $liked = false;
    } else {
        // Like
        $stmt = $db->prepare("INSERT INTO session_likes (session_id, user_id) VALUES (?, ?)");
        $stmt->execute([$sessionId, $userId]);
        $liked = true;
    }
    
    // Get updated count
    $stmt = $db->prepare("SELECT COUNT(*) FROM session_likes WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    $likesCount = (int)$stmt->fetchColumn();
    
    return ['liked' => $liked, 'count' => $likesCount];
}

/**
 * Add comment to session
 */
function addSessionComment(int $sessionId, int $userId, string $comment): bool
{
    $db = getDB();
    
    $stmt = $db->prepare("
        INSERT INTO session_comments (session_id, user_id, comment, status)
        VALUES (?, ?, ?, 'pending')
    ");
    
    return $stmt->execute([$sessionId, $userId, $comment]);
}

/**
 * Get session comments
 */
function getSessionComments(int $sessionId, string $status = 'approved'): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT sc.*, u.username, u.avatar
        FROM session_comments sc
        JOIN users u ON sc.user_id = u.id
        WHERE sc.session_id = ? AND sc.status = ?
        ORDER BY sc.created_at DESC
    ");
    $stmt->execute([$sessionId, $status]);
    return $stmt->fetchAll();
}

/**
 * Toggle casting favorite
 */
function toggleCastingFavorite(int $castingId, int $userId): bool
{
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id FROM casting_favorites WHERE casting_id = ? AND user_id = ?");
    $stmt->execute([$castingId, $userId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Remove favorite
        $stmt = $db->prepare("DELETE FROM casting_favorites WHERE casting_id = ? AND user_id = ?");
        $stmt->execute([$castingId, $userId]);
        return false;
    } else {
        // Add favorite
        $stmt = $db->prepare("INSERT INTO casting_favorites (casting_id, user_id) VALUES (?, ?)");
        $stmt->execute([$castingId, $userId]);
        return true;
    }
}

/**
 * Get user's favorite castings
 */
function getUserFavoriteCastings(int $userId): array
{
    $db = getDB();
    $stmt = $db->prepare("
        SELECT c.*
        FROM castings c
        JOIN casting_favorites cf ON c.id = cf.casting_id
        WHERE cf.user_id = ?
        ORDER BY cf.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}