<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-4 sm:mb-0">
            Edit User
        </h1>
        <a href="admin.php?page=users" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Users
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Card -->
        <div class="lg:col-span-2" data-aos="fade-up">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="p-6">
                    <form method="post" action="admin.php?page=users">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?? '' ?>">
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300" 
                                   value="<?= htmlspecialchars($user['username'] ?? '') ?>" readonly>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Username cannot be changed</p>
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                   id="email" name="email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                   id="password" name="password" minlength="8">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Leave empty to keep current password</p>
                        </div>

                        <div class="mb-6">
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                            <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white 
                                    <?= ($user['role'] ?? '') === 'admin' ? 'bg-gray-100 dark:bg-gray-700' : '' ?>" 
                                    id="role" name="role" required <?= ($user['role'] ?? '') === 'admin' ? 'disabled' : '' ?>>
                                <option value="user" <?= ($user['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="editor" <?= ($user['role'] ?? '') === 'editor' ? 'selected' : '' ?>>Editor</option>
                                <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">Admin role cannot be changed</p>
                            <?php else: ?>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>User: Can create and manage their own links</li>
                                    <li>Editor: Can manage all links</li>
                                    <li>Admin: Full access to all features</li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="edit_user" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
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
        
        <!-- Side Cards -->
        <div class="lg:col-span-1 space-y-6">
            <!-- User Statistics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Statistics</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Total Links:</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?= number_format($user['total_links'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Total Clicks:</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?= number_format($user['total_clicks'] ?? 0) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Created:</span>
                        <span class="font-semibold text-gray-900 dark:text-white"><?= isset($user['created_at']) ? date('M j, Y', strtotime($user['created_at'])) : 'N/A' ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Last Login:</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            <?= isset($user['last_login']) && $user['last_login'] ? date('M j, Y H:i', strtotime($user['last_login'])) : 'Never' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Account Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Account Status</h3>
                </div>
                <div class="p-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="account_status" class="sr-only peer" <?= ($user['is_active'] ?? false) ? 'checked' : '' ?>>
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Account Active</span>
                    </label>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        When disabled, user cannot log in or access any features
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$pageScripts = <<<EOT
<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountStatus = document.getElementById('account_status');
    accountStatus.addEventListener('change', function() {
        fetch('admin.php?action=toggle_user_status&id={$user['id']}', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = 'Account status has been updated successfully';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            } else {
                // Revert the checkbox and show error
                accountStatus.checked = !accountStatus.checked;
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                toast.textContent = data.message || 'Failed to update account status';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            }
        })
        .catch(error => {
            // Revert the checkbox and show error
            accountStatus.checked = !accountStatus.checked;
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.textContent = 'Failed to update account status';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2000);
        });
    });
});
</script>
EOT;
?> 