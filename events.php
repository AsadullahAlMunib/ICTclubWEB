<?php
$page_title = "Events";
include 'includes/header.php';

$events = load_json_data('events.json');

// Handle event registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_event') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $event_id = sanitize_input($_POST['event_id']);
    
    if (empty($name) || empty($email) || empty($event_id)) {
        json_response(error_response('All fields are required.'));
    }
    
    if (validate_email($email)) {
        // Find the event
        $all_events = load_json_data('events.json');
        $event_title = '';
        $event_found = false;
        foreach ($all_events as $evt) {
            if ($evt['id'] == $event_id) {
                $event_title = $evt['title'];
                $event_found = true;
                break;
            }
        }
        
        // Validate event exists
        if (!$event_found) {
            json_response(error_response('Invalid event. Please select a valid event.'));
        }
        
        // Save registration to JSON file
        $registration = [
            'event_id' => $event_id,
            'event_title' => $event_title,
            'name' => $name,
            'email' => $email,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'confirmed'
        ];
        
        $event_registrations = load_json_data('event_registrations.json');
        
        // Check if already registered
        $already_registered = false;
        foreach ($event_registrations as $reg) {
            if ($reg['email'] === $email && $reg['event_id'] == $event_id) {
                $already_registered = true;
                break;
            }
        }
        
        if ($already_registered) {
            json_response(success_response('You are already registered for this event!'));
        }
        
        $event_registrations[] = $registration;
        $save_result = save_json_data('event_registrations.json', $event_registrations);
        
        if (!$save_result) {
            json_response(error_response('Failed to save registration. Please try again later.'));
        }
        
        // Send confirmation email to participant
        $subject = "Event Registration Confirmation - $event_title";
        $message = "
            <h2>Registration Confirmed!</h2>
            <p>Dear $name,</p>
            <p>You have successfully registered for: <strong>$event_title</strong></p>
            <p>We look forward to seeing you at the event!</p>
            <p>Best regards,<br>" . SITE_NAME . " Team</p>
        ";
        
        $email_sent = send_email($email, $subject, $message);
        
        if (!$email_sent) {
            json_response(error_response('Registration saved but confirmation email failed. Please contact us to verify your registration.'));
        }
        
        // Notify admin
        $admin_subject = "New Event Registration - $event_title";
        $admin_message = "
            <h2>New Event Registration</h2>
            <p><strong>Event:</strong> $event_title</p>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Registered:</strong> " . date('Y-m-d H:i:s') . "</p>
        ";
        
        $admin_email_sent = send_email(SITE_EMAIL, $admin_subject, $admin_message, $email);
        
        if (!$admin_email_sent) {
            error_log("Failed to send admin notification for event registration: $event_title - $name ($email)");
        }
        
        json_response(success_response('Successfully registered for the event! Check your email for confirmation.'));
    } else {
        json_response(error_response('Invalid email address'));
    }
    exit;
}

$upcoming_events = array_filter($events, function($event) {
    return $event['status'] === 'Upcoming';
});

$past_events = array_filter($events, function($event) {
    return $event['status'] === 'Past';
});
?>

<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-display font-black mb-6">Club Events</h1>
            <p class="text-base md:text-2xl font-medium max-w-3xl mx-auto">
                Join us for exciting workshops, hackathons, and networking opportunities.
            </p>
        </div>
    </div>
</section>

<!-- Upcoming Events Calendar -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Upcoming Events
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Don't miss out on these amazing opportunities</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <?php foreach ($upcoming_events as $event): ?>
            <div class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up">
                <div class="relative h-64 overflow-hidden">
                    <img src="<?php echo $event['image']; ?>" alt="<?php echo $event['title']; ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <div class="flex items-center justify-between mb-2">
                            <span class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white text-sm font-semibold rounded-full">
                                <?php echo $event['category']; ?>
                            </span>
                            <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full">
                                <?php echo format_date($event['date'], 'M j, Y'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <h3 class="text-xl md:text-3xl font-bold mb-4 text-gray-900 dark:text-white"><?php echo $event['title']; ?></h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 text-sm md:text-lg"><?php echo $event['description']; ?></p>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            <span class="font-medium"><?php echo format_date($event['date'], 'F j, Y'); ?></span>
                        </div>
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <span class="font-medium"><?php echo $event['time']; ?></span>
                        </div>
                        <div class="flex items-center text-gray-700 dark:text-gray-300">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-orange-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                            <span class="font-medium"><?php echo $event['location']; ?></span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                            <span>Registration Progress</span>
                            <span class="font-semibold"><?php echo $event['registered']; ?> / <?php echo $event['spots']; ?> spots filled</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="progress-bar h-3 rounded-full" style="width: <?php echo ($event['registered'] / $event['spots']) * 100; ?>%"></div>
                        </div>
                    </div>

                    <?php if ($event['registered'] < $event['spots']): ?>
                    <button onclick="openModal('event-register-<?php echo $event['id']; ?>')" 
                            class="w-full px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold text-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                        <i class="fas fa-ticket-alt mr-2"></i>Register Now
                    </button>
                    <?php else: ?>
                    <div class="w-full px-6 py-4 bg-gray-300 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-xl font-semibold text-lg text-center">
                        <i class="fas fa-check-circle mr-2"></i>Fully Booked
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Registration Modal -->
            <div id="event-register-<?php echo $event['id']; ?>" class="modal hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-2xl w-full p-8" onclick="event.stopPropagation()">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white">Register for Event</h3>
                        <button onclick="closeModal('event-register-<?php echo $event['id']; ?>')" 
                                class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="mb-6 p-4 bg-gradient-to-br from-blue-50 to-purple-50 dark:from-gray-700 dark:to-gray-600 rounded-xl">
                        <h4 class="font-bold text-lg text-gray-900 dark:text-white mb-2"><?php echo $event['title']; ?></h4>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <div><i class="fas fa-calendar mr-2"></i><?php echo format_date($event['date'], 'F j, Y'); ?></div>
                            <div><i class="fas fa-clock mr-2"></i><?php echo $event['time']; ?></div>
                            <div><i class="fas fa-map-marker-alt mr-2"></i><?php echo $event['location']; ?></div>
                        </div>
                    </div>

                    <form action="" method="POST" class="space-y-4" data-validate="true">
                        <input type="hidden" name="action" value="register_event">
                        <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Full Name <span class="text-red-500 font-bold">*</span></label>
                            <input type="text" name="name" required
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">Email Address <span class="text-red-500 font-bold">*</span></label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 focus:border-purple-500 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-300 shadow-lg">
                            <i class="fas fa-check mr-2"></i>Confirm Registration
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Workshop Schedules -->
<section class="py-20 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-800 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Workshop Schedule
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Learn from industry experts</p>
        </div>

        <div class="max-w-4xl mx-auto space-y-6">
            <?php 
            $workshops = array_filter($upcoming_events, function($e) { return $e['category'] === 'Workshop'; });
            foreach ($workshops as $workshop): ?>
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300" data-aos="fade-right">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo $workshop['title']; ?></h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-3"><?php echo $workshop['description']; ?></p>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <span class="text-gray-700 dark:text-gray-300"><i class="fas fa-calendar mr-2 text-blue-500"></i><?php echo format_date($workshop['date']); ?></span>
                            <span class="text-gray-700 dark:text-gray-300"><i class="fas fa-clock mr-2 text-purple-500"></i><?php echo $workshop['time']; ?></span>
                            <span class="text-gray-700 dark:text-gray-300"><i class="fas fa-map-marker-alt mr-2 text-pink-500"></i><?php echo $workshop['location']; ?></span>
                        </div>
                    </div>
                    <button onclick="openModal('event-register-<?php echo $workshop['id']; ?>')" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold hover:from-blue-600 hover:to-purple-600 transition-all duration-300 whitespace-nowrap">
                        Register
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Competition Announcements -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Competitions & Hackathons
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Challenge yourself and win prizes</p>
        </div>

        <?php 
        $competitions = array_filter($upcoming_events, function($e) { return $e['category'] === 'Competition'; });
        if (count($competitions) > 0):
            $competition = reset($competitions);
        ?>
        <div class="max-w-5xl mx-auto">
            <div class="bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-3xl p-1 shadow-2xl" data-aos="zoom-in">
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 md:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-12 items-center">
                        <div>
                            <div class="mb-4">
                                <span class="px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-sm font-bold rounded-full">
                                    🏆 FEATURED EVENT
                                </span>
                            </div>
                            <h3 class="text-4xl font-bold text-gray-900 dark:text-white mb-4"><?php echo $competition['title']; ?></h3>
                            <p class="text-gray-600 dark:text-gray-400 text-lg mb-6"><?php echo $competition['description']; ?></p>
                            <div class="space-y-3 mb-8">
                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-calendar-alt mr-3 text-blue-500 text-xl"></i>
                                    <span><?php echo format_date($competition['date'], 'F j, Y'); ?></span>
                                </div>
                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-users mr-3 text-purple-500 text-xl"></i>
                                    <span><?php echo $competition['spots']; ?> participants max</span>
                                </div>
                                <div class="flex items-center text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-trophy mr-3 text-yellow-500 text-xl"></i>
                                    <span>Amazing prizes to be won!</span>
                                </div>
                            </div>
                            <button onclick="openModal('event-register-<?php echo $competition['id']; ?>')" 
                                    class="px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-xl font-semibold text-lg hover:from-blue-600 hover:to-purple-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                                <i class="fas fa-rocket mr-2"></i>Register Your Team
                            </button>
                        </div>
                        <div>
                            <img src="<?php echo $competition['image']; ?>" alt="<?php echo $competition['title']; ?>" class="w-full h-96 object-cover rounded-2xl shadow-2xl">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Past Events Gallery -->
<section class="py-20 bg-gradient-to-br from-purple-50 via-blue-50 to-pink-50 dark:from-gray-900 dark:via-purple-900 dark:to-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Past Events Gallery
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Relive our amazing moments</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($past_events as $event): ?>
            <div class="group relative overflow-hidden rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300" data-aos="fade-up">
                <img src="<?php echo $event['image']; ?>" alt="<?php echo $event['title']; ?>" 
                     class="w-full h-72 object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-60 group-hover:opacity-90 transition-opacity duration-300"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white transform translate-y-6 group-hover:translate-y-0 transition-transform duration-300">
                    <h3 class="text-2xl font-bold mb-2"><?php echo $event['title']; ?></h3>
                    <p class="text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"><?php echo format_date($event['date']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Event Highlights -->
<section class="py-20 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl md:text-5xl font-display font-bold mb-4 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Event Highlights
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-lg">What makes our events special</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php 
            $highlights = [
                ['icon' => 'fa-chalkboard-teacher', 'title' => 'Expert Speakers', 'description' => 'Learn from industry professionals'],
                ['icon' => 'fa-hands-helping', 'title' => 'Hands-on Practice', 'description' => 'Real-world project experience'],
                ['icon' => 'fa-certificate', 'title' => 'Certificates', 'description' => 'Recognition for participation'],
                ['icon' => 'fa-users', 'title' => 'Networking', 'description' => 'Connect with peers and mentors']
            ];
            foreach ($highlights as $highlight): ?>
            <div class="text-center" data-aos="zoom-in">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas <?php echo $highlight['icon']; ?> text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2"><?php echo $highlight['title']; ?></h3>
                <p class="text-gray-600 dark:text-gray-400"><?php echo $highlight['description']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
