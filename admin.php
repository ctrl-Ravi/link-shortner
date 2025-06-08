<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();
define('ADMIN_ACCESS', true);

// Include required files
require_once 'db.php';
require_once 'functions.php';
require_once 'includes/admin_functions.php';

// Check if connected to database
$dbConnected = true;
if ($conn->connect_error) {
    $dbConnected = false;
}

// Handle login form submission
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            
            // Log login activity
            logUserActivity($user['id'], 'login', 'User logged in');
            
            // Update last login
            $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            
            header("Location: admin.php");
            exit;
        } else {
            $alertType = 'danger';
            $alertMessage = "Invalid username or password";
        }
    } else {
        $alertType = 'danger';
        $alertMessage = "Invalid username or password";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    if (isAuthenticated()) {
        logUserActivity($_SESSION['user_id'], 'logout', 'User logged out');
    }
    session_unset();
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Set current page for navigation
$currentPage = 'dashboard';
if (isset($_GET['page'])) {
    // Clean up page parameter and convert hyphens to underscores
    $currentPage = str_replace('-', '_', $_GET['page']);
    $currentPage = preg_replace('/[^a-zA-Z0-9_]/', '', $currentPage);
}

// Include header
require_once 'includes/header.php';

if (!$dbConnected) {
    // Show database error
    include 'pages/db_error.php';
} elseif (!isAuthenticated()) {
    // Show login form
    include 'pages/login.php';
} else {
    // Handle actions first
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        switch ($action) {
            case 'delete_link':
                if (isset($_GET['id'])) {
                    deleteLink($_GET['id']);
                }
                break;
            case 'delete_user':
                if (isAdmin() && isset($_GET['id'])) {
                    deleteUser($_GET['id']);
                }
                break;
            // Add more actions as needed
        }
    }
    
    // Load appropriate page
    $pagePath = 'pages/' . $currentPage . '.php';
    if (file_exists($pagePath)) {
        include $pagePath;
    } else {
        // Try alternate format if file not found
        $alternatePage = str_replace('_', '-', $currentPage);
        $alternatePath = 'pages/' . $alternatePage . '.php';
        if (file_exists($alternatePath)) {
            include $alternatePath;
        } else {
            include 'pages/not_found.php';
        }
    }
}

// Include footer
require_once 'includes/footer.php'; 