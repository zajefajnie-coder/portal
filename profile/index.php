<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

$username = $_GET['u'] ?? $_SESSION['username'] ?? '';
$user = getUserByUsername($username);

if (!$user) {
    header('Location: ../index.php');
    exit;
}

$isOwnProfile = $logged_in && $user['id'] == $user_id;
$followCounts = getFollowCounts($user['id']);
$userStats = getUserStats($user['id']);

// Get user's sessions
$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * ITEMS_PER_PAGE;

$tabs = ['sessions', 'images', 'about', 'followers', 'following', 'references'];
$activeTab = in_array($_GET['tab'] ?? 'sessions', $tabs) ? $_GET['tab'] : 'sessions';

switch ($activeTab) {
    case 'sessions':
        $userSessions = getUserSessions($user['id'], ITEMS_PER_PAGE, $offset);
        break;
    case 'images':
        $userImages = getUserImages($user['id'], ITEMS_PER_PAGE, $offset);
        break;
    case 'followers':
        $userFollowers = getUserFollowers($user['id'], ITEMS_PER_PAGE, $offset);
        break;
    case 'following':
        $userFollowing = getUserFollowing($user['id'], ITEMS_PER_PAGE, $offset);
        break;
    case 'references':
        $userReferences = getUserReferences($user['id']);
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username']) ?>'s Profile - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <!-- Profile Header -->
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                <img src="<?= htmlspecialchars($user['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                     class="rounded-circle mb-3" width="150" height="150" alt="Avatar">
                <h4><?= htmlspecialchars($user['username']) ?></h4>
                <div class="d-flex justify-content-center align-items-center">
                    <span class="status-indicator <?= $user['is_online'] ? 'status-online' : 'status-offline' ?>"></span>
                    <small><?= $user['is_online'] ? 'Online' : 'Offline' ?></small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2><?= htmlspecialchars($user['username']) ?></h2>
                        <p class="text-muted"><?= htmlspecialchars($user['specialization'] ?? 'Model') ?> • <?= htmlspecialchars($user['location'] ?? 'Earth') ?></p>
                        <?php if ($user['bio']): ?>
                            <p><?= htmlspecialchars($user['bio']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php if ($logged_in && !$isOwnProfile): ?>
                        <button class="btn <?= isFollowing($user_id, $user['id']) ? 'btn-secondary follow-btn following' : 'btn-outline-primary follow-btn' ?>" 
                                data-user-id="<?= $user['id'] ?>" 
                                data-follow-count="<?= $user['id'] ?>">
                            <?= isFollowing($user_id, $user['id']) ? 'Following' : 'Follow' ?>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="row text-center mt-3">
                    <div class="col">
                        <strong><?= $userStats['session_count'] ?></strong>
                        <div>Sessions</div>
                    </div>
                    <div class="col">
                        <strong><?= $userStats['image_count'] ?></strong>
                        <div>Images</div>
                    </div>
                    <div class="col">
                        <strong><?= $followCounts['followers_count'] ?></strong>
                        <div>Followers</div>
                    </div>
                    <div class="col">
                        <strong><?= $followCounts['following_count'] ?></strong>
                        <div>Following</div>
                    </div>
                    <div class="col">
                        <strong><?= $userStats['avg_rating'] ?></strong>
                        <div>Avg Rating</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <?php if ($user['instagram']): ?>
                    <a href="https://instagram.com/<?= htmlspecialchars($user['instagram']) ?>" target="_blank" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-instagram"></i> Instagram
                    </a>
                <?php endif; ?>
                <?php if ($user['website']): ?>
                    <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank" class="btn btn-outline-primary w-100">
                        <i class="bi bi-globe"></i> Website
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Profile Tabs -->
        <ul class="nav nav-tabs profile-tabs">
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'sessions' ? 'active' : '' ?>" 
                   href="?u=<?= htmlspecialchars($username) ?>&tab=sessions">Sessions</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'images' ? 'active' : '' ?>" 
                   href="?u=<?= htmlspecialchars($username) ?>&tab=images">Images</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'about' ? 'active' : '' ?>" 
                   href="?u=<?= htmlspecialchars($username) ?>&tab=about">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'followers' ? 'active' : '' ?>" 
                   href="?u=<?= htmlspecialchars($username) ?>&tab=followers">Followers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'following' ? 'active' : '' ?>" 
                   href="?u=<?= htmlspecialchars($username) ?>&tab=following">Following</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $activeTab === 'references' ? 'active' : '' ?>" 
                   href="?u=<?= htmlspecialchars($username) ?>&tab=references">References</a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content mt-4">
            <!-- Sessions Tab -->
            <?php if ($activeTab === 'sessions'): ?>
                <div class="tab-pane active">
                    <div class="row g-4">
                        <?php foreach ($userSessions as $session): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card session-card">
                                    <?php if ($session['cover_image']): ?>
                                        <img src="../uploads/cover_<?= htmlspecialchars($session['cover_image']) ?>" 
                                             class="card-img-top" alt="<?= htmlspecialchars($session['title']) ?>" 
                                             style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bi bi-image fs-1 text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($session['title']) ?></h5>
                                        <p class="card-text text-muted"><?= htmlspecialchars(substr($session['description'], 0, 100)) ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><?= date('M j, Y', strtotime($session['created_at'])) ?></small>
                                            <div>
                                                <span class="text-muted me-3">
                                                    <i class="bi bi-heart"></i> <?= $session['likes_count'] ?>
                                                </span>
                                                <span class="text-muted">
                                                    <i class="bi bi-chat"></i> <?= $session['comments_count'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (empty($userSessions)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-image fs-1 text-muted"></i>
                            <p class="text-muted">No sessions yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Images Tab -->
            <?php if ($activeTab === 'images'): ?>
                <div class="tab-pane active">
                    <div class="image-grid">
                        <?php foreach ($userImages as $image): ?>
                            <div class="image-item">
                                <img src="../uploads/thumb_<?= htmlspecialchars($image['filename']) ?>" 
                                     alt="<?= htmlspecialchars($image['title'] ?? 'Image') ?>">
                                <div class="image-overlay">
                                    <div><?= htmlspecialchars($image['title'] ?? 'Untitled') ?></div>
                                    <small><?= htmlspecialchars($image['session_title'] ?? 'No Session') ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (empty($userImages)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-image fs-1 text-muted"></i>
                            <p class="text-muted">No images yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- About Tab -->
            <?php if ($activeTab === 'about'): ?>
                <div class="tab-pane active">
                    <div class="row">
                        <div class="col-md-8">
                            <?php if ($user['bio']): ?>
                                <h5>Bio</h5>
                                <p><?= htmlspecialchars($user['bio']) ?></p>
                            <?php endif; ?>
                            
                            <?php if ($user['equipment']): ?>
                                <h5>Equipment</h5>
                                <p><?= htmlspecialchars($user['equipment']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <h5>Details</h5>
                            <ul class="list-unstyled">
                                <?php if ($user['specialization']): ?>
                                    <li><strong>Specialization:</strong> <?= htmlspecialchars($user['specialization']) ?></li>
                                <?php endif; ?>
                                <?php if ($user['location']): ?>
                                    <li><strong>Location:</strong> <?= htmlspecialchars($user['location']) ?></li>
                                <?php endif; ?>
                                <li><strong>Member since:</strong> <?= date('M Y', strtotime($user['created_at'])) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Followers Tab -->
            <?php if ($activeTab === 'followers'): ?>
                <div class="tab-pane active">
                    <div class="row g-3">
                        <?php foreach ($userFollowers as $follower): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($follower['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                                 width="50" height="50" class="rounded-circle me-3" alt="Avatar">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($follower['username']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($follower['specialization'] ?? 'Model') ?></small>
                                                <div class="d-flex align-items-center">
                                                    <span class="status-indicator <?= $follower['is_online'] ? 'status-online' : 'status-offline' ?>"></span>
                                                    <small><?= $follower['is_online'] ? 'Online' : 'Offline' ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (empty($userFollowers)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">No followers yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- Following Tab -->
            <?php if ($activeTab === 'following'): ?>
                <div class="tab-pane active">
                    <div class="row g-3">
                        <?php foreach ($userFollowing as $followed): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= htmlspecialchars($followed['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                                 width="50" height="50" class="rounded-circle me-3" alt="Avatar">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($followed['username']) ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($followed['specialization'] ?? 'Model') ?></small>
                                                <div class="d-flex align-items-center">
                                                    <span class="status-indicator <?= $followed['is_online'] ? 'status-online' : 'status-offline' ?>"></span>
                                                    <small><?= $followed['is_online'] ? 'Online' : 'Offline' ?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (empty($userFollowing)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">Not following anyone yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <!-- References Tab -->
            <?php if ($activeTab === 'references'): ?>
                <div class="tab-pane active">
                    <?php foreach ($userReferences as $reference): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <img src="<?= htmlspecialchars($reference['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                         width="40" height="40" class="rounded-circle me-3" alt="Avatar">
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($reference['username']) ?></h6>
                                        <p class="mb-1"><?= htmlspecialchars($reference['reference_text']) ?></p>
                                        <small class="text-muted"><?= date('M j, Y', strtotime($reference['reference_date'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($userReferences)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">No references yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>