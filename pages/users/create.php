<?php if (!defined('ADMIN_ACCESS')) die('Direct access not permitted'); ?>

<div class="min-h-screen p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6" data-aos="fade-up">
        <h1 class="text-2xl font-bold text-gradient mb-4 sm:mb-0">
            Create New User
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
                        <div class="mb-6">
                            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Username</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                   id="username" name="username" required pattern="[a-zA-Z0-9-_]+" placeholder="johndoe">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Only letters, numbers, hyphens, and underscores allowed</p>
                        </div>

                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                   id="email" name="email" required placeholder="john@example.com">
                        </div>

                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                            <input type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                   id="password" name="password" required minlength="8">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Minimum 8 characters</p>
                        </div>

                        <div class="mb-6">
                            <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                            <select class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" 
                                    id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="editor">Editor</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li>User: Can create and manage their own links</li>
                                    <li>Editor: Can manage all links</li>
                                    <li>Admin: Full access to all features</li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="create_user" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Guidelines Card -->
        <div class="lg:col-span-1" data-aos="fade-up" data-aos-delay="100">
            <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden card-shadow">
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Creation Guidelines</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3 text-gray-700 dark:text-gray-300">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Choose a unique username</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Use a valid email address</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Create a strong password</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Assign appropriate role based on needs</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-primary-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Users can change their password later</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> 