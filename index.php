<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

// Get featured sessions
$db = getDB();
$stmt = $db->prepare("
    SELECT s.*, u.username, u.avatar,
           (SELECT COUNT(*) FROM session_likes WHERE session_id = s.id) AS likes_count,
           (SELECT COUNT(*) FROM session_comments WHERE session_id = s.id AND status = 'approved') AS comments_count
    FROM sessions s
    JOIN users u ON s.user_id = u.id
    WHERE s.status = 'public'
    ORDER BY s.created_at DESC
    LIMIT 6
");
$stmt->execute();
$featuredSessions = $stmt->fetchAll();

// Get popular categories
$stmt = $db->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM session_categories sc WHERE sc.category_id = c.id) AS session_count
    FROM categories c
    ORDER BY session_count DESC
    LIMIT 8
");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StageOne - Modeling Portfolio Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">StageOne</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="collections/sessions.php">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="castings/">Castings</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($logged_in): ?>
                        <li class="nav-item dropdown">
                            <?php 
                            $currentUser = getUserById($user_id);
                            ?>
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'assets/images/default-avatar.png') ?>" width="30" height="30" class="rounded-circle" alt="Avatar">
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile/?u=<?= htmlspecialchars($currentUser['username'] ?? '') ?>">My Profile</a></li>
                                <li><a class="dropdown-item" href="profile/edit.php">Edit Profile</a></li>
                                <li><a class="dropdown-item" href="collections/create.php">Create Session</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero-section bg-primary text-white py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold">Showcase Your Artistic Vision</h1>
                        <p class="lead">Join the premier modeling portfolio platform connecting artists, photographers, and models.</p>
                        <?php if (!$logged_in): ?>
                            <div class="mt-4">
                                <a href="register.php" class="btn btn-light btn-lg me-2">Join Now</a>
                                <a href="login.php" class="btn btn-outline-light btn-lg">Sign In</a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-6">
                        <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="assets/images/hero1.jpg" class="d-block w-100 rounded" alt="Model Portfolio">
                                </div>
                                <div class="carousel-item">
                                    <img src="assets/images/hero2.jpg" class="d-block w-100 rounded" alt="Fashion Photography">
                                </div>
                                <div class="carousel-item">
                                    <img src="assets/images/hero3.jpg" class="d-block w-100 rounded" alt="Editorial Shoot">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Sessions -->
        <section class="py-5">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Featured Sessions</h2>
                    <a href="collections/sessions.php" class="btn btn-outline-primary">View All</a>
                </div>
                
                <div class="row g-4">
                    <?php foreach ($featuredSessions as $session): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <?php if ($session['cover_image']): ?>
                                    <img src="uploads/cover_<?= htmlspecialchars($session['cover_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($session['title']) ?>" style="height: 200px; object-fit: cover;">
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
                                                by <a href="profile/?u=<?= htmlspecialchars($session['username']) ?>"><?= htmlspecialchars($session['username']) ?></a>
                                            </small>
                                            <div>
                                                <small class="text-muted me-3">
                                                    <i class="bi bi-heart"></i> <?= $session['likes_count'] ?>
                                                </small>
                                                <small class="text-muted">
                                                    <i class="bi bi-chat"></i> <?= $session['comments_count'] ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="py-5 bg-light">
            <div class="container">
                <h2 class="fw-bold mb-4">Popular Categories</h2>
                <div class="row g-3">
                    <?php foreach ($categories as $category): ?>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                                    <p class="card-text text-muted"><?= $category['session_count'] ?> sessions</p>
                                    <a href="collections/sessions.php?category=<?= $category['id'] ?>" class="btn btn-outline-primary">Explore</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <?php if (!$logged_in): ?>
            <section class="py-5">
                <div class="container">
                    <div class="bg-primary text-white rounded p-5 text-center">
                        <h2 class="fw-bold">Ready to showcase your work?</h2>
                        <p class="lead">Join thousands of models, photographers, and artists on StageOne.</p>
                        <a href="register.php" class="btn btn-light btn-lg">Create Your Portfolio</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-light py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>StageOne</h5>
                    <p>The premier modeling portfolio platform connecting artists, photographers, and models.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="collections/sessions.php" class="text-light">Portfolio</a></li>
                        <li><a href="castings/" class="text-light">Castings</a></li>
                        <li><a href="#" class="text-light">About Us</a></li>
                        <li><a href="#" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-light"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 StageOne. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>