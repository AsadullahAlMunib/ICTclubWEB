<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$current_page = getCurrentPage();
?>
<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }" x-init="darkMode = localStorage.getItem('darkMode') === 'true'">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?>">
    <meta name="keywords" content="ICT Club, Technology, Innovation, Programming, Web Development">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.png">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?>">
    <meta name="twitter:description" content="<?php echo SITE_NAME; ?> - <?php echo SITE_TAGLINE; ?>">
    <meta name="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.png">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad1' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%234f46e5;stop-opacity:1' /><stop offset='50%' style='stop-color:%239333ea;stop-opacity:1' /><stop offset='100%' style='stop-color:%23ec4899;stop-opacity:1' /></linearGradient></defs><rect fill='url(%23grad1)' width='100' height='100' rx='20'/><text x='50' y='70' font-size='50' font-weight='bold' fill='white' text-anchor='middle' font-family='Arial' letter-spacing='2'>ICT</text></svg>">
    <link rel="shortcut icon" href="favicon.ico">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- AOS (Animate On Scroll) -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'display': ['Poppins', 'sans-serif']
                    },
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
                            900: '#0c4a6e'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Navigation - CLEAN & MINIMAL with Glow Effect -->
    <nav x-data="{ mobileMenuOpen: false, scrolled: false }" 
         @scroll.window="scrolled = window.pageYOffset > 50"
         :class="scrolled ? 'bg-white dark:bg-gray-900 shadow-lg shadow-purple-500/10 border-b border-gray-200 dark:border-gray-800' : 'bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 shadow-sm shadow-purple-500/5'"
         class="fixed w-full z-50 navbar-clean" style="transition: all 0.2s ease; pointer-events: auto;">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo - Enhanced Animation -->
                <a href="index.php" class="flex items-center space-x-2 group flex-shrink-0">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center transform group-hover:scale-125 group-hover:rotate-12 transition-all duration-300 shadow-md group-hover:shadow-lg group-hover:shadow-purple-500/50">
                        <i class="fas fa-code text-white text-lg group-hover:animate-pulse"></i>
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-display font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                            <?php echo SITE_NAME; ?>
                        </h1>
                    </div>
                </a>

                <!-- Desktop Navigation - Colorful Pills -->
                <div class="hidden lg:flex items-center gap-1.5">
                    <?php foreach ($nav_menu as $item): ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="nav-pill-color px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-300 flex items-center gap-1.5 hover:scale-105 active:scale-95 bg-gray-100 dark:bg-gray-700 <?php echo isActivePage($item['url']) ? 'bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white shadow-lg shadow-purple-500/50 animate-gradient-pulse' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600'; ?>">
                        <i class="fas <?php echo $item['icon']; ?>"></i>
                        <span><?php echo $item['name']; ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Controls -->
                <div class="flex items-center gap-3">
                    <!-- Enhanced Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                            :class="darkMode ? 'bg-amber-100 text-amber-600' : 'bg-indigo-100 text-indigo-600'"
                            class="w-10 h-10 flex items-center justify-center rounded-lg hover:scale-110 transition-all duration-300 shadow-md hover:shadow-lg">
                        <i :class="darkMode ? 'fa-sun' : 'fa-moon'" class="fas text-base"></i>
                    </button>
                    
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all duration-300">
                        <i :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'" class="fas text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu - Enhanced Styling -->
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="lg:hidden py-4 px-3 border-t border-gray-200 dark:border-gray-800 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900 backdrop-blur-sm" style="pointer-events: auto;">
                <?php foreach ($nav_menu as $item): ?>
                <a href="<?php echo $item['url']; ?>" 
                   class="block px-4 py-3 rounded-xl font-semibold text-gray-700 dark:text-gray-200 mb-2 transition-all duration-300 transform hover:scale-105 flex items-center gap-3 <?php echo isActivePage($item['url']) ? 'bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 text-white shadow-lg shadow-purple-500/50' : 'hover:bg-gray-100 dark:hover:bg-gray-700'; ?>">
                    <i class="fas <?php echo $item['icon']; ?> text-lg"></i>
                    <span><?php echo $item['name']; ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div class="h-20"></div>
