    <!-- Premium Footer -->
    <footer class="bg-gradient-to-b from-gray-900 via-gray-900 to-black dark:from-gray-950 dark:via-gray-950 dark:to-black text-white pt-20 pb-8 mt-20 border-t border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Footer Content -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                <!-- Brand Section -->
                <div class="lg:col-span-1">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-code text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-display font-bold"><?php echo SITE_NAME; ?></h3>
                            <p class="text-xs text-blue-400">Tech Excellence</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm mb-6 leading-relaxed">Empowering students through technology, innovation, and collaboration.</p>
                    <div class="flex space-x-3">
                        <?php foreach ($social_links as $social): ?>
                        <a href="<?php echo $social['url']; ?>" target="_blank" rel="noopener noreferrer"
                           class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-800 to-gray-700 hover:from-blue-500 hover:to-purple-500 flex items-center justify-center transition-all duration-300 transform hover:scale-110 shadow-md hover:shadow-lg">
                            <i class="fab <?php echo $social['icon']; ?> text-sm"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Navigation & Resources (Side by Side) -->
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Navigation Links -->
                    <div>
                        <h4 class="text-base font-semibold mb-6 text-white">Navigation</h4>
                        <ul class="space-y-3">
                            <?php foreach (array_slice($nav_menu, 0, 4) as $item): ?>
                            <li>
                                <a href="<?php echo $item['url']; ?>" class="text-gray-400 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                                    <span class="w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-2 transition-all duration-300 mr-2"></span>
                                    <i class="fas <?php echo $item['icon']; ?> mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    <?php echo $item['name']; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Resources -->
                    <div>
                        <h4 class="text-base font-semibold mb-6 text-white">Resources</h4>
                        <ul class="space-y-3">
                            <li>
                                <a href="projects.php" class="text-gray-400 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                                    <span class="w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-2 transition-all duration-300 mr-2"></span>
                                    <i class="fas fa-code-branch mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    Our Projects
                                </a>
                            </li>
                            <li>
                                <a href="blog.php" class="text-gray-400 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                                    <span class="w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-2 transition-all duration-300 mr-2"></span>
                                    <i class="fas fa-blog mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    Blog Articles
                                </a>
                            </li>
                            <li>
                                <a href="events.php" class="text-gray-400 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                                    <span class="w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-2 transition-all duration-300 mr-2"></span>
                                    <i class="fas fa-calendar-alt mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    Events
                                </a>
                            </li>
                            <li>
                                <a href="members.php" class="text-gray-400 hover:text-blue-400 transition-colors duration-300 flex items-center group">
                                    <span class="w-0 h-0.5 bg-gradient-to-r from-blue-500 to-purple-500 group-hover:w-2 transition-all duration-300 mr-2"></span>
                                    <i class="fas fa-users mr-2 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                    Members
                                </a>
                            </li>
                        </ul>
                    </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="lg:col-span-1">
                    <h4 class="text-base font-semibold mb-6 text-white">Get In Touch</h4>
                    <div class="space-y-4">
                        <div class="flex items-start group cursor-pointer">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center mr-3 group-hover:from-blue-500 group-hover:to-purple-500 transition-all">
                                <i class="fas fa-envelope text-blue-400 group-hover:text-white transition-colors"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Email</p>
                                <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-sm text-gray-300 hover:text-blue-400 transition-colors"><?php echo SITE_EMAIL; ?></a>
                            </div>
                        </div>
                        <div class="flex items-start group cursor-pointer">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center mr-3 group-hover:from-purple-500 group-hover:to-pink-500 transition-all">
                                <i class="fas fa-map-marker-alt text-purple-400 group-hover:text-white transition-colors"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Location</p>
                                <p class="text-sm text-gray-300">University Campus<br><span class="text-xs">Building A, Room 101</span></p>
                            </div>
                        </div>
                        <div class="flex items-start group cursor-pointer">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-pink-500/20 to-red-500/20 flex items-center justify-center mr-3 group-hover:from-pink-500 group-hover:to-red-500 transition-all">
                                <i class="fas fa-clock text-pink-400 group-hover:text-white transition-colors"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Hours</p>
                                <p class="text-sm text-gray-300">Sunday - Thursday<br><span class="text-xs">8:00 AM - 2:00 PM</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Newsletter Subscription -->
                <div class="lg:col-span-1">
                    <h4 class="text-base font-semibold mb-6 text-white">Stay Updated</h4>
                    <p class="text-gray-400 text-sm mb-4 leading-relaxed">Get the latest news, events, and opportunities delivered to your inbox.</p>
                    <form action="/contact.php" method="POST" class="space-y-3" data-validate="true" id="newsletter-form">
                        <input type="hidden" name="action" value="newsletter">
                        <div id="newsletter-message" class="hidden p-3 rounded-lg text-sm font-medium mb-3"></div>
                        <div class="relative group">
                            <input type="email" name="email" placeholder="email@example.com" 
                                   class="w-full px-4 py-2.5 rounded-lg bg-gray-800/50 border border-gray-700 group-hover:border-blue-500 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none text-white placeholder-gray-500 text-sm transition-all"
                                   required>
                        </div>
                        <button type="submit" class="w-full px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white rounded-lg font-semibold text-sm transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>Subscribe
                        </button>
                    </form>
                    <p class="text-xs text-gray-500 mt-3">We respect your privacy. No spam, ever.</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-800/50 my-8"></div>

            <!-- Bottom Footer -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <!-- Copyright -->
                <div class="text-center md:text-left">
                    <p class="text-gray-400 text-sm">&copy; <span class="font-semibold text-white"><?php echo date('Y'); ?> <?php echo SITE_NAME; ?></span>. All rights reserved.</p>
                    <p class="text-gray-500 text-xs mt-1">Made with <i class="fas fa-heart text-red-500 animate-pulse"></i> by the ICT Club Team</p>
                </div>

                <!-- Footer Links -->
                <div class="flex flex-wrap justify-center md:justify-end gap-6 text-sm">
                    <a href="javascript:void(0)" class="text-gray-400 hover:text-blue-400 transition-colors">Privacy Policy</a>
                    <a href="javascript:void(0)" class="text-gray-400 hover:text-blue-400 transition-colors">Terms of Service</a>
                    <a href="contact.php" class="text-gray-400 hover:text-blue-400 transition-colors">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTop" 
            class="fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-full shadow-lg opacity-0 invisible transition-all duration-300 hover:scale-110 hover:shadow-xl z-40 flex items-center justify-center">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 100
        });

        // Scroll to top button
        const scrollTopBtn = document.getElementById('scrollTop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollTopBtn.classList.remove('opacity-0', 'invisible');
                scrollTopBtn.classList.add('opacity-100', 'visible');
            } else {
                scrollTopBtn.classList.add('opacity-0', 'invisible');
                scrollTopBtn.classList.remove('opacity-100', 'visible');
            }
        });

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
