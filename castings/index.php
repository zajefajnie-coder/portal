<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

// Get all castings
$db = getDB();
$stmt = $db->prepare("
    SELECT c.*, u.username, u.avatar,
           (SELECT COUNT(*) FROM casting_applications WHERE casting_id = c.id) AS applications_count
    FROM castings c
    JOIN users u ON c.user_id = u.id
    WHERE c.status = 'open'
    ORDER BY c.created_at DESC
");
$stmt->execute();
$castings = $stmt->fetchAll();

// Get user's favorite castings if logged in
$userFavoriteCastings = [];
if ($logged_in) {
    $userFavoriteCastings = getUserFavoriteCastings($user_id);
    $userFavoriteCastingIds = array_column($userFavoriteCastings, 'id');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Castings - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Castings</h1>
            <?php if ($logged_in): ?>
                <a href="create.php" class="btn btn-primary">Create Casting</a>
            <?php endif; ?>
        </div>
        
        <div class="row g-4">
            <?php foreach ($castings as $casting): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card casting-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="card-title"><?= htmlspecialchars($casting['title']) ?></h5>
                                <?php if ($logged_in): ?>
                                    <button class="btn favorite-btn <?= in_array($casting['id'], $userFavoriteCastingIds ?? []) ? 'btn-warning' : 'btn-outline-warning' ?>" 
                                            data-casting-id="<?= $casting['id'] ?>">
                                        <i class="bi <?= in_array($casting['id'], $userFavoriteCastingIds ?? []) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <p class="card-text text-muted"><?= htmlspecialchars(substr($casting['description'], 0, 100)) ?>...</p>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> by 
                                    <a href="../profile/?u=<?= htmlspecialchars($casting['username']) ?>">
                                        <?= htmlspecialchars($casting['username']) ?>
                                    </a>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt"></i> <?= htmlspecialchars($casting['location'] ?? 'Not specified') ?>
                                </small>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="bi bi-cash"></i> <?= htmlspecialchars($casting['budget'] ?? 'Not specified') ?>
                                </small>
                            </div>
                            
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-calendar"></i> Deadline: <?= date('M j, Y', strtotime($casting['deadline'])) ?>
                                </small>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-person-check"></i> <?= $casting['applications_count'] ?> applications
                                </small>
                                <a href="view.php?id=<?= $casting['id'] ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($castings)): ?>
            <div class="text-center py-5">
                <i class="bi bi-person-video3 fs-1 text-muted"></i>
                <h5>No castings available</h5>
                <p class="text-muted">Check back later for new casting opportunities</p>
                <?php if ($logged_in): ?>
                    <a href="create.php" class="btn btn-primary">Create Your First Casting</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Favorite casting functionality
        document.querySelectorAll('.favorite-btn').forEach(button => {
            button.addEventListener('click', function() {
                const castingId = this.dataset.castingId;
                const isFavorite = this.classList.contains('btn-warning');
                
                fetch('../api/toggle_casting_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        casting_id: castingId,
                        action: isFavorite ? 'remove' : 'add'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (isFavorite) {
                            this.classList.remove('btn-warning');
                            this.classList.add('btn-outline-warning');
                            this.innerHTML = '<i class="bi bi-star"></i>';
                        } else {
                            this.classList.remove('btn-outline-warning');
                            this.classList.add('btn-warning');
                            this.innerHTML = '<i class="bi bi-star-fill"></i>';
                        }
                    } else {
                        alert('Error updating favorite status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating favorite status');
                });
            });
        });
    </script>
</body>
</html>