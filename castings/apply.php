<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

if (!$logged_in) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$castingId = (int)($_POST['casting_id'] ?? 0);
$applicationText = trim($_POST['application_text'] ?? '');

if (!$castingId || empty($applicationText)) {
    header('Location: index.php');
    exit;
}

// Check if casting exists and is open
$db = getDB();
$stmt = $db->prepare("SELECT id, status FROM castings WHERE id = ? AND status = 'open'");
$stmt->execute([$castingId]);
$casting = $stmt->fetch();

if (!$casting) {
    header('Location: index.php');
    exit;
}

// Check if user already applied
$stmt = $db->prepare("SELECT id FROM casting_applications WHERE casting_id = ? AND user_id = ?");
$stmt->execute([$castingId, $user_id]);
if ($stmt->fetch()) {
    header("Location: view.php?id=$castingId&error=already_applied");
    exit;
}

// Handle file uploads
$cvFile = null;
$portfolioFiles = [];

if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
    $cvUpload = uploadImage($_FILES['cv_file'], UPLOAD_PATH, 'cv_');
    if ($cvUpload) {
        $cvFile = $cvUpload['original'];
    }
}

if (isset($_FILES['portfolio_files'])) {
    $fileCount = count($_FILES['portfolio_files']['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES['portfolio_files']['error'][$i] === UPLOAD_ERR_OK) {
            $portfolioUpload = uploadImage([
                'name' => $_FILES['portfolio_files']['name'][$i],
                'type' => $_FILES['portfolio_files']['type'][$i],
                'tmp_name' => $_FILES['portfolio_files']['tmp_name'][$i],
                'error' => $_FILES['portfolio_files']['error'][$i],
                'size' => $_FILES['portfolio_files']['size'][$i]
            ], UPLOAD_PATH, 'portfolio_');
            
            if ($portfolioUpload) {
                $portfolioFiles[] = $portfolioUpload['original'];
            }
        }
    }
}

try {
    // Insert application
    $stmt = $db->prepare("
        INSERT INTO casting_applications (casting_id, user_id, application_text, cv_file, portfolio_files, applied_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $portfolioFilesJson = !empty($portfolioFiles) ? json_encode($portfolioFiles) : null;
    $result = $stmt->execute([$castingId, $user_id, $applicationText, $cvFile, $portfolioFilesJson]);
    
    if ($result) {
        // Create notification for casting owner
        $stmt = $db->prepare("SELECT user_id FROM castings WHERE id = ?");
        $stmt->execute([$castingId]);
        $castingOwnerId = (int)$stmt->fetchColumn();
        
        if ($castingOwnerId) {
            $stmt = $db->prepare("
                INSERT INTO notifications (user_id, from_user_id, type, title, message, link)
                VALUES (?, ?, 'casting_application', 'New Application', ?, ?)
            ");
            $stmt->execute([
                $castingOwnerId,
                $user_id,
                $_SESSION['username'] . ' applied to your casting',
                "castings/applications.php?id=$castingId"
            ]);
        }
        
        header("Location: view.php?id=$castingId&success=1");
        exit;
    } else {
        header("Location: view.php?id=$castingId&error=application_failed");
        exit;
    }
} catch (Exception $e) {
    error_log("Casting application error: " . $e->getMessage());
    header("Location: view.php?id=$castingId&error=application_failed");
    exit;
}