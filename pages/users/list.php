<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-4 sm:mb-0">
            Manage Users
        </h1>
        <a href="admin.php?page=users&action=new" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New User
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow" data-aos="fade-up">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Links</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Clicks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden xl:table-cell">Last Login</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No users found
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                <?= htmlspecialchars($user['username']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden md:table-cell">
                                <?= htmlspecialchars($user['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' : 
                                       ($user['role'] === 'editor' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200' : 
                                       'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200') ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden lg:table-cell">
                                <?= number_format($user['total_links']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden lg:table-cell">
                                <?= number_format($user['total_clicks'] ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden xl:table-cell">
                                <?php if ($user['last_login']): ?>
                                    <?= date('M j, Y H:i', strtotime($user['last_login'])) ?>
                                <?php else: ?>
                                    Never
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm hidden sm:table-cell">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $user['is_active'] ? 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200' : 
                                    'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200' ?>">
                                    <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <a href="admin.php?page=users&action=edit&id=<?= $user['id'] ?>" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <?php if ($user['role'] !== 'admin'): ?>
                                    <button type="button" onclick="confirmDelete('user', <?= $user['id'] ?>)"
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-0">
                    Showing <?= ($offset + 1) ?>-<?= min($offset + $perPage, $totalUsers) ?> of <?= $totalUsers ?> users
                </div>
                <nav class="flex justify-center">
                    <ul class="flex space-x-2">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li>
                            <a href="admin.php?page=users&page_num=<?= $i ?>" 
                               class="px-3 py-1 rounded-md text-sm <?= $i === $page ? 
                                'bg-primary-600 text-white dark:bg-primary-500' : 
                                'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(type, id) {
    if (confirm(`Are you sure you want to delete this ${type}?`)) {
        window.location.href = `admin.php?page=users&action=delete&id=${id}`;
    }
}
</script> 