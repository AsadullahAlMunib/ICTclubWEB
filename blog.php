<?php
$page_title = 'Blog';
require_once 'includes/header.php';

$blogs = load_json_data('blog.json');
$members = load_json_data('members.json');

// Create member image lookup
$member_images = [];
foreach ($members as $member) {
    $member_images[$member['name']] = $member['image'];
}

// Handle search and filter
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? sanitize_input($_GET['category']) : '';

// Filter blogs
$filtered_blogs = array_filter($blogs, function($blog) use ($search, $category_filter) {
    $matches_search = empty($search) || 
        stripos($blog['title'], $search) !== false || 
        stripos($blog['excerpt'], $search) !== false;
    $matches_category = empty($category_filter) || $blog['category'] === $category_filter;
    return $matches_search && $matches_category;
});

// Get unique categories
$categories = array_values(array_unique(array_map(function($blog) { return $blog['category']; }, $blogs)));

// Count articles per category
$category_counts = [];
foreach ($blogs as $blog) {
    $cat = $blog['category'];
    $category_counts[$cat] = ($category_counts[$cat] ?? 0) + 1;
}

// Sort by date (newest first)
usort($filtered_blogs, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Pagination setup
$posts_per_page = 10;
$total_posts = count($filtered_blogs);
$total_pages = ceil($total_posts / $posts_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$current_page = min($current_page, $total_pages);

// Calculate offset
$offset = ($current_page - 1) * $posts_per_page;
$paginated_blogs = array_slice($filtered_blogs, $offset, $posts_per_page);

// Category gradient colors map
$category_colors = [
    'Web Development' => 'blue',
    'UI/UX Design' => 'pink',
    'Programming' => 'purple',
    'JavaScript Frameworks' => 'yellow',
    'Backend Development' => 'indigo',
    'Security' => 'red',
    'Cloud Computing' => 'cyan'
];
?>

<main class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300 pt-8">
    <!-- Hero Section - Enhanced -->
    <div class="relative py-24 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-1/4 w-72 h-72 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute top-0 right-1/4 w-72 h-72 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 w-72 h-72 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="mb-6 flex justify-center" data-aos="fade-down">
                <i class="fas fa-book-open text-6xl text-white opacity-80"></i>
            </div>
            <h1 class="text-4xl md:text-6xl font-display font-black mb-4 text-white" data-aos="fade-up">
                Our Blog
            </h1>
            <p class="text-base md:text-xl text-white/90 mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                Insights, tutorials, and stories from our ICT Club community
            </p>
            <div class="flex justify-center gap-6 text-white/80 text-sm" data-aos="fade-up" data-aos-delay="200">
                <span><i class="fas fa-file-alt mr-2"></i><?php echo count($blogs); ?> Articles</span>
                <span><i class="fas fa-layer-group mr-2"></i><?php echo count($categories); ?> Categories</span>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <section class="py-8 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-2">
                    <form method="GET" class="flex items-center gap-2">
                        <?php if (!empty($category_filter)): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                        <?php endif; ?>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search articles..." 
                               class="flex-1 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <button type="submit" class="px-3 md:px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg transition-all duration-300 flex items-center gap-2 justify-center">
                            <i class="fas fa-search"></i>
                            <span class="hidden md:inline">Search</span>
                        </button>
                    </form>
                </div>

                <!-- Category Filter -->
                <form method="GET" class="flex items-center gap-2">
                    <?php if (!empty($search)): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>
                    <select name="category" onchange="this.form.submit()"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 cursor-pointer transition-all duration-300 hover:shadow-md">
                        <option value="">All Categories (<?php echo count($blogs); ?>)</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category_filter === $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?> (<?php echo $category_counts[$cat]; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="py-20 container mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($filtered_blogs)): ?>
            <!-- No Results - Premium -->
            <div class="text-center py-24">
                <div class="mb-6 inline-block" data-aos="zoom-in">
                    <div class="relative">
                        <div class="absolute -inset-2 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full blur opacity-20"></div>
                        <div class="relative w-24 h-24 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-4xl text-purple-600 dark:text-purple-400"></i>
                        </div>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3" data-aos="fade-up">No articles found</h3>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto mb-8" data-aos="fade-up" data-aos-delay="100">
                    Try adjusting your search or filter criteria. We have <?php echo count($blogs); ?> amazing articles to explore!
                </p>
                <a href="blog.php" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition-all" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-redo"></i>
                    <span>Browse All Articles</span>
                </a>
            </div>
        <?php else: ?>
            <!-- Results Count -->
            <div class="mb-8 text-center" data-aos="fade-up">
                <p class="text-gray-600 dark:text-gray-400">
                    <i class="fas fa-file-alt mr-2 text-purple-600"></i>
                    Showing <span class="font-bold text-gray-900 dark:text-white"><?php echo count($filtered_blogs); ?></span> article<?php echo count($filtered_blogs) !== 1 ? 's' : ''; ?>
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($paginated_blogs as $idx => $blog): 
                    $color = $category_colors[$blog['category']] ?? 'purple';
                ?>
                    <!-- Blog Card -->
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

            <!-- Clear All Filters Button -->
            <?php if (!empty($search) || !empty($category_filter)): ?>
            <div class="mt-8 text-center">
                <a href="blog.php" class="inline-flex items-center gap-2 px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-semibold transition-all duration-300 shadow-md hover:shadow-lg">
                    <i class="fas fa-times"></i>
                    Clear All Filters
                </a>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <?php 
            // Helper function to build pagination URL
            function build_page_url($page, $search, $category) {
                $params = [];
                if ($page > 1) $params['page'] = $page;
                if (!empty($search)) $params['search'] = $search;
                if (!empty($category)) $params['category'] = $category;
                
                if (empty($params)) {
                    return 'blog.php';
                }
                return 'blog.php?' . http_build_query($params);
            }
            ?>
            <div class="mt-16 flex justify-center items-center gap-2">
                <!-- Previous Button -->
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo build_page_url($current_page - 1, $search, $category_filter); ?>" 
                       class="px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 font-semibold">
                        <i class="fas fa-chevron-left mr-2"></i>Previous
                    </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <div class="flex gap-1">
                    <?php 
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1): ?>
                        <a href="<?php echo build_page_url(1, $search, $category_filter); ?>" 
                           class="px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
                            1
                        </a>
                        <?php if ($start_page > 2): ?>
                            <span class="px-2 py-2 text-gray-500 dark:text-gray-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($page = $start_page; $page <= $end_page; $page++): ?>
                        <?php if ($page === $current_page): ?>
                            <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold transition-all duration-300 shadow-lg">
                                <?php echo $page; ?>
                            </button>
                        <?php else: ?>
                            <a href="<?php echo build_page_url($page, $search, $category_filter); ?>" 
                               class="px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 font-semibold">
                                <?php echo $page; ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <span class="px-2 py-2 text-gray-500 dark:text-gray-400">...</span>
                        <?php endif; ?>
                        <a href="<?php echo build_page_url($total_pages, $search, $category_filter); ?>" 
                           class="px-3 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
                            <?php echo $total_pages; ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Next Button -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo build_page_url($current_page + 1, $search, $category_filter); ?>" 
                       class="px-4 py-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 font-semibold">
                        Next<i class="fas fa-chevron-right ml-2"></i>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Page Info -->
            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Showing <span class="font-semibold text-gray-900 dark:text-white"><?php echo $offset + 1; ?></span> to 
                <span class="font-semibold text-gray-900 dark:text-white"><?php echo min($offset + $posts_per_page, $total_posts); ?></span> of 
                <span class="font-semibold text-gray-900 dark:text-white"><?php echo $total_posts; ?></span> articles 
                (Page <?php echo $current_page; ?> of <?php echo $total_pages; ?>)
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>
