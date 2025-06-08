<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check file/directory permissions
function checkPermissions($path) {
    return [
        'path' => $path,
        'exists' => file_exists($path),
        'readable' => is_readable($path),
        'writable' => is_writable($path),
        'permissions' => file_exists($path) ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A'
    ];
}

// Function to check if a PHP extension is loaded
function checkExtension($name) {
    return [
        'name' => $name,
        'loaded' => extension_loaded($name),
        'version' => phpversion($name)
    ];
}

// Required files
$required_files = [
    'admin.php',
    'db.php',
    'functions.php',
    'includes/admin_functions.php',
    'includes/header.php',
    'includes/footer.php',
    'pages/dashboard.php',
    'pages/login.php'
];

// Required directories
$required_dirs = [
    'includes',
    'pages',
    'assets',
    'assets/css',
    'assets/js'
];

// Required PHP extensions
$required_extensions = [
    'mysqli',
    'session',
    'json',
    'mbstring',
    'openssl'
];

// Check PHP version and configuration
$php_info = [
    'version' => PHP_VERSION,
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'display_errors' => ini_get('display_errors')
];

// Check database connection
try {
    require_once 'db.php';
    $db_status = [
        'connected' => $conn->ping(),
        'server_info' => $conn->server_info,
        'character_set' => $conn->character_set_name()
    ];
} catch (Exception $e) {
    $db_status = [
        'connected' => false,
        'error' => $e->getMessage()
    ];
}

// Output results
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Check</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .section { margin-bottom: 30px; }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>System Check Results</h1>

    <div class="section">
        <h2>PHP Information</h2>
        <table>
            <tr><th>Setting</th><th>Value</th></tr>
            <?php foreach ($php_info as $key => $value): ?>
            <tr><td><?= htmlspecialchars($key) ?></td><td><?= htmlspecialchars($value) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>Database Status</h2>
        <table>
            <tr><th>Setting</th><th>Value</th></tr>
            <?php foreach ($db_status as $key => $value): ?>
            <tr><td><?= htmlspecialchars($key) ?></td><td><?= htmlspecialchars(print_r($value, true)) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>Required Files</h2>
        <table>
            <tr><th>File</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Permissions</th></tr>
            <?php foreach ($required_files as $file): ?>
            <?php $check = checkPermissions($file); ?>
            <tr>
                <td><?= htmlspecialchars($check['path']) ?></td>
                <td class="<?= $check['exists'] ? 'success' : 'error' ?>"><?= $check['exists'] ? 'Yes' : 'No' ?></td>
                <td class="<?= $check['readable'] ? 'success' : 'error' ?>"><?= $check['readable'] ? 'Yes' : 'No' ?></td>
                <td class="<?= $check['writable'] ? 'success' : 'error' ?>"><?= $check['writable'] ? 'Yes' : 'No' ?></td>
                <td><?= $check['permissions'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>Required Directories</h2>
        <table>
            <tr><th>Directory</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Permissions</th></tr>
            <?php foreach ($required_dirs as $dir): ?>
            <?php $check = checkPermissions($dir); ?>
            <tr>
                <td><?= htmlspecialchars($check['path']) ?></td>
                <td class="<?= $check['exists'] ? 'success' : 'error' ?>"><?= $check['exists'] ? 'Yes' : 'No' ?></td>
                <td class="<?= $check['readable'] ? 'success' : 'error' ?>"><?= $check['readable'] ? 'Yes' : 'No' ?></td>
                <td class="<?= $check['writable'] ? 'success' : 'error' ?>"><?= $check['writable'] ? 'Yes' : 'No' ?></td>
                <td><?= $check['permissions'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="section">
        <h2>Required PHP Extensions</h2>
        <table>
            <tr><th>Extension</th><th>Status</th><th>Version</th></tr>
            <?php foreach ($required_extensions as $ext): ?>
            <?php $check = checkExtension($ext); ?>
            <tr>
                <td><?= htmlspecialchars($check['name']) ?></td>
                <td class="<?= $check['loaded'] ? 'success' : 'error' ?>"><?= $check['loaded'] ? 'Loaded' : 'Not Loaded' ?></td>
                <td><?= $check['version'] ? htmlspecialchars($check['version']) : 'N/A' ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html> 