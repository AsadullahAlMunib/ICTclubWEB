<?php
require_once 'includes/header.php';

$members = load_json_data('members.json');
$blogs = load_json_data('blog.json');
$person_name = isset($_GET['person']) ? urldecode(sanitize_input($_GET['person'])) : '';

// Find person in members
$person = null;
foreach ($members as $m) {
    if ($m['name'] === $person_name) {
        $person = $m;
        break;
    }
}

if (!$person) {
    header("Location: blog.php");
    exit();
}

// Get all posts by this person
$person_posts = array_values(array_filter($blogs, function($b) use ($person_name) {
    return $b['author'] === $person_name;
}));

// Filter active social links
$active_socials = array_filter($person['social'], function($url) { 
    return !empty($url); 
});

// Calculate article stats
$total_words = 0;
$latest_post_date = null;
foreach ($person_posts as $post) {
    $total_words += str_word_count($post['content']);
    if ($latest_post_date === null || strtotime($post['date']) > strtotime($latest_post_date)) {
        $latest_post_date = $post['date'];
    }
}

$page_title = $person['name'];
$post_count = count($person_posts);
$skill_count = count($person['skills']);
$social_count = count($active_socials);

// Social icons
$social_icons = [
    'github' => ['icon' => 'fab fa-github', 'name' => 'GitHub'],
    'linkedin' => ['icon' => 'fab fa-linkedin', 'name' => 'LinkedIn'],
    'twitter' => ['icon' => 'fab fa-twitter', 'name' => 'X'],
    'facebook' => ['icon' => 'fab fa-facebook', 'name' => 'Facebook'],
    'instagram' => ['icon' => 'fab fa-instagram', 'name' => 'Instagram'],
    'whatsapp' => ['icon' => 'fab fa-whatsapp', 'name' => 'WhatsApp'],
    'telegram' => ['icon' => 'fab fa-telegram', 'name' => 'Telegram'],
    'discord' => ['icon' => 'fab fa-discord', 'name' => 'Discord'],
    'phone' => ['icon' => 'fas fa-phone', 'name' => 'Phone'],
    'website' => ['icon' => 'fas fa-globe', 'name' => 'Website']
];

// Get current URL for sharing
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$encoded_url = urlencode($current_url);
$encoded_name = urlencode($person['name']);

// Determine role badge color and icon
$role_colors = [
    'President' => ['gradient' => 'from-yellow-400 to-amber-500', 'icon' => 'fas fa-crown', 'label' => '👑'],
    'Vice President' => ['gradient' => 'from-slate-400 to-slate-500', 'icon' => 'fas fa-star', 'label' => '⭐'],
];
$badge_config = $role_colors[$person['role']] ?? ['gradient' => 'from-purple-500 to-pink-500', 'icon' => 'fas fa-badge', 'label' => '✓'];

// Group articles by month
$articles_by_month = [];
foreach ($person_posts as $post) {
    $month = date('F Y', strtotime($post['date']));
    if (!isset($articles_by_month[$month])) {
        $articles_by_month[$month] = [];
    }
    $articles_by_month[$month][] = $post;
}
// Sort by date (newest first)
krsort($articles_by_month);

// Get latest article
$latest_article = null;
if (!empty($person_posts)) {
    $latest = $person_posts[0];
    foreach ($person_posts as $post) {
        if (strtotime($post['date']) > strtotime($latest['date'])) {
            $latest = $post;
        }
    }
    $latest_article = $latest;
}

// Calculate profile completion percentage
$profile_fields = [
    !empty($person['image']),
    !empty($person['bio']),
    !empty($person['email']),
    !empty($person['skills']) && count($person['skills']) > 0,
    !empty($active_socials),
    !empty($person_posts)
];
$completion_percent = (array_sum($profile_fields) / count($profile_fields)) * 100;
?>

<main class="min-h-screen bg-white dark:bg-slate-900" x-data="{ showNotification: false, notificationMessage: '', notificationLink: '', copiedEmail: false }">

    <!-- Notification Toast -->
    <div x-show="showNotification" x-transition class="fixed top-6 right-6 z-50 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-2xl shadow-2xl max-w-sm">
        <div class="p-6">
            <div class="flex items-start justify-between mb-3">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-check-circle"></i> <span x-text="copiedEmail ? 'Email Copied!' : 'Link Copied!'"></span>
                </h3>
                <button @click="showNotification = false" class="text-white/70 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-sm text-white/90 mb-4" x-text="copiedEmail ? 'Email address copied to clipboard' : 'Profile link copied to clipboard'"></p>
            <div class="bg-white/20 rounded-lg p-3 break-all">
                <p class="text-xs font-mono text-white/80" x-text="copiedEmail ? '<?php echo htmlspecialchars($person['email']); ?>' : notificationLink"></p>
            </div>
        </div>
    </div>

    <!-- Hero Card Section -->
    <section class="max-w-5xl mx-auto px-6 py-12">
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-slate-800 dark:to-slate-900 rounded-3xl p-6 border border-purple-200 dark:border-slate-700">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8 items-start">
                <!-- Left: Photo -->
                <div class="relative flex justify-center pt-6 md:pt-0">
                    <!-- Image with Fallback -->
                    <div class="relative w-48 h-48 md:w-72 md:h-72 lg:w-80 lg:h-80 bg-gradient-to-br from-slate-200 to-slate-300 dark:from-slate-700 dark:to-slate-600 rounded-3xl flex items-center justify-center border-4 border-white dark:border-slate-700 shadow-lg overflow-hidden">
                        <img src="<?php echo htmlspecialchars($person['image']); ?>" 
                             alt="Profile picture of <?php echo htmlspecialchars($person['name']); ?> from <?php echo htmlspecialchars($person['position']); ?>"
                             class="w-full h-full object-cover"
                             onerror="this.style.display='none'">
                        <i class="fas fa-user text-4xl text-slate-500 dark:text-slate-400 absolute" id="fallback-icon-<?php echo $person['id']; ?>" style="display: none;"></i>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-display font-bold text-slate-900 dark:text-white mb-3 leading-tight">
                            <?php echo htmlspecialchars($person['name']); ?>
                        </h1>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-4 py-2 bg-gradient-to-r <?php echo $badge_config['gradient']; ?> text-white rounded-full font-semibold text-sm flex items-center gap-2 shadow-lg" role="badge" aria-label="<?php echo htmlspecialchars($person['role']); ?>">
                                <i class="<?php echo $badge_config['icon']; ?>"></i>
                                <?php echo htmlspecialchars($person['role']); ?>
                            </span>
                            <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-full font-semibold text-sm" role="badge" aria-label="Position: <?php echo htmlspecialchars($person['position']); ?>">
                                <i class="fas fa-briefcase mr-1"></i><?php echo htmlspecialchars($person['position']); ?>
                            </span>
                        </div>
                        <p class="text-base md:text-lg text-slate-600 dark:text-slate-300 mb-6">
                            <?php echo htmlspecialchars($person['bio']); ?>
                        </p>
                        <!-- Statistics with Achievements -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-3 mb-6">
                            <div class="bg-white dark:bg-slate-700 rounded-lg p-3 md:p-4 text-center border border-slate-200 dark:border-slate-600 hover:border-purple-400 transition-all duration-200 hover:shadow-md" role="status" aria-label="Total articles published">
                                <p class="text-xl md:text-2xl font-bold text-purple-600 dark:text-purple-400 leading-none"><?php echo $post_count; ?></p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1"><i class="fas fa-newspaper mr-1"></i>Articles</p>
                            </div>
                            <div class="bg-white dark:bg-slate-700 rounded-lg p-3 md:p-4 text-center border border-slate-200 dark:border-slate-600 hover:border-blue-400 transition-all duration-200 hover:shadow-md" role="status" aria-label="Total skills">
                                <p class="text-xl md:text-2xl font-bold text-blue-600 dark:text-blue-400 leading-none"><?php echo $skill_count; ?></p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1"><i class="fas fa-star mr-1"></i>Skills</p>
                            </div>
                            <div class="bg-white dark:bg-slate-700 rounded-lg p-3 md:p-4 text-center border border-slate-200 dark:border-slate-600 hover:border-emerald-400 transition-all duration-200 hover:shadow-md" role="status" aria-label="Social profiles connected">
                                <p class="text-xl md:text-2xl font-bold text-emerald-600 dark:text-emerald-400 leading-none"><?php echo $social_count; ?></p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1"><i class="fas fa-link mr-1"></i>Profiles</p>
                            </div>
                            <div class="bg-white dark:bg-slate-700 rounded-lg p-3 md:p-4 text-center border border-slate-200 dark:border-slate-600 hover:border-orange-400 transition-all duration-200 hover:shadow-md" role="status" aria-label="Total words written">
                                <p class="text-xl md:text-2xl font-bold text-orange-600 dark:text-orange-400 leading-none"><?php echo ceil($total_words / 100); ?>k</p>
                                <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold mt-1"><i class="fas fa-feather mr-1"></i>Words</p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Display with Copy Button -->
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-purple-200 dark:border-slate-700 mb-4 hover:border-purple-400 dark:hover:border-purple-500 transition-all duration-200">
                        <p class="text-xs text-slate-600 dark:text-slate-400 font-semibold mb-2 uppercase tracking-wider">Email</p>
                        <div class="flex items-center justify-between gap-2">
                            <a href="mailto:<?php echo htmlspecialchars($person['email']); ?>" class="text-purple-600 dark:text-purple-400 hover:underline font-bold break-all text-sm flex-1" aria-label="Send email to <?php echo htmlspecialchars($person['email']); ?>">
                                <?php echo htmlspecialchars($person['email']); ?>
                            </a>
                            <button @click="navigator.clipboard.writeText('<?php echo addslashes($person['email']); ?>'); copiedEmail = true; showNotification = true; setTimeout(() => { showNotification = false; copiedEmail = false; }, 3000);" class="px-3 py-2 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 text-white rounded-lg text-xs font-semibold uppercase tracking-wider transition-all duration-200 flex items-center gap-2 whitespace-nowrap shadow-md hover:shadow-lg hover:-translate-y-0.5" aria-label="Copy email address">
                                <i class="fas fa-copy text-xs"></i><span class="hidden md:inline">Copy</span>
                            </button>
                        </div>
                    </div>

                    <!-- Share Profile Buttons -->
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-300">Share Profile</h3>
                        <div class="flex flex-wrap gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $encoded_url; ?>" target="_blank" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-all hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fab fa-facebook-f"></i>
                                <span class="hidden sm:inline">Facebook</span>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $encoded_url; ?>" target="_blank" class="flex-1 px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg font-semibold text-sm transition-all hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fab fa-linkedin-in"></i>
                                <span class="hidden sm:inline">LinkedIn</span>
                            </a>
                            <a href="mailto:?subject=Check%20out%20<?php echo $encoded_name; ?>'s%20profile&body=<?php echo $encoded_url; ?>" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition-all hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-envelope"></i>
                                <span class="hidden sm:inline">Email</span>
                            </a>
                            <button @click="navigator.clipboard.writeText('<?php echo addslashes($current_url); ?>'); showNotification = true; notificationLink = '<?php echo addslashes($current_url); ?>'; setTimeout(() => { showNotification = false; }, 4000);" class="flex-1 px-4 py-2 bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all hover:shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-link"></i>
                                <span class="hidden sm:inline">Copy Profile Link</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Divider -->
    <div class="max-w-5xl mx-auto px-6 py-8 animate-fade-in">
        <div class="h-px bg-gradient-to-r from-transparent via-slate-300 dark:via-slate-600 to-transparent"></div>
    </div>

    <!-- Skills Section -->
    <section class="max-w-5xl mx-auto px-6 py-12 animate-fade-in">
        <h2 class="text-3xl md:text-4xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3 leading-tight">
            <i class="fas fa-star text-yellow-500"></i> Skills & Expertise
        </h2>
        <?php if (empty($person['skills'])): ?>
            <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-12 text-center border-2 border-dashed border-slate-300 dark:border-slate-600">
                <i class="fas fa-graduation-cap text-4xl text-slate-400 dark:text-slate-500 mb-4 block"></i>
                <p class="text-lg font-semibold text-slate-600 dark:text-slate-300 mb-2">No skills listed yet</p>
                <p class="text-slate-500 dark:text-slate-400">This member hasn't added any skills to their profile.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($person['skills'] as $skill): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700 flex items-center gap-3 hover:shadow-md transition-all duration-200 hover:border-purple-300 dark:hover:border-purple-500 hover:-translate-y-1" role="listitem" aria-label="Skill: <?php echo htmlspecialchars($skill); ?>">
                        <i class="fas fa-check-circle text-purple-600 dark:text-purple-400 text-lg flex-shrink-0" aria-hidden="true"></i>
                        <span class="font-semibold text-slate-900 dark:text-white text-sm md:text-base"><?php echo htmlspecialchars($skill); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Section Divider -->
    <div class="max-w-5xl mx-auto px-6 py-8 animate-fade-in">
        <div class="h-px bg-gradient-to-r from-transparent via-slate-300 dark:via-slate-600 to-transparent"></div>
    </div>

    <!-- Latest Article Highlight (if has articles) -->
    <?php if ($latest_article): ?>
    <section class="max-w-5xl mx-auto px-6 py-12 animate-fade-in">
        <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-2">
            <i class="fas fa-fire text-red-500"></i> Latest Article
        </h2>
        <a href="blog-post.php?post_id=<?php echo $latest_article['id']; ?>" class="group block bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 hover:border-red-400 dark:hover:border-red-500 transition-all hover:shadow-2xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 md:p-8">
                <!-- Image -->
                <div class="relative h-64 md:h-48 overflow-hidden rounded-lg">
                    <img src="<?php echo htmlspecialchars($latest_article['image']); ?>" alt="Featured image for latest article: <?php echo htmlspecialchars($latest_article['title']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" onerror="this.style.display='none'">
                    <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">
                        <i class="fas fa-star mr-1"></i>Latest
                    </div>
                </div>
                <!-- Content -->
                <div class="md:col-span-2 flex flex-col justify-between">
                    <div>
                        <p class="text-xs font-bold text-red-600 dark:text-red-400 mb-2 uppercase tracking-widest">
                            <i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($latest_article['category']); ?>
                        </p>
                        <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-3 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors line-clamp-2">
                            <?php echo htmlspecialchars($latest_article['title']); ?>
                        </h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4 line-clamp-2">
                            <?php echo htmlspecialchars($latest_article['excerpt']); ?>
                        </p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            <i class="fas fa-calendar mr-2"></i><?php echo format_date($latest_article['date']); ?>
                        </p>
                        <span class="text-red-600 dark:text-red-400 font-semibold group-hover:translate-x-2 transition-transform">
                            Read Article <i class="fas fa-arrow-right ml-2"></i>
                        </span>
                    </div>
                </div>
            </div>
        </a>
    </section>
    <?php endif; ?>

    <!-- Contact & Social Section -->
    <section class="max-w-5xl mx-auto px-6 pb-16 animate-fade-in">
        <!-- Main Contact Card -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border-2 border-slate-200 dark:border-slate-700 shadow-lg">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-center">
                <!-- Left: Contact Info -->
                <div class="lg:col-span-1">
                    <div class="mb-3 text-center">
                        <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/40 dark:to-pink-900/40 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-comments text-4xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Contact <?php echo htmlspecialchars($person['name']); ?></h2>
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 text-lg mb-8">Ready to collaborate or have questions? Let's connect!</p>
                    
                    <a href="mailto:<?php echo htmlspecialchars($person['email']); ?>" class="w-full block px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-2xl font-bold transition-all hover:shadow-xl transform hover:scale-105 text-center mb-6">
                        <i class="fas fa-paper-plane mr-2"></i>Send Email
                    </a>

                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                        <i class="fas fa-circle text-emerald-500 text-xs"></i>
                        <span>Response typically within 24 hours</span>
                    </div>
                </div>

                <!-- Center: Divider -->
                <div class="hidden lg:block h-48 w-px bg-gradient-to-b from-transparent via-slate-300 dark:via-slate-600 to-transparent"></div>

                <!-- Right: Social Links -->
                <div class="lg:col-span-1">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <i class="fas fa-user-check text-blue-600 dark:text-blue-400"></i> Follow (<?php echo $social_count; ?>)
                    </h3>
                    <div class="grid grid-cols-4 gap-5" role="list" aria-label="Social media profiles">
                        <?php foreach ($active_socials as $platform => $url): ?>
                            <?php if (isset($social_icons[$platform])): ?>
                                <?php 
                                    $href = $url;
                                    $target_blank = true;
                                    if ($platform === 'phone') {
                                        $href = 'tel:' . $url;
                                        $target_blank = false;
                                    }
                                ?>
                                <div class="group relative" role="listitem">
                                    <a href="<?php echo htmlspecialchars($href); ?>" <?php if ($target_blank): ?>target="_blank" rel="noopener noreferrer"<?php endif; ?> aria-label="Visit on <?php echo $social_icons[$platform]['name']; ?>"
                                       class="w-14 h-14 bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/40 dark:to-cyan-900/40 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 hover:from-blue-500 hover:to-cyan-500 hover:text-white transition-all duration-200 hover:shadow-xl hover:-translate-y-2 border border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600">
                                        <i class="<?php echo $social_icons[$platform]['icon']; ?> text-lg group-hover:scale-110 transition-transform duration-200"></i>
                                    </a>
                                    <!-- Platform Name Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-slate-900 dark:bg-slate-700 text-white text-xs font-semibold rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                                        <?php echo $social_icons[$platform]['name']; ?>
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-slate-900 dark:border-t-slate-700"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Divider -->
    <div class="max-w-5xl mx-auto px-6 py-8 animate-fade-in">
        <div class="h-px bg-gradient-to-r from-transparent via-slate-300 dark:via-slate-600 to-transparent"></div>
    </div>

    <!-- Articles Timeline Section -->
    <section class="max-w-5xl mx-auto px-6 py-12 animate-fade-in">
        <h2 class="text-3xl md:text-4xl font-display font-bold text-slate-900 dark:text-white mb-8 flex items-center gap-3 leading-tight">
            <i class="fas fa-newspaper text-orange-600"></i> All Articles (<?php echo $post_count; ?>)
        </h2>
        <?php if (empty($person_posts)): ?>
            <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-12 text-center border-2 border-dashed border-slate-300 dark:border-slate-600">
                <i class="fas fa-pen-fancy text-4xl text-slate-400 dark:text-slate-500 mb-4 block"></i>
                <p class="text-xl font-semibold text-slate-600 dark:text-slate-300 mb-2">No articles yet</p>
                <p class="text-slate-500 dark:text-slate-400">This member hasn't published any articles on the blog.</p>
                <a href="blog.php" class="inline-block mt-6 px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold transition-all">
                    Browse Blog Articles
                </a>
            </div>
        <?php else: ?>
            <!-- Timeline by Month -->
            <div class="space-y-12">
                <?php foreach ($articles_by_month as $month => $posts): ?>
                <div>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-3 h-3 bg-orange-600 rounded-full"></div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                            <i class="fas fa-calendar-alt mr-2 text-orange-600"></i><?php echo $month; ?>
                        </h3>
                        <span class="ml-auto text-sm text-slate-600 dark:text-slate-400"><?php echo count($posts); ?> article<?php echo count($posts) !== 1 ? 's' : ''; ?></span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ml-4 border-l-2 border-orange-600 pl-6">
                        <?php foreach ($posts as $post): ?>
                        <a href="blog-post.php?post_id=<?php echo $post['id']; ?>" class="group block rounded-xl overflow-hidden border-2 border-slate-200 dark:border-slate-600 hover:border-orange-400 dark:hover:border-orange-500 transition-all hover:shadow-xl">
                            <!-- Featured Image -->
                            <div class="relative h-40 overflow-hidden bg-slate-200 dark:bg-slate-700">
                                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Featured image for article: <?php echo htmlspecialchars($post['title']); ?>" class="w-full h-full object-cover group-hover:scale-125 transition-transform duration-500" onerror="this.style.display='none'">
                            </div>
                            <!-- Content -->
                            <div class="p-4 bg-white dark:bg-slate-800">
                                <p class="text-xs font-bold text-orange-600 dark:text-orange-400 mb-2 uppercase"><?php echo htmlspecialchars($post['category']); ?></p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 mb-2 transition-colors">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    <?php echo format_date($post['date']); ?>
                                </p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <script>
        // Show fallback icon when profile image fails to load
        document.addEventListener('DOMContentLoaded', function() {
            const profileImg = document.querySelector('.relative.w-48.h-48 img');
            if (profileImg) {
                profileImg.onerror = function() {
                    this.style.display = 'none';
                    const fallbackIcon = document.getElementById('fallback-icon-<?php echo $person['id']; ?>');
                    if (fallbackIcon) {
                        fallbackIcon.style.display = 'block';
                    }
                };
            }
        });
    </script>

</main>

<?php require_once 'includes/footer.php'; ?>
