<?php
$page_title = "About Us";
include 'includes/header.php';

$members = load_json_data('members.json');
$executive_members = array_filter($members, function($member) {
    return $member['position'] === 'Executive';
});
?>

<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full mix-blend-multiply filter blur-xl animate-blob"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-72 h-72 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl animate-blob animation-delay-4000"></div>
    </div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-display font-black mb-6">About <?php echo SITE_NAME; ?></h1>
            <p class="text-base md:text-2xl font-medium max-w-3xl mx-auto">
                Empowering students through technology, fostering innovation, and building a community of future tech leaders.
            </p>
        </div>
    </div>
</section>

<!-- Interactive Timeline -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Our Journey
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">From humble beginnings to excellence</p>
        </div>

        <div class="max-w-4xl mx-auto">
            <?php 
            $timeline = [
                ['year' => '2018', 'title' => 'Club Founded', 'description' => 'Started with just 15 passionate students and a vision to revolutionize tech education on campus.'],
                ['year' => '2019', 'title' => 'First Hackathon', 'description' => 'Organized our first 24-hour hackathon with 50+ participants and amazing innovations.'],
                ['year' => '2020', 'title' => 'Online Transition', 'description' => 'Successfully pivoted to online workshops and events, reaching 200+ students globally.'],
                ['year' => '2022', 'title' => 'National Recognition', 'description' => 'Won Best Student Tech Club award at the National Technology Summit.'],
                ['year' => '2024', 'title' => 'Innovation Hub', 'description' => 'Launched our state-of-the-art innovation lab with cutting-edge technology.'],
                ['year' => '2025', 'title' => 'Global Partnerships', 'description' => 'Established partnerships with leading tech companies and expanded to 150+ active members.']
            ];
            foreach ($timeline as $index => $event): ?>
            <div class="relative pl-8 pb-12 border-l-4 border-purple-500 last:pb-0" data-aos="zoom-in" data-aos-delay="<?php echo $index * 100; ?>">
                <div class="absolute left-0 top-0 w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full transform -translate-x-1/2 flex items-center justify-center border-4 border-white dark:border-gray-900 shadow-lg">
                    <div class="w-3 h-3 bg-white rounded-full"></div>
                </div>
                <div class="ml-4">
                    <div class="text-xl md:text-2xl font-bold text-purple-600 dark:text-purple-400 mb-2"><?php echo $event['year']; ?></div>
                    <h3 class="text-lg md:text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo $event['title']; ?></h3>
                    <p class="text-gray-600 dark:text-gray-400"><?php echo $event['description']; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Mission & Vision Cards -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl transform hover:-translate-y-2 transition-all duration-300" data-aos="flip-up">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-bullseye text-white text-3xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-center mb-4 text-gray-900 dark:text-white">Our Mission</h3>
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    To empower students with cutting-edge technology skills, foster innovation, and create a collaborative community of future tech leaders.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl transform hover:-translate-y-2 transition-all duration-300" data-aos="flip-up" data-aos-delay="100">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-eye text-white text-3xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-center mb-4 text-gray-900 dark:text-white">Our Vision</h3>
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    To be the leading student tech organization, driving innovation and preparing the next generation of technology pioneers.
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-xl transform hover:-translate-y-2 transition-all duration-300" data-aos="flip-up" data-aos-delay="200">
                <div class="w-20 h-20 bg-gradient-to-br from-pink-500 to-orange-500 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                    <i class="fas fa-heart text-white text-3xl"></i>
                </div>
                <h3 class="text-xl md:text-2xl font-bold text-center mb-4 text-gray-900 dark:text-white">Our Values</h3>
                <p class="text-gray-600 dark:text-gray-400 text-center">
                    Innovation, collaboration, inclusivity, continuous learning, and excellence in everything we do.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Achievement Statistics -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="600">
            <h2 class="text-3xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Impact By Numbers
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Our achievements speak for themselves</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php foreach ($club_stats as $stat): ?>
            <div class="text-center" data-aos="zoom-in">
                <div class="counter counter-value text-4xl md:text-6xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2" data-target="<?php echo $stat['value']; ?>" data-suffix="<?php echo $stat['suffix']; ?>">0</div>
                <div class="text-gray-600 dark:text-gray-400 font-semibold text-lg"><?php echo $stat['label']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Leadership Team Grid -->
<section class="py-20 bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Leadership Team
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Meet the people who make it all happen</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-8 w-full">
            <?php 
            $member_index = 0;
            $role_colors = [
                'President' => 'border-purple-500',
                'Vice President' => 'border-blue-500',
                'Technical Lead' => 'border-pink-500',
                'Events Coordinator' => 'border-green-500',
                'Treasurer' => 'border-orange-500',
                'Secretary' => 'border-indigo-500'
            ];
            $role_badge_colors = [
                'President' => 'from-purple-100 to-purple-50 dark:from-purple-900/40 dark:to-purple-800/30 text-purple-700 dark:text-purple-300',
                'Vice President' => 'from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-300',
                'Technical Lead' => 'from-pink-100 to-pink-50 dark:from-pink-900/40 dark:to-pink-800/30 text-pink-700 dark:text-pink-300',
                'Events Coordinator' => 'from-green-100 to-green-50 dark:from-green-900/40 dark:to-green-800/30 text-green-700 dark:text-green-300',
                'Treasurer' => 'from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-800/30 text-orange-700 dark:text-orange-300',
                'Secretary' => 'from-indigo-100 to-indigo-50 dark:from-indigo-900/40 dark:to-indigo-800/30 text-indigo-700 dark:text-indigo-300'
            ];
            foreach ($executive_members as $member): 
                $animation_delay = $member_index * 100;
                $member_index++;
                $border_color = $role_colors[$member['role']] ?? 'border-gray-300';
                $badge_color = $role_badge_colors[$member['role']] ?? 'from-gray-100 to-gray-50 dark:from-gray-900/40 dark:to-gray-800/30 text-gray-700 dark:text-gray-300';
                $is_leadership = in_array($member['role'], ['President', 'Vice President']);
                
                // Count projects for this member
                $projects = json_decode(file_get_contents('data/projects.json'), true);
                $member_projects = 0;
                foreach ($projects as $project) {
                    if (in_array($member['name'], $project['team'])) {
                        $member_projects++;
                    }
                }
            ?>
            <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="group flex flex-col w-full" data-aos="fade-up" style="animation-delay: <?php echo $animation_delay; ?>ms;">
                <div class="bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-3 relative flex flex-col border-b-4 <?php echo $border_color; ?>">
                    <!-- Circular Image Container -->
                    <div class="relative px-6 pt-4 pb-6 flex justify-center">
                        <div class="w-48 h-48 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg group-hover:shadow-xl group-hover:scale-105 transition-all duration-500 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 p-1 flex-shrink-0">
                            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" 
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 pb-8 text-center flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Name with Spotlight Badge -->
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <h3 class="text-xl md:text-2xl font-display font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    <?php echo $member['name']; ?>
                                </h3>
                                <?php if ($is_leadership): ?>
                                <span class="text-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">⭐</span>
                                <?php endif; ?>
                            </div>

                            <!-- Quick Stats -->
                            <div class="flex justify-center gap-3 mb-3 text-xs text-gray-600 dark:text-gray-400">
                                <span><i class="fas fa-project-diagram text-blue-500 mr-1"></i><?php echo $member_projects; ?> projects</span>
                                <span><i class="fas fa-star text-yellow-500 mr-1"></i><?php echo count($member['skills']); ?> skills</span>
                            </div>

                            <!-- Role - Colored Badge with Gradient Text -->
                            <div class="inline-block mb-4">
                                <span class="px-4 py-1.5 bg-gradient-to-r <?php echo $badge_color; ?> rounded-full text-xs font-bold">
                                    <?php echo $member['role']; ?>
                                </span>
                            </div>

                            <!-- Bio -->
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-4 leading-relaxed line-clamp-2">
                                <?php echo $member['bio']; ?>
                            </p>

                            <!-- Skills -->
                            <?php if (!empty($member['skills'])): ?>
                            <div class="flex flex-wrap justify-center gap-2 mb-4">
                                <?php foreach (array_slice($member['skills'], 0, 3) as $skill): ?>
                                <span class="px-3 py-1 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/30 dark:to-purple-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold border border-blue-200 dark:border-blue-700/50">
                                    <?php echo $skill; ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Enhanced Divider -->
                        <div class="h-1 bg-gradient-to-r from-transparent via-blue-400 dark:via-blue-500 to-transparent my-3 rounded-full"></div>

                        <!-- Connect Button -->
                        <a href="mailto:<?php echo $member['email']; ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-semibold text-sm shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 mb-3">
                            <i class="fas fa-arrow-right text-sm"></i>
                            <span>Connect</span>
                        </a>

                        <!-- Social Links -->
                        <div class="flex flex-wrap justify-center items-center gap-2 leading-none">
                            <?php 
                            $social = isset($member['social']) ? $member['social'] : [];
                            $social_icons = ['github' => 'fab fa-github', 'linkedin' => 'fab fa-linkedin', 'twitter' => 'fab fa-twitter', 'facebook' => 'fab fa-facebook', 'instagram' => 'fab fa-instagram', 'whatsapp' => 'fab fa-whatsapp', 'telegram' => 'fab fa-telegram', 'discord' => 'fab fa-discord', 'phone' => 'fas fa-phone', 'website' => 'fas fa-globe'];
                            $count = 0;
                            foreach ($social_icons as $platform => $icon):
                                if (!empty($social[$platform]) && $count < 10):
                                    $link = $platform === 'phone' ? 'tel:' . str_replace([' ', '-'], '', $social[$platform]) : $social[$platform];
                                    $target = $platform === 'phone' ? '' : 'target="_blank"';
                                    $count++;
                            ?>
                            <a href="<?php echo $link; ?>" <?php echo $target; ?> rel="noopener noreferrer" class="w-11 h-11 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 hover:bg-gradient-to-br hover:from-blue-500 hover:to-purple-500 hover:text-white transition-all duration-300 text-base" title="<?php echo ucfirst($platform); ?>">
                                <i class="<?php echo $icon; ?>"></i>
                            </a>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Club Facilities Showcase -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Our Facilities
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">State-of-the-art resources at your disposal</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            $facilities = [
                ['icon' => 'fa-laptop-code', 'title' => 'Innovation Lab', 'description' => 'Equipped with latest computers, VR headsets, and development tools'],
                ['icon' => 'fa-robot', 'title' => 'AI & Robotics', 'description' => 'Advanced robotics kits, AI servers, and machine learning resources'],
                ['icon' => 'fa-network-wired', 'title' => 'Networking Lab', 'description' => 'Cisco certified lab with enterprise-grade networking equipment'],
                ['icon' => 'fa-server', 'title' => 'Cloud Infrastructure', 'description' => 'Access to AWS, Azure, and Google Cloud platforms for projects'],
                ['icon' => 'fa-vr-cardboard', 'title' => 'VR/AR Studio', 'description' => 'Virtual and augmented reality development station'],
                ['icon' => 'fa-book-open', 'title' => 'Resource Library', 'description' => 'Extensive collection of tech books, courses, and learning materials']
            ];
            foreach ($facilities as $facility): ?>
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-aos="zoom-in">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas <?php echo $facility['icon']; ?> text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3"><?php echo $facility['title']; ?></h3>
                <p class="text-gray-600 dark:text-gray-400"><?php echo $facility['description']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Future Roadmap -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Future Roadmap
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Where we're heading next</p>
        </div>

        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php 
            $roadmap = [
                ['title' => 'AI Research Center', 'quarter' => 'Q1 2026', 'icon' => 'fa-brain'],
                ['title' => 'International Partnerships', 'quarter' => 'Q2 2026', 'icon' => 'fa-globe'],
                ['title' => 'Startup Incubator', 'quarter' => 'Q3 2026', 'icon' => 'fa-rocket'],
                ['title' => 'Alumni Network Platform', 'quarter' => 'Q4 2026', 'icon' => 'fa-users-cog']
            ];
            foreach ($roadmap as $item): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border-l-4 border-purple-500" data-aos="fade-right">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas <?php echo $item['icon']; ?> text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white"><?php echo $item['title']; ?></h3>
                        <span class="text-sm text-purple-600 dark:text-purple-400 font-semibold"><?php echo $item['quarter']; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Testimonial Carousel -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                What People Say
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Hear from our amazing community</p>
        </div>

        <div class="swiper testimonial-swiper max-w-4xl mx-auto" data-aos="fade-up">
            <div class="swiper-wrapper">
                <?php 
                $testimonials = [
                    ['name' => 'John Smith', 'role' => 'Alumni', 'text' => 'ICT Club transformed my career. The skills I learned here helped me land my dream job at a top tech company!'],
                    ['name' => 'Emma Wilson', 'role' => 'Member', 'text' => 'The community here is incredible. I\'ve made lifelong friends and learned more than I ever imagined.'],
                    ['name' => 'Michael Brown', 'role' => 'Workshop Attendee', 'text' => 'The workshops are world-class. Expert instructors and hands-on projects make learning fun and effective.']
                ];
                foreach ($testimonials as $testimonial): ?>
                <div class="swiper-slide">
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-3xl p-12 shadow-xl">
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full mx-auto mb-6 flex items-center justify-center">
                                <i class="fas fa-quote-left text-white text-2xl"></i>
                            </div>
                            <p class="text-xl text-gray-700 dark:text-gray-300 mb-6 italic">"<?php echo $testimonial['text']; ?>"</p>
                            <h4 class="font-bold text-gray-900 dark:text-white text-lg"><?php echo $testimonial['name']; ?></h4>
                            <p class="text-purple-600 dark:text-purple-400"><?php echo $testimonial['role']; ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination mt-8"></div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
