<?php
declare(strict_types=1);

// StageOne - Modeling Portal
// Main entry point

session_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';

$page = $_GET['page'] ?? 'home';

switch($page) {
    case 'login':
        include 'templates/login.php';
        break;
    case 'register':
        include 'templates/register.php';
        break;
    case 'profile':
        include 'templates/profile.php';
        break;
    case 'sessions':
        include 'templates/sessions.php';
        break;
    case 'portfolio':
        include 'templates/portfolio.php';
        break;
    case 'casting':
        include 'templates/casting.php';
        break;
    case 'messages':
        include 'templates/messages.php';
        break;
    case 'notifications':
        include 'templates/notifications.php';
        break;
    case 'admin':
        include 'admin/dashboard.php';
        break;
    case 'moderator':
        include 'moderator/dashboard.php';
        break;
    default:
        include 'templates/home.php';
        break;
}
?>