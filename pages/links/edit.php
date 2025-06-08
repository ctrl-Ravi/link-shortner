<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">
            Edit Link
        </h1>
        <a href="admin.php?page=links" 
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Links
        </a>
    </div>

    <?php if ($alertMessage): ?>
    <div class="mb-6 rounded-lg p-4 <?= $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200' ?>">
        <?= htmlspecialchars($alertMessage) ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <form method="post" action="admin.php?page=links" class="p-6">
                    <input type="hidden" name="link_id" value="<?= $link['id'] ?>">
                    
                    <div class="space-y-6">
                        <!-- Destination URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Destination URL
                            </label>
                            <div class="mt-1">
                                <input type="text" value="<?= htmlspecialchars($link['destination_url']) ?>" readonly
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm">
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Destination URL cannot be changed
                            </p>
                        </div>

                        <!-- Custom Slug -->
                        <div>
                            <label for="custom_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Custom Slug
                            </label>
                            <div class="mt-1">
                                <input type="text" id="custom_slug" name="custom_slug"
                                       value="<?= htmlspecialchars($link['slug']) ?>"
                                       pattern="[a-zA-Z0-9-_]+" required
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Only letters, numbers, hyphens, and underscores allowed
                            </p>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Category (Optional)
                            </label>
                            <div class="mt-1">
                                <select id="category_id" name="category_id"
                                        class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category['id'] === $link['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Expiration Date -->
                        <div>
                            <label for="expires_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Expiration Date (Optional)
                            </label>
                            <div class="mt-1">
                                <input type="datetime-local" id="expires_at" name="expires_at"
                                       value="<?= $link['expires_at'] ? date('Y-m-d\TH:i', strtotime($link['expires_at'])) : '' ?>"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Leave empty if the link should never expire
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" name="edit_link" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Side Cards -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Statistics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Link Statistics</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Clicks</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= number_format($link['clicks']) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Created</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white"><?= date('M j, Y', strtotime($link['created_at'])) ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Last Click</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                <?= $link['last_click'] ? date('M j, Y', strtotime($link['last_click'])) : 'Never' ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
                    <div class="space-y-3">
                        <button type="button" onclick="copyToClipboard('<?= getFullUrl($link['slug']) ?>')"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Copy Short URL
                        </button>
                        <a href="<?= getFullUrl($link['slug']) ?>" target="_blank"
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Open Link
                        </a>
                        <button type="button" onclick="confirmDelete('link', <?= $link['id'] ?>)"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 dark:border-red-600 rounded-lg text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
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