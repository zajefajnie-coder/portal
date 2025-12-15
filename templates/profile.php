<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ?page=login');
    exit;
}

$profileUserId = (int)($_GET['id'] ?? getCurrentUserId());
$isOwnProfile = $profileUserId === getCurrentUserId();

// Get user data
$stmt = $pdo->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM sessions WHERE user_id = u.id AND status = 'public') as session_count,
           (SELECT COUNT(*) FROM session_images si JOIN sessions s ON si.session_id = s.id WHERE s.user_id = u.id AND s.status = 'public') as photo_count
    FROM users u 
    WHERE u.id = ?
");
$stmt->execute([$profileUserId]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}

// Get follow counts
$followCounts = getFollowCounts($profileUserId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitizeOutput($user['username']); ?>'s Profile - <?php echo SITE_NAME; ?></title>
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
        <!-- Profile Header -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?php echo getUserAvatar($user['id'], $user['avatar']); ?>" 
                     alt="<?php echo sanitizeOutput($user['username']); ?>'s avatar" 
                     class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                
                <h2><?php echo sanitizeOutput($user['username']); ?></h2>
                
                <?php if (!empty($user['specialization'])): ?>
                    <p class="text-muted"><?php echo sanitizeOutput($user['specialization']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($user['location'])): ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo sanitizeOutput($user['location']); ?></p>
                <?php endif; ?>
                
                <div class="row mt-3">
                    <div class="col-md-4">
                        <strong><?php echo $user['session_count']; ?></strong> Sessions
                    </div>
                    <div class="col-md-4">
                        <strong><?php echo $user['photo_count']; ?></strong> Photos
                    </div>
                    <div class="col-md-4">
                        <strong><?php echo $followCounts['followers_count']; ?></strong> Followers
                    </div>
                </div>
                
                <?php if (!$isOwnProfile): ?>
                    <div class="mt-3">
                        <?php if (isFollowing(getCurrentUserId(), $profileUserId)): ?>
                            <button class="btn btn-sm btn-outline-secondary" onclick="unfollow(<?php echo $profileUserId; ?>)">Unfollow</button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-primary" onclick="follow(<?php echo $profileUserId; ?>)">Follow</button>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-outline-primary ms-2" onclick="sendMessage(<?php echo $profileUserId; ?>)">Message</button>
                    </div>
                <?php endif; ?>
                
                <?php if ($isOwnProfile): ?>
                    <div class="mt-3">
                        <a href="?page=profile&action=edit" class="btn btn-primary">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Profile Tabs -->
        <ul class="nav nav-tabs" id="profileTabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#sessions">Sessions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#photos">Photos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#about">About</a>
            </li>
            <?php if ($isOwnProfile || isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#stats">Stats</a>
                </li>
            <?php endif; ?>
        </ul>
        
        <div class="tab-content">
            <!-- Sessions Tab -->
            <div class="tab-pane fade show active p-3" id="sessions">
                <div class="row">
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT s.* 
                        FROM sessions s 
                        WHERE s.user_id = ? AND s.status = 'public' 
                        ORDER BY s.created_at DESC
                    ");
                    $stmt->execute([$profileUserId]);
                    $sessions = $stmt->fetchAll();
                    
                    foreach ($sessions as $session):
                    ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <?php if (!empty($session['cover_image'])): ?>
                                    <img src="/sessions/<?php echo $session['cover_image']; ?>" 
                                         class="card-img-top" style="height: 200px; object-fit: cover;" 
                                         alt="<?php echo sanitizeOutput($session['title']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo sanitizeOutput($session['title']); ?></h5>
                                    <p class="card-text"><?php echo truncateText(sanitizeOutput($session['description']), 100); ?></p>
                                    <small class="text-muted"><?php echo formatDate($session['created_at']); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($sessions)): ?>
                        <p class="text-center text-muted">No sessions published yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Photos Tab -->
            <div class="tab-pane fade p-3" id="photos">
                <div class="row">
                    <?php
                    $stmt = $pdo->prepare("
                        SELECT si.filename, s.title as session_title
                        FROM session_images si
                        JOIN sessions s ON si.session_id = s.id
                        WHERE s.user_id = ? AND s.status = 'public'
                        ORDER BY si.position ASC
                        LIMIT 24
                    ");
                    $stmt->execute([$profileUserId]);
                    $images = $stmt->fetchAll();
                    
                    foreach ($images as $image):
                    ?>
                        <div class="col-md-3 mb-3">
                            <img src="/sessions/<?php echo $image['filename']; ?>" 
                                 class="img-fluid rounded" 
                                 style="height: 150px; object-fit: cover;"
                                 alt="<?php echo sanitizeOutput($image['session_title']); ?>">
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($images)): ?>
                        <p class="text-center text-muted">No photos published yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- About Tab -->
            <div class="tab-pane fade p-3" id="about">
                <?php if (!empty($user['bio'])): ?>
                    <h5>About Me</h5>
                    <p><?php echo nl2br(sanitizeOutput($user['bio'])); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($user['equipment'])): ?>
                    <h5>Equipment</h5>
                    <p><?php echo sanitizeOutput($user['equipment']); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($user['social_links'])): 
                    $socialLinks = json_decode($user['social_links'], true);
                    if ($socialLinks):
                ?>
                    <h5>Social Links</h5>
                    <div class="d-flex gap-2">
                        <?php foreach ($socialLinks as $platform => $url): ?>
                            <a href="<?php echo sanitizeOutput($url); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                <?php echo ucfirst($platform); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; endif; ?>
            </div>
            
            <!-- Stats Tab (for own profile or admin) -->
            <?php if ($isOwnProfile || isAdmin()): ?>
                <div class="tab-pane fade p-3" id="stats">
                    <h5>Statistics</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <td>Total Sessions:</td>
                                    <td><?php echo $user['session_count']; ?></td>
                                </tr>
                                <tr>
                                    <td>Total Photos:</td>
                                    <td><?php echo $user['photo_count']; ?></td>
                                </tr>
                                <tr>
                                    <td>Followers:</td>
                                    <td><?php echo $followCounts['followers_count']; ?></td>
                                </tr>
                                <tr>
                                    <td>Following:</td>
                                    <td><?php echo $followCounts['following_count']; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="/js/profile.js"></script>
</body>
</html>