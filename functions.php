<?php
require_once 'db.php';

/**
 * Generates a random unique slug
 * 
 * @param int $length Length of the slug
 * @param int $userId The user ID to check uniqueness against
 * @return string The generated slug
 */
function generateSlug($length = 5, $userId = null) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    global $conn;
    
    // Try to generate a unique slug
    $attempts = 0;
    do {
        $slug = '';
        for ($i = 0; $i < $length; $i++) {
            $slug .= $characters[rand(0, $charactersLength - 1)];
        }
        
        // Check if slug already exists for this user
        if ($userId) {
            $stmt = $conn->prepare("SELECT id FROM links WHERE slug = ? AND user_id = ?");
            $stmt->bind_param("si", $slug, $userId);
        } else {
            $stmt = $conn->prepare("SELECT id FROM links WHERE slug = ?");
            $stmt->bind_param("s", $slug);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        
        $attempts++;
    } while ($exists && $attempts < 10);
    
    // If we couldn't generate a unique slug in 10 attempts, try with a longer length
    if ($exists) {
        return generateSlug($length + 1, $userId);
    }
    
    return $slug;
}

/**
 * Creates a new short link in the database
 * 
 * @param string $destinationUrl The destination URL
 * @param int $userId The user ID
 * @param array $options Optional parameters (steps, customSlug)
 * @return array|false The created link data or false on failure
 */
function createLink($destinationUrl, $userId, $options = []) {
    global $conn;
    
    // Set default options
    $defaults = [
        'steps' => 1,
        'customSlug' => null
    ];
    $options = array_merge($defaults, $options);
    
    // Validate steps (between 1 and 3)
    $steps = max(1, min(3, intval($options['steps'])));
    
    // Generate or use custom slug
    $slug = $options['customSlug'] ?: generateSlug(5, $userId);
    
    // Check if slug is available for this user
    if (!isSlugAvailableForUser($slug, $userId)) {
        if ($options['customSlug']) {
            return false; // Custom slug already taken by this user
        }
        // If it was auto-generated, try again with new options
        return createLink($destinationUrl, $userId, $options);
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO links (destination_url, slug, user_id, steps) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $destinationUrl, $slug, $userId, $steps);
    
    if ($stmt->execute()) {
        $linkId = $stmt->insert_id;
        $username = getUsernameById($userId);
        return [
            'id' => $linkId,
            'destination_url' => $destinationUrl,
            'slug' => $slug,
            'steps' => $steps,
            'full_url' => getFullUrl($username, $slug)
        ];
    }
    
    return false;
}

/**
 * Gets a link by its slug and optionally username
 * 
 * @param string $slug The slug to look up
 * @param string|null $username Optional username for new URL format
 * @return array|false The link data or false if not found
 */
function getLinkBySlug($slug, $username = null) {
    global $conn;
    
    if ($username) {
        // First check if user exists
        $userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $userStmt->bind_param("s", $username);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        
        if ($userResult->num_rows === 0) {
            // User doesn't exist
            return false;
        }
        
        $userId = $userResult->fetch_assoc()['id'];
        
        // Now look for the link with this user's ID
        $stmt = $conn->prepare("SELECT * FROM links WHERE user_id = ? AND slug = ?");
        $stmt->bind_param("is", $userId, $slug);
    } else {
        // Legacy format: just slug
        $stmt = $conn->prepare("SELECT * FROM links WHERE slug = ?");
        $stmt->bind_param("s", $slug);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    return $result->fetch_assoc();
}

/**
 * Increments the click counter for a link
 * 
 * @param string $slug The slug to update
 * @return bool Success or failure
 */
function incrementClicks($slug) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE links SET clicks = clicks + 1 WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    return $stmt->execute();
}

/**
 * Gets all links from the database
 * 
 * @return array Array of all links
 */
function getAllLinks() {
    global $conn;
    
    $result = $conn->query("SELECT * FROM links ORDER BY created_at DESC");
    $links = [];
    
    while ($row = $result->fetch_assoc()) {
        $links[] = $row;
    }
    
    return $links;
}

/**
 * Validates if a user is authenticated
 * 
 * @return bool True if authenticated, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Gets the base URL of the application
 * 
 * @return string The base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return "$protocol://$host";
}

/**
 * Get the full URL for a shortened link
 * 
 * @param string $username The username
 * @param string $slug The slug
 * @return string The full URL
 */
function getFullUrl($username, $slug) {
    return getBaseUrl() . "/go/" . $username . "/" . $slug;
}

/**
 * Gets ad script for a specific user and type
 * 
 * @param int $userId The user ID to get script for
 * @param string $adType The type of ad script to get
 * @return string The ad script or empty string
 */
function getAdScript($userId, $adType) {
    global $conn;
    
    // Get user's ad settings
    $stmt = $conn->prepare("SELECT * FROM user_ad_settings WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $userSettings = $stmt->get_result()->fetch_assoc();
    
    // If no settings found, create default settings
    if (!$userSettings) {
        require_once __DIR__ . '/includes/admin_functions.php';
        createDefaultAdSettings($userId);
        
        // Get the newly created settings
        $stmt->execute();
        $userSettings = $stmt->get_result()->fetch_assoc();
    }
    
    // If using default ads, get admin's ad scripts
    if ($userSettings['is_using_default']) {
        $stmt = $conn->prepare("SELECT * FROM user_ad_settings WHERE user_id = 1");
        $stmt->execute();
        $adminSettings = $stmt->get_result()->fetch_assoc();
        
        // If admin settings exist and have the requested ad script
        if ($adminSettings && !empty($adminSettings[$adType . '_script'])) {
            return $adminSettings[$adType . '_script'];
        }
        return ''; // Return empty if no admin script found
    }
    
    // Return user's specific ad script if not using default
    return !empty($userSettings[$adType . '_script']) ? $userSettings[$adType . '_script'] : '';
}

/**
 * Updates ad settings for a user
 * 
 * @param int $userId The user ID to update settings for
 * @param array $settings The settings to update
 * @return bool Success or failure
 */
function updateUserAdSettings($userId, $settings) {
    global $conn;
    
    $sql = "INSERT INTO user_ad_settings (
        user_id, 
        social_bar_script,
        popunder_script,
        native_banner_script,
        banner_300x250_script,
        banner_728x90_script,
        banner_320x50_script,
        direct_link_1,
        direct_link_2,
        direct_link_3,
        direct_link_4,
        direct_link_5,
        is_using_default
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        social_bar_script = VALUES(social_bar_script),
        popunder_script = VALUES(popunder_script),
        native_banner_script = VALUES(native_banner_script),
        banner_300x250_script = VALUES(banner_300x250_script),
        banner_728x90_script = VALUES(banner_728x90_script),
        banner_320x50_script = VALUES(banner_320x50_script),
        direct_link_1 = VALUES(direct_link_1),
        direct_link_2 = VALUES(direct_link_2),
        direct_link_3 = VALUES(direct_link_3),
        direct_link_4 = VALUES(direct_link_4),
        direct_link_5 = VALUES(direct_link_5),
        is_using_default = VALUES(is_using_default)";
        
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssssi",
        $userId,
        $settings['social_bar_script'],
        $settings['popunder_script'],
        $settings['native_banner_script'],
        $settings['banner_300x250_script'],
        $settings['banner_728x90_script'],
        $settings['banner_320x50_script'],
        $settings['direct_link_1'],
        $settings['direct_link_2'],
        $settings['direct_link_3'],
        $settings['direct_link_4'],
        $settings['direct_link_5'],
        $settings['is_using_default']
    );
    
    return $stmt->execute();
}

/**
 * Get username by user ID
 * 
 * @param int $userId The user ID
 * @return string|null The username or null if not found
 */
function getUsernameById($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result ? $result['username'] : null;
}

/**
 * Check if a slug is available for a user
 * 
 * @param string $slug The slug to check
 * @param int $userId The user ID
 * @return bool True if available, false if taken
 */
function isSlugAvailableForUser($slug, $userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM links WHERE slug = ? AND user_id = ?");
    $stmt->bind_param("si", $slug, $userId);
    $stmt->execute();
    return $stmt->get_result()->num_rows === 0;
}
?> 


