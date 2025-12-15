<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

if (!$logged_in) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Get all categories for the form
$db = getDB();
$stmt = $db->prepare("SELECT id, name FROM categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $sessionDate = $_POST['session_date'] ?? date('Y-m-d');
    $status = $_POST['status'] ?? 'draft';
    $selectedCategories = $_POST['categories'] ?? [];

    // Validate required fields
    if (empty($title)) {
        $error = 'Title is required';
    } elseif (strlen($title) > 255) {
        $error = 'Title is too long';
    } elseif (!in_array($status, ['draft', 'private', 'public'])) {
        $error = 'Invalid status';
    } else {
        // Handle cover image upload if provided
        $coverImage = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['cover_image'], UPLOAD_PATH, 'cover_');
            if ($uploadResult) {
                $coverImage = $uploadResult['original'];
            } else {
                $error = 'Error uploading cover image';
            }
        }

        if (empty($error)) {
            // Create the session
            $sessionData = [
                'title' => $title,
                'description' => $description,
                'location' => $location,
                'session_date' => $sessionDate,
                'cover_image' => $coverImage,
                'status' => $status
            ];

            $sessionId = createSession($user_id, $sessionData);

            if ($sessionId) {
                // Assign categories if any were selected
                if (!empty($selectedCategories)) {
                    assignSessionCategories($sessionId, $selectedCategories);
                }

                $success = 'Session created successfully!';
                header("Location: session.php?id=$sessionId");
                exit;
            } else {
                $error = 'Error creating session';
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
    <title>Create Session - StageOne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="container mt-4">
        <h1>Create New Session</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Session Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                                <div class="form-text">Give your session a descriptive title</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                <div class="form-text">Describe the session, theme, or concept</div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="session_date" class="form-label">Session Date</label>
                                        <input type="date" class="form-control" id="session_date" name="session_date" value="<?= htmlspecialchars($_POST['session_date'] ?? date('Y-m-d')) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">Cover Image</label>
                                <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                                <div class="form-text">This will be the main image representing your session</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Session Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="draft" <?= (($_POST['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                                    <option value="private" <?= (($_POST['status'] ?? 'private') === 'private') ? 'selected' : '' ?>>Private</option>
                                    <option value="public" <?= (($_POST['status'] ?? '') === 'public') ? 'selected' : '' ?>>Public</option>
                                </select>
                                <div class="form-text">
                                    Draft: Only you can see it<br>
                                    Private: Only shared with specific people<br>
                                    Public: Visible to all users
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Categories</label>
                                <div class="row">
                                    <?php foreach ($categories as $category): ?>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="categories[]" value="<?= $category['id'] ?>" 
                                                       id="cat_<?= $category['id'] ?>" 
                                                       <?= in_array($category['id'], $_POST['categories'] ?? []) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="cat_<?= $category['id'] ?>">
                                                    <?= htmlspecialchars($category['name']) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Session</button>
                        <a href="sessions.php" class="btn btn-outline-secondary mt-2">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>