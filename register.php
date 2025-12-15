<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/init.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    if (!checkRateLimit('register_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'), 3, 300)) {
        $error = 'Too many registration attempts. Please try again later.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

        // Validate reCAPTCHA if enabled
        if (!empty(RECAPTCHA_SECRET_KEY)) {
            if (!validateRecaptcha($recaptchaResponse)) {
                $error = 'reCAPTCHA verification failed. Please try again.';
            }
        }

        if (empty($error)) {
            // Validation
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Please fill in all fields.';
            } elseif (!validateEmail($email)) {
                $error = 'Please enter a valid email address.';
            } elseif (!validatePassword($password)) {
                $error = 'Password must be at least 8 characters with uppercase, lowercase, and number.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } else {
                $db = getDB();
                
                // Check if username or email already exists
                $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                
                if ($stmt->fetch()) {
                    $error = 'Username or email already exists.';
                } else {
                    // Insert new user
                    $hashedPassword = hashPassword($password);
                    $stmt = $db->prepare("
                        INSERT INTO users (username, email, password, created_at) 
                        VALUES (?, ?, ?, NOW())
                    ");
                    
                    if ($stmt->execute([$username, $email, $hashedPassword])) {
                        $success = 'Registration successful! You can now login.';
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
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
    <title>Register - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4 mt-5">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Create Account</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Must be at least 8 characters with uppercase, lowercase, and number</div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <?php if (!empty(RECAPTCHA_SITE_KEY)): ?>
                                <div class="mb-3">
                                    <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                                </div>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
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