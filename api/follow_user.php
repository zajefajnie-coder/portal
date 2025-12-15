<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!$logged_in) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to follow users']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = (int)($input['user_id'] ?? 0);
$action = $input['action'] ?? '';

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

if (!in_array($action, ['follow', 'unfollow'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

$db = getDB();

try {
    if ($action === 'follow') {
        // Check if already following
        $stmt = $db->prepare("SELECT id FROM followers WHERE follower_id = ? AND followed_id = ?");
        $stmt->execute([$user_id, $userId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Already following this user']);
            exit;
        }
        
        // Insert follow record
        $stmt = $db->prepare("INSERT INTO followers (follower_id, followed_id, status) VALUES (?, ?, 'active')");
        $result = $stmt->execute([$user_id, $userId]);
        
        if ($result) {
            // Get updated follow count
            $stmt = $db->prepare("SELECT COUNT(*) FROM followers WHERE followed_id = ?");
            $stmt->execute([$userId]);
            $count = (int)$stmt->fetchColumn();
            
            // Create notification for the followed user
            $stmt = $db->prepare("
                INSERT INTO notifications (user_id, from_user_id, type, title, message, link)
                VALUES (?, ?, 'follow', 'New Follower', ?, ?)
            ");
            $stmt->execute([
                $userId, 
                $user_id, 
                $_SESSION['username'] . ' started following you', 
                "profile/?u=" . getUserById($user_id)['username']
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Successfully followed user',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to follow user']);
        }
    } else { // unfollow
        $stmt = $db->prepare("DELETE FROM followers WHERE follower_id = ? AND followed_id = ?");
        $result = $stmt->execute([$user_id, $userId]);
        
        if ($result) {
            // Get updated follow count
            $stmt = $db->prepare("SELECT COUNT(*) FROM followers WHERE followed_id = ?");
            $stmt->execute([$userId]);
            $count = (int)$stmt->fetchColumn();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Successfully unfollowed user',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to unfollow user']);
        }
    }
} catch (Exception $e) {
    error_log("Follow user error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}