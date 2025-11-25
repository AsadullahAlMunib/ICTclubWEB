<?php
// Configuration file for ICT Club website

// Site Configuration
define('SITE_NAME', 'ICT Club');
define('SITE_TAGLINE', 'Innovation • Collaboration • Technology');
define('SITE_URL', 'http://localhost:5000');
define('SITE_EMAIL', 'contact@ictclub.com');

// Navigation Menu
$nav_menu = [
    ['name' => 'Home', 'url' => 'index.php', 'icon' => 'fa-home'],
    ['name' => 'About', 'url' => 'about.php', 'icon' => 'fa-info-circle'],
    ['name' => 'Members', 'url' => 'members.php', 'icon' => 'fa-users'],
    ['name' => 'Projects', 'url' => 'projects.php', 'icon' => 'fa-code'],
    ['name' => 'Events', 'url' => 'events.php', 'icon' => 'fa-calendar'],
    ['name' => 'Blog', 'url' => 'blog.php', 'icon' => 'fa-newspaper'],
    ['name' => 'Contact', 'url' => 'contact.php', 'icon' => 'fa-envelope']
];

// Club Statistics
$club_stats = [
    ['label' => 'Active Members', 'value' => 150, 'icon' => 'fa-users', 'suffix' => '+'],
    ['label' => 'Projects Completed', 'value' => 45, 'icon' => 'fa-project-diagram', 'suffix' => '+'],
    ['label' => 'Events Hosted', 'value' => 30, 'icon' => 'fa-calendar-check', 'suffix' => '+'],
    ['label' => 'Awards Won', 'value' => 12, 'icon' => 'fa-trophy', 'suffix' => '']
];

// Social Media Links
$social_links = [
    ['name' => 'Facebook', 'url' => '#', 'icon' => 'fa-facebook'],
    ['name' => 'Twitter', 'url' => '#', 'icon' => 'fa-twitter'],
    ['name' => 'Instagram', 'url' => '#', 'icon' => 'fa-instagram'],
    ['name' => 'GitHub', 'url' => '#', 'icon' => 'fa-github'],
    ['name' => 'LinkedIn', 'url' => '#', 'icon' => 'fa-linkedin']
];

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Helper function to get current page
function getCurrentPage() {
    return basename($_SERVER['PHP_SELF']);
}

// Helper function to check if page is active
function isActivePage($page) {
    $current = getCurrentPage();
    
    // Treat blog-post.php as part of blog system
    if ($page === 'blog.php' && $current === 'blog-post.php') {
        return 'active';
    }
    
    return $current === $page ? 'active' : '';
}
?>
