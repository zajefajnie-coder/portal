<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!$logged_in) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to rate images']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$imageId = (int)($input['image_id'] ?? 0);
$rating = (int)($input['rating'] ?? 0);

if (!$imageId || $rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

$db = getDB();

try {
    // Check if user owns the image to prevent rating own images
    $stmt = $db->prepare("SELECT user_id FROM images WHERE id = ?");
    $stmt->execute([$imageId]);
    $image = $stmt->fetch();
    
    if (!$image) {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
        exit;
    }
    
    if ($image['user_id'] == $user_id) {
        echo json_encode(['success' => false, 'message' => 'You cannot rate your own image']);
        exit;
    }
    
    // Check if already rated
    $stmt = $db->prepare("SELECT id FROM image_ratings WHERE image_id = ? AND user_id = ?");
    $stmt->execute([$imageId, $user_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing rating
        $stmt = $db->prepare("UPDATE image_ratings SET rating = ?, updated_at = NOW() WHERE image_id = ? AND user_id = ?");
        $result = $stmt->execute([$rating, $imageId, $user_id]);
    } else {
        // Insert new rating
        $stmt = $db->prepare("INSERT INTO image_ratings (image_id, user_id, rating) VALUES (?, ?, ?)");
        $result = $stmt->execute([$imageId, $user_id, $rating]);
    }
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Rating saved successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save rating']);
    }
} catch (Exception $e) {
    error_log("Rate image error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}