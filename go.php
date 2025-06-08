<?php
// Start session and include error handler
session_start();

// Get the correct path to the root directory
$rootPath = dirname(__FILE__);

// Include required files
require_once $rootPath . '/includes/error_handler.php';
require_once $rootPath . '/functions.php';
require_once $rootPath . '/db.php';

// Get username and slug from URL
$username = $_GET['username'] ?? null;
$slug = $_GET['slug'] ?? null;

// Handle legacy URLs (temporary support)
if (!$username && $slug) {
    // Try to find the link directly by slug
    $link = getLinkBySlug($slug);
    
    if ($link) {
        // Get username for the redirect
        $username = getUsernameById($link['user_id']);
        // Redirect to new URL format
        header("Location: " . getFullUrl($username, $link['slug']), true, 301);
        exit;
    }
} else if ($username && $slug) {
    // Get link using new username/slug format
    $link = getLinkBySlug($slug, $username);
    
    // Check if username exists
    $userExists = false;
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $userExists = $stmt->get_result()->num_rows > 0;
    
    if (!$userExists) {
        $_SESSION['error'] = "User '$username' does not exist.";
        include $rootPath . '/pages/not_found.php';
        exit;
    }
    
    if (!$link) {
        $_SESSION['error'] = "Link with slug '$slug' not found for user '$username'.";
        include $rootPath . '/pages/not_found.php';
        exit;
    }
}

// If no link found, show 404 page
if (!$link) {
    $_SESSION['error'] = "The requested link could not be found.";
    include $rootPath . '/pages/not_found.php';
    exit;
}

// Update click count
incrementClicks($link['id']);

// Get user's ad settings
$userId = $link['user_id'];

// Redirect to ad page or final destination
if ($link['destination_url']) {
    // Store the link ID in session for verification
    $_SESSION['current_link_id'] = $link['id'];
    
    // Use absolute path for redirection with proper path
    $adPageUrl = getBaseUrl() . "/ad-page.php?link_id=" . $link['id'];
    header("Location: " . $adPageUrl, true, 302);
    exit;
} else {
    $_SESSION['error'] = "Invalid link configuration.";
    include $rootPath . '/pages/not_found.php';
    exit;
}
?> 