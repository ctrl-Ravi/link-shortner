<?php
session_start();
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelupa Monetizer - Your Links, Your Ads, Your Revenue</title>
    <meta name="description" content="Turn simple links into income with your own Adsterra ads. No commission, complete control over your earnings.">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- AOS Library -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    
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
                    },
                    animation: {
                        'gradient': 'gradient 8s linear infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        gradient: {
                            '0%, 100%': {
                                'background-size': '200% 200%',
                                'background-position': 'left center'
                            },
                            '50%': {
                                'background-size': '200% 200%',
                                'background-position': 'right center'
                            },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                    },
                },
            },
        }
    </script>
    
    <style type="text/tailwindcss">
        @layer utilities {
            .text-gradient {
                @apply bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary-400 dark:to-blue-400;
            }
            .bg-gradient {
                @apply bg-gradient-to-r from-primary-600 to-blue-600 dark:from-primary-400 dark:to-blue-400;
            }
            .card-shadow {
                @apply shadow-[0_8px_30px_rgb(0,0,0,0.12)] dark:shadow-[0_8px_30px_rgba(0,0,0,0.3)];
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
            .premium-pattern-dark {
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
        }
    </style>
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
                <div class="flex items-center space-x-4">
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                        </svg>
                        <svg class="w-6 h-6 dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                    </button>
                    <a href="<?= getBaseUrl() ?>/admin.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-20 lg:pt-24 overflow-hidden hero-glow">
        <!-- Background Elements -->
        <div class="absolute inset-0 z-0 grid-pattern dark:grid-pattern-dark">
            <div class="absolute top-0 left-1/4 w-72 h-72 bg-primary-200/20 dark:bg-primary-900/20 rounded-full mix-blend-multiply filter blur-3xl animate-float"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-blue-200/20 dark:bg-blue-900/20 rounded-full mix-blend-multiply filter blur-3xl animate-float" style="animation-delay: 2s"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 lg:pt-32 lg:pb-24">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-8" data-aos="fade-up">
                    <span class="text-gradient">Monetize Every Click.</span>
                    <br />
                    <span class="text-gradient">Your Ads. Your Revenue.</span>
                </h1>
                <p class="max-w-2xl mx-auto text-lg sm:text-xl text-gray-600 dark:text-gray-400 mb-10" data-aos="fade-up" data-aos-delay="100">
                    Turn simple links into income with your own Adsterra ads — no commission, no complexity. Built for creators, marketers, and hustlers.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-16" data-aos="fade-up" data-aos-delay="200">
                    <a href="#features" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-gradient shadow-lg hover:opacity-90 transition-opacity">
                        Get Started
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="inline-flex items-center justify-center px-6 py-3 border-2 border-primary-500 dark:border-primary-400 text-base font-medium rounded-lg text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-colors">
                        See How It Works
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">50K+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Links Created</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">1M+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Monthly Clicks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">5K+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Active Users</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">$100K+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Paid to Users</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="relative py-20 bg-gray-50/50 dark:bg-gray-800/30 grid-pattern dark:grid-pattern-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gradient mb-4" data-aos="fade-up">
                    Everything You Need
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400" data-aos="fade-up" data-aos-delay="100">
                    Simple tools to maximize your link earnings with complete control.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature Cards -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-4xl mb-4">💸</div>
                    <h3 class="text-xl font-semibold mb-4">Direct Earnings to Your Adsterra</h3>
                    <p class="text-gray-600 dark:text-gray-400">Connect your Adsterra account and receive earnings directly. No middleman, no waiting.</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up" data-aos-delay="300">
                    <div class="text-4xl mb-4">🔗</div>
                    <h3 class="text-xl font-semibold mb-4">Short Links with Ad Pages</h3>
                    <p class="text-gray-600 dark:text-gray-400">Choose between 2-3 ad pages before final redirection. Maximize your earning potential.</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up" data-aos-delay="400">
                    <div class="text-4xl mb-4">⚙️</div>
                    <h3 class="text-xl font-semibold mb-4">No Coding Required</h3>
                    <p class="text-gray-600 dark:text-gray-400">Just paste your link, choose ad pages, and add your Adsterra code. We handle the rest!</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up" data-aos-delay="500">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-xl font-semibold mb-4">Analytics Dashboard</h3>
                    <p class="text-gray-600 dark:text-gray-400">Coming Soon: Track your links' performance and earnings in real-time.</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up" data-aos-delay="600">
                    <div class="text-4xl mb-4">🛡️</div>
                    <h3 class="text-xl font-semibold mb-4">Zero Commission</h3>
                    <p class="text-gray-600 dark:text-gray-400">Keep 100% of your earnings. We don't take any commission from your revenue.</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up" data-aos-delay="700">
                    <div class="text-4xl mb-4">🚀</div>
                    <h3 class="text-xl font-semibold mb-4">Instant Setup</h3>
                    <p class="text-gray-600 dark:text-gray-400">Get started in minutes. No technical knowledge required.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="relative py-20 premium-pattern dark:premium-pattern-dark">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gradient mb-4" data-aos="fade-up">
                    How It Works
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400" data-aos="fade-up" data-aos-delay="100">
                    Start earning in four simple steps
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="relative" data-aos="fade-up" data-aos-delay="200">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 card-shadow">
                        <div class="absolute -top-4 left-4 w-8 h-8 bg-gradient rounded-full flex items-center justify-center text-white font-bold">1</div>
                        <h3 class="text-xl font-semibold mt-4 mb-2">Paste Your URL</h3>
                        <p class="text-gray-600 dark:text-gray-400">Enter the long URL you want to monetize</p>
                    </div>
                </div>

                <div class="relative" data-aos="fade-up" data-aos-delay="300">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 card-shadow">
                        <div class="absolute -top-4 left-4 w-8 h-8 bg-gradient rounded-full flex items-center justify-center text-white font-bold">2</div>
                        <h3 class="text-xl font-semibold mt-4 mb-2">Choose Ad Pages</h3>
                        <p class="text-gray-600 dark:text-gray-400">Select number of ad pages (2-3)</p>
                    </div>
                </div>

                <div class="relative" data-aos="fade-up" data-aos-delay="400">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 card-shadow">
                        <div class="absolute -top-4 left-4 w-8 h-8 bg-gradient rounded-full flex items-center justify-center text-white font-bold">3</div>
                        <h3 class="text-xl font-semibold mt-4 mb-2">Add Your Ad Code</h3>
                        <p class="text-gray-600 dark:text-gray-400">Insert your Adsterra publisher code</p>
                    </div>
                </div>

                <div class="relative" data-aos="fade-up" data-aos-delay="500">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 card-shadow">
                        <div class="absolute -top-4 left-4 w-8 h-8 bg-gradient rounded-full flex items-center justify-center text-white font-bold">4</div>
                        <h3 class="text-xl font-semibold mt-4 mb-2">Share & Earn</h3>
                        <p class="text-gray-600 dark:text-gray-400">Share your link and start earning</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Join Now Section -->
    <section class="relative py-20 bg-gray-50/50 dark:bg-gray-800/30 grid-pattern dark:grid-pattern-dark">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 card-shadow" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-gradient mb-4">Want to Join Pelupa Monetizer?</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                    Contact our admin to create your account and start earning today.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="https://wa.me/917279062862" target="_blank" class="inline-flex items-center justify-center px-6 py-3 bg-[#25D366] text-white rounded-lg hover:bg-opacity-90 transition-opacity">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        WhatsApp
                    </a>
                    <a href="mailto:support@pelupa.in" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-opacity-90 transition-opacity">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Email Support
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-600 dark:text-gray-400">
                © 2025 Pelupa Monetizer. Built with <span class="text-red-500">❤️</span> to help creators earn more.
            </p>
        </div>
    </footer>

    <!-- AOS Init -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true,
            offset: 100,
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

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 