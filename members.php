<?php
// Include required files for POST handling
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Handle member registration form submission BEFORE including header (to avoid "headers already sent" error)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $skills = sanitize_input($_POST['skills']);
    $motivation = sanitize_input($_POST['motivation']);
    
    // Advanced Profile Fields (Optional)
    $profile_photo = !empty($_POST['profile_photo']) ? sanitize_input($_POST['profile_photo']) : null;
    $bio = !empty($_POST['bio']) ? sanitize_input($_POST['bio']) : null;
    $social_links = [];
    
    // Process optional social media links
    $social_platforms = ['github', 'linkedin', 'twitter', 'facebook', 'instagram', 'website', 'discord', 'telegram'];
    foreach ($social_platforms as $platform) {
        $field_name = 'social_' . $platform;
        if (!empty($_POST[$field_name])) {
            $social_links[$platform] = sanitize_input($_POST[$field_name]);
        }
    }
    
    if (empty($name) || empty($email) || empty($phone) || empty($skills) || empty($motivation)) {
        json_response(error_response('All required fields are required.'));
    }
    
    if (validate_email($email)) {
        // Load registrations and check for duplicate email (case-insensitive)
        $registrations = load_json_data('registrations.json');
        $email_lower = strtolower(trim($email));
        $already_registered = false;
        
        foreach ($registrations as $reg) {
            if (strtolower(trim($reg['email'])) === $email_lower) {
                $already_registered = true;
                break;
            }
        }
        
        if ($already_registered) {
            json_response(warning_response('This email is already registered for membership! Please use a different email or contact us if you need to update your information.'));
        }
        
        // Generate unique member ID: ICTYYYNNNN (sequential number 000-999)
        $currentYear = date('Y');
        $sequentialNumber = 0;
        
        // Count registrations for current year to get next sequential number
        foreach ($registrations as $reg) {
            if (isset($reg['member_id']) && strpos($reg['member_id'], 'ICT-' . $currentYear) === 0) {
                $sequentialNumber++;
            }
        }
        
        $member_id = 'ICT-' . $currentYear . str_pad($sequentialNumber + 1, 3, '0', STR_PAD_LEFT);
        
        // Save registration to JSON file
        $registration = [
            'member_id' => $member_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'skills' => $skills,
            'motivation' => $motivation,
            'profile_photo' => $profile_photo,
            'bio' => $bio,
            'social_links' => $social_links,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'pending'
        ];
        
        $registrations[] = $registration;
        $save_result = save_json_data('registrations.json', $registrations);
        
        if (!$save_result) {
            json_response(error_response('Failed to save registration. Please try again later.'));
        }
        
        // Send email to admin
        $subject = "New Member Registration - $name";
        $message = "
            <h2>New Member Registration</h2>
            <p><strong>Member ID:</strong> $member_id</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Skills:</strong> $skills</p>
            <p><strong>Motivation:</strong> $motivation</p>";
        
        if ($profile_photo) $message .= "<p><strong>Profile Photo:</strong> $profile_photo</p>";
        if ($bio) $message .= "<p><strong>Bio:</strong> $bio</p>";
        if (!empty($social_links)) {
            $message .= "<p><strong>Social Links:</strong> " . json_encode($social_links) . "</p>";
        }
        
        $message .= "<p><strong>Submitted:</strong> " . date('Y-m-d H:i:s') . "</p>";
        
        $admin_email_sent = send_email(SITE_EMAIL, $subject, $message, $email);
        
        if (!$admin_email_sent) {
            error_log("Failed to send admin notification for member registration: $name ($email)");
        }
        
        // Send confirmation email to applicant
        $confirmation_subject = "Registration Received - " . SITE_NAME;
        $confirmation_message = "
            <h2>Thank you for your interest!</h2>
            <p>Dear $name,</p>
            <p>We have received your membership registration. Your Member ID is: <strong>$member_id</strong></p>
            <p>Our team will review your application and get back to you within 3-5 business days.</p>
            <p>Best regards,<br>" . SITE_NAME . " Team</p>
        ";
        
        $email_sent = send_email($email, $confirmation_subject, $confirmation_message);
        
        json_response(success_response('Registration Successful.', ['member_id' => $member_id]));
    } else {
        json_response(error_response('Invalid email address'));
    }
    exit;
}

// Now include header after handling POST request
$page_title = "Members";
include 'includes/header.php';

$members = load_json_data('members.json');
$executive_members = array_filter($members, function($member) {
    return $member['position'] === 'Executive';
});

// Calculate member statistics
$total_members = count($members);
$total_executives = count(array_filter($members, function($m) { return $m['position'] === 'Executive'; }));
$all_skills = [];
$all_projects = 0;
$blogs = json_decode(file_get_contents('data/blog.json'), true);
$projects = json_decode(file_get_contents('data/projects.json'), true);
foreach ($members as $m) {
    $all_skills = array_merge($all_skills, $m['skills']);
    foreach ($blogs as $b) {
        if ($b['author'] === $m['name']) $all_projects++;
    }
}
$unique_skills = count(array_unique($all_skills));
?>

<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-display font-black mb-6">Our Amazing Team</h1>
            <p class="text-base md:text-2xl font-medium max-w-3xl mx-auto">
                Meet the talented individuals who make <?php echo SITE_NAME; ?> a thriving community of innovation.
            </p>
        </div>
    </div>
</section>

<!-- Member Statistics Dashboard -->
<section class="py-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-2xl p-6 border border-blue-200 dark:border-blue-700 text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up">
                <i class="fas fa-users text-4xl text-blue-600 dark:text-blue-400 mb-3 block"></i>
                <p class="stat-number text-3xl md:text-4xl font-bold text-blue-900 dark:text-blue-200" data-target="<?php echo $total_members; ?>"><?php echo $total_members; ?></p>
                <p class="text-sm text-blue-700 dark:text-blue-300 font-semibold mt-2">Total Members</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-2xl p-6 border border-purple-200 dark:border-purple-700 text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up" style="animation-delay: 100ms;">
                <i class="fas fa-crown text-4xl text-purple-600 dark:text-purple-400 mb-3 block"></i>
                <p class="stat-number text-3xl md:text-4xl font-bold text-purple-900 dark:text-purple-200" data-target="<?php echo $total_executives; ?>"><?php echo $total_executives; ?></p>
                <p class="text-sm text-purple-700 dark:text-purple-300 font-semibold mt-2">Leadership Team</p>
            </div>

            <div class="bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/30 dark:to-pink-800/30 rounded-2xl p-6 border border-pink-200 dark:border-pink-700 text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up" style="animation-delay: 200ms;">
                <i class="fas fa-star text-4xl text-pink-600 dark:text-pink-400 mb-3 block"></i>
                <p class="stat-number text-3xl md:text-4xl font-bold text-pink-900 dark:text-pink-200" data-target="<?php echo $unique_skills; ?>"><?php echo $unique_skills; ?></p>
                <p class="text-sm text-pink-700 dark:text-pink-300 font-semibold mt-2">Unique Skills</p>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 rounded-2xl p-6 border border-orange-200 dark:border-orange-700 text-center hover:shadow-lg transition-all duration-300" data-aos="fade-up" style="animation-delay: 300ms;">
                <i class="fas fa-project-diagram text-4xl text-orange-600 dark:text-orange-400 mb-3 block"></i>
                <p class="stat-number text-3xl md:text-4xl font-bold text-orange-900 dark:text-orange-200" data-target="<?php echo count($projects); ?>"><?php echo count($projects); ?></p>
                <p class="text-sm text-orange-700 dark:text-orange-300 font-semibold mt-2">Active Projects</p>
            </div>
        </div>
    </div>
</section>

<!-- Executive Committee -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Executive Committee
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Our dedicated leadership team</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-8">
            <?php 
            $member_index = 0;
            $role_colors = [
                'President' => 'border-purple-500 shadow-purple-500/30',
                'Vice President' => 'border-blue-500 shadow-blue-500/30',
                'Technical Lead' => 'border-pink-500 shadow-pink-500/30',
                'Events Coordinator' => 'border-green-500 shadow-green-500/30',
                'Treasurer' => 'border-orange-500 shadow-orange-500/30',
                'Secretary' => 'border-indigo-500 shadow-indigo-500/30'
            ];
            $role_badge_colors = [
                'President' => 'from-purple-100 to-purple-50 dark:from-purple-900/40 dark:to-purple-800/30 text-purple-700 dark:text-purple-300',
                'Vice President' => 'from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-300',
                'Technical Lead' => 'from-pink-100 to-pink-50 dark:from-pink-900/40 dark:to-pink-800/30 text-pink-700 dark:text-pink-300',
                'Events Coordinator' => 'from-green-100 to-green-50 dark:from-green-900/40 dark:to-green-800/30 text-green-700 dark:text-green-300',
                'Treasurer' => 'from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-800/30 text-orange-700 dark:text-orange-300',
                'Secretary' => 'from-indigo-100 to-indigo-50 dark:from-indigo-900/40 dark:to-indigo-800/30 text-indigo-700 dark:text-indigo-300'
            ];
            $skill_colors = [
                'from-blue-100 to-blue-50 dark:from-blue-900/40 dark:to-blue-800/30 text-blue-700 dark:text-blue-300',
                'from-purple-100 to-purple-50 dark:from-purple-900/40 dark:to-purple-800/30 text-purple-700 dark:text-purple-300',
                'from-pink-100 to-pink-50 dark:from-pink-900/40 dark:to-pink-800/30 text-pink-700 dark:text-pink-300',
                'from-green-100 to-green-50 dark:from-green-900/40 dark:to-green-800/30 text-green-700 dark:text-green-300',
                'from-orange-100 to-orange-50 dark:from-orange-900/40 dark:to-orange-800/30 text-orange-700 dark:text-orange-300',
                'from-indigo-100 to-indigo-50 dark:from-indigo-900/40 dark:to-indigo-800/30 text-indigo-700 dark:text-indigo-300',
                'from-red-100 to-red-50 dark:from-red-900/40 dark:to-red-800/30 text-red-700 dark:text-red-300',
                'from-cyan-100 to-cyan-50 dark:from-cyan-900/40 dark:to-cyan-800/30 text-cyan-700 dark:text-cyan-300'
            ];
            
            foreach ($executive_members as $member):
                $animation_delay = $member_index * 100;
                $member_index++;
                $border_color = $role_colors[$member['role']] ?? 'border-gray-300';
                $badge_color = $role_badge_colors[$member['role']] ?? 'from-gray-100 to-gray-50 dark:from-gray-900/40 dark:to-gray-800/30 text-gray-700 dark:text-gray-300';
                $is_leadership = in_array($member['role'], ['President', 'Vice President']);
                
                // Role icons mapping
                $role_icons = [
                    'President' => '👑',
                    'Vice President' => '📊',
                    'Technical Lead' => '⚙️',
                    'Events Coordinator' => '🎤',
                    'Treasurer' => '💰',
                    'Secretary' => '📝'
                ];
                $role_icon = $role_icons[$member['role']] ?? '⭐';
                
                // Count projects and blogs for this member
                $projects = json_decode(file_get_contents('data/projects.json'), true);
                $blogs = json_decode(file_get_contents('data/blog.json'), true);
                $member_projects = 0;
                $member_blogs = 0;
                foreach ($projects as $project) {
                    if (in_array($member['name'], $project['team'])) {
                        $member_projects++;
                    }
                }
                foreach ($blogs as $blog) {
                    if ($blog['author'] === $member['name']) {
                        $member_blogs++;
                    }
                }
            ?>
            <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="group bg-white dark:bg-gray-800 rounded-t-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-4 relative border-b-4 <?php echo $border_color; ?>" data-aos="fade-up" style="animation-delay: <?php echo $animation_delay; ?>ms;">
                <div class="absolute inset-0 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none shadow-lg" style="box-shadow: 0 0 20px rgba(139, 92, 246, 0.3);"></div>
                <div class="relative h-80 overflow-hidden cursor-pointer rounded-t-2xl">
                    <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 image-glow-pulse">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-60"></div>
                    <!-- Profile Link Indicator -->
                    <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-md rounded-full p-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <i class="fas fa-arrow-right text-white text-sm"></i>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-xl md:text-2xl font-bold group-hover:text-blue-200 transition-colors duration-300"><?php echo $member['name']; ?></h3>
                            <?php if ($is_leadership): ?>
                            <span class="text-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">⭐</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-purple-200 font-semibold flex items-center gap-1 group-hover:text-purple-100 transition-colors duration-300"><span><?php echo $role_icon; ?></span> <?php echo $member['role']; ?></p>
                    </div>
                </div>
                <div class="p-6 -mt-8 rounded-b-2xl bg-gradient-to-br from-indigo-50 via-purple-50 to-white dark:from-gray-800 dark:via-gray-750 dark:to-gray-700">
                    <!-- Quick Stats -->
                    <div class="flex justify-center gap-3 mb-3 text-xs text-gray-600 dark:text-gray-400">
                        <span><i class="fas fa-project-diagram text-blue-500 mr-1"></i><?php echo $member_projects; ?> projects</span>
                        <span><i class="fas fa-file-alt text-purple-500 mr-1"></i><?php echo $member_blogs; ?> articles</span>
                        <span><i class="fas fa-star text-yellow-500 mr-1"></i><?php echo count($member['skills']); ?> skills</span>
                    </div>

                    <!-- Bio with Gradient Fade -->
                    <div class="relative mb-4">
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed line-clamp-2 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-300" style="background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.05) 100%); -webkit-mask-image: linear-gradient(to bottom, black 70%, transparent 100%); mask-image: linear-gradient(to bottom, black 70%, transparent 100%);"><?php echo $member['bio']; ?></p>
                    </div>
                    
                    <!-- Enhanced Divider -->
                    <div class="h-1 bg-gradient-to-r from-transparent via-blue-400 dark:via-blue-500 to-transparent my-3 rounded-full"></div>

                    <!-- Skills with Color Variation -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php 
                        $skill_index = 0;
                        foreach ($member['skills'] as $skill): 
                            $skill_color = $skill_colors[$skill_index % count($skill_colors)];
                            $skill_index++;
                        ?>
                        <span class="px-3 py-1 bg-gradient-to-r <?php echo $skill_color; ?> text-xs rounded-full font-medium">
                            <?php echo $skill; ?>
                        </span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Enhanced Connect Button with Pulse -->
                    <a href="mailto:<?php echo $member['email']; ?>" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-semibold text-sm shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 mb-3 connect-pulse">
                        <i class="fas fa-arrow-right text-sm"></i>
                        <span>Connect</span>
                    </a>

                    <!-- Social Links Row Enhanced -->
                    <div class="flex flex-wrap gap-2 justify-center">
                        <!-- Social Media Links -->
                        <?php 
                        $social = isset($member['social']) ? $member['social'] : [];
                        $social_bg_colors = [
                            'github' => 'from-gray-600 to-gray-800',
                            'linkedin' => 'from-blue-600 to-blue-800',
                            'twitter' => 'from-cyan-400 to-blue-600',
                            'facebook' => 'from-blue-600 to-blue-800',
                            'instagram' => 'from-pink-500 to-purple-600',
                            'whatsapp' => 'from-green-500 to-green-700',
                            'telegram' => 'from-sky-400 to-blue-600',
                            'discord' => 'from-indigo-600 to-indigo-800',
                            'phone' => 'from-red-500 to-red-700',
                            'website' => 'from-orange-500 to-orange-700'
                        ];
                        $social_icons = [
                            'github' => ['icon' => 'fab fa-github', 'color' => 'text-gray-900 dark:text-white'],
                            'linkedin' => ['icon' => 'fab fa-linkedin', 'color' => 'text-blue-600'],
                            'twitter' => ['icon' => 'fab fa-twitter', 'color' => 'text-blue-400'],
                            'facebook' => ['icon' => 'fab fa-facebook', 'color' => 'text-blue-600'],
                            'instagram' => ['icon' => 'fab fa-instagram', 'color' => 'text-pink-600'],
                            'whatsapp' => ['icon' => 'fab fa-whatsapp', 'color' => 'text-green-600'],
                            'telegram' => ['icon' => 'fab fa-telegram', 'color' => 'text-blue-500'],
                            'discord' => ['icon' => 'fab fa-discord', 'color' => 'text-indigo-600'],
                            'phone' => ['icon' => 'fas fa-phone', 'color' => 'text-red-600'],
                            'website' => ['icon' => 'fas fa-globe', 'color' => 'text-orange-600']
                        ];
                        foreach ($social_icons as $platform => $details):
                            if (!empty($social[$platform])):
                                $href = $social[$platform];
                                $target = true;
                                if ($platform === 'phone') {
                                    $href = 'tel:' . $href;
                                    $target = false;
                                }
                                $bg_color = $social_bg_colors[$platform] ?? 'from-gray-500 to-gray-700';
                        ?>
                        <a href="<?php echo htmlspecialchars($href); ?>" <?php if ($target): ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>
                           title="<?php echo ucfirst($platform); ?>"
                           class="w-10 h-10 bg-gradient-to-br <?php echo $bg_color; ?> rounded-lg flex items-center justify-center hover:scale-125 hover:shadow-lg transition-all duration-300 text-white shadow-sm">
                            <i class="<?php echo $details['icon']; ?>"></i>
                        </a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Member Directory with Search -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12" data-aos="fade-up">
            <div class="flex items-center justify-center gap-3 mb-4">
                <h2 class="text-3xl md:text-5xl font-display font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    All Members
                </h2>
                <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-sm font-bold rounded-full">
                    <?php echo count($members); ?> Total
                </span>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">Search and connect with our community</p>
            
            <!-- Role Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-2 mb-6">
                <button class="role-filter px-4 py-2 rounded-full font-semibold text-sm transition-all duration-300 bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-md hover:shadow-lg" data-role="all">
                    All
                </button>
                <?php 
                $roles = [];
                foreach ($members as $member) {
                    if (!in_array($member['role'], $roles) && !in_array($member['role'], ['President', 'Vice President'])) {
                        $roles[] = $member['role'];
                    }
                }
                foreach ($roles as $role): 
                ?>
                <button class="role-filter px-4 py-2 rounded-full font-semibold text-sm transition-all duration-300 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600" data-role="<?php echo $role; ?>">
                    <?php echo $role; ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input type="text" data-search=".member-card" placeholder="Search members by name or skills..." 
                           class="w-full px-6 py-4 rounded-full border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 pl-12 text-lg">
                    <i class="fas fa-search absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3"><span class="search-counter"><?php echo count($members); ?></span> members found</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php 
            $skill_colors = [
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
            foreach ($members as $member): 
            ?>
            <div class="member-card group bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-3" data-aos="zoom-in" data-role="<?php echo $member['role']; ?>">
                <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="block relative w-32 h-32 mx-auto mb-4 cursor-pointer group-hover:scale-110 transition-transform duration-300">
                    <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" class="w-full h-full rounded-full object-cover border-4 border-purple-500 group-hover:border-blue-500 transition-colors duration-300">
                    <div class="absolute bottom-0 right-0 w-8 h-8 bg-green-500 rounded-full border-4 border-white dark:border-gray-700 group-hover:bg-blue-500 transition-colors duration-300"></div>
                </a>
                <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="block text-lg font-bold text-center text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300"><?php echo $member['name']; ?></a>
                <p class="text-purple-600 dark:text-purple-400 text-center font-semibold mb-3 text-sm group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors duration-300"><?php echo $member['role']; ?></p>
                <div class="flex flex-wrap gap-2 justify-center mb-4">
                    <?php 
                    $skill_index = 0;
                    foreach (array_slice($member['skills'], 0, 2) as $skill): 
                        $skill_gradient = $skill_colors[$skill_index % count($skill_colors)];
                        $skill_index++;
                    ?>
                    <span class="px-3 py-1 bg-gradient-to-r <?php echo $skill_gradient; ?> text-white text-xs font-semibold rounded-full group-hover:scale-105 transition-transform duration-300">
                        <?php echo $skill; ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="mailto:<?php echo $member['email']; ?>" title="Email" class="w-9 h-9 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center hover:bg-gradient-to-r hover:from-blue-500 hover:to-purple-500 hover:text-white hover:scale-125 hover:shadow-lg transition-all duration-300 text-gray-700 dark:text-gray-300 email-hover">
                        <i class="fas fa-envelope text-xs"></i>
                    </a>
                    <!-- Social Media Links for Directory -->
                    <?php 
                    $social = isset($member['social']) ? $member['social'] : [];
                    $social_bg_colors = [
                        'github' => 'from-gray-600 to-gray-800',
                        'linkedin' => 'from-blue-600 to-blue-800',
                        'twitter' => 'from-cyan-400 to-blue-600',
                        'facebook' => 'from-blue-600 to-blue-800',
                        'instagram' => 'from-pink-500 to-purple-600',
                        'whatsapp' => 'from-green-500 to-green-700',
                        'telegram' => 'from-sky-400 to-blue-600',
                        'discord' => 'from-indigo-600 to-indigo-800',
                        'phone' => 'from-red-500 to-red-700',
                        'website' => 'from-orange-500 to-orange-700'
                    ];
                    $social_icons = [
                        'github' => 'fab fa-github',
                        'linkedin' => 'fab fa-linkedin',
                        'twitter' => 'fab fa-twitter',
                        'facebook' => 'fab fa-facebook',
                        'instagram' => 'fab fa-instagram',
                        'whatsapp' => 'fab fa-whatsapp',
                        'telegram' => 'fab fa-telegram',
                        'discord' => 'fab fa-discord',
                        'imo' => 'fas fa-phone',
                        'website' => 'fas fa-globe'
                    ];
                    foreach ($social_icons as $platform => $icon):
                        if (!empty($social[$platform])):
                            $link = $platform === 'imo' ? 'tel:' . str_replace([' ', '-'], '', $social[$platform]) : $social[$platform];
                            $target = $platform === 'imo' ? '' : 'target="_blank"';
                            $bg_color = $social_bg_colors[$platform] ?? 'from-gray-500 to-gray-700';
                    ?>
                    <a href="<?php echo $link; ?>" <?php echo $target; ?> rel="noopener noreferrer" title="<?php echo ucfirst($platform); ?>" 
                       class="w-9 h-9 bg-gradient-to-br <?php echo $bg_color; ?> rounded-lg flex items-center justify-center hover:scale-125 hover:shadow-lg transition-all duration-300 text-white">
                        <i class="<?php echo $icon; ?> text-xs"></i>
                    </a>
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Achievement Gallery -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Member Achievements
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Celebrating our members' successes</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php 
            $achievements = [
                ['title' => 'Hackathon Winners', 'count' => 25, 'suffix' => '+', 'icon' => 'fa-trophy', 'color' => 'from-yellow-400 to-orange-500'],
                ['title' => 'Published Papers', 'count' => 15, 'suffix' => '+', 'icon' => 'fa-file-alt', 'color' => 'from-blue-400 to-purple-500'],
                ['title' => 'Job Placements', 'count' => 50, 'suffix' => '+', 'icon' => 'fa-briefcase', 'color' => 'from-green-400 to-blue-500'],
                ['title' => 'Certifications', 'count' => 100, 'suffix' => '+', 'icon' => 'fa-certificate', 'color' => 'from-pink-400 to-red-500'],
                ['title' => 'Open Source Contributions', 'count' => 200, 'suffix' => '+', 'icon' => 'fa-code-branch', 'color' => 'from-indigo-400 to-purple-500'],
                ['title' => 'Startup Founders', 'count' => 10, 'suffix' => '+', 'icon' => 'fa-rocket', 'color' => 'from-purple-400 to-pink-500']
            ];
            foreach ($achievements as $achievement): ?>
            <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2" data-aos="zoom-in" data-aos-duration="500">
                <div class="w-20 h-20 bg-gradient-to-br <?php echo $achievement['color']; ?> rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas <?php echo $achievement['icon']; ?> text-white text-3xl"></i>
                </div>
                <div class="counter counter-value text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2" data-target="<?php echo $achievement['count']; ?>" data-suffix="<?php echo $achievement['suffix']; ?>">0</div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo $achievement['title']; ?></h3>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Join the Club CTA Section -->
<section class="py-20 bg-gradient-to-br from-blue-600 to-purple-600">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center text-white" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-6">Ready to Join <?php echo SITE_NAME; ?>?</h2>
            <p class="text-xl text-blue-100 mb-12">Become part of our thriving community of innovators and tech enthusiasts</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <i class="fas fa-network-wired text-4xl mb-4 block text-blue-200"></i>
                    <h3 class="text-xl font-bold mb-2">Network</h3>
                    <p class="text-sm text-blue-100">Connect with like-minded tech professionals and mentors</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <i class="fas fa-lightbulb text-4xl mb-4 block text-blue-200"></i>
                    <h3 class="text-xl font-bold mb-2">Learn & Grow</h3>
                    <p class="text-sm text-blue-100">Develop skills through workshops, projects, and events</p>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/20 transition-all duration-300">
                    <i class="fas fa-rocket text-4xl mb-4 block text-blue-200"></i>
                    <h3 class="text-xl font-bold mb-2">Build Impact</h3>
                    <p class="text-sm text-blue-100">Collaborate on real-world projects and make a difference</p>
                </div>
            </div>

            <a href="#join-form" class="inline-block px-8 py-4 bg-white text-purple-600 font-bold rounded-lg hover:bg-blue-50 transform hover:scale-105 transition-all duration-300 shadow-xl">
                <i class="fas fa-arrow-down mr-2"></i>Start Your Application
            </a>
        </div>
    </div>
</section>

<!-- Join Club Application Form -->
<section class="py-20 bg-white dark:bg-gray-900" id="join-form">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-3xl p-8 md:p-12 shadow-2xl border-2 border-purple-200 dark:border-purple-700" data-aos="zoom-in">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Membership Application
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Join our community - it only takes a few minutes!</p>
                    <div class="flex justify-center gap-1 mt-6">
                        <span class="w-3 h-3 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></span>
                        <span class="w-3 h-3 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></span>
                        <span class="w-3 h-3 bg-gray-300 dark:bg-gray-600 rounded-full"></span>
                    </div>
                </div>

                <form action="/members.php" method="POST" class="space-y-6" data-validate="true" id="membership-form" onsubmit="return submitForm(event)">
                    <input type="hidden" name="action" value="register">
                    
                    <!-- Form Section 1: Contact Info -->
                    <div class="space-y-4 pb-6 border-b-2 border-gray-300 dark:border-gray-600">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full text-sm font-bold">1</span>
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                    <i class="fas fa-user text-blue-500"></i> Full Name *
                                </label>
                                <input type="text" name="name" id="field-name" placeholder="Abdullah" required
                                       class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300">
                                <p class="text-xs text-red-500 dark:text-red-400 mt-1 hidden" id="error-name"></p>
                            </div>
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                    <i class="fas fa-envelope text-pink-500"></i> Email Address *
                                </label>
                                <input type="email" name="email" id="field-email" placeholder="email@example.com" required
                                       class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300">
                                <p class="text-xs text-red-500 dark:text-red-400 mt-1 hidden" id="error-email"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                <i class="fas fa-phone text-green-500"></i> Phone Number *
                            </label>
                            <input type="tel" name="phone" id="phone-input" placeholder="+880 1234-567890" value="+880 1" required
                                   class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                                   data-phone-format="true">
                            <p class="text-xs text-red-500 dark:text-red-400 mt-1 hidden" id="error-phone"></p>
                        </div>
                    </div>

                    <!-- Form Section 2: Skills & Motivation -->
                    <div class="space-y-4" x-data="{ showAdvanced: false }">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="flex items-center justify-center w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full text-sm font-bold">2</span>
                            Your Profile
                        </h3>
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                <i class="fas fa-star text-yellow-500"></i> Your Skills & Interests *
                            </label>
                            
                            <!-- Skills Search Component -->
                            <div class="skills-component space-y-3">
                                <!-- Max Skills Progress Bar -->
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Skills Progress</span>
                                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400" id="skills-counter">0/10</span>
                                </div>
                                <div class="w-full h-2 bg-gray-300 dark:bg-gray-600 rounded-full overflow-hidden">
                                    <div id="skills-progress-bar" class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full transition-all duration-500" style="width: 0%"></div>
                                </div>

                                <!-- Selected Skills Display with Better Empty State -->
                                <div id="selected-skills" class="flex flex-wrap gap-1 min-h-8 p-2 bg-gray-50 dark:bg-gray-700 rounded-xl border-2 border-gray-300 dark:border-gray-600">
                                    <div id="empty-state" class="w-full text-center py-4">
                                        <i class="fas fa-lightbulb text-3xl text-gray-400 dark:text-gray-500 mb-2 block"></i>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">Select your skills and interests to showcase your expertise!</p>
                                    </div>
                                </div>

                                <!-- Skills Search Input -->
                                <div class="relative">
                                    <input type="text" id="skills-search" placeholder="Type to search skills..." 
                                           class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300">
                                    <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    
                                    <!-- Skills Dropdown Suggestions with Counter -->
                                    <div id="skills-dropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl shadow-lg z-50 max-h-72 overflow-y-auto">
                                        <!-- Search Counter -->
                                        <div id="dropdown-counter" class="sticky top-0 px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                            <span id="results-count">0</span> results found
                                        </div>
                                        <!-- Populated by JavaScript -->
                                    </div>
                                </div>

                            </div>

                            <!-- Hidden Input to Store Skills -->
                            <input type="hidden" name="skills" id="skills-hidden" required>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Search and click skills to add them</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                <i class="fas fa-heart text-red-500"></i> Why do you want to join? *
                            </label>
                            <textarea name="motivation" id="field-motivation" rows="5" placeholder="Tell us what excites you about our club and what you hope to contribute or learn..." required
                                      class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 resize-none"
                                      @input="updateCharCounter('motivation', 500)"></textarea>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400" id="motivation-counter">0/500</span>
                            </div>
                            <p class="text-xs text-red-500 dark:text-red-400 mt-1 hidden" id="error-motivation"></p>
                        </div>

                        <!-- Advanced Profile Section (Optional) -->
                        <div class="border-t-2 border-gray-300 dark:border-gray-600 pt-6 mt-6">
                            <button type="button" @click="showAdvanced = !showAdvanced" 
                                    class="flex items-center gap-3 text-lg font-bold text-gray-900 dark:text-white hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300 w-full">
                                <span class="flex items-center justify-center w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 text-white rounded-full text-sm font-bold">+</span>
                                <span>Advanced Profile <span class="text-sm text-gray-500 dark:text-gray-400">(Optional)</span></span>
                                <i class="fas fa-chevron-down ml-auto transition-transform duration-300" :class="showAdvanced ? 'transform rotate-180' : ''"></i>
                            </button>

                            <div x-show="showAdvanced" x-transition class="mt-6 space-y-4 p-4 bg-purple-50 dark:bg-gray-800 rounded-xl border-2 border-purple-200 dark:border-purple-700">
                                <!-- Profile Photo URL -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                        <i class="fas fa-image text-blue-500"></i> Profile Photo URL
                                    </label>
                                    <input type="url" name="profile_photo" id="field-profile_photo" placeholder="https://example.com/your-photo.jpg"
                                           class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300"
                                           @input="previewProfilePhoto()">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Paste a direct link to your profile photo - preview will appear after entering URL</p>
                                </div>

                                <!-- Profile Photo Preview -->
                                <div id="preview-section" class="hidden">
                                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                        <i class="fas fa-image text-blue-500"></i> Profile Photo Preview
                                    </label>
                                    <div id="photo-preview" class="mb-4 p-4 bg-white dark:bg-gray-700 rounded-lg border-2 border-blue-300 dark:border-blue-600">
                                        <img id="preview-image" src="" alt="Profile preview" class="max-h-48 rounded-lg mx-auto object-cover">
                                    </div>
                                </div>

                                <!-- Bio -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-3 flex items-center gap-2">
                                        <i class="fas fa-pen text-pink-500"></i> Bio
                                        <span class="ml-auto text-xs" id="bio-counter">0/300</span>
                                    </label>
                                    <textarea name="bio" id="field-bio" rows="4" placeholder="Tell us about yourself, your interests, and what makes you unique..."
                                              class="w-full px-6 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 resize-none"
                                              @input="updateCharCounter('bio', 300)"></textarea>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">Share a brief bio about yourself (max 300 characters)</p>
                                </div>

                                <!-- Social Media Links -->
                                <div class="border-t-2 border-purple-200 dark:border-purple-600 pt-4">
                                    <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-4 flex items-center gap-2">
                                        <i class="fas fa-link text-green-500"></i> Social Media Links
                                    </label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-github text-gray-600 dark:text-gray-400 mr-2"></i>GitHub
                                            </label>
                                            <input type="url" name="social_github" placeholder="https://github.com/yourname"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-linkedin text-blue-600 dark:text-blue-400 mr-2"></i>LinkedIn
                                            </label>
                                            <input type="url" name="social_linkedin" placeholder="https://linkedin.com/in/yourname"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-twitter text-blue-400 dark:text-blue-300 mr-2"></i>Twitter
                                            </label>
                                            <input type="url" name="social_twitter" placeholder="https://twitter.com/yourname"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-facebook text-blue-700 dark:text-blue-500 mr-2"></i>Facebook
                                            </label>
                                            <input type="url" name="social_facebook" placeholder="https://facebook.com/yourname"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-instagram text-pink-600 dark:text-pink-400 mr-2"></i>Instagram
                                            </label>
                                            <input type="url" name="social_instagram" placeholder="https://instagram.com/yourname"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fas fa-globe text-green-600 dark:text-green-400 mr-2"></i>Website
                                            </label>
                                            <input type="url" name="social_website" placeholder="https://yourwebsite.com"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-discord text-indigo-600 dark:text-indigo-400 mr-2"></i>Discord
                                            </label>
                                            <input type="text" name="social_discord" placeholder="YourUsername#0000"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-700 dark:text-gray-300 font-medium mb-2">
                                                <i class="fab fa-telegram text-blue-500 dark:text-blue-400 mr-2"></i>Telegram
                                            </label>
                                            <input type="url" name="social_telegram" placeholder="https://t.me/yourname"
                                                   class="w-full px-4 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-300 text-sm">
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-3">All social media links are optional - only add the ones you'd like to share</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" id="submit-btn" class="w-full px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-bold text-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span id="submit-text">Submit Application</span>
                            <span id="submit-spinner" class="hidden ml-2">
                                <i class="fas fa-spinner fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Success Modal -->
                <div id="success-modal" class="hidden fixed inset-0 bg-black/50 dark:bg-black/70 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeSuccessModal()">
                    <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 md:p-12 max-w-md w-full shadow-2xl transform transition-all">
                        <div class="text-center">
                            <div class="mx-auto w-16 h-16 bg-gradient-to-r from-green-400 to-green-500 rounded-full flex items-center justify-center mb-6 animate-bounce">
                                <i class="fas fa-check text-white text-2xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Success!</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Your membership application has been submitted successfully.</p>
                            
                            <div class="bg-purple-50 dark:bg-gray-700 rounded-xl p-4 mb-6">
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">Your Member ID:</p>
                                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="member-id-display">ICT-2025-001</p>
                            </div>

                            <button onclick="window.location.href='/'" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-300">
                                Return to Home
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills & Expertise Showcase -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Our Collective Expertise
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Technologies and skills our members excel at</p>
        </div>

        <div class="flex flex-wrap justify-center gap-4">
            <?php 
            $skills = ['Python', 'JavaScript', 'React', 'Node.js', 'PHP', 'Java', 'C++', 'Machine Learning', 
                      'AI', 'Cloud Computing', 'DevOps', 'UI/UX Design', 'Blockchain', 'IoT', 'Cybersecurity', 
                      'Mobile Development', 'Data Science', 'Web Development'];
            foreach ($skills as $skill): ?>
            <div class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full font-semibold hover:from-blue-600 hover:to-purple-600 transform hover:scale-110 transition-all duration-300 shadow-lg cursor-pointer" data-aos="zoom-in">
                <?php echo $skill; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Alumni Network -->
<section class="py-20 bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Alumni Network
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Our alumni work at leading tech companies worldwide</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
            <?php 
            $companies = [
                'Google' => ['type' => 'img', 'value' => 'https://cdn-icons-png.flaticon.com/128/281/281764.png'],
                'Microsoft' => ['type' => 'icon', 'value' => 'fab fa-microsoft'],
                'Amazon' => ['type' => 'icon', 'value' => 'fab fa-amazon'],
                'Meta' => ['type' => 'img', 'value' => 'https://cdn.simpleicons.org/meta/1877F2'],
                'Apple' => ['type' => 'img', 'value' => 'https://cdn.simpleicons.org/apple/000000'],
                'Netflix' => ['type' => 'img', 'value' => 'https://cdn.simpleicons.org/netflix/E50914'],
                'Tesla' => ['type' => 'img', 'value' => 'https://cdn.simpleicons.org/tesla/E82127'],
                'SpaceX' => ['type' => 'img', 'value' => 'https://cdn.simpleicons.org/spacex/000000'],
                'IBM' => ['type' => 'img', 'value' => 'https://cdn-icons-png.flaticon.com/128/5969/5969141.png'],
                'Oracle' => ['type' => 'img', 'value' => 'https://cdn.brandfetch.io/idnq7H7qT0/w/400/h/400/theme/dark/icon.png?c=1bxid64Mup7aczewSAYMX&t=1667576597154'],
                'Adobe' => ['type' => 'img', 'value' => 'https://cdn-icons-png.flaticon.com/128/888/888835.png'],
                'Salesforce' => ['type' => 'img', 'value' => 'https://cdn.simpleicons.org/salesforce/00A1DE']
            ];
            foreach ($companies as $company => $logo): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 flex items-center justify-center" data-aos="zoom-in">
                <div class="text-center">
                    <?php if ($logo['type'] === 'img'): ?>
                    <div class="w-20 h-20 flex items-center justify-center mx-auto mb-3">
                        <img src="<?php echo $logo['value']; ?>" alt="<?php echo $company; ?> logo" class="max-w-full max-h-full object-contain" loading="lazy">
                    </div>
                    <?php else: ?>
                    <div class="text-5xl mb-3 text-blue-600 dark:text-blue-400">
                        <i class="<?php echo $logo['value']; ?>"></i>
                    </div>
                    <?php endif; ?>
                    <p class="font-semibold text-gray-900 dark:text-white text-sm"><?php echo $company; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Member Spotlight -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Member Spotlight
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Featuring our outstanding members this month</p>
        </div>

        <div class="max-w-4xl mx-auto bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-3xl p-8 md:p-12 shadow-2xl" data-aos="fade-up">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <img src="<?php echo $members[0]['image']; ?>" alt="<?php echo $members[0]['name']; ?>" class="w-full h-80 object-cover rounded-2xl shadow-xl">
                </div>
                <div>
                    <div class="mb-4">
                        <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-sm font-semibold rounded-full">
                            Member of the Month
                        </span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-2"><?php echo $members[0]['name']; ?></h3>
                    <p class="text-purple-600 dark:text-purple-400 font-semibold text-xl mb-4"><?php echo $members[0]['role']; ?></p>
                    <p class="text-gray-600 dark:text-gray-400 mb-6"><?php echo $members[0]['bio']; ?></p>
                    <div class="flex flex-wrap gap-2 mb-6">
                        <?php foreach ($members[0]['skills'] as $skill): ?>
                        <span class="px-3 py-1 bg-white dark:bg-gray-600 text-blue-700 dark:text-blue-300 text-sm rounded-full font-medium">
                            <?php echo $skill; ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="mailto:<?php echo $members[0]['email']; ?>" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg hover:from-blue-600 hover:to-purple-600 transition-all duration-300">
                            <i class="fas fa-envelope mr-2"></i>Contact
                        </a>
                        <?php 
                        $social = isset($members[0]['social']) ? $members[0]['social'] : [];
                        $social_icons = [
                            'github' => 'fab fa-github',
                            'linkedin' => 'fab fa-linkedin',
                            'twitter' => 'fab fa-twitter',
                            'facebook' => 'fab fa-facebook',
                            'instagram' => 'fab fa-instagram',
                            'whatsapp' => 'fab fa-whatsapp',
                            'telegram' => 'fab fa-telegram',
                            'discord' => 'fab fa-discord',
                            'imo' => 'fas fa-phone',
                            'website' => 'fas fa-globe'
                        ];
                        foreach ($social_icons as $platform => $icon):
                            if (!empty($social[$platform])):
                                $link = $platform === 'imo' ? 'tel:' . str_replace([' ', '-'], '', $social[$platform]) : $social[$platform];
                                $target = $platform === 'imo' ? '' : 'target="_blank"';
                        ?>
                        <a href="<?php echo $link; ?>" <?php echo $target; ?> rel="noopener noreferrer" title="<?php echo ucfirst($platform); ?>" 
                           class="px-4 py-3 bg-white dark:bg-gray-600 text-gray-900 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-500 transition-all duration-300">
                            <i class="<?php echo $icon; ?> mr-2"></i><?php echo ucfirst($platform); ?>
                        </a>
                        <?php endif; endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
