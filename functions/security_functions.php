<?php
declare(strict_types=1);

/**
 * Validate CSRF token
 */
function validateCSRFToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 */
function generateCSRFToken(): string
{
    if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_time'] + CSRF_TOKEN_LIFETIME) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Sanitize user input
 */
function sanitizeInput(string $input): string
{
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 */
function validatePassword(string $password): bool
{
    // At least 8 characters, one uppercase, one lowercase, one number
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password) === 1;
}

/**
 * Hash password securely
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536, // 64 MB
        'time_cost' => 4,       // 4 iterations
        'threads' => 3,         // 3 threads
    ]);
}

/**
 * Verify password
 */
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Validate reCAPTCHA response
 */
function validateRecaptcha(string $recaptchaResponse): bool
{
    if (empty(RECAPTCHA_SECRET_KEY)) {
        return true; // Skip validation if not configured
    }
    
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => RECAPTCHA_SECRET_KEY,
        'response' => $recaptchaResponse,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result, true);
    
    return $response['success'] ?? false;
}

/**
 * Check rate limit
 */
function checkRateLimit(string $key, int $maxRequests = 10, int $timeWindow = 300): bool
{
    $cacheKey = 'rate_limit_' . md5($key);
    
    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = ['count' => 0, 'time' => time()];
    }
    
    $limit = &$_SESSION[$cacheKey];
    
    if (time() - $limit['time'] > $timeWindow) {
        $limit = ['count' => 0, 'time' => time()];
    }
    
    if ($limit['count'] >= $maxRequests) {
        return false;
    }
    
    $limit['count']++;
    return true;
}

/**
 * Encrypt message with AES-256-GCM
 */
function encryptMessage(string $plaintext, string $key): array
{
    $iv = random_bytes(12); // 96-bit IV for GCM
    $tag = '';
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', hex2bin($key), OPENSSL_RAW_DATA, $iv, $tag);
    
    if ($ciphertext === false) {
        throw new Exception('Encryption failed');
    }
    
    return [
        'ciphertext' => base64_encode($ciphertext),
        'iv' => base64_encode($iv),
        'tag' => base64_encode($tag)
    ];
}

/**
 * Decrypt message with AES-256-GCM
 */
function decryptMessage(array $encryptedData, string $key): string
{
    $ciphertext = base64_decode($encryptedData['ciphertext']);
    $iv = base64_decode($encryptedData['iv']);
    $tag = base64_decode($encryptedData['tag']);
    
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', hex2bin($key), OPENSSL_RAW_DATA, $iv, $tag);
    
    if ($plaintext === false) {
        throw new Exception('Decryption failed');
    }
    
    return $plaintext;
}

/**
 * Generate encryption key
 */
function generateEncryptionKey(): string
{
    return bin2hex(random_bytes(32)); // 256-bit key as hex
}

/**
 * Validate file upload
 */
function validateUpload(array $file): array
{
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error occurred';
        return ['valid' => false, 'errors' => $errors];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'File size exceeds maximum allowed size';
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_TYPES)) {
        $errors[] = 'Invalid file type. Allowed types: ' . implode(', ', ALLOWED_TYPES);
    }
    
    $imageInfo = getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        $errors[] = 'File is not a valid image';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}