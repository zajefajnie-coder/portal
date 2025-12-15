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
$userIdToUnfollow = (int)($input['userId'] ?? 0);

if ($userIdToUnfollow === getCurrentUserId()) {
    http_response_code(400);
    echo json_encode(['error' => 'Cannot unfollow yourself']);
    exit;
}

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userIdToUnfollow]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        exit;
    }
    
    // Check if currently following
    $stmt = $pdo->prepare("
        SELECT id 
        FROM user_follows 
        WHERE follower_id = ? AND followed_id = ? AND status = 'active'
    ");
    $stmt->execute([getCurrentUserId(), $userIdToUnfollow]);
    $follow = $stmt->fetch();
    
    if (!$follow) {
        http_response_code(400);
        echo json_encode(['error' => 'Not following this user']);
        exit;
    }
    
    // Update follow status to inactive (soft delete)
    $stmt = $pdo->prepare("
        UPDATE user_follows 
        SET status = 'inactive' 
        WHERE id = ?
    ");
    $stmt->execute([$follow['id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Successfully unfollowed user'
    ]);
    
} catch (Exception $e) {
    error_log("Unfollow user error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred']);
}