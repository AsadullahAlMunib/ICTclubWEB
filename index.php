<?php
$page_title = "Home";
include 'includes/header.php';

$members = load_json_data('members.json');
$projects = load_json_data('projects.json');
$events = load_json_data('events.json');
$blogs = load_json_data('blog.json');
// Load club_stats from config if not already defined
if (!isset($club_stats)) {
    $club_stats = [];
}

// Create member image lookup map
$member_images = [];
foreach ($members as $member) {
    $member_images[$member['name']] = $member['image'];
}

// Category colors for blog cards
$category_colors = [
    'Web Development' => 'blue',
    'UI/UX Design' => 'pink',
    'Programming' => 'purple',
    'JavaScript Frameworks' => 'yellow',
    'Backend Development' => 'indigo',
    'Security' => 'red',
    'Cloud Computing' => 'cyan'
];

$upcoming_events = array_filter($events, function($event) {
    return $event['status'] === 'Upcoming';
});
$upcoming_events = array_slice($upcoming_events, 0, 3);

$featured_blogs = array_slice($blogs, -2);
?>

<!-- Hero Section with Particles -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900 hero-gradient-animate">
    <div id="particles-js"></div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center" data-aos="fade-up">
            <h1 class="text-4xl sm:text-5xl md:text-7xl font-display font-black mb-6">
                <span class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Welcome to <?php echo SITE_NAME; ?>
                </span>
            </h1>
            <p class="text-base sm:text-xl md:text-2xl text-gray-700 dark:text-gray-300 mb-4 font-medium">
                <?php echo SITE_TAGLINE; ?>
            </p>
            <p class="text-sm sm:text-lg text-gray-600 dark:text-gray-400 mb-12 max-w-3xl mx-auto">
                Join us in exploring the exciting world of technology, building innovative projects, and creating the future together.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="members.php" class="px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold text-sm sm:text-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-xl hover:shadow-2xl">
                    <i class="fas fa-user-plus mr-2"></i>Join Our Club
                </a>
                <a href="projects.php" class="px-6 sm:px-8 py-3 sm:py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white rounded-full font-semibold text-sm sm:text-lg hover:bg-gray-100 dark:hover:bg-gray-700 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <i class="fas fa-code mr-2"></i>View Projects
                </a>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 animate-bounce">
        <i class="fas fa-chevron-down text-3xl text-purple-600 dark:text-purple-400"></i>
    </div>
</section>

<!-- Live Statistics Counter -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php foreach ($club_stats as $stat): ?>
            <div class="text-center transform hover:scale-110 transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fas <?php echo $stat['icon']; ?> text-3xl text-white"></i>
                </div>
                <div class="counter counter-value text-4xl sm:text-4xl md:text-5xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent" data-target="<?php echo $stat['value']; ?>" data-suffix="<?php echo $stat['suffix']; ?>">0</div>
                <div class="text-gray-600 dark:text-gray-400 mt-2 font-medium"><?php echo $stat['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Projects Slider -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Featured Projects
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-lg">Showcasing our innovative work and achievements</p>
        </div>

        <div class="swiper projects-swiper" data-aos="fade-up" data-aos-delay="200">
            <div class="swiper-wrapper">
                <?php foreach (array_slice($projects, 0, 6) as $project): ?>
                <div class="swiper-slide flex flex-col">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-3 hover:scale-105 h-full flex flex-col">
                        <!-- Image Section - Responsive with reduced overlay -->
                        <div class="relative h-56 sm:h-64 bg-gradient-to-br from-blue-500 to-purple-500 overflow-hidden group">
                            <img src="<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-85">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/10 via-black/5 to-transparent group-hover:opacity-60 transition-opacity duration-300"></div>
                            
                            <!-- Status Badge - Top Left -->
                            <div class="absolute top-3 left-3">
                                <?php 
                                $status_colors = [
                                    'Completed' => 'bg-green-500 text-white',
                                    'In Progress' => 'bg-yellow-500 text-white',
                                    'Planning' => 'bg-blue-500 text-white'
                                ];
                                $status_color = $status_colors[$project['status']] ?? 'bg-gray-500 text-white';
                                ?>
                                <span class="px-2 py-0.5 <?php echo $status_color; ?> text-xs font-bold rounded-full shadow-lg">
                                    <?php echo $project['status']; ?>
                                </span>
                            </div>
                            
                            <!-- Category Badge - Top Right -->
                            <div class="absolute top-3 right-3">
                                <span class="px-2 py-0.5 bg-white/95 backdrop-blur-sm text-purple-600 text-xs font-semibold rounded-full shadow-lg">
                                    <?php echo $project['category']; ?>
                                </span>
                            </div>
                            
                            <!-- GitHub Link - Floating Button -->
                            <?php if (!empty($project['github']) && $project['github'] !== '#'): ?>
                            <a href="<?php echo $project['github']; ?>" target="_blank" rel="noopener noreferrer" 
                               class="absolute bottom-3 right-3 w-11 h-11 bg-gray-900/80 hover:bg-gray-950 text-white rounded-full flex items-center justify-center shadow-lg hover:scale-125 transition-all opacity-0 group-hover:opacity-100">
                                <i class="fab fa-github text-lg"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-5 sm:p-6 flex-1 flex flex-col">
                            <!-- Title -->
                            <h3 class="text-lg sm:text-xl font-bold mb-2 text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                <?php echo $project['title']; ?>
                            </h3>
                            
                            <!-- Description -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 flex-grow">
                                <?php echo truncate_text($project['description'], 90); ?>
                            </p>
                            
                            <!-- Technology Badges with Colors -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <?php 
                                $tech_gradients = [
                                    'from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/20 text-blue-700 dark:text-blue-300',
                                    'from-purple-100 to-purple-50 dark:from-purple-900/30 dark:to-purple-800/20 text-purple-700 dark:text-purple-300',
                                    'from-pink-100 to-pink-50 dark:from-pink-900/30 dark:to-pink-800/20 text-pink-700 dark:text-pink-300'
                                ];
                                foreach (array_slice($project['technologies'], 0, 3) as $index => $tech): 
                                    $gradient = $tech_gradients[$index % 3];
                                ?>
                                <span class="px-2.5 py-1 bg-gradient-to-r <?php echo $gradient; ?> text-xs rounded-full font-medium hover:shadow-md transition-all">
                                    <?php echo $tech; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            
                            <!-- Team Info -->
                            <?php if (!empty($project['team'])): ?>
                            <div class="mb-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold mb-2">
                                    <i class="fas fa-users mr-1"></i> Team (<?php echo count($project['team']); ?>)
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($project['team'] as $member): ?>
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded-full">
                                        <?php echo $member; ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- View Details Button -->
                            <a href="projects.php" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 mt-auto w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold text-sm rounded-lg shadow-md hover:shadow-lg hover:scale-105 transition-all duration-200">
                                <span>View Details</span>
                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination mt-16"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <div class="text-center mt-8" data-aos="fade-up">
            <a href="projects.php" class="px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold text-sm sm:text-base hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg inline-block">
                View All Projects <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Blog Section -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Latest Articles
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-lg">Insights and knowledge from our blog</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featured_blogs as $idx => $blog): 
                $color = $category_colors[$blog['category']] ?? 'purple';
            ?>
            <div class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-200 dark:border-gray-700" data-aos="fade-up" data-aos-delay="<?php echo $idx * 75; ?>">
                
                <!-- Image with Overlay -->
                <a href="blog-post.php?post_id=<?php echo urlencode($blog['id']); ?>" class="block relative h-48 overflow-hidden bg-gray-300 dark:bg-gray-700">
                    <img src="<?php echo $blog['image']; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <!-- Category Badge - Gradient -->
                    <span class="absolute top-2 right-2 px-2 py-1 bg-gradient-to-r from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-600 text-white text-[8px] font-bold uppercase rounded-md shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                        <?php echo $blog['category']; ?>
                    </span>
                </a>

                <!-- Content -->
                <div class="p-5">
                    <!-- Title -->
                    <a href="blog-post.php?post_id=<?php echo urlencode($blog['id']); ?>" class="block">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                            <?php echo htmlspecialchars($blog['title']); ?>
                        </h3>
                    </a>

                    <!-- Excerpt -->
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300">
                        <?php echo htmlspecialchars($blog['excerpt']); ?>
                    </p>

                    <!-- Meta - Enhanced with Icons -->
                    <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-purple-600"></i>
                            <span><?php echo format_date($blog['date']); ?></span>
                        </div>
                        <a href="profile.php?person=<?php echo urlencode($blog['author']); ?>" class="flex items-center gap-2 hover:text-purple-600 transition-colors" onclick="event.stopPropagation();">
                            <?php if (isset($member_images[$blog['author']])): ?>
                                <img src="<?php echo $member_images[$blog['author']]; ?>" alt="<?php echo htmlspecialchars($blog['author']); ?>" 
                                     class="w-5 h-5 rounded-full object-cover border border-purple-600/30 hover:border-purple-600 transition-all">
                            <?php else: ?>
                                <i class="fas fa-user text-purple-600"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($blog['author']); ?></span>
                        </a>
                    </div>

                    <!-- Read More -->
                    <a href="blog-post.php?post_id=<?php echo urlencode($blog['id']); ?>" class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 font-semibold text-sm hover:gap-3 transition-all duration-300 group/link">
                        <span>Read More</span>
                        <i class="fas fa-arrow-right transform group-hover/link:translate-x-1 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12" data-aos="fade-up">
            <a href="blog.php" class="px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold text-sm sm:text-base hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg inline-block">
                Read All Articles <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Upcoming Events Countdown -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up" data-aos-duration="600">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Upcoming Events
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-lg">Don't miss out on our exciting events and workshops</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($upcoming_events as $event): ?>
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-6 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-xs font-semibold rounded-full">
                        <?php echo $event['category']; ?>
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">
                        <i class="fas fa-calendar mr-2"></i><?php echo format_date($event['date'], 'M j'); ?>
                    </span>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold mb-3 text-gray-900 dark:text-white"><?php echo $event['title']; ?></h3>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4"><?php echo truncate_text($event['description'], 80); ?></p>
                <div class="space-y-2 mb-4 text-sm text-gray-600 dark:text-gray-400">
                    <div><i class="fas fa-clock mr-2 text-blue-500"></i><?php echo $event['time']; ?></div>
                    <div><i class="fas fa-map-marker-alt mr-2 text-blue-500"></i><?php echo $event['location']; ?></div>
                    <div>
                        <i class="fas fa-users mr-2 text-blue-500"></i>
                        <?php echo $event['registered']; ?> / <?php echo $event['spots']; ?> registered
                    </div>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-4">
                    <div class="progress-bar" style="width: <?php echo ($event['registered'] / $event['spots']) * 100; ?>%"></div>
                </div>
                <a href="events.php" class="block w-full text-center px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold text-sm hover:from-blue-600 hover:to-purple-600 transition-all duration-300">
                    Register Now
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-12" data-aos="fade-up">
            <a href="events.php" class="px-6 sm:px-8 py-3 sm:py-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-full font-semibold text-sm sm:text-base hover:bg-gray-100 dark:hover:bg-gray-600 transform hover:scale-105 transition-all duration-300 shadow-lg inline-block">
                View All Events <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Meet Our Leaders -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl sm:text-5xl font-bold mb-6 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                Meet Our Leaders
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Exceptional individuals driving innovation, excellence, and growth across our organization
            </p>
        </div>

        <!-- Members Carousel - Redesigned -->
        <div class="swiper leaders-swiper" data-aos="fade-up" data-aos-delay="200">
            <div class="swiper-wrapper">
            <?php foreach ($members as $member): ?>
                <div class="swiper-slide flex flex-col">
                <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="group block h-full flex flex-col">
                <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-850 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 h-full flex flex-col border border-gray-200 dark:border-gray-700 transform hover:-translate-y-2 hover:scale-105">
                    
                    <!-- Image Section - Responsive with Overlay -->
                    <div class="relative h-64 sm:h-80 w-full overflow-hidden bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
                        <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-black/10 to-transparent opacity-100 group-hover:opacity-70 transition-opacity duration-300"></div>
                        
                        <!-- Role Badge - Overlay on Image -->
                        <div class="absolute top-3 right-3">
                            <span class="px-3 py-1 bg-white/95 backdrop-blur-sm text-purple-600 text-xs font-bold rounded-full shadow-lg">
                                <?php echo $member['role']; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-6 sm:p-7 flex-1 flex flex-col">
                        <!-- Name -->
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                            <?php echo $member['name']; ?>
                        </h3>

                        <!-- Bio - Mobile friendly truncation -->
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-5 flex-grow line-clamp-2 sm:line-clamp-3">
                            <?php echo $member['bio']; ?>
                        </p>

                        <!-- Skills with Color Variation -->
                        <?php if (!empty($member['skills'])): ?>
                        <div class="mb-5 flex flex-wrap gap-2">
                            <?php 
                            $skill_gradients = [
                                'from-blue-100 to-blue-50 dark:from-blue-900/30 dark:to-blue-800/20 text-blue-700 dark:text-blue-300',
                                'from-purple-100 to-purple-50 dark:from-purple-900/30 dark:to-purple-800/20 text-purple-700 dark:text-purple-300',
                                'from-pink-100 to-pink-50 dark:from-pink-900/30 dark:to-pink-800/20 text-pink-700 dark:text-pink-300',
                                'from-green-100 to-green-50 dark:from-green-900/30 dark:to-green-800/20 text-green-700 dark:text-green-300'
                            ];
                            foreach (array_slice($member['skills'], 0, 4) as $index => $skill): 
                                $gradient = $skill_gradients[$index % 4];
                            ?>
                            <span class="px-3 py-1 bg-gradient-to-r <?php echo $gradient; ?> rounded-full text-xs font-semibold hover:shadow-md transition-all">
                                <?php echo $skill; ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Contact Button -->
                        <a href="mailto:<?php echo $member['email']; ?>" 
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold text-sm transition-all duration-200 mb-4 shadow-md hover:shadow-lg hover:scale-105">
                            <i class="fas fa-envelope text-base"></i>
                            <span>Get In Touch</span>
                        </a>

                        <!-- Social Links with Platform Colors -->
                        <?php if (!empty($member['social'])): ?>
                        <div class="flex gap-2 justify-center flex-wrap">
                            <?php 
                            $social_colors = [
                                'github' => ['fab fa-github', 'from-gray-800 to-gray-700 hover:from-gray-900 hover:to-gray-800'],
                                'linkedin' => ['fab fa-linkedin', 'from-blue-700 to-blue-600 hover:from-blue-800 hover:to-blue-700'],
                                'twitter' => ['fab fa-twitter', 'from-cyan-500 to-cyan-400 hover:from-cyan-600 hover:to-cyan-500'],
                                'facebook' => ['fab fa-facebook', 'from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600'],
                                'instagram' => ['fab fa-instagram', 'from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600'],
                                'whatsapp' => ['fab fa-whatsapp', 'from-green-500 to-green-400 hover:from-green-600 hover:to-green-500'],
                                'telegram' => ['fab fa-telegram', 'from-sky-500 to-sky-400 hover:from-sky-600 hover:to-sky-500'],
                                'discord' => ['fab fa-discord', 'from-indigo-600 to-indigo-500 hover:from-indigo-700 hover:to-indigo-600'],
                                'phone' => ['fas fa-phone', 'from-red-500 to-red-400 hover:from-red-600 hover:to-red-500'],
                                'website' => ['fas fa-globe', 'from-purple-600 to-purple-500 hover:from-purple-700 hover:to-purple-600']
                            ];
                            
                            foreach ($social_colors as $platform => $config):
                                if (!empty($member['social'][$platform])):
                                    [$icon, $colors] = $config;
                                    $url = $member['social'][$platform];
                                    if ($platform === 'phone') {
                                        $url = 'tel:' . $url;
                                    }
                            ?>
                            <a href="<?php echo htmlspecialchars($url); ?>" <?php if ($platform !== 'phone'): ?>target="_blank" rel="noopener noreferrer"<?php endif; ?> 
                               class="w-11 h-11 flex items-center justify-center rounded-lg bg-gradient-to-br <?php echo $colors; ?> text-white shadow-md hover:shadow-lg hover:scale-125 transition-all duration-200 flex-shrink-0"
                               title="<?php echo ucfirst($platform); ?>">
                                <i class="<?php echo $icon; ?> text-sm"></i>
                            </a>
                            <?php endif; endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                </a>
                </div>
            <?php endforeach; ?>
            </div>
            <div class="swiper-pagination mt-16"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

        <!-- CTA Section -->
        <div class="text-center mt-20" data-aos="fade-up">
            <a href="members.php" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                <span>View All Members</span>
                <i class="fas fa-arrow-right text-base"></i>
            </a>
        </div>
    </div>
</section>

<!-- Latest Announcements -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Latest Announcements
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-lg">Stay updated with club news and updates</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-8 shadow-lg" data-aos="flip-up">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-bullhorn text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">New Workshop Series</h3>
                        <span class="text-sm text-gray-600 dark:text-gray-400">2 days ago</span>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300">
                    Exciting new workshop series on Full Stack Development starting next month. Registration opens soon!
                </p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-8 shadow-lg" data-aos="flip-up" data-aos-delay="100">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Competition Winners</h3>
                        <span class="text-sm text-gray-600 dark:text-gray-400">5 days ago</span>
                    </div>
                </div>
                <p class="text-gray-700 dark:text-gray-300">
                    Congratulations to our team for winning first place in the National Coding Competition!
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Achievement Showcase -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Our Achievements
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-lg">Celebrating our milestones and success stories</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-aos="zoom-in">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-medal text-white text-2xl"></i>
                </div>
                <h3 class="stat-number text-2xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2" data-target="12">12</h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Awards Won</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-aos="zoom-in" data-aos-delay="100">
                <div class="w-16 h-16 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-code-branch text-white text-2xl"></i>
                </div>
                <h3 class="stat-number text-2xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2" data-target="45">45</h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Projects Completed</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-aos="zoom-in" data-aos-delay="200">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-graduation-cap text-white text-2xl"></i>
                </div>
                <h3 class="stat-number text-2xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2" data-target="500">500</h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Students Trained</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-aos="zoom-in" data-aos-delay="300">
                <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-handshake text-white text-2xl"></i>
                </div>
                <h3 class="stat-number text-2xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2" data-target="20">20</h3>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Partnerships</p>
            </div>
        </div>
    </div>
</section>

<!-- Quick Contact Form -->
<section class="py-20 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-3xl p-1 shadow-2xl" data-aos="zoom-in">
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 md:p-12">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl sm:text-4xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            Get In Touch
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-lg">Have questions? We'd love to hear from you!</p>
                    </div>

                    <form action="contact.php" method="POST" class="space-y-6" data-validate="true">
                        <input type="hidden" name="action" value="contact">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <input type="text" name="name" placeholder="Your Name" required
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300">
                            </div>
                            <div>
                                <input type="email" name="email" placeholder="Your Email" required
                                       class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300">
                            </div>
                        </div>
                        <div>
                            <textarea name="message" rows="5" placeholder="Your Message" required
                                      class="w-full px-6 py-4 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"></textarea>
                        </div>
                        <div>
                            <button type="submit" class="w-full px-6 sm:px-8 py-3 sm:py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold text-sm sm:text-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-paper-plane mr-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
