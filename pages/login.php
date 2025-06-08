<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full" data-aos="fade-up">
        <!-- Login Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 card-shadow">
            <div class="text-center mb-8">
                <img src="<?= getBaseUrl() ?>/favicon.png" alt="Logo" class="mx-auto h-16 w-16 mb-4">
                <h2 class="text-2xl font-bold text-gradient">
                    Admin Login
                </h2>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Sign in to manage your links and earnings
                </p>
            </div>

            <form method="post" action="admin.php">
                <?php if (isset($alertMessage)): ?>
                <div class="mb-6 rounded-lg p-4 <?= $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200' ?>">
                    <?= htmlspecialchars($alertMessage) ?>
                </div>
                <?php endif; ?>

                <div class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Username
                        </label>
                        <div class="mt-1">
                            <input type="text" id="username" name="username" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                placeholder="Enter your username">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password
                        </label>
                        <div class="mt-1">
                            <input type="password" id="password" name="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white sm:text-sm"
                                placeholder="Enter your password">
                        </div>
                    </div>

                    <div>
                        <button type="submit" name="login" class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Login
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            Need help?
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="https://wa.me/917279062862" target="_blank" 
                        class="w-full flex justify-center items-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-5 h-5 mr-2 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        Contact Admin on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> 