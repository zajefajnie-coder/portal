<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    if (!checkRateLimit('login_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 5, 300)) {
        $error = 'Too many login attempts. Please try again later.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

        // Validate reCAPTCHA if enabled
        if (!empty(RECAPTCHA_SECRET_KEY)) {
            if (!validateRecaptcha($recaptchaResponse)) {
                $error = 'reCAPTCHA verification failed. Please try again.';
            }
        }

        if (empty($error)) {
            if (empty($username) || empty($password)) {
                $error = 'Please fill in all fields.';
            } else {
                $db = getDB();
                $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $username]);
                $user = $stmt->fetch();

                if ($user && verifyPassword($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Update last activity
                    updateLastActivity($user['id']);
                    
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Login to StageOne</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <?php if (!empty(RECAPTCHA_SITE_KEY)): ?>
                                <div class="mb-3">
                                    <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                                </div>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="register.php">Register here</a></p>
                            <a href="index.php">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (!empty(RECAPTCHA_SITE_KEY)): ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif; ?>
</body>
</html>