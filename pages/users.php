<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

if (!isAdmin()) {
    include 'unauthorized.php';
    exit;
}

$action = $_GET['action'] ?? 'list';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        if (createUser($username, $email, $password, $role)) {
            $alertType = 'success';
            $alertMessage = 'User created successfully!';
            $action = 'list';
        } else {
            $alertType = 'danger';
            $alertMessage = 'Error creating user. Username or email may already exist.';
        }
    } elseif (isset($_POST['edit_user'])) {
        $userId = $_POST['user_id'];
        $data = [
            'email' => $_POST['email'],
            'role' => $_POST['role']
        ];
        
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        if (updateUser($userId, $data)) {
            $alertType = 'success';
            $alertMessage = 'User updated successfully!';
            $action = 'list';
        } else {
            $alertType = 'danger';
            $alertMessage = 'Error updating user.';
        }
    }
}

// Handle different views
switch ($action) {
    case 'new':
        include 'users/create.php';
        break;
        
    case 'edit':
        $userId = $_GET['id'] ?? 0;
        
        // Get user data with statistics
        $stmt = $conn->prepare("
            SELECT 
                u.*,
                COUNT(DISTINCT l.id) as total_links,
                COALESCE(SUM(l.clicks), 0) as total_clicks
            FROM users u
            LEFT JOIN links l ON u.id = l.user_id
            WHERE u.id = ?
            GROUP BY u.id
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!$user) {
            $alertType = 'danger';
            $alertMessage = 'User not found.';
            include 'users/list.php';
        } else {
            // Ensure all required fields have default values
            $user = array_merge([
                'total_links' => 0,
                'total_clicks' => 0,
                'is_active' => true,
                'role' => 'user',
                'email' => '',
                'created_at' => null,
                'last_login' => null
            ], $user);
            
            include 'users/edit.php';
        }
        break;
        
    default:
        // Pagination
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Get total users count
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
        $stmt->execute();
        $totalUsers = $stmt->get_result()->fetch_assoc()['total'];
        $totalPages = ceil($totalUsers / $perPage);
        
        // Get users for current page
        $stmt = $conn->prepare("
            SELECT 
                u.*,
                COUNT(DISTINCT l.id) as total_links,
                SUM(l.clicks) as total_clicks
            FROM users u
            LEFT JOIN links l ON u.id = l.user_id
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("ii", $perPage, $offset);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        include 'users/list.php';
        break;
}
?> 