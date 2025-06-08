<?php
if (!defined('ADMIN_ACCESS')) {
    die('Direct access not permitted');
}

$userId = getCurrentUserId();

// Get user data
$stmt = $conn->prepare("
    SELECT 
        u.*,
        COUNT(DISTINCT l.id) as total_links,
        SUM(l.clicks) as total_clicks
    FROM users u
    LEFT JOIN links l ON u.id = l.user_id
    WHERE u.id = ?
    GROUP BY u.id
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $email = $_POST['email'];
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        
        // Verify current password
        if (password_verify($currentPassword, $user['password'])) {
            $data = ['email' => $email];
            
            if (!empty($newPassword)) {
                $data['password'] = $newPassword;
            }
            
            if (updateUser($userId, $data)) {
                $alertType = 'success';
                $alertMessage = 'Profile updated successfully!';
                
                // Log the activity
                logUserActivity($userId, 'password_change', 'User updated their profile');
                
                // Refresh user data
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $alertType = 'danger';
                $alertMessage = 'Error updating profile.';
            }
        } else {
            $alertType = 'danger';
            $alertMessage = 'Current password is incorrect.';
        }
    }
}

// Get recent activity
$stmt = $conn->prepare("
    SELECT * FROM user_activity_logs 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 10
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-2">
            My Profile
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Manage your account settings and view activity
        </p>
    </div>

    <!-- Alert Message -->
    <?php if (isset($alertMessage)): ?>
    <div class="mb-6 rounded-lg p-4 <?= $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200' ?>" role="alert">
        <?= htmlspecialchars($alertMessage) ?>
        <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Form -->
        <div class="lg:col-span-2" data-aos="fade-up">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Profile Settings</h2>
                </div>
                <div class="p-6">
                    <form method="post" action="admin.php?page=profile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Username Field -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                                <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" 
                                       value="<?= htmlspecialchars($user['username']) ?>" readonly>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Username cannot be changed</p>
                            </div>

                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                       id="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Current Password Field -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                                <div class="relative">
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                           id="current_password" name="current_password" required>
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400" onclick="togglePasswordVisibility('current_password')">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="current_password_icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Required to make any changes</p>
                            </div>

                            <!-- New Password Field -->
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                                <div class="relative">
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                           id="new_password" name="new_password" minlength="8">
                                    <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 dark:text-gray-400" onclick="togglePasswordVisibility('new_password')">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="new_password_icon">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty to keep current password. Minimum 8 characters if changing.</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="update_profile" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Account Info and Activity -->
        <div class="lg:col-span-1">
            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow mb-6" data-aos="fade-up">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Account Information</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Role</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' : 
                                   ($user['role'] === 'editor' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200' : 
                                   'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200') ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Total Links</span>
                            <span class="font-medium text-gray-900 dark:text-white"><?= number_format($user['total_links']) ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Total Clicks</span>
                            <span class="font-medium text-gray-900 dark:text-white"><?= number_format($user['total_clicks'] ?? 0) ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Member Since</span>
                            <span class="font-medium text-gray-900 dark:text-white"><?= date('M j, Y', strtotime($user['created_at'])) ?></span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">Last Login</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                <?= $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h2>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($activities)): ?>
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        No recent activity
                    </div>
                    <?php else: ?>
                        <?php foreach ($activities as $activity): ?>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex items-center">
                                    <?php
                                    switch ($activity['activity_type']) {
                                        case 'login':
                                            echo '<svg class="w-4 h-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                  </svg> Login';
                                            break;
                                        case 'link_create':
                                            echo '<svg class="w-4 h-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                  </svg> Created Link';
                                            break;
                                        case 'link_edit':
                                            echo '<svg class="w-4 h-4 text-indigo-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                  </svg> Updated Link';
                                            break;
                                        case 'link_delete':
                                            echo '<svg class="w-4 h-4 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                  </svg> Deleted Link';
                                            break;
                                        case 'settings_update':
                                            echo '<svg class="w-4 h-4 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                  </svg> Updated Settings';
                                            break;
                                        case 'password_change':
                                            echo '<svg class="w-4 h-4 text-yellow-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                  </svg> Changed Password';
                                            break;
                                        default:
                                            echo '<svg class="w-4 h-4 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                  </svg> ' . ucfirst(str_replace('_', ' ', $activity['activity_type']));
                                    }
                                    ?>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <?= date('M j, Y H:i', strtotime($activity['created_at'])) ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 ml-6">
                                <?= htmlspecialchars($activity['description']) ?>
                            </p>
                            <?php if ($activity['ip_address']): ?>
                            <div class="mt-1 ml-6 flex items-center">
                                <svg class="w-3 h-3 text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <?= htmlspecialchars($activity['ip_address']) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
        `;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}
</script> 