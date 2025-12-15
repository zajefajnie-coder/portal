<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

if (!$logged_in) {
    header('Location: ../login.php');
    exit;
}

// Get conversations
$db = getDB();
$stmt = $db->prepare("
    SELECT DISTINCT 
        u.id, u.username, u.avatar, u.is_online,
        m.sent_at, m.message,
        (SELECT COUNT(*) FROM messages m2 WHERE 
            ((m2.sender_id = u.id AND m2.recipient_id = ?) OR 
             (m2.sender_id = ? AND m2.recipient_id = u.id)) 
            AND m2.is_read = 0 AND m2.sender_id != ?) as unread_count
    FROM messages m
    JOIN users u ON (
        (m.sender_id = u.id AND m.recipient_id = ?) OR 
        (m.recipient_id = u.id AND m.sender_id = ?)
    )
    WHERE (m.sender_id = ? OR m.recipient_id = ?)
    ORDER BY m.sent_at DESC
");
$stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
$conversations = $stmt->fetchAll();

// Get specific conversation if user_id is provided
$otherUserId = (int)($_GET['user'] ?? 0);
$otherUser = null;
$messages = [];

if ($otherUserId) {
    $otherUser = getUserById($otherUserId);
    
    if ($otherUser) {
        // Mark messages as read
        $stmt = $db->prepare("
            UPDATE messages 
            SET is_read = 1, read_at = NOW() 
            WHERE sender_id = ? AND recipient_id = ? AND is_read = 0
        ");
        $stmt->execute([$otherUserId, $user_id]);
        
        // Get messages between users
        $stmt = $db->prepare("
            SELECT m.*, 
                   u1.username as sender_username, u1.avatar as sender_avatar,
                   u2.username as recipient_username, u2.avatar as recipient_avatar
            FROM messages m
            JOIN users u1 ON m.sender_id = u1.id
            JOIN users u2 ON m.recipient_id = u2.id
            WHERE (m.sender_id = ? AND m.recipient_id = ?) OR (m.sender_id = ? AND m.recipient_id = ?)
            ORDER BY m.sent_at ASC
        ");
        $stmt->execute([$user_id, $otherUserId, $otherUserId, $user_id]);
        $messages = $stmt->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .chat-container {
            display: flex;
            height: 70vh;
        }
        .conversation-list {
            width: 300px;
            border-right: 1px solid #dee2e6;
            overflow-y: auto;
        }
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        .message-bubble {
            max-width: 70%;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 1rem;
        }
        .message-sent {
            align-self: flex-end;
            background-color: #0d6efd;
            color: white;
            border-bottom-right-radius: 0;
        }
        .message-received {
            align-self: flex-start;
            background-color: #e9ecef;
            color: #212529;
            border-bottom-left-radius: 0;
        }
        .message-form {
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <h1>Messages</h1>
        
        <div class="chat-container">
            <!-- Conversation List -->
            <div class="conversation-list">
                <div class="list-group list-group-flush">
                    <?php foreach ($conversations as $conv): ?>
                        <a href="?user=<?= $conv['id'] ?>" 
                           class="list-group-item list-group-item-action <?= $otherUserId == $conv['id'] ? 'active' : '' ?>">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($conv['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                     width="40" height="40" class="rounded-circle me-3" alt="Avatar">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($conv['username']) ?></strong>
                                        <?php if ($conv['unread_count'] > 0): ?>
                                            <span class="badge bg-danger"><?= $conv['unread_count'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <span class="status-indicator <?= $conv['is_online'] ? 'status-online' : 'status-offline' ?>"></span>
                                        <?= $conv['is_online'] ? 'Online' : 'Offline' ?>
                                    </small>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="chat-area">
                <?php if ($otherUser): ?>
                    <!-- Chat Header -->
                    <div class="border-bottom p-3">
                        <div class="d-flex align-items-center">
                            <img src="<?= htmlspecialchars($otherUser['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                 width="40" height="40" class="rounded-circle me-3" alt="Avatar">
                            <div>
                                <h5 class="mb-0"><?= htmlspecialchars($otherUser['username']) ?></h5>
                                <small class="text-muted">
                                    <span class="status-indicator <?= $otherUser['is_online'] ? 'status-online' : 'status-offline' ?>"></span>
                                    <?= $otherUser['is_online'] ? 'Online' : 'Offline' ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages -->
                    <div class="messages-container" id="messagesContainer">
                        <?php foreach ($messages as $msg): ?>
                            <div class="d-flex <?= $msg['sender_id'] == $user_id ? 'justify-content-end' : 'justify-content-start' ?>">
                                <div class="message-bubble <?= $msg['sender_id'] == $user_id ? 'message-sent' : 'message-received' ?>">
                                    <?php if ($msg['is_encrypted']): ?>
                                        <small class="text-muted"><i class="bi bi-lock"></i> Encrypted</small><br>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($msg['message']) ?>
                                    <div class="text-end">
                                        <small class="text-muted"><?= date('g:i A', strtotime($msg['sent_at'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Message Form -->
                    <div class="message-form">
                        <form id="messageForm" method="POST" action="send.php">
                            <input type="hidden" name="recipient_id" value="<?= $otherUserId ?>">
                            <div class="input-group">
                                <input type="text" class="form-control" name="message" placeholder="Type a message..." required>
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center h-100">
                        <div class="text-center">
                            <i class="bi bi-chat-dots fs-1 text-muted"></i>
                            <p class="text-muted">Select a conversation to start messaging</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-scroll to bottom of messages
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
            
            // Auto-refresh messages if in a conversation
            <?php if ($otherUserId): ?>
            setInterval(function() {
                // In a real app, you would fetch new messages via AJAX
                // This is just a placeholder
            }, 5000);
            <?php endif; ?>
        });
    </script>
</body>
</html>