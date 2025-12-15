<?php
declare(strict_types=1);

require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$sessionId = (int)($input['sessionId'] ?? 0);

try {
    // Check if session exists and is public
    $stmt = $pdo->prepare("
        SELECT s.id, s.user_id, u.username
        FROM sessions s
        JOIN users u ON s.user_id = u.id
        WHERE s.id = ? AND s.status = 'public'
    ");
    $stmt->execute([$sessionId]);
    $session = $stmt->fetch();
    
    if (!$session) {
        http_response_code(404);
        echo json_encode(['error' => 'Session not found or not public']);
        exit;
    }
    
    $userId = getCurrentUserId();
    
    // Check if already liked
    $stmt = $pdo->prepare("
        SELECT id 
        FROM likes 
        WHERE user_id = ? AND target_type = 'session' AND target_id = ?
    ");
    $stmt->execute([$userId, $sessionId]);
    $existingLike = $stmt->fetch();
    
    if ($existingLike) {
        // Unlike (remove like)
        $stmt = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $stmt->execute([$existingLike['id']]);
        
        // Update likes count
        $stmt = $pdo->prepare("
            UPDATE sessions 
            SET likes_count = GREATEST(0, likes_count - 1) 
            WHERE id = ?
        ");
        $stmt->execute([$sessionId]);
        
        // Get updated count
        $stmt = $pdo->prepare("SELECT likes_count FROM sessions WHERE id = ?");
        $stmt->execute([$sessionId]);
        $newCount = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'message' => 'Session unliked',
            'newCount' => $newCount,
            'liked' => false
        ]);
    } else {
        // Like the session
        $stmt = $pdo->prepare("
            INSERT INTO likes (user_id, target_type, target_id, created_at) 
            VALUES (?, 'session', ?, NOW())
        ");
        $stmt->execute([$userId, $sessionId]);
        
        // Update likes count
        $stmt = $pdo->prepare("
            UPDATE sessions 
            SET likes_count = likes_count + 1 
            WHERE id = ?
        ");
        $stmt->execute([$sessionId]);
        
        // Create notification for session owner (if not self-liking)
        if ($session['user_id'] !== $userId) {
            createNotification(
                $session['user_id'], 
                'like', 
                getCurrentUser()['username'] . ' liked your session: ' . $session['username']
            );
        }
        
        // Get updated count
        $stmt = $pdo->prepare("SELECT likes_count FROM sessions WHERE id = ?");
        $stmt->execute([$sessionId]);
        $newCount = $stmt->fetchColumn();
        
        echo json_encode([
            'success' => true,
            'message' => 'Session liked',
            'newCount' => $newCount,
            'liked' => true
        ]);
    }
    
} catch (Exception $e) {
    error_log("Like session error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred']);
}