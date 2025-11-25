<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Handle form submissions BEFORE including header (which outputs HTML)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'contact') {
            $name = sanitize_input($_POST['name']);
            $email = sanitize_input($_POST['email']);
            $subject_line = sanitize_input($_POST['subject']);
            $category = sanitize_input($_POST['category']);
            $message = sanitize_input($_POST['message']);
            
            if (empty($name) || empty($email) || empty($subject_line) || empty($message)) {
                json_response(error_response('All fields are required.'));
            }
            
            if (validate_email($email)) {
                $subject = "New Contact: $subject_line from $name";
                $email_body = "
                    <h2>New Contact Form Submission</h2>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Category:</strong> $category</p>
                    <p><strong>Subject:</strong> $subject_line</p>
                    <p><strong>Message:</strong></p>
                    <p>$message</p>
                ";
                
                // Load existing contacts and generate message number
                $contacts = load_json_data('contacts.json');
                $message_number = count($contacts) + 1;
                
                // Save submission to JSON file
                $submission = [
                    'message_number' => $message_number,
                    'timestamp' => date('Y-m-d H:i:s'),
                    'type' => 'contact',
                    'name' => $name,
                    'email' => $email,
                    'subject' => $subject_line,
                    'category' => $category,
                    'message' => $message
                ];
                
                $contacts[] = $submission;
                $save_result = save_json_data('contacts.json', $contacts);
                
                if (!$save_result) {
                    json_response(error_response('Failed to save your message. Please try again later.'));
                }
                
                // Send email
                $email_sent = send_email(SITE_EMAIL, $subject, $email_body, $email);
                
                // Always show success since message was saved (email is secondary)
                if (!$email_sent) {
                    json_response(warning_response('Your message has been received! We\'ll get back to you soon. Email notification is currently unavailable, but we have your contact information.'));
                }
                
                json_response(success_response('Thank you for contacting us! We will get back to you soon.'));
            } else {
                json_response(error_response('Please provide a valid email address.'));
            }
        }
        elseif ($action === 'newsletter') {
            $email = sanitize_input($_POST['email']);
            $email_lower = strtolower(trim($email));
            
            if (empty($email)) {
                json_response(error_response('Email address is required.'));
            }
            
            if (validate_email($email)) {
                // Save subscription to JSON file
                $subscription = [
                    'email' => $email_lower,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
                
                $subscribers = load_json_data('subscribers.json');
                
                // Check if already subscribed (case-insensitive)
                $already_subscribed = false;
                foreach ($subscribers as $subscriber) {
                    if (strtolower(trim($subscriber['email'])) === $email_lower) {
                        $already_subscribed = true;
                        break;
                    }
                }
                
                if ($already_subscribed) {
                    json_response(warning_response('Warning! This email is already subscribed to our newsletter!'));
                } else {
                    $subscribers[] = $subscription;
                    $save_result = save_json_data('subscribers.json', $subscribers);
                    
                    if (!$save_result) {
                        json_response(error_response('Failed to save subscription. Please try again later.'));
                    }
                    
                    // Send welcome email
                    $subject = "Welcome to " . SITE_NAME . " Newsletter";
                    $email_body = "
                        <h2>Thank you for subscribing!</h2>
                        <p>You have successfully subscribed to our newsletter.</p>
                        <p>You will now receive updates about our events, workshops, and opportunities.</p>
                        <p>Best regards,<br>" . SITE_NAME . " Team</p>
                    ";
                    
                    $email_sent = send_email($email_lower, $subject, $email_body);
                    
                    // Always return success if subscription was saved, even if email fails
                    if ($email_sent) {
                        json_response(success_response('Successfully subscribed! Check your email for confirmation.'));
                    } else {
                        json_response(success_response('Successfully subscribed to our newsletter!'));
                    }
                }
            } else {
                json_response(error_response('Please provide a valid email address.'));
            }
        }
    }
    exit;
}

$page_title = "Contact Us";
include 'includes/header.php';

$members = load_json_data('members.json');
$executive_members = array_filter($members, function($member) {
    return $member['position'] === 'Executive';
});
?>

<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white" data-aos="fade-up">
            <div class="mb-6">
                <i class="fas fa-envelope text-6xl mb-6 inline-block opacity-80"></i>
            </div>
            <h1 class="text-4xl md:text-6xl font-display font-black mb-6">Get In Touch</h1>
            <p class="text-base md:text-2xl font-medium max-w-3xl mx-auto">
                We'd love to hear from you. Send us a message and we'll respond as soon as possible.
            </p>
        </div>
    </div>
</section>

<!-- Main Contact Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
            <!-- Contact Information -->
            <div data-aos="fade-right">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-8">
                    Contact Information
                </h2>
                <p class="text-slate-600 dark:text-slate-400 text-lg mb-8">
                    Multiple ways to reach out to our team.
                </p>

                <div class="space-y-6">
                    <div class="flex gap-4 p-4 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-300" data-aos="fade-right" data-aos-delay="100">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-envelope text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-1">Email</h3>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition-colors"><?php echo SITE_EMAIL; ?></a>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-300" data-aos="fade-right" data-aos-delay="200">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-phone text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-1">Phone</h3>
                            <p class="text-slate-600 dark:text-slate-400">+1 (555) 123-4567</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-300" data-aos="fade-right" data-aos-delay="300">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-map-marker-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-1">Location</h3>
                            <p class="text-slate-600 dark:text-slate-400">University Campus<br>Building A, Room 101</p>
                        </div>
                    </div>

                    <div class="flex gap-4 p-4 rounded-xl hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-300" data-aos="fade-right" data-aos-delay="400">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-1">Office Hours</h3>
                            <p class="text-slate-600 dark:text-slate-400">Monday - Friday<br>9:00 AM - 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gradient-to-br from-purple-50 to-blue-50 dark:bg-gradient-to-br dark:from-slate-800 dark:to-slate-700 rounded-2xl p-8 shadow-lg" data-aos="fade-left">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">Send us a Message</h3>
                <div id="form-message" class="mb-4 hidden p-4 rounded-lg text-sm font-medium"></div>
                
                <form action="/contact.php" method="POST" class="space-y-4" data-validate="true" id="contact-form">
                    <input type="hidden" name="action" value="contact">
                    
                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-2">
                            Your Name
                            <span class="text-red-500 font-bold">*</span>
                        </label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-2">
                            Your Email
                            <span class="text-red-500 font-bold">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-2">
                            Subject
                            <span class="text-red-500 font-bold">*</span>
                        </label>
                        <input type="text" name="subject" required placeholder="What is this about?"
                               class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-2">
                            Category
                            <span class="text-red-500 font-bold">*</span>
                        </label>
                        <select name="category" required
                                class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all">
                            <option value="">Select a category</option>
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Partnership">Partnership</option>
                            <option value="Event Sponsorship">Event Sponsorship</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Feedback">Feedback</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-700 dark:text-slate-300 font-semibold mb-2">
                            Your Message
                            <span class="text-red-500 font-bold">*</span>
                        </label>
                        <textarea name="message" rows="5" required placeholder="Tell us more..."
                                  class="w-full px-4 py-3 rounded-lg border-2 border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all resize-none"></textarea>
                    </div>

                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:shadow-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i><span>Send Message</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Executive Contacts -->
<section class="py-20 bg-slate-50 dark:bg-slate-800">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">Leadership Team</h2>
            <p class="text-slate-600 dark:text-slate-400 text-lg">Connect directly with our executive members</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            <?php 
            $role_colors = [
                'President' => 'purple',
                'Vice President' => 'blue',
                'Technical Lead' => 'pink',
                'Events Coordinator' => 'green',
                'Treasurer' => 'orange',
                'Secretary' => 'indigo'
            ];
            foreach (array_slice($executive_members, 0, 6) as $idx => $member): 
                $roleKey = $member['role'] ?? 'President';
                $color = $role_colors[$roleKey] ?? 'purple';
            ?>
            <div class="bg-white dark:bg-slate-700 rounded-2xl overflow-hidden shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300" data-aos="fade-up" data-aos-delay="<?php echo $idx * 100; ?>">
                <!-- Role Badge -->
                <div class="h-1 bg-gradient-to-r from-<?php echo $color; ?>-400 to-<?php echo $color; ?>-600"></div>
                
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="flex-shrink-0 cursor-pointer hover:opacity-80 transition-opacity">
                            <img src="<?php echo $member['image']; ?>" alt="<?php echo $member['name']; ?>" 
                                 class="w-16 h-16 rounded-full object-cover border-2 border-<?php echo $color; ?>-500 shadow-md">
                        </a>
                        <div>
                            <a href="profile.php?person=<?php echo urlencode($member['name']); ?>" class="font-bold text-slate-900 dark:text-white hover:text-<?php echo $color; ?>-600 dark:hover:text-<?php echo $color; ?>-400 transition-colors block"><?php echo $member['name']; ?></a>
                            <p class="text-<?php echo $color; ?>-600 dark:text-<?php echo $color; ?>-400 text-sm font-semibold"><?php echo $member['role']; ?></p>
                        </div>
                    </div>
                    
                    <!-- Social Links (excluding email) -->
                    <div class="flex gap-2 mb-4">
                        <?php
                        $social_platforms = ['github' => 'fab fa-github', 'linkedin' => 'fab fa-linkedin', 'twitter' => 'fab fa-twitter'];
                        
                        foreach ($social_platforms as $platform => $icon):
                            if (!empty($member[$platform])): ?>
                                <a href="<?php echo $member[$platform]; ?>" title="<?php echo ucfirst($platform); ?>" target="_blank" class="flex-1 px-3 py-2 bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-600 dark:to-slate-700 text-slate-700 dark:text-slate-200 rounded-lg hover:shadow-md hover:scale-105 transition-all text-center font-semibold">
                                    <i class="<?php echo $icon; ?>"></i>
                                </a>
                            <?php endif; 
                        endforeach; ?>
                    </div>
                    
                    <!-- Email Button - Premium Redesign -->
                    <?php if (!empty($member['email'])): ?>
                    <a href="mailto:<?php echo $member['email']; ?>" class="w-full block px-6 py-4 bg-gradient-to-r from-<?php echo $color; ?>-50 to-<?php echo $color; ?>-100 dark:from-<?php echo $color; ?>-900/30 dark:to-<?php echo $color; ?>-800/30 border-2 border-<?php echo $color; ?>-200 dark:border-<?php echo $color; ?>-700 rounded-xl text-center font-bold text-base hover:shadow-xl hover:border-<?php echo $color; ?>-400 dark:hover:border-<?php echo $color; ?>-500 transition-all duration-300 group/email relative overflow-hidden">
                        <!-- Gradient Background Animation -->
                        <div class="absolute inset-0 bg-gradient-to-r from-<?php echo $color; ?>-400/0 via-<?php echo $color; ?>-300/20 to-<?php echo $color; ?>-400/0 opacity-0 group-hover/email:opacity-100 transition-opacity duration-500" style="background-position: 200% center; animation: shimmer 3s infinite;"></div>
                        
                        <div class="relative flex items-center justify-center gap-3 text-<?php echo $color; ?>-700 dark:text-<?php echo $color; ?>-200 group-hover/email:text-<?php echo $color; ?>-900 dark:group-hover/email:text-<?php echo $color; ?>-100 transition-colors duration-300">
                            <i class="fas fa-envelope text-lg group-hover/email:scale-110 transition-transform duration-300"></i>
                            <span>Email Me</span>
                            <i class="fas fa-chevron-right text-sm opacity-60 group-hover/email:opacity-100 group-hover/email:translate-x-1 transition-all duration-300"></i>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white dark:bg-slate-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-slate-900 dark:text-white mb-4">Frequently Asked Questions</h2>
            <p class="text-slate-600 dark:text-slate-400 text-lg">Find answers to common questions</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4">
            <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden group hover:shadow-lg transition-all duration-300" data-aos="fade-up">
                <button class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors faq-toggle">
                    <span class="font-semibold text-slate-900 dark:text-white text-left">How can I join the ICT Club?</span>
                    <i class="fas fa-chevron-down text-slate-600 dark:text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="hidden faq-content">
                    <p class="px-6 py-4 text-slate-600 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700">
                        You can join the ICT Club by visiting our Members page and filling out the club membership application form. We welcome all students interested in technology and innovation!
                    </p>
                </div>
            </div>

            <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden group hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="100">
                <button class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors faq-toggle">
                    <span class="font-semibold text-slate-900 dark:text-white text-left">What types of events do you organize?</span>
                    <i class="fas fa-chevron-down text-slate-600 dark:text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="hidden faq-content">
                    <p class="px-6 py-4 text-slate-600 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700">
                        We organize workshops, hackathons, competitions, webinars, and networking events. Check our Events page for upcoming opportunities to learn, collaborate, and showcase your skills!
                    </p>
                </div>
            </div>

            <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden group hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="200">
                <button class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors faq-toggle">
                    <span class="font-semibold text-slate-900 dark:text-white text-left">How do I register for events?</span>
                    <i class="fas fa-chevron-down text-slate-600 dark:text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="hidden faq-content">
                    <p class="px-6 py-4 text-slate-600 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700">
                        Navigate to the Events page and click the Register button on any event. Fill in your details and you'll receive a confirmation email. Registration is free for all club members!
                    </p>
                </div>
            </div>

            <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden group hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                <button class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors faq-toggle">
                    <span class="font-semibold text-slate-900 dark:text-white text-left">Can I collaborate on club projects?</span>
                    <i class="fas fa-chevron-down text-slate-600 dark:text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="hidden faq-content">
                    <p class="px-6 py-4 text-slate-600 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700">
                        Absolutely! We encourage collaboration on all our projects. Visit the Projects page to see current initiatives and reach out to the team members working on projects you're interested in.
                    </p>
                </div>
            </div>

            <div class="border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden group hover:shadow-lg transition-all duration-300" data-aos="fade-up" data-aos-delay="400">
                <button class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800 flex items-center justify-between hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors faq-toggle">
                    <span class="font-semibold text-slate-900 dark:text-white text-left">Where can I find more information?</span>
                    <i class="fas fa-chevron-down text-slate-600 dark:text-slate-400 transition-transform duration-300"></i>
                </button>
                <div class="hidden faq-content">
                    <p class="px-6 py-4 text-slate-600 dark:text-slate-400 border-t border-slate-200 dark:border-slate-700">
                        Check out our Blog for articles and tutorials, visit About Us for our mission and vision, or follow our social media channels for updates. Feel free to contact us directly with any questions!
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-20 bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-900 dark:to-pink-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-4xl font-bold text-white mb-4">Stay Updated</h2>
            <p class="text-white/90 text-lg mb-8">
                Subscribe to our newsletter for the latest news and updates
            </p>
            
            <div id="newsletter-message-contact" class="hidden p-4 rounded-lg text-sm font-medium mb-4 w-full"></div>
            <form action="/contact.php" method="POST" class="flex flex-col sm:flex-row gap-3" data-validate="true" id="newsletter-form-contact">
                <input type="hidden" name="action" value="newsletter">
                <input type="email" name="email" placeholder="Enter your email" required
                       class="flex-1 px-6 py-3 rounded-lg text-slate-900 dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-white">
                <button type="submit" class="px-8 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-gray-100 dark:bg-purple-600 dark:text-white dark:hover:bg-purple-700 transition-all">
                    <i class="fas fa-paper-plane mr-2"></i>Subscribe
                </button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
