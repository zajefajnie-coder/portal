<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

if (!$logged_in) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$recipientId = (int)($_POST['recipient_id'] ?? 0);
$messageText = trim($_POST['message'] ?? '');

if (!$recipientId || empty($messageText)) {
    header('Location: index.php');
    exit;
}

// Check if recipient exists
$recipient = getUserById($recipientId);
if (!$recipient) {
    header('Location: index.php');
    exit;
}

$db = getDB();

try {
    // Insert message
    $stmt = $db->prepare("
        INSERT INTO messages (sender_id, recipient_id, message, sent_at)
        VALUES (?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([$user_id, $recipientId, $messageText]);
    
    if ($result) {
        // Create notification for recipient
        $stmt = $db->prepare("
            INSERT INTO notifications (user_id, from_user_id, type, title, message, link)
            VALUES (?, ?, 'message', 'New Message', ?, ?)
        ");
        $stmt->execute([
            $recipientId,
            $user_id,
            $_SESSION['username'] . ' sent you a message',
            "messages/?user=$recipientId"
        ]);
        
        header("Location: index.php?user=$recipientId");
        exit;
    } else {
        header("Location: index.php?user=$recipientId&error=1");
        exit;
    }
} catch (Exception $e) {
    error_log("Send message error: " . $e->getMessage());
    header("Location: index.php?user=$recipientId&error=1");
    exit;
}