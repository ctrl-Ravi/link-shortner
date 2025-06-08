<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="p-4 sm:p-6 lg:p-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-0">
            Create New Link
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
                    <div class="space-y-6">
                        <!-- Destination URL -->
                        <div>
                            <label for="destination" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Destination URL
                            </label>
                            <div class="mt-1">
                                <input type="url" id="destination" name="destination" required
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="https://example.com">
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Enter the full URL you want to shorten
                            </p>
                        </div>

                        <!-- Custom Slug -->
                        <div>
                            <label for="custom_slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Custom Slug (Optional)
                            </label>
                            <div class="mt-1">
                                <input type="text" id="custom_slug" name="custom_slug"
                                       pattern="[a-zA-Z0-9-_]+"
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                       placeholder="my-custom-url">
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Leave empty for auto-generated slug. Only letters, numbers, hyphens, and underscores allowed.
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
                                    <option value="<?= $category['id'] ?>">
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
                                       class="block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Leave empty if the link should never expire
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" name="create_link" 
                                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Create Link
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tips</h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Use descriptive slugs for better memorability</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Categorize links for easier management</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">Set expiration dates for temporary links</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm text-gray-600 dark:text-gray-300">URLs must start with http:// or https://</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> 