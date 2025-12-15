<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!$logged_in) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to favorite castings']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$castingId = (int)($input['casting_id'] ?? 0);
$action = $input['action'] ?? '';

if (!$castingId || !in_array($action, ['add', 'remove'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$db = getDB();

try {
    if ($action === 'add') {
        // Check if already favorited
        $stmt = $db->prepare("SELECT id FROM casting_favorites WHERE casting_id = ? AND user_id = ?");
        $stmt->execute([$castingId, $user_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Already favorited this casting']);
            exit;
        }
        
        // Add to favorites
        $stmt = $db->prepare("INSERT INTO casting_favorites (casting_id, user_id) VALUES (?, ?)");
        $result = $stmt->execute([$castingId, $user_id]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Casting added to favorites'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to favorite casting']);
        }
    } else { // remove
        $stmt = $db->prepare("DELETE FROM casting_favorites WHERE casting_id = ? AND user_id = ?");
        $result = $stmt->execute([$castingId, $user_id]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Casting removed from favorites'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove from favorites']);
        }
    }
} catch (Exception $e) {
    error_log("Toggle casting favorite error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}