<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ?page=login');
    exit;
}

$currentUser = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">StageOne</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=sessions">Sessions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=casting">Castings</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo sanitizeOutput($currentUser['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?page=profile">Profile</a></li>
                            <li><a class="dropdown-item active" href="?page=messages">Messages</a></li>
                            <li><a class="dropdown-item" href="?page=notifications">Notifications</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/api/logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="container mt-4">
        <div class="row">
            <!-- Contacts sidebar -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Conversations</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php
                            // Get recent conversations
                            $stmt = $pdo->prepare("
                                SELECT DISTINCT u.id, u.username, u.avatar, 
                                       m.created_at, m.content,
                                       (SELECT COUNT(*) FROM messages m2 WHERE 
                                           (m2.sender_id = u.id AND m2.recipient_id = ?) OR
                                           (m2.sender_id = ? AND m2.recipient_id = u.id)
                                       AND m2.is_read = 0 AND m2.sender_id != ?) as unread_count
                                FROM messages m
                                JOIN users u ON (
                                    (m.sender_id = u.id AND m.recipient_id = ?) OR 
                                    (m.recipient_id = u.id AND m.sender_id = ?)
                                )
                                WHERE (m.sender_id = ? OR m.recipient_id = ?)
                                GROUP BY u.id
                                ORDER BY m.created_at DESC
                            ");
                            $userId = getCurrentUserId();
                            $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId]);
                            $contacts = $stmt->fetchAll();
                            
                            foreach ($contacts as $contact):
                            ?>
                                <a href="?page=messages&contact=<?php echo $contact['id']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo isset($_GET['contact']) && $_GET['contact'] == $contact['id'] ? 'active' : ''; ?>">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo getUserAvatar($contact['id'], $contact['avatar']); ?>" 
                                             class="avatar-sm me-3" alt="<?php echo sanitizeOutput($contact['username']); ?>">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <strong><?php echo sanitizeOutput($contact['username']); ?></strong>
                                                <small><?php echo formatDate($contact['created_at']); ?></small>
                                            </div>
                                            <div class="text-truncate" style="max-width: 180px;">
                                                <?php echo truncateText(sanitizeOutput($contact['content']), 50); ?>
                                            </div>
                                        </div>
                                        <?php if ($contact['unread_count'] > 0): ?>
                                            <span class="badge bg-danger rounded-pill"><?php echo $contact['unread_count']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                            
                            <?php if (empty($contacts)): ?>
                                <div class="list-group-item text-center text-muted">
                                    No conversations yet
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Message area -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <?php if (isset($_GET['contact'])): 
                            $contactId = (int)$_GET['contact'];
                            $stmt = $pdo->prepare("SELECT username, avatar FROM users WHERE id = ?");
                            $stmt->execute([$contactId]);
                            $contact = $stmt->fetch();
                        ?>
                            <h5><?php echo sanitizeOutput($contact['username']); ?></h5>
                        <?php else: ?>
                            <h5>Select a conversation</h5>
                        <?php endif; ?>
                    </div>
                    <div class="card-body" style="height: 400px; overflow-y: auto;">
                        <?php if (isset($_GET['contact'])): ?>
                            <div id="messages-container">
                                <?php
                                $stmt = $pdo->prepare("
                                    SELECT m.*, u.username, u.avatar
                                    FROM messages m
                                    JOIN users u ON m.sender_id = u.id
                                    WHERE (m.sender_id = ? AND m.recipient_id = ?) OR 
                                          (m.sender_id = ? AND m.recipient_id = ?)
                                    ORDER BY m.created_at ASC
                                ");
                                $stmt->execute([$contactId, $userId, $userId, $contactId]);
                                $messages = $stmt->fetchAll();
                                
                                foreach ($messages as $message):
                                ?>
                                    <div class="d-flex mb-3 <?php echo $message['sender_id'] == $userId ? 'justify-content-end' : 'justify-content-start'; ?>">
                                        <?php if ($message['sender_id'] != $userId): ?>
                                            <img src="<?php echo getUserAvatar($message['sender_id'], $message['avatar']); ?>" 
                                                 class="avatar-sm me-2" alt="<?php echo sanitizeOutput($message['username']); ?>">
                                        <?php endif; ?>
                                        
                                        <div class="message-bubble p-3 rounded <?php echo $message['sender_id'] == $userId ? 'bg-primary text-white' : 'bg-light'; ?>" 
                                             style="max-width: 70%;">
                                            <div><?php echo nl2br(sanitizeOutput($message['content'])); ?></div>
                                            <small class="d-block mt-1 opacity-75">
                                                <?php echo date('H:i', strtotime($message['created_at'])); ?>
                                            </small>
                                        </div>
                                        
                                        <?php if ($message['sender_id'] == $userId): ?>
                                            <img src="<?php echo getUserAvatar($message['sender_id'], $currentUser['avatar']); ?>" 
                                                 class="avatar-sm ms-2" alt="<?php echo sanitizeOutput($currentUser['username']); ?>">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <p>Select a conversation to start messaging</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($_GET['contact'])): ?>
                        <div class="card-footer">
                            <form id="message-form">
                                <div class="input-group">
                                    <input type="hidden" id="recipient-id" value="<?php echo $contactId; ?>">
                                    <input type="text" class="form-control" id="message-input" placeholder="Type your message..." required>
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/messages.js"></script>
</body>
</html>