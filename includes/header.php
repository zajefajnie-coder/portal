<?php
declare(strict_types=1);

if (!isset($logged_in)) {
    $logged_in = isset($_SESSION['user_id']);
    $user_id = $logged_in ? (int)$_SESSION['user_id'] : 0;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">StageOne</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../collections/sessions.php">Portfolio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../castings/">Castings</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if ($logged_in): ?>
                    <?php 
                    $currentUser = getUserById($user_id);
                    ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="<?= htmlspecialchars($currentUser['avatar'] ?? '../assets/images/default-avatar.png') ?>" width="30" height="30" class="rounded-circle" alt="Avatar">
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../profile/?u=<?= htmlspecialchars($currentUser['username'] ?? '') ?>">My Profile</a></li>
                            <li><a class="dropdown-item" href="../profile/edit.php">Edit Profile</a></li>
                            <li><a class="dropdown-item" href="../collections/create.php">Create Session</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>