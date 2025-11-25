<?php
$page_title = "Projects";
include 'includes/header.php';

$projects = load_json_data('projects.json');
$members = load_json_data('members.json');
$categories = array_unique(array_column($projects, 'category'));

// Create member image lookup map
$memberImages = [];
foreach ($members as $member) {
    $memberImages[$member['name']] = $member['image'];
}
?>

<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white" data-aos="fade-up">
            <div class="flex items-center justify-center gap-3 mb-6">
                <h1 class="text-4xl md:text-6xl font-display font-black">Our Projects</h1>
                <span class="px-4 py-2 bg-white/20 backdrop-blur-md text-white text-lg font-bold rounded-full">
                    <?php echo count($projects); ?>
                </span>
            </div>
            <p class="text-base md:text-2xl font-medium max-w-3xl mx-auto">
                Explore our innovative projects that are shaping the future of technology.
            </p>
        </div>
    </div>
</section>

<!-- Filter Buttons -->
<section class="py-12 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-4" data-aos="fade-up">
            <button data-filter="all" data-target=".project-card" 
                    class="active px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-300 shadow-lg">
                All Projects
            </button>
            <?php foreach ($categories as $category): ?>
            <button data-filter="<?php echo strtolower($category); ?>" data-target=".project-card"
                    class="px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full font-semibold hover:bg-gradient-to-r hover:from-blue-500 hover:to-purple-500 hover:text-white transition-all duration-300">
                <?php echo $category; ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Project Portfolio Grid -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            $tech_colors = [
                'from-blue-500 to-blue-600',
                'from-purple-500 to-purple-600',
                'from-pink-500 to-pink-600',
                'from-green-500 to-green-600',
                'from-yellow-500 to-yellow-600',
                'from-red-500 to-red-600',
                'from-indigo-500 to-indigo-600',
                'from-cyan-500 to-cyan-600',
                'from-orange-500 to-orange-600',
                'from-teal-500 to-teal-600'
            ];
            $projectIndex = 0;
            foreach ($projects as $project): 
            ?>
            <div class="project-card group bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-4" 
                 data-category="<?php echo strtolower($project['category']); ?>" data-technologies="<?php echo implode(',', $project['technologies']); ?>" data-aos="fade-up" data-aos-delay="<?php echo $projectIndex * 50; ?>">
                <div class="relative h-56 overflow-hidden bg-gradient-to-br from-blue-500 to-purple-500">
                    <img src="<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-80">
                    
                    <!-- Status Badge with Enhanced Styling -->
                    <span class="absolute top-4 right-4 px-4 py-2 bg-<?php echo $project['status'] === 'Completed' ? 'green' : ($project['status'] === 'In Progress' ? 'yellow' : 'blue'); ?>-500 backdrop-blur-md text-white text-sm font-bold rounded-full shadow-lg group-hover:scale-110 transition-all duration-300 flex items-center gap-1">
                        <i class="fas fa-<?php echo $project['status'] === 'Completed' ? 'check-circle' : ($project['status'] === 'In Progress' ? 'spinner animate-spin' : 'hourglass-start'); ?>"></i>
                        <?php echo $project['status']; ?>
                    </span>
                    
                    <!-- View Details Button Enhanced -->
                    <button onclick="openModal('project-modal-<?php echo $project['id']; ?>')" 
                            class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 backdrop-blur-sm">
                        <span class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-bold text-lg shadow-lg group-hover:scale-110 transition-transform duration-300 flex items-center gap-2">
                            <i class="fas fa-play"></i> View Details
                        </span>
                    </button>
                </div>
                <div class="p-6">
                    <h3 class="text-xl md:text-2xl font-bold mb-3 text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300"><?php echo $project['title']; ?></h3>
                    
                    <!-- Description with Gradient Fade -->
                    <div class="relative mb-4 h-12 overflow-hidden">
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2" style="background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.05) 100%); -webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 100%); mask-image: linear-gradient(to bottom, black 70%, transparent 100%);"><?php echo $project['description']; ?></p>
                    </div>
                    
                    <!-- Technology Badges with Colorful Gradients -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php 
                        $techIndex = 0;
                        foreach (array_slice($project['technologies'], 0, 4) as $tech): 
                            $techGradient = $tech_colors[$techIndex % count($tech_colors)];
                            $techIndex++;
                        ?>
                        <span class="px-3 py-1 bg-gradient-to-r <?php echo $techGradient; ?> text-white text-xs rounded-full font-semibold group-hover:scale-110 transition-transform duration-300">
                            <?php echo $tech; ?>
                        </span>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <!-- Team Member Avatars with Real Images -->
                        <div class="flex -space-x-2">
                            <?php foreach (array_slice($project['team'], 0, 3) as $memberIdx => $member): 
                                $memberImage = isset($memberImages[$member]) ? $memberImages[$member] : null;
                            ?>
                            <?php if ($memberImage): ?>
                            <a href="profile.php?person=<?php echo urlencode($member); ?>" class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-700 cursor-pointer hover:scale-125 hover:z-10 transition-all duration-300 group/member relative overflow-hidden shadow-md hover:shadow-lg" title="<?php echo htmlspecialchars($member); ?>">
                                <img src="<?php echo htmlspecialchars($memberImage); ?>" alt="<?php echo htmlspecialchars($member); ?>" class="w-full h-full object-cover">
                                <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-1 rounded text-xs font-semibold opacity-0 group-hover/member:opacity-100 transition-opacity duration-300 whitespace-nowrap z-50">
                                    <?php echo htmlspecialchars($member); ?>
                                </div>
                            </a>
                            <?php else: ?>
                            <div class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-700 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-md hover:shadow-lg cursor-default group/member relative" title="<?php echo htmlspecialchars($member); ?>">
                                <?php echo strtoupper(substr($member, 0, 1)); ?>
                                <div class="absolute -bottom-8 left-1/2 transform -translate-x-1/2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-2 py-1 rounded text-xs font-semibold opacity-0 group-hover/member:opacity-100 transition-opacity duration-300 whitespace-nowrap z-50">
                                    <?php echo htmlspecialchars($member); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- GitHub and Demo Buttons Enhanced -->
                        <div class="flex space-x-2">
                            <a href="<?php echo $project['github']; ?>" title="GitHub Repository" class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-800 rounded-lg flex items-center justify-center hover:scale-125 hover:shadow-lg transition-all duration-300 text-white shadow-md">
                                <i class="fab fa-github"></i>
                            </a>
                            <a href="<?php echo $project['demo']; ?>" title="Live Demo" class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center hover:scale-125 hover:shadow-lg transition-all duration-300 text-white shadow-md">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            <!-- Share Button -->
                            <button onclick="shareProject('<?php echo htmlspecialchars($project['title']); ?>', '<?php echo htmlspecialchars($project['demo']); ?>')" title="Share Project" class="w-10 h-10 bg-gradient-to-br from-pink-500 to-orange-500 rounded-lg flex items-center justify-center hover:scale-125 hover:shadow-lg transition-all duration-300 text-white shadow-md">
                                <i class="fas fa-share-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Modal -->
            <div id="project-modal-<?php echo $project['id']; ?>" class="modal hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
                    <div class="relative h-80 bg-gradient-to-br from-blue-500 to-purple-500">
                        <img src="<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="w-full h-full object-cover opacity-80">
                        <span class="absolute top-4 left-4 px-4 py-2 bg-<?php echo $project['status'] === 'Completed' ? 'green' : ($project['status'] === 'In Progress' ? 'yellow' : 'blue'); ?>-500/90 backdrop-blur-sm text-white rounded-full font-semibold">
                            <?php echo $project['status']; ?>
                        </span>
                        <button onclick="closeModal('project-modal-<?php echo $project['id']; ?>')" 
                                class="absolute top-4 right-4 w-12 h-12 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center hover:bg-white transition-all duration-300">
                            <i class="fas fa-times text-gray-900"></i>
                        </button>
                    </div>
                    <div class="p-6 md:p-8">
                        <h2 class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6"><?php echo $project['title']; ?></h2>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-lg mb-6"><?php echo $project['description']; ?></p>
                        
                        <div class="mb-6">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3">Technology Stack</h3>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($project['technologies'] as $tech): ?>
                                <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 text-blue-700 dark:text-blue-300 rounded-full font-medium">
                                    <?php echo $tech; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3">Team Members</h3>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($project['team'] as $member): ?>
                                <span class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                    <?php echo $member; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <a href="<?php echo $project['github']; ?>" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg text-center font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-300">
                                <i class="fab fa-github mr-2"></i>View on GitHub
                            </a>
                            <a href="<?php echo $project['demo']; ?>" class="flex-1 px-6 py-3 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg text-center font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300">
                                <i class="fas fa-external-link-alt mr-2"></i>Live Demo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Technology Stack Badges -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Technologies We Use
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Cutting-edge tools and frameworks</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            <?php 
            $tech_icons = [
                'PHP' => 'fab fa-php',
                'Python' => 'fab fa-python',
                'JavaScript' => 'fab fa-js-square',
                'React' => 'fab fa-react',
                'Vue' => 'fab fa-vuejs',
                'Angular' => 'fab fa-angular',
                'Node.js' => 'fab fa-node-js',
                'TypeScript' => 'fab fa-js-square',
                'HTML5' => 'fab fa-html5',
                'CSS3' => 'fab fa-css3-alt',
                'MySQL' => 'fas fa-database',
                'MongoDB' => 'fas fa-database',
                'PostgreSQL' => 'fas fa-database',
                'Docker' => 'fab fa-docker',
                'AWS' => 'fab fa-aws',
                'Git' => 'fab fa-git-alt',
                'Bootstrap' => 'fab fa-bootstrap',
                'Tailwind' => 'fas fa-wand-magic-sparkles',
                'REST API' => 'fas fa-code',
                'GraphQL' => 'fas fa-code',
                'Firebase' => 'fas fa-fire',
                'Stripe' => 'fab fa-stripe-s',
                'Jenkins' => 'fas fa-cogs',
                'Linux' => 'fab fa-linux',
                'Ubuntu' => 'fab fa-ubuntu',
                'Mac' => 'fab fa-apple',
                'Windows' => 'fab fa-windows'
            ];
            
            function get_tech_icon($tech, $icons) {
                if (isset($icons[$tech])) {
                    return $icons[$tech];
                }
                // Default icon based on tech type
                if (strpos(strtolower($tech), 'database') !== false || strpos(strtolower($tech), 'sql') !== false) {
                    return 'fas fa-database';
                } elseif (strpos(strtolower($tech), 'framework') !== false) {
                    return 'fas fa-cube';
                }
                return 'fas fa-code';
            }
            
            $allTechnologies = [];
            $techUsageCount = [];
            foreach ($projects as $project) {
                $allTechnologies = array_merge($allTechnologies, $project['technologies']);
                foreach ($project['technologies'] as $tech) {
                    $techUsageCount[$tech] = ($techUsageCount[$tech] ?? 0) + 1;
                }
            }
            $uniqueTechnologies = array_unique($allTechnologies);
            // Sort by usage count (descending)
            arsort($techUsageCount);
            $animationIndex = 0;
            foreach (array_keys($techUsageCount) as $tech): 
                $usageCount = $techUsageCount[$tech];
                $isPopular = $usageCount >= 3;
            ?>
            <div class="tech-card group bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 text-center shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-3 cursor-pointer relative" data-aos="zoom-in" data-aos-delay="<?php echo $animationIndex * 50; ?>" data-tech="<?php echo htmlspecialchars($tech); ?>">
                <?php if ($isPopular): ?>
                <div class="absolute top-2 right-2 bg-gradient-to-r from-yellow-400 to-orange-400 text-white px-2 py-1 rounded-full text-xs font-bold flex items-center gap-1">
                    <i class="fas fa-star text-yellow-100"></i> Popular
                </div>
                <?php endif; ?>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3 shadow-lg group-hover:shadow-2xl group-hover:scale-110 group-hover:from-blue-600 group-hover:to-pink-600 transition-all duration-300">
                    <i class="<?php echo get_tech_icon($tech, $tech_icons); ?> text-white text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300"><?php echo $tech; ?></h3>
            </div>
            <?php 
            $animationIndex++;
            endforeach; ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update tech count
    const techCards = document.querySelectorAll('.tech-card');
    document.querySelector('.tech-total-count').textContent = techCards.length;
    
    // Tech selector functionality
    techCards.forEach(card => {
        card.addEventListener('click', function() {
            const selectedTech = this.getAttribute('data-tech');
            
            // Toggle selected state
            const isSelected = this.classList.contains('selected-tech');
            document.querySelectorAll('.tech-card').forEach(c => c.classList.remove('selected-tech'));
            
            if (!isSelected) {
                this.classList.add('selected-tech');
                // Filter projects
                const projectCards = document.querySelectorAll('.project-card');
                projectCards.forEach(project => {
                    const techList = project.getAttribute('data-technologies') || '';
                    if (techList.includes(selectedTech)) {
                        project.style.display = 'block';
                        project.style.opacity = '1';
                    } else {
                        project.style.opacity = '0.3';
                    }
                });
            } else {
                // Show all projects again
                document.querySelectorAll('.project-card').forEach(project => {
                    project.style.display = 'block';
                    project.style.opacity = '1';
                });
            }
        });
    });
});
</script>

<style>
.selected-tech {
    box-shadow: 0 0 30px rgba(59, 130, 246, 0.5) !important;
    transform: scale(1.05) translateY(-12px) !important;
}
</style>

<!-- Collaboration Showcase -->
<section class="py-20 bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Collaboration & Impact
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Working together to create amazing solutions</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center shadow-xl" data-aos="fade-up">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-users text-white text-3xl"></i>
                </div>
                <h3 class="counter counter-value text-4xl font-bold text-gray-900 dark:text-white mb-2" data-target="<?php echo count(array_unique(array_merge(...array_column($projects, 'team')))); ?>" data-suffix="+">0</h3>
                <p class="text-gray-600 dark:text-gray-400">Team Members Involved</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center shadow-xl" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-code-branch text-white text-3xl"></i>
                </div>
                <h3 class="counter counter-value text-4xl font-bold text-gray-900 dark:text-white mb-2" data-target="<?php echo count($uniqueTechnologies); ?>" data-suffix="+">0</h3>
                <p class="text-gray-600 dark:text-gray-400">Technologies Used</p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 text-center shadow-xl" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-star text-white text-3xl"></i>
                </div>
                <h3 class="counter counter-value text-4xl font-bold text-gray-900 dark:text-white mb-2" data-target="<?php echo count(array_filter($projects, function($p) { return $p['status'] === 'Completed'; })); ?>" data-suffix="">0</h3>
                <p class="text-gray-600 dark:text-gray-400">Completed Projects</p>
            </div>
        </div>
    </div>
</section>

<!-- GitHub Integration Section -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <div class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-3xl p-1 shadow-2xl">
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-12">
                    <i class="fab fa-github text-6xl text-gray-900 dark:text-white mb-6"></i>
                    <h2 class="text-3xl md:text-4xl font-display font-bold mb-4 text-gray-900 dark:text-white">
                        All Our Projects are Open Source
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                        Explore our repositories, contribute to ongoing projects, and learn from our codebase on GitHub.
                    </p>
                    <a href="https://github.com" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold text-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fab fa-github mr-2"></i>Visit Our GitHub
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
