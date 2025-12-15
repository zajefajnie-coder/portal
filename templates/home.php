<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
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
                    <?php if (isLoggedIn()): ?>
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
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=login">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?page=register">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <main class="container mt-4">
        <div class="jumbotron text-center py-5">
            <h1 class="display-4">Welcome to StageOne</h1>
            <p class="lead">A premium modeling portfolio platform connecting talent with opportunities</p>
            
            <?php if (!isLoggedIn()): ?>
                <div class="mt-4">
                    <a href="?page=register" class="btn btn-primary btn-lg mx-2">Join Now</a>
                    <a href="?page=login" class="btn btn-outline-primary btn-lg mx-2">Login</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Portfolio</h5>
                        <p class="card-text">Showcase your work with beautiful session portfolios. Organize your photos into themed collections.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Community</h5>
                        <p class="card-text">Connect with other models, photographers, and industry professionals. Follow and engage with others.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Opportunities</h5>
                        <p class="card-text">Discover casting calls and job opportunities tailored to your profile and preferences.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-dark text-light text-center py-4 mt-5">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> StageOne Modeling Portal. All rights reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/main.js"></script>
</body>
</html>