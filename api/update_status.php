<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json');

if (!$logged_in) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

if ($action !== 'update') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

try {
    updateLastActivity($user_id);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Update status error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}