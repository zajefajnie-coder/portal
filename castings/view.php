<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

$castingId = (int)($_GET['id'] ?? 0);
$casting = null;

$db = getDB();
$stmt = $db->prepare("
    SELECT c.*, u.username, u.avatar
    FROM castings c
    JOIN users u ON c.user_id = u.id
    WHERE c.id = ?
");
$stmt->execute([$castingId]);
$casting = $stmt->fetch();

if (!$casting) {
    header('Location: index.php');
    exit;
}

// Check if user has applied
$userApplied = false;
$canApply = false;
if ($logged_in) {
    $stmt = $db->prepare("SELECT id FROM casting_applications WHERE casting_id = ? AND user_id = ?");
    $stmt->execute([$castingId, $user_id]);
    $userApplied = $stmt->fetch() !== false;
    
    // Users can't apply to their own castings
    $canApply = $casting['user_id'] != $user_id && !$userApplied;
}

// Get casting applications if user is the owner
$castingApplications = [];
if ($logged_in && $casting['user_id'] == $user_id) {
    $stmt = $db->prepare("
        SELECT ca.*, u.username, u.avatar, u.specialization, u.location
        FROM casting_applications ca
        JOIN users u ON ca.user_id = u.id
        WHERE ca.casting_id = ?
        ORDER BY ca.applied_at DESC
    ");
    $stmt->execute([$castingId]);
    $castingApplications = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($casting['title']) ?> - Castings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h1><?= htmlspecialchars($casting['title']) ?></h1>
                            <?php if ($logged_in): ?>
                                <button class="btn favorite-btn <?= in_array($casting['id'], array_column(getUserFavoriteCastings($user_id), 'id')) ? 'btn-warning' : 'btn-outline-warning' ?>" 
                                        data-casting-id="<?= $casting['id'] ?>">
                                    <i class="bi <?= in_array($casting['id'], array_column(getUserFavoriteCastings($user_id), 'id')) ? 'bi-star-fill' : 'bi-star' ?>"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <p class="text-muted"><?= htmlspecialchars($casting['description']) ?></p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Requirements</h5>
                                <p><?= htmlspecialchars($casting['requirements'] ?? 'No specific requirements listed') ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Project Details</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Location:</strong> <?= htmlspecialchars($casting['location'] ?? 'Not specified') ?></li>
                                    <li><strong>Budget:</strong> <?= htmlspecialchars($casting['budget'] ?? 'Not specified') ?></li>
                                    <li><strong>Deadline:</strong> <?= date('M j, Y', strtotime($casting['deadline'])) ?></li>
                                    <li><strong>Start Date:</strong> <?= $casting['start_date'] ? date('M j, Y', strtotime($casting['start_date'])) : 'Not specified' ?></li>
                                    <li><strong>End Date:</strong> <?= $casting['end_date'] ? date('M j, Y', strtotime($casting['end_date'])) : 'Not specified' ?></li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h5>Posted by</h5>
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($casting['avatar'] ?? '../assets/images/default-avatar.png') ?>" 
                                     width="50" height="50" class="rounded-circle me-3" alt="Avatar">
                                <div>
                                    <h6 class="mb-0">
                                        <a href="../profile/?u=<?= htmlspecialchars($casting['username']) ?>">
                                            <?= htmlspecialchars($casting['username']) ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">Posted on <?= date('M j, Y', strtotime($casting['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($canApply): ?>
                    <div class="card mt-4">
                        <div class="card-body">
                            <h4>Apply for this casting</h4>
                            <form method="POST" action="apply.php" enctype="multipart/form-data">
                                <input type="hidden" name="casting_id" value="<?= $casting['id'] ?>">
                                <div class="mb-3">
                                    <label for="application_text" class="form-label">Cover Letter</label>
                                    <textarea class="form-control" id="application_text" name="application_text" rows="4" required></textarea>
                                    <div class="form-text">Tell the casting director why you're perfect for this role</div>
                                </div>
                                <div class="mb-3">
                                    <label for="cv_file" class="form-label">Upload CV/Resume</label>
                                    <input type="file" class="form-control" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx">
                                </div>
                                <div class="mb-3">
                                    <label for="portfolio_files" class="form-label">Upload Portfolio</label>
                                    <input type="file" class="form-control" id="portfolio_files" name="portfolio_files[]" accept="image/*" multiple>
                                    <div class="form-text">Select multiple images to showcase your work</div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($userApplied): ?>
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="bi bi-check-circle"></i> You have already applied for this casting.
                            </div>
                        </div>
                    </div>
                <?php elseif (!$logged_in): ?>
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> You must be logged in to apply for this casting.
                                <a href="../login.php" class="btn btn-primary btn-sm ms-2">Login</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Casting Info</h5>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-calendar-check text-primary"></i> Status: <span class="badge bg-primary"><?= ucfirst($casting['status']) ?></span></li>
                            <li class="mt-2"><i class="bi bi-person-check text-success"></i> Applications: 
                                <?php
                                $stmt = $db->prepare("SELECT COUNT(*) FROM casting_applications WHERE casting_id = ?");
                                $stmt->execute([$casting['id']]);
                                echo $stmt->fetchColumn();
                                ?>
                            </li>
                            <li class="mt-2"><i class="bi bi-clock text-info"></i> Posted: <?= date('M j, Y', strtotime($casting['created_at'])) ?></li>
                            <li class="mt-2"><i class="bi bi-calendar text-warning"></i> Deadline: <?= date('M j, Y', strtotime($casting['deadline'])) ?></li>
                        </ul>
                        
                        <?php if ($logged_in && $casting['user_id'] == $user_id): ?>
                            <div class="mt-4">
                                <h6>Your Casting</h6>
                                <a href="edit.php?id=<?= $casting['id'] ?>" class="btn btn-outline-primary btn-sm me-2">Edit</a>
                                <a href="applications.php?id=<?= $casting['id'] ?>" class="btn btn-outline-secondary btn-sm">
                                    View Applications (<?= count($castingApplications) ?>)
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-body">
                        <h5>Quick Actions</h5>
                        <div class="d-grid gap-2">
                            <a href="../profile/?u=<?= htmlspecialchars($casting['username']) ?>" class="btn btn-outline-primary">
                                <i class="bi bi-person"></i> View Poster's Profile
                            </a>
                            <?php if ($logged_in): ?>
                                <a href="../messages/?user=<?= $casting['user_id'] ?>" class="btn btn-outline-success">
                                    <i class="bi bi-chat"></i> Message Poster
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Favorite casting functionality
        document.querySelector('.favorite-btn')?.addEventListener('click', function() {
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
    </script>
</body>
</html>