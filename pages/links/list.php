<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">
            Manage Links
        </h1>
        <a href="admin.php?page=links&action=new" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Link
        </a>
    </div>

    <!-- Links Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Short URL</th>
                        <th class="hidden md:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Destination</th>
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Clicks</th>
                        <th class="hidden sm:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                        <th class="hidden lg:table-cell px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (empty($links)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            No links found. Create your first link to get started!
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($links as $link): 
                            $username = getUsernameById($link['user_id']);
                            $fullUrl = getFullUrl($username, $link['slug']);
                        ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="<?= $fullUrl ?>" target="_blank" 
                                       class="text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 text-sm truncate max-w-[150px] sm:max-w-[200px]">
                                        <?= htmlspecialchars($username) ?>/<?= htmlspecialchars($link['slug']) ?>
                                    </a>
                                    <button onclick="copyToClipboard('<?= $fullUrl ?>')" 
                                            class="p-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="hidden md:table-cell px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-gray-200 truncate max-w-[200px] lg:max-w-[300px]">
                                    <?= htmlspecialchars($link['destination_url']) ?>
                                </div>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($link['category_name'] ?? 'Uncategorized') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-gray-200">
                                    <?= number_format($link['clicks']) ?>
                                </span>
                            </td>
                            <td class="hidden sm:table-cell px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-gray-200">
                                    <?= date('M j, Y', strtotime($link['created_at'])) ?>
                                </span>
                            </td>
                            <td class="hidden lg:table-cell px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-gray-200">
                                    <?php if ($link['expires_at']): ?>
                                        <?= date('M j, Y', strtotime($link['expires_at'])) ?>
                                    <?php else: ?>
                                        Never
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="admin.php?page=links&action=edit&id=<?= $link['id'] ?>" 
                                       class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('link', <?= $link['id'] ?>)" 
                                            class="p-2 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <?= ($offset + 1) ?>-<?= min($offset + $perPage, $totalLinks) ?> of <?= $totalLinks ?> links
                </div>
                <nav class="flex justify-center">
                    <ul class="flex space-x-1">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li>
                            <a href="admin.php?page=links&page_num=<?= $i ?>" 
                               class="<?= $i === $page 
                                    ? 'bg-primary-600 text-white' 
                                    : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' ?> 
                                    relative inline-flex items-center px-4 py-2 text-sm font-medium rounded-md focus:z-10 focus:outline-none focus:ring-2 focus:ring-primary-500">
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
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show a toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-black/75 text-white px-4 py-2 rounded-lg shadow-lg';
        toast.textContent = 'Copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2000);
    });
}

function confirmDelete(type, id) {
    if (confirm('Are you sure you want to delete this link?')) {
        window.location.href = `admin.php?page=links&action=delete&id=${id}`;
    }
}
</script> 