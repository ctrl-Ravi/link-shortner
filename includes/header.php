<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $page_title ?? 'Pelupa Monetizer - Your Links, Your Ads, Your Revenue' ?></title>
    <meta name="description" content="<?= $page_description ?? 'Turn simple links into income with your own Adsterra ads. No commission, complete control over your earnings.' ?>">
    
    <!-- Prevent flash of wrong theme -->
    <script>
        // Immediately set the theme before the page renders
        (function() {
            const isDarkMode = localStorage.getItem('theme') === 'dark' || 
                (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDarkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= getBaseUrl() ?>/assets/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= getBaseUrl() ?>/assets/images/favicon.png">
    <link rel="apple-touch-icon" href="<?= getBaseUrl() ?>/assets/images/favicon.png">
    <meta name="theme-color" content="#ffffff">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    }
                }
            }
        }
    </script>
    
    <style>
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-image: linear-gradient(to right, #0284c7, #2563eb);
        }
        .dark .text-gradient {
            background-image: linear-gradient(to right, #38bdf8, #60a5fa);
        }
        .bg-gradient {
            background-image: linear-gradient(to right, #0284c7, #2563eb);
        }
        .dark .bg-gradient {
            background-image: linear-gradient(to right, #38bdf8, #60a5fa);
        }
        .card-shadow {
            box-shadow: 0 8px 30px rgb(0,0,0,0.12);
        }
        .dark .card-shadow {
            box-shadow: 0 8px 30px rgba(0,0,0,0.3);
        }
        .premium-pattern {
            background-image: 
                radial-gradient(at 40% 20%, rgba(14, 165, 233, 0.05) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(56, 189, 248, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(14, 165, 233, 0.05) 0px, transparent 50%),
                radial-gradient(at 80% 50%, rgba(56, 189, 248, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(14, 165, 233, 0.05) 0px, transparent 50%),
                radial-gradient(at 80% 100%, rgba(56, 189, 248, 0.05) 0px, transparent 50%),
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.05) 0px, transparent 50%);
        }
        .dark .premium-pattern {
            background-image: 
                radial-gradient(at 40% 20%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 0%, rgba(56, 189, 248, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 50%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 50%, rgba(56, 189, 248, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(14, 165, 233, 0.15) 0px, transparent 50%),
                radial-gradient(at 80% 100%, rgba(56, 189, 248, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 0%, rgba(14, 165, 233, 0.15) 0px, transparent 50%);
        }
        .hero-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 600px;
            background: radial-gradient(circle at 50% 0%, rgba(14, 165, 233, 0.15), transparent 70%);
            pointer-events: none;
        }
        .grid-pattern {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(14, 165, 233, 0.05) 1px, transparent 1px),
                         linear-gradient(to bottom, rgba(14, 165, 233, 0.05) 1px, transparent 1px);
        }
        .grid-pattern-dark {
            background-size: 40px 40px;
            background-image: linear-gradient(to right, rgba(14, 165, 233, 0.1) 1px, transparent 1px),
                         linear-gradient(to bottom, rgba(14, 165, 233, 0.1) 1px, transparent 1px);
        }
    </style>
    <?php if (isset($extra_styles)) echo $extra_styles; ?>
</head>
<body class="antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen premium-pattern dark:premium-pattern-dark">
    <!-- Navbar -->
    <nav class="fixed w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-lg z-50 border-b border-gray-200/50 dark:border-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= getBaseUrl() ?>" class="flex items-center space-x-2">
                        <span class="text-2xl">🔗</span>
                        <span class="font-bold text-xl bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary-400 dark:to-blue-400">
                            Pelupa Monetizer
                        </span>
                    </a>
                </div>
                
                <!-- Mobile menu button and theme toggle -->
                <div class="flex items-center space-x-2">
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                        </svg>
                        <svg class="w-6 h-6 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </button>
                    <div class="sm:hidden">
                        <button id="mobile-menu-button" class="p-2 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Desktop menu -->
                <div class="hidden sm:flex items-center space-x-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=dashboard" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400">
                            Dashboard
                        </a>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=links" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400">
                            Links
                        </a>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=analytics" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400">
                            Analytics
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=users" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:text-primary-600 dark:hover:text-primary-400">
                            Users
                        </a>
                        <?php endif; ?>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                                Account
                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="<?= getBaseUrl() ?>/admin.php?page=profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                    <a href="<?= getBaseUrl() ?>/admin.php?page=settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                                    <a href="<?= getBaseUrl() ?>/admin.php?logout=1" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= getBaseUrl() ?>/admin.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="sm:hidden hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=dashboard" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Dashboard
                        </a>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=links" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Links
                        </a>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=analytics" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Analytics
                        </a>
                        <?php if (isAdmin()): ?>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=users" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Users
                        </a>
                        <?php endif; ?>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=profile" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Profile
                        </a>
                        <a href="<?= getBaseUrl() ?>/admin.php?page=settings" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Settings
                        </a>
                        <a href="<?= getBaseUrl() ?>/admin.php?logout=1" class="block px-3 py-2 text-base font-medium text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Logout
                        </a>
                    <?php else: ?>
                        <a href="<?= getBaseUrl() ?>/admin.php" class="block px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-md">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            document.getElementById('mobile-menu-button').addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.toggle('hidden');
            });

            // Dark mode toggle
            const html = document.documentElement;
            const themeToggle = document.getElementById('theme-toggle');
            
            // Check for saved theme preference or system preference
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }

            // Toggle theme
            themeToggle.addEventListener('click', () => {
                html.classList.toggle('dark');
                localStorage.theme = html.classList.contains('dark') ? 'dark' : 'light';
            });
        });
    </script>

    <main class="pt-16">
        <?php if (isset($alertMessage) && !empty(trim($alertMessage))): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="rounded-lg p-4 <?= $alertType === 'success' ? 'bg-green-50 dark:bg-green-900/50 text-green-800 dark:text-green-200' : 'bg-red-50 dark:bg-red-900/50 text-red-800 dark:text-red-200' ?>">
                <?= htmlspecialchars($alertMessage) ?>
                <button type="button" class="float-right" onclick="this.parentElement.remove()">×</button>
            </div>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>