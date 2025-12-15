<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!$logged_in) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to like content']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$targetId = (int)($input['target_id'] ?? 0);
$targetType = $input['target_type'] ?? '';
$action = $input['action'] ?? '';

if (!$targetId || !in_array($targetType, ['session', 'image']) || !in_array($action, ['like', 'unlike'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$db = getDB();

try {
    $table = $targetType === 'session' ? 'session_likes' : 'image_likes';
    $idColumn = $targetType . '_id';
    
    if ($action === 'like') {
        // Check if already liked
        $stmt = $db->prepare("SELECT id FROM $table WHERE $idColumn = ? AND user_id = ?");
        $stmt->execute([$targetId, $user_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Already liked this content']);
            exit;
        }
        
        // Insert like
        $stmt = $db->prepare("INSERT INTO $table ($idColumn, user_id) VALUES (?, ?)");
        $result = $stmt->execute([$targetId, $user_id]);
        
        if ($result) {
            // Get updated count
            $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE $idColumn = ?");
            $stmt->execute([$targetId]);
            $count = (int)$stmt->fetchColumn();
            
            // Create notification if it's a session and not liking your own
            if ($targetType === 'session') {
                $stmt = $db->prepare("SELECT user_id FROM sessions WHERE id = ?");
                $stmt->execute([$targetId]);
                $sessionOwnerId = (int)$stmt->fetchColumn();
                
                if ($sessionOwnerId !== $user_id) {
                    $stmt = $db->prepare("
                        INSERT INTO notifications (user_id, from_user_id, type, title, message, link)
                        VALUES (?, ?, 'like', 'New Like', ?, ?)
                    ");
                    $stmt->execute([
                        $sessionOwnerId,
                        $user_id,
                        $_SESSION['username'] . ' liked your session',
                        "collections/session.php?id=$targetId"
                    ]);
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Successfully liked',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to like content']);
        }
    } else { // unlike
        $stmt = $db->prepare("DELETE FROM $table WHERE $idColumn = ? AND user_id = ?");
        $result = $stmt->execute([$targetId, $user_id]);
        
        if ($result) {
            // Get updated count
            $stmt = $db->prepare("SELECT COUNT(*) FROM $table WHERE $idColumn = ?");
            $stmt->execute([$targetId]);
            $count = (int)$stmt->fetchColumn();
            
            echo json_encode([
                'success' => true,
                'message' => 'Successfully unliked',
                'count' => $count
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to unlike content']);
        }
    }
} catch (Exception $e) {
    error_log("Toggle like error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}