<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ?page=login');
    exit;
}

// Get all public sessions
$stmt = $pdo->prepare("
    SELECT s.*, u.username, u.avatar
    FROM sessions s
    JOIN users u ON s.user_id = u.id
    WHERE s.status = 'public'
    ORDER BY s.created_at DESC
");
$stmt->execute();
$sessions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions - <?php echo SITE_NAME; ?></title>
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
                        <a class="nav-link active" href="?page=sessions">Sessions</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?page=casting">Castings</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <?php echo sanitizeOutput(getCurrentUser()['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?page=profile">Profile</a></li>
                            <li><a class="dropdown-item" href="?page=messages">Messages</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Photo Sessions</h2>
            <a href="?page=sessions&action=create" class="btn btn-primary">Create Session</a>
        </div>
        
        <div class="row">
            <?php foreach ($sessions as $session): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 session-card">
                        <?php if (!empty($session['cover_image'])): ?>
                            <img src="/sessions/<?php echo $session['cover_image']; ?>" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;" 
                                 alt="<?php echo sanitizeOutput($session['title']); ?>">
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo sanitizeOutput($session['title']); ?></h5>
                            <p class="card-text flex-grow-1"><?php echo truncateText(sanitizeOutput($session['description']), 100); ?></p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        by <a href="?page=profile&id=<?php echo $session['user_id']; ?>">
                                            <?php echo sanitizeOutput($session['username']); ?>
                                        </a>
                                    </small>
                                    <small class="text-muted"><?php echo formatDate($session['created_at']); ?></small>
                                </div>
                                
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-outline-secondary me-2" 
                                            onclick="likeSession(<?php echo $session['id']; ?>)">
                                        <i class="fas fa-heart"></i> <?php echo $session['likes_count']; ?>
                                    </button>
                                    <a href="?page=sessions&action=view&id=<?php echo $session['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($sessions)): ?>
                <div class="col-12">
                    <p class="text-center text-muted">No sessions available yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="/js/sessions.js"></script>
</body>
</html>