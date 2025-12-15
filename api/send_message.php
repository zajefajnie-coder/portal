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
$recipientId = (int)($input['recipientId'] ?? 0);
$messageContent = trim($input['message'] ?? '');

if (empty($messageContent)) {
    http_response_code(400);
    echo json_encode(['error' => 'Message content is required']);
    exit;
}

if ($recipientId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Recipient ID is required']);
    exit;
}

if ($recipientId === getCurrentUserId()) {
    http_response_code(400);
    echo json_encode(['error' => 'Cannot send message to yourself']);
    exit;
}

try {
    // Check if recipient exists
    $stmt = $pdo->prepare("SELECT id, username, avatar FROM users WHERE id = ?");
    $stmt->execute([$recipientId]);
    $recipient = $stmt->fetch();
    
    if (!$recipient) {
        http_response_code(404);
        echo json_encode(['error' => 'Recipient not found']);
        exit;
    }
    
    // Insert the message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, recipient_id, content, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([getCurrentUserId(), $recipientId, $messageContent]);
    
    $messageId = $pdo->lastInsertId();
    
    // Get sender info
    $sender = getCurrentUser();
    
    // Create notification for recipient
    createNotification($recipientId, 'message', 
        $sender['username'] . ' sent you a message');
    
    // Return success response with message data
    echo json_encode([
        'success' => true,
        'message' => [
            'id' => $messageId,
            'content' => $messageContent,
            'sender_id' => getCurrentUserId(),
            'sender_username' => $sender['username'],
            'sender_avatar' => $sender['avatar'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'recipient_id' => $recipientId
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Send message error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while sending the message']);
}