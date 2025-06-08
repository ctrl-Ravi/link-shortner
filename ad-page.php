<?php
session_start();

// This is a template file that can be included by go.php
// It shouldn't be accessed directly

// Include required files
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

// Get link ID from query parameters
$linkId = $_GET['link_id'] ?? null;

// Verify the link ID matches the one in session
if (!$linkId || !isset($_SESSION['current_link_id']) || $_SESSION['current_link_id'] != $linkId) {
    header('Location: ' . getBaseUrl());
    exit;
}

// Get link details
$stmt = $conn->prepare("SELECT * FROM links WHERE id = ?");
$stmt->bind_param("i", $linkId);
$stmt->execute();
$link = $stmt->get_result()->fetch_assoc();

if (!$link) {
    header('Location: ' . getBaseUrl());
    exit;
}

// Set current step (default to 1)
$currentStep = $_GET['step'] ?? 1;
$currentStep = max(1, min($link['steps'], intval($currentStep)));

// Set next URL based on current step
if ($currentStep >= $link['steps']) {
    $nextUrl = $link['destination_url'];
} else {
    $nextUrl = getBaseUrl() . "/ad-page.php?link_id=" . $link['id'] . "&step=" . ($currentStep + 1);
}

// Get user's ad settings
$stmt = $conn->prepare("SELECT * FROM user_ad_settings WHERE user_id = ?");
$stmt->bind_param("i", $link['user_id']);
$stmt->execute();
$adSettings = $stmt->get_result()->fetch_assoc();

// If user is using default ads or settings not found, get default settings
if (!$adSettings || $adSettings['is_using_default']) {
    $stmt = $conn->prepare("SELECT * FROM user_ad_settings WHERE user_id = 1");
    $stmt->execute();
    $adSettings = $stmt->get_result()->fetch_assoc();
}

// Collect all non-empty direct links
$directLinkAds = [];
for ($i = 1; $i <= 5; $i++) {
    if (!empty($adSettings["direct_link_$i"])) {
        $directLinkAds[] = $adSettings["direct_link_$i"];
    }
}

// If no direct links are set, use a default one
if (empty($directLinkAds)) {
    $directLinkAds[] = getBaseUrl() . "/ad-redirect.php?next=" . urlencode($nextUrl) . "&slug=" . urlencode($link['slug']);
}

// Randomly select a direct link ad
$randomDirectLinkAd = $directLinkAds[array_rand($directLinkAds)];

// Set the page title based on the destination
$fileExtension = pathinfo(parse_url($link['destination_url'], PHP_URL_PATH), PATHINFO_EXTENSION);
$fileExtension = !empty($fileExtension) ? strtoupper($fileExtension) : 'File';

// Default to "File" if we can't determine the type
$contentType = "File";

// Determine content type based on URL or extension
if (strpos($link['destination_url'], 'youtube') !== false || strpos($link['destination_url'], 'vimeo') !== false) {
    $contentType = "Video";
} elseif (strpos($link['destination_url'], 'mp3') !== false || strpos($link['destination_url'], 'spotify') !== false) {
    $contentType = "Audio";
} elseif (strpos($link['destination_url'], 'jpg') !== false || strpos($link['destination_url'], 'png') !== false || strpos($link['destination_url'], 'image') !== false) {
    $contentType = "Image";
} elseif (strpos($link['destination_url'], 'pdf') !== false) {
    $contentType = "PDF";
} elseif (strpos($link['destination_url'], 'zip') !== false || strpos($link['destination_url'], 'rar') !== false) {
    $contentType = "Archive";
} elseif (strpos($link['destination_url'], 'doc') !== false || strpos($link['destination_url'], 'txt') !== false) {
    $contentType = "Document";
} elseif (!empty($fileExtension) && $fileExtension != 'html' && $fileExtension != 'php') {
    $contentType = $fileExtension;
}

$pageTitle = "Your $contentType is Ready • Step $currentStep of {$link['steps']}";

// Set cookie to track returning visitors
$isReturningVisitor = isset($_COOKIE['visited_before']) ? true : false;
setcookie('visited_before', '1', time() + (86400 * 30), "/"); // 30 days cookie

// Create URLs for different scenarios
$adRedirectUrl = getBaseUrl() . "/ad-redirect.php?next=" . urlencode($nextUrl) . "&slug=" . urlencode($link['slug']);
$skipAdUrl = $randomDirectLinkAd . (strpos($randomDirectLinkAd, '?') !== false ? '&' : '?') . 
             "next=" . urlencode($nextUrl) . 
             "&slug=" . urlencode($link['slug']) . 
             "&from_direct=1";

// Progress percentage calculation
$progressPercentage = (($currentStep - 1) / $link['steps']) * 100;

// Generate random download tips
$downloadTips = [
    "Click on any ad to support our free service",
    "Your download is almost ready",
    "Please support our sponsors to keep this service free",
    "Your file will be available after this step",
    "Thank you for your patience",
    "We're preparing your content",
    "Just one more step to access your content",
    "Your download link will appear shortly"
];

shuffle($downloadTips);
$randomTip = $downloadTips[0];

// Check if user is returning from direct link ad
$fromDirectAd = isset($_GET['from_direct']) && $_GET['from_direct'] == '1';
if ($fromDirectAd) {
    // Increment a counter in session to track direct ad views
    if (!isset($_SESSION['direct_ad_views'])) {
        $_SESSION['direct_ad_views'] = 0;
    }
    $_SESSION['direct_ad_views']++;
    
    // After viewing 2 direct ads, proceed to next step
    if ($_SESSION['direct_ad_views'] >= 2) {
        $_SESSION['direct_ad_views'] = 0; // Reset counter
        // For intermediate steps, create the next step URL
        if ($currentStep < $link['steps']) {
            $nextUrl = getBaseUrl() . "/go/" . $link['slug'] . "?step=" . ($currentStep + 1);
        }
        header('Location: ' . $nextUrl);
        exit;
    } else {
        // Show another direct ad
        $nextDirectAd = $directLinkAds[array_rand($directLinkAds)];
        $redirectUrl = $nextDirectAd . (strpos($nextDirectAd, '?') !== false ? '&' : '?') . 
                      "next=" . urlencode($nextUrl) . 
                      "&slug=" . urlencode($link['slug']) . 
                      "&from_direct=1";
        header('Location: ' . $redirectUrl);
        exit;
    }
}

// Add more engaging messages based on step and content type
$stepMessages = [
    [
        "title" => "🎯 Get Access Now",
        "subtitle" => "Scroll Down to Continue"
    ],
    [
        "title" => "⚡ Almost Done",
        "subtitle" => "Keep Going!"
    ],
    [
        "title" => "✨ Last Step",
        "subtitle" => "You're Close!"
    ]
];
$currentStepMsg = $currentStep <= count($stepMessages) ? $stepMessages[$currentStep - 1] : $stepMessages[0];

// Get user ID from the link
$userId = $link['user_id'];

// Get ad scripts
$socialBarScript = getAdScript($userId, 'social_bar');
$popunderScript = getAdScript($userId, 'popunder');
$nativeBannerScript = getAdScript($userId, 'native_banner');
$banner300x250Script = getAdScript($userId, 'banner_300x250');
$banner728x90Script = getAdScript($userId, 'banner_728x90');
$banner320x50Script = getAdScript($userId, 'banner_320x50');
$banner468x60Script = getAdScript($userId, 'banner_468x60');
$banner160x300Script = getAdScript($userId, 'banner_160x300');
$banner160x600Script = getAdScript($userId, 'banner_160x600');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= getBaseUrl() ?>/style.css">
    <script src="<?= getBaseUrl() ?>/scripts.js"></script>
    <style>
        /* Modern, responsive layout */
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #818cf8;
            --accent-color: #c7d2fe;
            --text-color: #1f2937;
            --light-text: #6b7280;
            --bg-color: #f9fafb;
            --card-bg: #ffffff;
            --border-color: #e5e7eb;
            --success-color: #10b981;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            max-width: 100%;
            padding: 0;
            margin: 0 auto;
        }
        
        .content-wrapper {
            max-width: 100%;
            padding: 15px;
            margin: 0 auto;
        }
        
        @media (min-width: 768px) {
            .content-wrapper {
                max-width: 750px;
                padding: 20px;
            }
        }
        
        .progress-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: var(--card-bg);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 10px 15px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 15px 0;
            gap: 10px;
        }
        
        .step {
            flex: 1;
            max-width: 150px;
            text-align: center;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            position: relative;
            background: #f3f4f6;
            color: #6b7280;
            transition: all 0.3s ease;
        }
        
        .step.completed {
            background: #dcfce7;
            color: #059669;
        }
        
        .step.active {
            background: #818cf8;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 4px 6px rgba(129, 140, 248, 0.2);
        }
        
        .step.active::after {
            content: "";
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid #818cf8;
        }
        
        .progress-bar-container {
            height: 4px;
            background-color: var(--border-color);
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            background-color: var(--primary-color);
            width: <?= $progressPercentage ?>%;
            transition: width 0.5s ease;
        }
        
        .header {
            margin-top: 70px;
            text-align: center;
            padding: 20px 0;
        }
        
        .content-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .content-meta {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 20px;
        }
        
        .content-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .download-area {
            background-color: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            text-align: center;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .download-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .download-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        .timer-container {
            margin: 20px 0;
            text-align: center;
        }
        
        .timer-text {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 10px;
        }
        
        .timer-number {
            font-size: 28px;
            font-weight: bold;
            color: var(--primary-color);
            background: rgba(79, 70, 229, 0.1);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .download-btn {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        
        .download-btn:disabled {
            background-color: var(--light-text);
            cursor: not-allowed;
        }
        
        .download-btn:hover:not(:disabled) {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        
        .skip-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 12px;
            transition: all 0.3s ease;
        }
        
        .skip-btn:hover {
            background-color: rgba(79, 70, 229, 0.1);
        }
        
        .ad-container {
            margin: 25px 0;
            text-align: center;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background: var(--card-bg);
            padding: 10px;
            transition: transform 0.3s ease;
        }
        
        .ad-container:hover {
            transform: translateY(-3px);
        }
        
        .ad-label {
            font-size: 12px;
            color: var(--light-text);
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .ad-container.featured {
            border: 1px solid var(--accent-color);
            background-color: rgba(199, 210, 254, 0.1);
        }
        
        .ad-container.in-content {
            float: none;
            margin: 20px auto;
            max-width: 100%;
        }
        
        @media (min-width: 768px) {
            .ad-container.in-content {
                float: right;
                margin: 0 0 20px 20px;
                max-width: 300px;
            }
        }
        
        .tip-box {
            background-color: rgba(79, 70, 229, 0.1);
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .tip-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
        }
        
        .tip-title svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
        }
        
        .tip-content {
            color: var(--text-color);
            font-size: 14px;
        }
        
        .file-info {
            display: flex;
            align-items: center;
            background-color: rgba(79, 70, 229, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .file-icon {
            font-size: 24px;
            margin-right: 15px;
            color: var(--primary-color);
            background: rgba(79, 70, 229, 0.1);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        
        .file-details {
            flex: 1;
        }
        
        .file-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .file-meta {
            font-size: 12px;
            color: var(--light-text);
        }
        
        .visual-cue {
            position: absolute;
            animation: pulse 2s infinite;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: rgba(79, 70, 229, 0.3);
            pointer-events: none;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0;
            }
        }
        
        .sponsor-message {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            background-color: rgba(79, 70, 229, 0.05);
            border-radius: 8px;
            font-size: 14px;
            color: var(--light-text);
        }
        
        .sponsor-message strong {
            color: var(--primary-color);
        }
        
        .related-downloads {
            margin-top: 30px;
        }
        
        .related-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }
        
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 15px;
        }
        
        .related-item {
            background: var(--card-bg);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .related-item:hover {
            transform: translateY(-3px);
        }
        
        .related-item a {
            text-decoration: none;
            color: var(--text-color);
            display: block;
        }
        
        .related-thumb {
            height: 100px;
            background-color: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .related-thumb-icon {
            font-size: 32px;
            color: var(--primary-color);
        }
        
        .related-info {
            padding: 10px;
        }
        
        .related-name {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .related-type {
            font-size: 12px;
            color: var(--light-text);
        }
        
        .success-message {
            color: var(--success-color);
            font-weight: 500;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: var(--light-text);
            padding: 20px 0;
            border-top: 1px solid var(--border-color);
        }
        
        /* Add these new styles */
        .timer-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
            margin: 20px 0;
            position: relative;
        }
        
        .timer-section {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        
        .timer-content {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .timer-number {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        
        .timer-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .download-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .download-btn:disabled {
            background-color: #6b7280;
            cursor: not-allowed;
        }
        
        .skip-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: transparent;
            color: #4f46e5;
            border: 1px solid #4f46e5;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .skip-btn:hover {
            background-color: rgba(79, 70, 229, 0.1);
        }
        
        .ad-container {
            margin: 15px 0;
            text-align: center;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            background: #ffffff;
            padding: 10px;
            transition: transform 0.3s ease;
        }
        
        .ad-container.featured {
            border: 1px solid #c7d2fe;
            background-color: rgba(199, 210, 254, 0.1);
        }
        
        /* Infinite Scroll Ads */
        .infinite-ads {
            margin-top: 30px;
            padding-bottom: 50px;
        }
        
        .scroll-indicator {
            text-align: center;
            padding: 20px;
            color: #4f46e5;
            font-size: 18px;
            font-weight: bold;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        
        /* Enhanced Mobile Responsiveness */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 10px;
            }
            
            .progress-container {
                padding: 8px;
            }
            
            .step {
                font-size: 12px;
                padding: 10px 5px;
            }
            
            .content-title {
                font-size: 20px;
                line-height: 1.4;
            }
            
            .content-meta {
                font-size: 13px;
            }
            
            .timer-section {
                margin: 15px -10px;
                border-radius: 0;
                padding: 15px 10px;
                background: rgba(255, 255, 255, 0.98);
            }
            
            .ad-container {
                margin: 10px -10px;
                border-radius: 0;
            }
            
            .ad-container.featured {
                margin: 15px -10px;
                padding: 15px 10px;
            }
            
            .scroll-indicator {
                font-size: 16px;
                padding: 15px;
            }
            
            .file-info {
                margin: 15px -10px;
                border-radius: 0;
                padding: 12px;
            }
        }
        
        /* Enhanced Ad Container Styles */
        .ad-sticky {
            position: sticky;
            top: 70px;
            z-index: 998;
            background: white;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .ad-float-left, .ad-float-right {
            display: none;
        }
        
        @media (min-width: 1200px) {
            .ad-float-left, .ad-float-right {
                display: block;
                position: fixed;
                top: 50%;
                transform: translateY(-50%);
                width: 160px;
            }
            
            .ad-float-left {
                left: 10px;
            }
            
            .ad-float-right {
                right: 10px;
            }
        }
        
        .download-progress {
            height: 4px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 2px;
            margin: 10px 0;
            overflow: hidden;
        }
        
        .download-progress-bar {
            height: 100%;
            background: #4f46e5;
            width: 0%;
            transition: width 0.5s ease;
            animation: progress 15s linear forwards;
        }
        
        @keyframes progress {
            0% { width: 0; }
            100% { width: 100%; }
        }
        
        .content-alert {
            background: rgba(79, 70, 229, 0.1);
            border-left: 4px solid #4f46e5;
            padding: 15px;
            margin: 15px 0;
            border-radius: 0 8px 8px 0;
            font-size: 14px;
            color: #1f2937;
        }
        
        /* Direct Link Areas */
        .direct-link-area {
            cursor: pointer;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: rgba(79, 70, 229, 0.05);
            transition: all 0.3s ease;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .direct-link-area::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(79, 70, 229, 0.1), transparent);
            z-index: 1;
        }
        
        .direct-link-area:hover {
            background: rgba(79, 70, 229, 0.1);
        }

        .fake-download-button {
            background: linear-gradient(45deg, #4f46e5, #818cf8);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            margin: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .fake-download-button:hover {
            transform: translateY(-2px);
        }

        .download-icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .download-text {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .file-size {
            font-size: 12px;
            opacity: 0.8;
        }

        .scroll-message {
            text-align: center;
            padding: 15px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 8px;
            margin: 20px 0;
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            animation: bounce 2s infinite;
        }

        .fake-progress-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            cursor: pointer;
            text-align: center;
        }

        .progress-icon {
            font-size: 24px;
            margin-bottom: 10px;
            animation: spin 2s linear infinite;
        }

        .progress-text {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
            margin-bottom: 10px;
        }

        .fake-progress-bar {
            height: 4px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin: 10px 0;
            position: relative;
        }

        .fake-progress-bar::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 30%;
            background: #4f46e5;
            animation: progress 2s ease infinite;
        }

        .progress-subtext {
            font-size: 14px;
            color: #6b7280;
            text-decoration: underline;
        }

        .timer-section.compact {
            max-width: 300px;
            margin: 20px auto;
            padding: 15px;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        @keyframes progress {
            0% { left: -30%; }
            100% { left: 100%; }
        }
    </style>
    <script>
        // Countdown timer
        let secondsLeft = <?= $isReturningVisitor ? "10" : "15" ?>; // Shorter timer for returning visitors
        let timerComplete = false;
        
        function updateTimer() {
            const timerElement = document.getElementById('timer');
            const downloadBtn = document.getElementById('download-btn');
            
            if (timerElement && downloadBtn) {
                timerElement.textContent = secondsLeft;
                
                if (secondsLeft <= 0) {
                    timerComplete = true;
                    downloadBtn.disabled = false;
                    downloadBtn.href = downloadBtn.getAttribute('data-final-url');
                    document.getElementById('countdown').innerHTML = "<span class='success-message'>Your download is ready!</span>";
                    document.getElementById('skip-btn').style.display = 'none';
                    
                    // Show visual cue
                    const visualCue = document.createElement('div');
                    visualCue.className = 'visual-cue';
                    downloadBtn.parentNode.appendChild(visualCue);
                } else {
                    secondsLeft--;
                    setTimeout(updateTimer, 1000);
                }
            }
        }
        
        // Initialize animations and timer
        window.onload = function() {
            // Start timer
            updateTimer();
            
            // Prevent back button
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function () {
                history.pushState(null, null, document.URL);
            });
        };
    </script>
    
    <!-- Adsterra Popunder -->
    <?php if (!empty($popunderScript)): ?>
        <?= $popunderScript ?>
    <?php endif; ?>
</head>
<body>
    <!-- Adsterra Social Bar -->
    <?php if (!empty($socialBarScript)): ?>
        <?= $socialBarScript ?>
    <?php endif; ?>

    <!-- Top Banner Ad -->
    <?php if (!empty($banner320x50Script)): ?>
    <div class="ad-container">
        <div class="ad-label">SPONSOR</div>
        <?= $banner320x50Script ?>
    </div>
    <?php endif; ?>

    <!-- Left Floating Ad -->
    <?php if (!empty($banner160x600Script)): ?>
    <div class="ad-float-left">
        <div class="ad-container">
            <div class="ad-label">SPONSOR</div>
            <?= $banner160x600Script ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Right Floating Ad -->
    <?php if (!empty($banner160x600Script)): ?>
    <div class="ad-float-right">
        <div class="ad-container">
            <div class="ad-label">SPONSOR</div>
            <?= $banner160x600Script ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Progress indicator at top -->
    <div class="progress-container">
        <div class="step-indicator">
            <?php for ($i = 1; $i <= $link['steps']; $i++): ?>
                <div class="step <?= $i < $currentStep ? 'completed' : ($i == $currentStep ? 'active' : '') ?>">
                    <?php if ($i < $currentStep): ?>
                        Step <?= $i ?> - Completed
                    <?php elseif ($i == $currentStep): ?>
                        Current Step <?= $i ?>
                    <?php else: ?>
                        Step <?= $i ?>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar-fill"></div>
        </div>
    </div>
    
    <div class="main-container">
        <div class="content-wrapper">
            <!-- Initial Scroll Message -->
            <div class="scroll-message" style="font-size: 20px; padding: 20px;">
                ⬇️ Your Download is Waiting Below ⬇️
            </div>

            <!-- Top Featured Ad -->
            <?php if (!empty($banner300x250Script)): ?>
            <div class="ad-container featured">
                <div class="ad-label">PREMIUM CONTENT</div>
                <?= $banner300x250Script ?>
            </div>
            <?php endif; ?>

            <!-- First Fake Download Button -->
            <div class="fake-download-button" onclick="window.location.href='<?= $randomDirectLinkAd ?>'">
                <div class="download-icon">⚡</div>
                <div class="download-text">Fast Download</div>
                <div class="file-size">Premium Speed</div>
            </div>

            <!-- Native Ad 1 -->
            <?php if (!empty($nativeBannerScript)): ?>
            <div class="ad-container">
                <div class="ad-label">RECOMMENDED</div>
                <?= $nativeBannerScript ?>
            </div>
            <?php endif; ?>

            <!-- Second Scroll Message -->
            <div class="scroll-message" style="background: rgba(79, 70, 229, 0.15);">
                ⏳ Almost There - Keep Scrolling ⏳
            </div>

            <!-- Banner Ad -->
            <?php if (!empty($banner320x50Script)): ?>
            <div class="ad-container">
                <div class="ad-label">SPONSOR</div>
                <?= $banner320x50Script ?>
            </div>
            <?php endif; ?>

            <!-- Fake Progress Section -->
            <div class="fake-progress-section" onclick="window.location.href='<?= $randomDirectLinkAd ?>'">
                <div class="progress-icon">📥</div>
                <div class="progress-text">Download Server Ready</div>
                <div class="fake-progress-bar"></div>
                <div class="progress-subtext">Click to Connect to Fast Server</div>
            </div>

            <!-- Featured Ad 1 -->
            <?php if (!empty($banner300x250Script)): ?>
            <div class="ad-container">
                <div class="ad-label">FEATURED CONTENT</div>
                <?= $banner300x250Script ?>
            </div>
            <?php endif; ?>

            <!-- Second Fake Download Button -->
            <div class="fake-download-button" onclick="window.location.href='<?= $randomDirectLinkAd ?>'" style="background: linear-gradient(45deg, #10b981, #059669);">
                <div class="download-icon">🚀</div>
                <div class="download-text">Premium Download</div>
                <div class="file-size">High Speed</div>
            </div>

            <!-- Native Ad 2 -->
            <?php if (!empty($nativeBannerScript)): ?>
            <div class="ad-container">
                <div class="ad-label">TRENDING NOW</div>
                <?= $nativeBannerScript ?>
            </div>
            <?php endif; ?>

            <!-- Final Scroll Message Before Timer -->
            <div class="scroll-message" style="background: linear-gradient(45deg, rgba(79, 70, 229, 0.1), rgba(129, 140, 248, 0.1));">
                ⬇️ Your Download Timer is Below ⬇️
            </div>

            <!-- Timer Section -->
            <div class="timer-section compact">
                <div class="timer-content">
                    <div class="timer-number" id="timer"><?= $isReturningVisitor ? "10" : "15" ?></div>
                    <div class="download-progress">
                        <div class="download-progress-bar"></div>
                    </div>
                    <div id="countdown" class="timer-text">
                        Scroll down while waiting...
                    </div>
                    
                    <a href="<?= $randomDirectLinkAd ?>" id="download-btn" class="download-btn" data-final-url="<?= $adRedirectUrl ?>">
                        Continue
                    </a>
                    
                    <a href="<?= $skipAdUrl ?>" id="skip-btn" class="skip-btn">
                        Skip (View Sponsor)
                    </a>
                </div>
            </div>
            
            <!-- Featured Ad 2 -->
            <?php if (!empty($banner300x250Script)): ?>
            <div class="ad-container">
                <div class="ad-label">SPECIAL OFFER</div>
                <?= $banner300x250Script ?>
            </div>
            <?php endif; ?>
            
            <!-- Additional Native Ad -->
            <?php if (!empty($nativeBannerScript)): ?>
            <div class="ad-container">
                <div class="ad-label">RECOMMENDED CONTENT</div>
                <?= $nativeBannerScript ?>
            </div>
            <?php endif; ?>
            
            <!-- Bottom Banner Ad -->
            <?php if (!empty($banner320x50Script)): ?>
            <div class="ad-container">
                <div class="ad-label">SPONSOR</div>
                <?= $banner320x50Script ?>
            </div>
            <?php endif; ?>
            
            <!-- Infinite Scroll Ads Section -->
            <div class="infinite-ads">
                <div class="scroll-indicator">
                    Scroll Down for More Content ⬇️
                </div>
                
                <?php for ($i = 0; $i < 5; $i++): ?>
                    <!-- Featured Ad -->
                    <?php if (!empty($banner300x250Script)): ?>
                    <div class="ad-container featured">
                        <div class="ad-label">SPONSOR</div>
                        <?= $banner300x250Script ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Native Ad -->
                    <?php if (!empty($nativeBannerScript)): ?>
                    <div class="ad-container">
                        <div class="ad-label">RECOMMENDED CONTENT</div>
                        <?= $nativeBannerScript ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Banner Ad -->
                    <?php if (!empty($banner320x50Script)): ?>
                    <div class="ad-container">
                        <div class="ad-label">SPONSOR</div>
                        <?= $banner320x50Script ?>
                    </div>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <div class="scroll-indicator">
                    Keep Scrolling for More ⬇️
                </div>
            </div>
            
            <footer>
                <p>© <?= date('Y') ?> File Hosting Service. All rights reserved.</p>
                <p>By using our service, you agree to our Terms of Service and Privacy Policy.</p>
                
                <!-- Footer Banner Ad -->
                <?php if (!empty($banner320x50Script)): ?>
                <div class="ad-container">
                    <div class="ad-label">SPONSOR</div>
                    <?= $banner320x50Script ?>
                </div>
                <?php endif; ?>
            </footer>
        </div>
    </div>
    
    <!-- Add more direct link areas throughout content -->
    <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="direct-link-area" onclick="window.location.href='<?= $randomDirectLinkAd ?>'">
            <div class="ad-label">SPONSORED</div>
            <p style="margin: 10px 0; font-size: 16px;">🔥 Premium Download Available</p>
        </div>
        
        <!-- Additional Popunder -->
        <?php if (!empty($popunderScript)): ?>
            <?= $popunderScript ?>
        <?php endif; ?>
    <?php endfor; ?>

    <!-- Additional Popunders -->
    <?php if (!empty($popunderScript)): ?>
        <?= $popunderScript ?>
    <?php endif; ?>
</body>
</html> 