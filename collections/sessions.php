<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

$page = (int)($_GET['page'] ?? 1);
$offset = ($page - 1) * ITEMS_PER_PAGE;
$categoryId = (int)($_GET['category'] ?? 0);

// Get sessions with pagination
$allSessions = getAllSessions('public', ITEMS_PER_PAGE, $offset, null, $categoryId ? [$categoryId] : null);

// Get all categories for filter
$db = getDB();
$stmt = $db->prepare("SELECT id, name FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Portfolio</h1>
            <?php if ($logged_in): ?>
                <a href="create.php" class="btn btn-primary">Create Session</a>
            <?php endif; ?>
        </div>
        
        <!-- Category Filter -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Filter by Category</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="sessions.php" class="btn <?= !$categoryId ? 'btn-primary' : 'btn-outline-primary' ?>">All</a>
                            <?php foreach ($categories as $category): ?>
                                <a href="sessions.php?category=<?= $category['id'] ?>" 
                                   class="btn <?= $categoryId == $category['id'] ? 'btn-primary' : 'btn-outline-primary' ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sessions Grid -->
        <div class="row g-4">
            <?php foreach ($allSessions as $session): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card session-card h-100">
                        <?php if ($session['cover_image']): ?>
                            <img src="../uploads/cover_<?= htmlspecialchars($session['cover_image']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($session['title']) ?>" 
                                 style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($session['title']) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars(substr($session['description'], 0, 100)) ?>...</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        by <a href="../profile/?u=<?= htmlspecialchars($session['username']) ?>"><?= htmlspecialchars($session['username']) ?></a>
                                    </small>
                                    <div>
                                        <button class="btn btn-sm like-btn" 
                                                data-target-id="<?= $session['id'] ?>" 
                                                data-target-type="session">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                        <span class="likes-count"><?= $session['likes_count'] ?></span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="session.php?id=<?= $session['id'] ?>" class="btn btn-outline-primary btn-sm">View Session</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($allSessions)): ?>
            <div class="text-center py-5">
                <i class="bi bi-image fs-1 text-muted"></i>
                <h5>No sessions found</h5>
                <p class="text-muted">Be the first to create a session in this category</p>
                <?php if ($logged_in): ?>
                    <a href="create.php" class="btn btn-primary">Create Your First Session</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>