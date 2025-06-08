<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

$userId = getCurrentUserId();
$action = $_GET['action'] ?? 'list';
$alertMessage = '';
$alertType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_link'])) {
        $destination = $_POST['destination'];
        $customSlug = !empty($_POST['custom_slug']) ? $_POST['custom_slug'] : generateRandomSlug();
        $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        
        // Check if slug already exists for this user
        $checkStmt = $conn->prepare("SELECT id FROM links WHERE slug = ? AND user_id = ?");
        $checkStmt->bind_param("si", $customSlug, $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $alertType = 'danger';
            $alertMessage = 'You already have a link with this slug. Please choose a different one.';
            $action = 'new'; // Stay on create form
        } else {
            $stmt = $conn->prepare("INSERT INTO links (destination_url, slug, user_id, category_id, expires_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiii", $destination, $customSlug, $userId, $categoryId, $expiresAt);
            
            if ($stmt->execute()) {
                $alertType = 'success';
                $alertMessage = 'Link created successfully!';
                $action = 'list';
            } else {
                $alertType = 'danger';
                $alertMessage = 'Error creating link.';
                $action = 'new'; // Stay on create form
            }
        }
    } elseif (isset($_POST['edit_link'])) {
        $linkId = $_POST['link_id'];
        $customSlug = $_POST['custom_slug'];
        $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'] : null;
        
        // Check if slug already exists for this user (excluding current link)
        $checkStmt = $conn->prepare("SELECT id FROM links WHERE slug = ? AND user_id = ? AND id != ?");
        $checkStmt->bind_param("sii", $customSlug, $userId, $linkId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $alertType = 'danger';
            $alertMessage = 'You already have a link with this slug. Please choose a different one.';
            $action = 'edit'; // Stay on edit form
        } else {
            $stmt = $conn->prepare("UPDATE links SET slug = ?, category_id = ?, expires_at = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("siiii", $customSlug, $categoryId, $expiresAt, $linkId, $userId);
            
            if ($stmt->execute()) {
                $alertType = 'success';
                $alertMessage = 'Link updated successfully!';
                $action = 'list';
            } else {
                $alertType = 'danger';
                $alertMessage = 'Error updating link.';
                $action = 'edit'; // Stay on edit form
            }
        }
    }
}

// Get categories for dropdown
$categories = [];
$stmt = $conn->prepare("SELECT * FROM link_categories WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle different views
switch ($action) {
    case 'new':
        include 'links/create.php';
        break;
        
    case 'edit':
        $linkId = isset($_POST['link_id']) ? $_POST['link_id'] : ($_GET['id'] ?? 0);
        $stmt = $conn->prepare("SELECT * FROM links WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $linkId, $userId);
        $stmt->execute();
        $link = $stmt->get_result()->fetch_assoc();
        
        if (!$link) {
            $alertType = 'danger';
            $alertMessage = 'Link not found.';
            include 'links/list.php';
        } else {
            include 'links/edit.php';
        }
        break;
        
    default:
        // Pagination
        $page = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Get total links count
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM links WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $totalLinks = $stmt->get_result()->fetch_assoc()['total'];
        $totalPages = ceil($totalLinks / $perPage);
        
        // Get links for current page
        $stmt = $conn->prepare("
            SELECT l.*, lc.name as category_name 
            FROM links l 
            LEFT JOIN link_categories lc ON l.category_id = lc.id 
            WHERE l.user_id = ? 
            ORDER BY l.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("iii", $userId, $perPage, $offset);
        $stmt->execute();
        $links = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        include 'links/list.php';
        break;
}
?> 