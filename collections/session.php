<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

$sessionId = (int)($_GET['id'] ?? 0);
$session = getSessionById($sessionId);

if (!$session) {
    header('Location: sessions.php');
    exit;
}

// Get session images
$sessionImages = getSessionImages($sessionId);

// Check if user can edit this session
$canEdit = $logged_in && $session['user_id'] == $user_id;

// Get session categories
$sessionCategories = getSessionCategories($sessionId);

// Handle comment submission
if ($logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        addSessionComment($sessionId, $user_id, $comment);
        header("Location: session.php?id=$sessionId");
        exit;
    }
}

// Get session comments
$sessionComments = getSessionComments($sessionId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($session['title']) ?> - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <!-- Session Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h1><?= htmlspecialchars($session['title']) ?></h1>
                <p class="text-muted"><?= htmlspecialchars($session['description']) ?></p>
                
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <?php foreach ($sessionCategories as $category): ?>
                        <span class="badge bg-primary"><?= htmlspecialchars($category['name']) ?></span>
                    <?php endforeach; ?>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <small class="text-muted">by <a href="../profile/?u=<?= htmlspecialchars($session['username']) ?>"><?= htmlspecialchars($session['username']) ?></a></small>
                    </div>
                    <div>
                        <small class="text-muted"><?= date('M j, Y', strtotime($session['session_date'])) ?></small>
                    </div>
                    <div>
                        <small class="text-muted"><?= $session['location'] ?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button class="btn like-btn" 
                                    data-target-id="<?= $session['id'] ?>" 
                                    data-target-type="session">
                                <i class="bi bi-heart"></i>
                            </button>
                            <span class="likes-count"><?= $session['likes_count'] ?></span>
                        </div>
                        <div class="d-flex justify-content-around">
                            <div>
                                <strong><?= $session['likes_count'] ?></strong>
                                <div>Likes</div>
                            </div>
                            <div>
                                <strong><?= $session['comments_count'] ?></strong>
                                <div>Comments</div>
                            </div>
                            <div>
                                <strong><?= count($sessionImages) ?></strong>
                                <div>Images</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Session Images Gallery -->
        <div class="row mb-4">
            <div class="col-12">
                <h3>Gallery</h3>
                <div class="image-grid">
                    <?php foreach ($sessionImages as $image): ?>
                        <div class="image-item">
                            <img src="../uploads/thumb_<?= htmlspecialchars($image['filename']) ?>" 
                                 alt="<?= htmlspecialchars($image['title'] ?? 'Image') ?>">
                            <div class="image-overlay">
                                <div><?= htmlspecialchars($image['title'] ?? 'Untitled') ?></div>
                                <small><?= htmlspecialchars($image['description'] ?? '') ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h3>Comments (<?= count($sessionComments) ?>)</h3>
                
                <?php if ($logged_in): ?>
                    <form method="POST" class="mb-4">
                        <div class="mb-3">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Add a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">Login to add a comment</p>
                <?php endif; ?>
                
                <div class="comments-list">
                    <?php foreach ($sessionComments as $comment): ?>
                        <div class="comment-item">
                            <div class="d-flex align-items-start">
                                <img src="<?= htmlspecialchars($comment['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                     width="40" height="40" class="rounded-circle me-3" alt="Avatar">
                                <div>
                                    <h6 class="mb-0">
                                        <a href="../profile/?u=<?= htmlspecialchars($comment['username']) ?>">
                                            <?= htmlspecialchars($comment['username']) ?>
                                        </a>
                                    </h6>
                                    <p class="mb-1"><?= htmlspecialchars($comment['comment']) ?></p>
                                    <small class="text-muted"><?= date('M j, Y g:i A', strtotime($comment['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>