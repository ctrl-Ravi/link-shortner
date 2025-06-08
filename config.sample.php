<?php
// Database configuration
$config = [
    'db' => [
        'host' => 'localhost',
        'username' => 'your_database_username',
        'password' => 'your_database_password',
        'database' => 'your_database_name'
    ],
    
    // Site configuration
    'site' => [
        'name' => 'Your Site Name',
        'url' => 'https://your-domain.com',
        'admin_email' => 'admin@your-domain.com'
    ],
    
    // Security settings
    'security' => [
        'session_lifetime' => 3600, // 1 hour
        'hash_algo' => PASSWORD_DEFAULT,
        'min_password_length' => 8
    ],
    
    // Link settings
    'links' => [
        'default_redirect_delay' => 5,
        'max_custom_length' => 20,
        'banned_slugs' => ['admin', 'login', 'register', 'api']
    ],
    
    // Ad settings
    'ads' => [
        'default_ad_steps' => 1,
        'max_ad_steps' => 3
    ]
];

// Error reporting (set to false in production)
$config['debug'] = true;

// Timezone
date_default_timezone_set('UTC');

return $config; 