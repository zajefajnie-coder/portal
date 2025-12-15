<?php
declare(strict_types=1);

/**
 * Upload and process image
 */
function uploadImage(array $file, string $targetDir, string $prefix = ''): ?array
{
    $validation = validateUpload($file);
    if (!$validation['valid']) {
        return null;
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $prefix . uniqid() . '.' . $ext;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Create thumbnail
        $thumbnailPath = $targetDir . 'thumb_' . $filename;
        createThumbnail($targetPath, $thumbnailPath, THUMBNAIL_WIDTH, THUMBNAIL_HEIGHT);
        
        // Create cover size if needed
        $coverPath = $targetDir . 'cover_' . $filename;
        createThumbnail($targetPath, $coverPath, COVER_WIDTH, COVER_HEIGHT);
        
        return [
            'original' => $filename,
            'thumbnail' => 'thumb_' . $filename,
            'cover' => 'cover_' . $filename,
            'path' => $targetPath
        ];
    }
    
    return null;
}

/**
 * Create thumbnail
 */
function createThumbnail(string $source, string $destination, int $width, int $height): bool
{
    list($origWidth, $origHeight, $imageType) = getimagesize($source);
    
    // Calculate dimensions maintaining aspect ratio
    $ratio = min($width / $origWidth, $height / $origHeight);
    $newWidth = (int)($origWidth * $ratio);
    $newHeight = (int)($origHeight * $ratio);
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    switch ($imageType) {
        case IMAGETYPE_GIF:
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $sourceImage = imagecreatefromgif($source);
            break;
        case IMAGETYPE_PNG:
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $sourceImage = imagecreatefrompng($source);
            break;
        default:
            $sourceImage = imagecreatefromjpeg($source);
            imagecolortransparent($newImage, imagecolorallocate($newImage, 0, 0, 0));
            break;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Resize image
    imagecopyresampled(
        $newImage, $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $origWidth, $origHeight
    );
    
    // Save thumbnail
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_GIF:
            $result = imagegif($newImage, $destination);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($newImage, $destination, 6);
            break;
        default:
            $result = imagejpeg($newImage, $destination, 85);
            break;
    }
    
    imagedestroy($newImage);
    imagedestroy($sourceImage);
    
    return $result;
}

/**
 * Add image to session
 */
function addImageToSession(int $sessionId, int $userId, string $filename, string $title = '', string $description = ''): ?int
{
    $db = getDB();
    
    $stmt = $db->prepare("
        INSERT INTO images (session_id, user_id, filename, title, description, sort_order)
        VALUES (?, ?, ?, ?, ?, (SELECT COALESCE(MAX(sort_order), 0) + 1 FROM (SELECT sort_order FROM images WHERE session_id = ?) AS temp))
    ");
    
    $result = $stmt->execute([$sessionId, $userId, $filename, $title, $description, $sessionId]);
    
    if ($result) {
        return $db->lastInsertId();
    }
    
    return null;
}

/**
 * Update image details
 */
function updateImage(int $imageId, int $userId, array $data): bool
{
    $db = getDB();
    
    $sql = "UPDATE images SET ";
    $params = [];
    $sets = [];
    
    foreach ($data as $field => $value) {
        if (in_array($field, ['title', 'description', 'sort_order'])) {
            $sets[] = "$field = ?";
            $params[] = $value;
        }
    }
    
    if (empty($sets)) {
        return false;
    }
    
    $sql .= implode(', ', $sets);
    $sql .= " WHERE id = ? AND user_id = ?";
    $params[] = $imageId;
    $params[] = $userId;
    
    $stmt = $db->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Delete image
 */
function deleteImage(int $imageId, int $userId): bool
{
    $db = getDB();
    
    // Get filename to delete files
    $stmt = $db->prepare("SELECT filename FROM images WHERE id = ? AND user_id = ?");
    $stmt->execute([$imageId, $userId]);
    $image = $stmt->fetch();
    
    if (!$image) {
        return false;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Delete related records
        $stmt = $db->prepare("DELETE FROM image_likes WHERE image_id = ?");
        $stmt->execute([$imageId]);
        
        $stmt = $db->prepare("DELETE FROM image_ratings WHERE image_id = ?");
        $stmt->execute([$imageId]);
        
        $stmt = $db->prepare("DELETE FROM image_collaborators WHERE image_id = ?");
        $stmt->execute([$imageId]);
        
        // Delete the image record
        $stmt = $db->prepare("DELETE FROM images WHERE id = ? AND user_id = ?");
        $result = $stmt->execute([$imageId, $userId]);
        
        if ($result) {
            // Delete physical files
            $basePath = UPLOAD_PATH;
            $filesToDelete = [
                $basePath . $image['filename'],
                $basePath . 'thumb_' . $image['filename'],
                $basePath . 'cover_' . $image['filename']
            ];
            
            foreach ($filesToDelete as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            
            $db->commit();
            return true;
        } else {
            $db->rollBack();
            return false;
        }
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error deleting image: " . $e->getMessage());
        return false;
    }
}

/**
 * Toggle image like
 */
function toggleImageLike(int $imageId, int $userId): array
{
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id FROM image_likes WHERE image_id = ? AND user_id = ?");
    $stmt->execute([$imageId, $userId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Unlike
        $stmt = $db->prepare("DELETE FROM image_likes WHERE image_id = ? AND user_id = ?");
        $stmt->execute([$imageId, $userId]);
        $liked = false;
    } else {
        // Like
        $stmt = $db->prepare("INSERT INTO image_likes (image_id, user_id) VALUES (?, ?)");
        $stmt->execute([$imageId, $userId]);
        $liked = true;
    }
    
    // Get updated count
    $stmt = $db->prepare("SELECT COUNT(*) FROM image_likes WHERE image_id = ?");
    $stmt->execute([$imageId]);
    $likesCount = (int)$stmt->fetchColumn();
    
    return ['liked' => $liked, 'count' => $likesCount];
}

/**
 * Rate an image
 */
function rateImage(int $imageId, int $userId, int $rating): bool
{
    if ($rating < 1 || $rating > 5) {
        return false;
    }
    
    $db = getDB();
    
    $stmt = $db->prepare("SELECT id FROM image_ratings WHERE image_id = ? AND user_id = ?");
    $stmt->execute([$imageId, $userId]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update rating
        $stmt = $db->prepare("UPDATE image_ratings SET rating = ?, updated_at = NOW() WHERE image_id = ? AND user_id = ?");
        return $stmt->execute([$rating, $imageId, $userId]);
    } else {
        // Insert new rating
        $stmt = $db->prepare("INSERT INTO image_ratings (image_id, user_id, rating) VALUES (?, ?, ?)");
        return $stmt->execute([$imageId, $userId, $rating]);
    }
}

/**
 * Get average rating for an image
 */
function getImageAverageRating(int $imageId): float
{
    $db = getDB();
    $stmt = $db->prepare("SELECT AVG(rating) FROM image_ratings WHERE image_id = ?");
    $stmt->execute([$imageId]);
    $avg = $stmt->fetchColumn();
    return $avg ? (float)$avg : 0.0;
}

/**
 * Reorder images in a session
 */
function reorderSessionImages(int $sessionId, array $imageOrder): bool
{
    $db = getDB();
    
    try {
        $db->beginTransaction();
        
        $stmt = $db->prepare("UPDATE images SET sort_order = ? WHERE id = ? AND session_id = ?");
        
        foreach ($imageOrder as $index => $imageId) {
            $stmt->execute([$index, $imageId, $sessionId]);
        }
        
        $db->commit();
        return true;
    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error reordering images: " . $e->getMessage());
        return false;
    }
}