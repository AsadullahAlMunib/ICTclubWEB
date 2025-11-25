<?php
require_once 'includes/header.php';

$blogs = load_json_data('blog.json');
$members = load_json_data('members.json');
$post_id = isset($_GET['post_id']) ? (int)sanitize_input($_GET['post_id']) : 0;

// Create member image lookup
$member_images = [];
foreach ($members as $member) {
    $member_images[$member['name']] = $member['image'];
}

// Find the blog post
$blog = null;
foreach ($blogs as $b) {
    if ($b['id'] == $post_id) {
        $blog = $b;
        break;
    }
}

if (!$blog) {
    header("Location: blog.php");
    exit();
}

// Find author in members
$author_member = null;
foreach ($members as $m) {
    if ($m['name'] === $blog['author']) {
        $author_member = $m;
        break;
    }
}

$page_title = $blog['title'];

// Get related articles
$related = array_filter($blogs, function($b) use ($blog) {
    return $b['category'] === $blog['category'] && $b['id'] !== $blog['id'];
});

// Sort by date (newest first) for related articles
usort($related, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

// Get previous and next articles (by date, newest first)
$sorted_blogs = $blogs;
usort($sorted_blogs, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$current_index = null;
foreach ($sorted_blogs as $idx => $b) {
    if ($b['id'] == $post_id) {
        $current_index = $idx;
        break;
    }
}

$prev_article = ($current_index > 0) ? $sorted_blogs[$current_index - 1] : null;
$next_article = ($current_index !== null && $current_index < count($sorted_blogs) - 1) ? $sorted_blogs[$current_index + 1] : null;
?>

<main class="min-h-screen bg-white dark:bg-gray-900 transition-colors duration-300">
    <!-- Featured Image Hero -->
    <div class="relative h-96 md:h-[550px] overflow-hidden">
        <img src="<?php echo htmlspecialchars($blog['image']); ?>" 
             alt="<?php echo htmlspecialchars($blog['title']); ?>" 
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-80"></div>
        
        <!-- Hero Content Overlay -->
        <div class="absolute inset-0 flex flex-col justify-between p-6 sm:p-10 md:p-16">
            <!-- Top Navigation on Image -->
            <div class="flex items-center justify-between">
                <!-- Back Link -->
                <a href="blog.php" class="inline-flex items-center gap-2 text-white/80 hover:text-white transition-colors font-medium">
                    <i class="fas fa-arrow-left"></i> Back to Blog
                </a>
                
                <!-- Category Badge -->
                <span class="inline-block bg-gradient-to-r from-purple-500 to-pink-500 text-white px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                    <?php echo htmlspecialchars($blog['category']); ?>
                </span>
            </div>

            <!-- Bottom Content -->
            <div class="max-w-4xl w-full">
                <!-- Title -->
                <h1 class="text-2xl sm:text-4xl md:text-5xl lg:text-6xl font-display font-bold text-white mb-6 leading-tight">
                    <?php echo htmlspecialchars($blog['title']); ?>
                </h1>

                <!-- Meta -->
                <div class="flex flex-wrap items-center gap-4 text-white/90 text-sm md:text-base">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($blog['author']); ?></span>
                    </div>
                    <span class="w-1 h-1 bg-white/50 rounded-full"></span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar"></i>
                        <span><?php echo format_date($blog['date']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Article Content -->
    <article class="py-16 md:py-24 container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Lead Section -->
            <div class="mb-12 p-4 md:p-8 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 border-l-4 border-purple-500 rounded-r-lg">
                <p class="text-sm md:text-lg lg:text-xl font-semibold text-gray-800 dark:text-gray-100">
                    <?php echo htmlspecialchars($blog['excerpt']); ?>
                </p>
            </div>


            <!-- Article Body -->
            <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                <?php 
                $paragraphs = array_filter(array_map('trim', explode("\n", $blog['content'])));
                foreach ($paragraphs as $para): 
                ?>
                    <p class="text-base md:text-lg leading-relaxed mb-6">
                        <?php echo htmlspecialchars($para); ?>
                    </p>
                <?php endforeach; ?>
            </div>

            <!-- Divider -->
            <div class="my-12 border-t-2 border-gray-200 dark:border-gray-700"></div>

            <!-- Author Card with Photo -->
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 border border-purple-200 dark:border-purple-800 rounded-xl p-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <!-- Author Photo -->
                    <a href="profile.php?person=<?php echo urlencode($blog['author']); ?>" class="flex-shrink-0 hover:opacity-80 transition-opacity">
                        <?php if ($author_member): ?>
                            <img src="<?php echo htmlspecialchars($author_member['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($author_member['name']); ?>"
                                 class="w-24 h-24 md:w-32 md:h-32 rounded-full object-cover border-4 border-purple-500 cursor-pointer">
                        <?php else: ?>
                            <div class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white text-3xl font-bold border-4 border-purple-500 cursor-pointer">
                                <?php echo strtoupper(substr($blog['author'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                    </a>
                    <!-- Author Info -->
                    <div class="flex-grow text-center sm:text-left">
                        <a href="profile.php?person=<?php echo urlencode($blog['author']); ?>" class="text-2xl font-bold text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-400 transition-colors mb-2 inline-block">
                            <?php echo htmlspecialchars($blog['author']); ?>
                        </a>
                        <?php if ($author_member): ?>
                            <p class="text-sm text-purple-600 dark:text-purple-400 font-semibold mb-2">
                                <?php echo htmlspecialchars($author_member['role']); ?>
                            </p>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                <?php echo htmlspecialchars($author_member['bio']); ?>
                            </p>
                        <?php else: ?>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                A passionate member of the ICT Club community, dedicated to sharing knowledge and expertise on modern technology, development, and innovation.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Share Section -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Share this article</h3>
                </div>
                <div class="flex flex-wrap gap-2 md:gap-3">
                    <?php 
                    $current_url = urlencode((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                    $article_title = urlencode($blog['title']);
                    ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $current_url; ?>" target="_blank" class="inline-flex items-center justify-center gap-2 w-10 h-10 md:w-auto md:h-auto md:px-5 md:py-2.5 rounded-full bg-blue-600 text-white hover:bg-blue-700 hover:shadow-lg transition-all duration-300 transform hover:scale-105" title="Share on Facebook">
                        <i class="fab fa-facebook-f text-sm md:text-base"></i>
                        <span class="hidden md:inline text-sm font-semibold">Facebook</span>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $current_url; ?>" target="_blank" class="inline-flex items-center justify-center gap-2 w-10 h-10 md:w-auto md:h-auto md:px-5 md:py-2.5 rounded-full bg-blue-700 text-white hover:bg-blue-800 hover:shadow-lg transition-all duration-300 transform hover:scale-105" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in text-sm md:text-base"></i>
                        <span class="hidden md:inline text-sm font-semibold">LinkedIn</span>
                    </a>
                    <a href="mailto:?subject=<?php echo $article_title; ?>&body=Check%20out%20this%20article:%20<?php echo $current_url; ?>" class="inline-flex items-center justify-center gap-2 w-10 h-10 md:w-auto md:h-auto md:px-5 md:py-2.5 rounded-full bg-red-600 text-white hover:bg-red-700 hover:shadow-lg transition-all duration-300 transform hover:scale-105" title="Share via Email">
                        <i class="fas fa-envelope text-sm md:text-base"></i>
                        <span class="hidden md:inline text-sm font-semibold">Email</span>
                    </a>
                    <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link copied to clipboard!');" class="inline-flex items-center justify-center gap-2 w-10 h-10 md:w-auto md:h-auto md:px-5 md:py-2.5 rounded-full bg-gray-600 dark:bg-gray-700 text-white hover:bg-gray-700 dark:hover:bg-gray-600 hover:shadow-lg transition-all duration-300 transform hover:scale-105" title="Copy link">
                        <i class="fas fa-link text-sm md:text-base"></i>
                        <span class="hidden md:inline text-sm font-semibold">Copy</span>
                    </button>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="mt-16 pt-12 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Leave a Comment</h3>
                
                <div class="bg-white dark:bg-gray-800 rounded-xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg">
                    <form class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Name</label>
                                <input type="text" placeholder="Your name" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Email</label>
                                <input type="email" placeholder="email@example.com" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-3">Your Comment</label>
                            <textarea placeholder="Share your thoughts about this article..." rows="6" required class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors resize-none"></textarea>
                        </div>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> Post Comment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Related Articles -->
            <?php if (!empty($related)): ?>
            <div class="mt-16 pt-12 border-t border-gray-200 dark:border-gray-700">
                <h2 class="text-3xl font-display font-bold text-gray-900 dark:text-white mb-10">
                    More from <span class="bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent"><?php echo htmlspecialchars($blog['category']); ?></span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                    <?php 
                    $category_colors = [
                        'Web Development' => 'blue',
                        'UI/UX Design' => 'pink',
                        'Programming' => 'purple',
                        'JavaScript Frameworks' => 'yellow',
                        'Backend Development' => 'indigo',
                        'Security' => 'red',
                        'Cloud Computing' => 'cyan'
                    ];
                    $count = 0;
                    foreach ($related as $related_blog):
                        if ($count >= 4) break;
                        $count++;
                        $color = isset($category_colors[$related_blog['category']]) ? $category_colors[$related_blog['category']] : 'purple';
                    ?>
                        <!-- Blog Card -->
                        <div class="group bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 border border-gray-200 dark:border-gray-700">
                            
                            <!-- Image with Overlay -->
                            <a href="blog-post.php?post_id=<?php echo urlencode($related_blog['id']); ?>" class="block relative h-48 overflow-hidden bg-gray-300 dark:bg-gray-700">
                                <img src="<?php echo htmlspecialchars($related_blog['image']); ?>" alt="<?php echo htmlspecialchars($related_blog['title']); ?>" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <!-- Gradient Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <!-- Category Badge - Gradient -->
                                <span class="absolute top-2 right-2 px-2 py-1 bg-gradient-to-r from-<?php echo $color; ?>-500 to-<?php echo $color; ?>-600 text-white text-[8px] font-bold uppercase rounded-md shadow-lg transform group-hover:scale-110 transition-transform duration-300">
                                    <?php echo htmlspecialchars($related_blog['category']); ?>
                                </span>
                            </a>

                            <!-- Content -->
                            <div class="p-5">
                                <!-- Title -->
                                <a href="blog-post.php?post_id=<?php echo urlencode($related_blog['id']); ?>" class="block">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                                        <?php echo htmlspecialchars($related_blog['title']); ?>
                                    </h3>
                                </a>

                                <!-- Excerpt -->
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300">
                                    <?php echo htmlspecialchars($related_blog['excerpt']); ?>
                                </p>

                                <!-- Meta - Enhanced with Icons -->
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400 mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-purple-600"></i>
                                        <span><?php echo format_date($related_blog['date']); ?></span>
                                    </div>
                                    <a href="profile.php?person=<?php echo urlencode($related_blog['author']); ?>" class="flex items-center gap-2 hover:text-purple-600 transition-colors" onclick="event.stopPropagation();">
                                        <?php if (isset($member_images[$related_blog['author']])): ?>
                                            <img src="<?php echo htmlspecialchars($member_images[$related_blog['author']]); ?>" alt="<?php echo htmlspecialchars($related_blog['author']); ?>" 
                                                 class="w-5 h-5 rounded-full object-cover border border-purple-600/30 hover:border-purple-600 transition-all">
                                        <?php else: ?>
                                            <i class="fas fa-user text-purple-600"></i>
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($related_blog['author']); ?></span>
                                    </a>
                                </div>

                                <!-- Read More -->
                                <a href="blog-post.php?post_id=<?php echo urlencode($related_blog['id']); ?>" class="inline-flex items-center gap-2 text-purple-600 dark:text-purple-400 font-semibold text-sm hover:gap-3 transition-all duration-300 group/link">
                                    <span>Read More</span>
                                    <i class="fas fa-arrow-right transform group-hover/link:translate-x-1 transition-transform duration-300"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Previous/Next Article Navigation -->
            <?php if ($prev_article || $next_article): ?>
            <div class="mt-16 pt-12 border-t border-gray-200 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Previous Article -->
                    <?php if ($prev_article): ?>
                    <a href="blog-post.php?post_id=<?php echo $prev_article['id']; ?>" class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        <div class="relative h-48 overflow-hidden bg-gray-200 dark:bg-gray-700">
                            <img src="<?php echo htmlspecialchars($prev_article['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($prev_article['title']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition-all duration-300"></div>
                            <div class="absolute inset-0 flex flex-col justify-end p-6 text-white">
                                <p class="text-xs font-semibold uppercase tracking-wider text-purple-300 mb-2">
                                    <i class="fas fa-chevron-left mr-2"></i>Previous Article
                                </p>
                                <h3 class="text-lg font-bold line-clamp-2 group-hover:text-purple-300 transition-colors">
                                    <?php echo htmlspecialchars($prev_article['title']); ?>
                                </h3>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>

                    <!-- Next Article -->
                    <?php if ($next_article): ?>
                    <a href="blog-post.php?post_id=<?php echo $next_article['id']; ?>" class="group relative overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        <div class="relative h-48 overflow-hidden bg-gray-200 dark:bg-gray-700">
                            <img src="<?php echo htmlspecialchars($next_article['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($next_article['title']); ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition-all duration-300"></div>
                            <div class="absolute inset-0 flex flex-col justify-end p-6 text-white">
                                <p class="text-xs font-semibold uppercase tracking-wider text-pink-300 mb-2">
                                    Next Article<i class="fas fa-chevron-right ml-2"></i>
                                </p>
                                <h3 class="text-lg font-bold line-clamp-2 group-hover:text-pink-300 transition-colors">
                                    <?php echo htmlspecialchars($next_article['title']); ?>
                                </h3>
                            </div>
                        </div>
                    </a>
                    <?php else: ?>
                    <div></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Back to Blog -->
            <div class="mt-16 text-center">
                <a href="blog.php" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <i class="fas fa-arrow-left"></i>
                    Back to All Articles
                </a>
            </div>
        </div>
    </article>
</main>

<?php require_once 'includes/footer.php'; ?>
