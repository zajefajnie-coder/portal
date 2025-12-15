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
$userIdToFollow = (int)($input['userId'] ?? 0);

if ($userIdToFollow === getCurrentUserId()) {
    http_response_code(400);
    echo json_encode(['error' => 'Cannot follow yourself']);
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userIdToFollow]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    // Check if already following
    $stmt = $pdo->prepare("
        SELECT id 
        FROM user_follows 
        WHERE follower_id = ? AND followed_id = ? AND status = 'active'
    ");
    $stmt->execute([getCurrentUserId(), $userIdToFollow]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['error' => 'Already following this user']);
        exit;
    }
    
    // Insert follow relationship
    $stmt = $pdo->prepare("
        INSERT INTO user_follows (follower_id, followed_id, status, created_at) 
        VALUES (?, ?, 'active', NOW())
    ");
    $stmt->execute([getCurrentUserId(), $userIdToFollow]);
    
    // Create notification for the followed user
    createNotification($userIdToFollow, 'follow', 
        getCurrentUser()['username'] . ' started following you');
    
    // Update followed user's follower count notification would be handled by the notification system
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully followed user'
    ]);
    
} catch (Exception $e) {
    error_log("Follow user error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred']);
}