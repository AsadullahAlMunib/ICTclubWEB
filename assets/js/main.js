// Main JavaScript for ICT Club Website

// Email Obfuscation Function
function obfuscateEmail(email) {
    const [localPart, domain] = email.split('@');
    const visibleChars = localPart.length > 3 ? 3 : 1;
    const obfuscated = localPart.substring(0, visibleChars) + '***@' + domain;
    return obfuscated;
}

// Form Loading State Handler
function setFormLoading(form, isLoading) {
    const button = form.querySelector('button[type="submit"]');
    if (button) {
        button.disabled = isLoading;
        button.innerHTML = isLoading ? '<i class="fas fa-spinner animate-spin mr-2"></i>Submitting...' : button.dataset.originalText || button.innerHTML;
        if (!isLoading && button.dataset.originalText === undefined) {
            button.dataset.originalText = button.innerHTML;
        }
    }
    form.classList.toggle('form-loading', isLoading);
}

// Show Success Message
function showSuccessMessage(container, message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `<i class="fas fa-check-circle"></i><span>${message}</span>`;
    
    if (container) {
        container.insertBefore(successDiv, container.firstChild);
        setTimeout(() => {
            successDiv.style.opacity = '0';
            successDiv.style.transition = 'opacity 0.3s ease-out';
            setTimeout(() => successDiv.remove(), 300);
        }, 4000);
    }
}

// ===== ANIMATION SYSTEMS (STANDALONE - NOT IN DOMCONTENTLOADED) =====

// Animated Counter Function
function animateCounter(element) {
    const target = parseInt(element.getAttribute('data-target'));
    const suffix = element.getAttribute('data-suffix') || '';
    
    if (isNaN(target)) {
        console.log('Invalid target for animateCounter: data-target is missing or not a number');
        return;
    }
    
    const duration = 1500; // 1.5 seconds
    const increment = target / (duration / 16);
    let current = 0;

    const updateCounter = () => {
        current += increment;
        if (current < target) {
            element.textContent = Math.floor(current) + suffix;
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target + suffix;
        }
    };

    updateCounter();
}

// Initialize Counter Animations
function initCounters() {
    const counterElements = document.querySelectorAll('.counter-value');
    
    if (counterElements.length === 0) return;

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (!entry.target.classList.contains('counted')) {
                    entry.target.classList.add('counted');
                    animateCounter(entry.target);
                }
            }
        });
    }, { threshold: 0.5 });

    counterElements.forEach(counter => {
        if (!counter.classList.contains('counted')) {
            counter.textContent = '0';
        }
        counterObserver.observe(counter);
        // Try to trigger animation immediately if already in view
        const rect = counter.getBoundingClientRect();
        if (rect.top <= window.innerHeight && rect.bottom >= 0) {
            if (!counter.classList.contains('counted')) {
                counter.classList.add('counted');
                animateCounter(counter);
            }
        }
    });
}

// Animated Stat Numbers Function with Fallback
function animateStatNumber(element) {
    const target = parseInt(element.getAttribute('data-target'));
    
    if (isNaN(target)) {
        console.log('Invalid target for animateStatNumber: data-target is missing or not a number');
        return;
    }
    
    // Set a timeout to ensure fallback display if animation fails
    const fallbackTimeout = setTimeout(() => {
        element.textContent = target;
    }, 2500); // 2.5 seconds - longer than animation duration
    
    const duration = 1500; // 1.5 seconds
    const increment = target / (duration / 16);
    let current = 0;
    let frameCount = 0;

    const updateNumber = () => {
        current += increment;
        frameCount++;
        if (current < target) {
            element.textContent = Math.floor(current);
            requestAnimationFrame(updateNumber);
        } else {
            clearTimeout(fallbackTimeout);
            element.textContent = target;
        }
    };

    updateNumber();
}

// Initialize Stat Number Animations
function initStatNumbers() {
    const statNumbers = document.querySelectorAll('.stat-number');
    
    if (statNumbers.length === 0) return;

    const statObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (!entry.target.classList.contains('animated')) {
                    entry.target.classList.add('animated');
                    animateStatNumber(entry.target);
                }
            }
        });
    }, { threshold: 0.5 });

    statNumbers.forEach(stat => {
        statObserver.observe(stat);
        // Try to trigger animation immediately if already in view
        const rect = stat.getBoundingClientRect();
        if (rect.top <= window.innerHeight && rect.bottom >= 0) {
            if (!stat.classList.contains('animated')) {
                stat.classList.add('animated');
                animateStatNumber(stat);
            }
        }
    });
}

// Initialize animations when DOM is ready
function initializeAnimations() {
    initCounters();
    initStatNumbers();
}

// ===== BULLETPROOF INITIALIZATION =====

// Store if we've initialized to avoid redundant work
let animationsInitialized = false;
let scrollListenerAttached = false;

// Wrapper to track initialization
function safeInitializeAnimations() {
    if (!animationsInitialized) {
        animationsInitialized = true;
        initializeAnimations();
    }
}

// Initialize immediately if DOM is ready
if (document.readyState !== 'loading') {
    safeInitializeAnimations();
}

// Initialize on DOMContentLoaded as backup
document.addEventListener('DOMContentLoaded', safeInitializeAnimations);

// Re-initialize at multiple time intervals for late-loading elements
setTimeout(safeInitializeAnimations, 500);
setTimeout(safeInitializeAnimations, 1000);
setTimeout(safeInitializeAnimations, 2000);

// Scroll listener as ultimate fallback (ensures animations trigger when user scrolls)
function attachScrollListener() {
    if (scrollListenerAttached) return;
    scrollListenerAttached = true;
    
    document.addEventListener('scroll', function() {
        // Only re-initialize if not already done
        if (!animationsInitialized) {
            safeInitializeAnimations();
        }
    }, { passive: true, once: false });
}

// Attach scroll listener on demand
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachScrollListener);
} else {
    attachScrollListener();
}

setTimeout(attachScrollListener, 100);

document.addEventListener('DOMContentLoaded', function() {
    
    // Detect mobile
    const isMobileDevice = window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    
    // Initialize AOS with optimized settings
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: isMobileDevice ? 400 : 600,
            once: true,
            disable: false,
            offset: 100,
            easing: 'ease-in-out'
        });
        
        // Refresh AOS after page fully loads
        setTimeout(() => {
            AOS.refresh();
        }, 1000);
    }
    
    // Initialize particles.js if element exists (NOT on mobile)
    if (document.getElementById('particles-js') && !isMobileDevice) {
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#667eea' },
                shape: { type: 'circle' },
                opacity: { value: 0.5, random: false },
                size: { value: 3, random: true },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: '#667eea',
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: 'none',
                    random: false,
                    straight: false,
                    out_mode: 'out',
                    bounce: false
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: {
                    onhover: { enable: true, mode: 'repulse' },
                    onclick: { enable: true, mode: 'push' },
                    resize: true
                }
            },
            retina_detect: true
        });
    }

    // Re-initialize animations on DOM content loaded
    initializeAnimations();

    // Typing Effect
    function typeWriter(element, text, speed = 100) {
        let i = 0;
        element.textContent = '';
        
        function type() {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        
        type();
    }

    // Initialize typing effect for elements with .typewriter class
    const typewriterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !entry.target.classList.contains('typed')) {
                entry.target.classList.add('typed');
                const text = entry.target.getAttribute('data-text');
                typeWriter(entry.target, text);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.typewriter').forEach(element => {
        typewriterObserver.observe(element);
    });

    // Form Validation
    const forms = document.querySelectorAll('form[data-validate="true"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Skip membership form - it has its own handler
            if (form.id === 'membership-form') {
                return;
            }
            
            e.preventDefault();
            
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('border-red-500');
                    
                    // Show error message
                    let errorMsg = field.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('p');
                        errorMsg.className = 'error-message text-red-500 text-sm mt-1';
                        errorMsg.textContent = 'This field is required';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.classList.remove('border-red-500');
                    const errorMsg = field.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            // Email validation
            const emailFields = form.querySelectorAll('input[type="email"]');
            emailFields.forEach(field => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (field.value && !emailRegex.test(field.value)) {
                    isValid = false;
                    field.classList.add('border-red-500');
                }
            });
            
            // Phone number validation (exactly 10 digits after +880)
            const phoneFields = form.querySelectorAll('input[type="tel"]');
            phoneFields.forEach(field => {
                if (field.value) {
                    const digits = field.value.replace(/\D/g, '');
                    if (digits.length !== 13) { // 880 + 10 digits = 13
                        isValid = false;
                        field.classList.add('border-red-500');
                        let errorMsg = field.nextElementSibling;
                        if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                            errorMsg = document.createElement('p');
                            errorMsg.className = 'error-message text-red-500 text-sm mt-1';
                            errorMsg.textContent = 'Phone number must be exactly 10 digits after +880';
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                    }
                }
            });
            
            if (isValid) {
                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                submitBtn.disabled = true;
                
                // Submit form
                const formData = new FormData(form);
                // Use getAttribute to avoid conflicts with form inputs named 'action'
                let formAction = form.getAttribute('action') || form.action;
                // Ensure absolute path
                if (!formAction.startsWith('/') && !formAction.startsWith('http')) {
                    formAction = '/' + formAction;
                }
                
                fetch(formAction, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showNotification('Success! ' + data.message, 'success');
                        form.reset();
                    } else if (data.type === 'warning') {
                        showNotification(data.message, 'warning');
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Form submission error:', error);
                    showNotification('An error occurred. Please try again.', 'error');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
            }
        });
    });

    // Phone Number Formatting: +880 1XXX-XXXXXX (total 13 digits)
    // "+880 " is completely immutable, user enters: 10 digits (1 + 9 more)
    const phoneInput = document.getElementById('phone-input');
    const PREFIX = '+880 '; // Immutable prefix
    const PROTECTED_LENGTH = 5; // "+880 " = 5 characters (including space after 0)
    const REQUIRED_DIGITS = 13; // 880 + 10 user digits
    
    if (phoneInput) {
        // Initialize field with immutable prefix only
        if (!phoneInput.value || !phoneInput.value.startsWith(PREFIX)) {
            phoneInput.value = PREFIX;
        }
        
        // Set cursor to position 5 (after the space) when field gets focus
        phoneInput.addEventListener('focus', function() {
            this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
            validatePhoneRealTime();
        });
        
        phoneInput.addEventListener('input', function(e) {
            let value = this.value;
            let digits = value.replace(/\D/g, ''); // Extract all digits
            
            // Keep first 3 digits (880) and then user input
            let protectedDigits = '880';
            let userDigits = digits.substring(3); // Get everything after 880
            
            // Enforce first user digit MUST be 1
            if (userDigits.length > 0) {
                if (userDigits[0] !== '1') {
                    // If first digit is not 1, remove everything after 880 and reset
                    userDigits = '';
                } else {
                    // First digit is 1, allow any digits after (0-9)
                    // Limit to exactly 10 user digits total
                    userDigits = userDigits.substring(0, 10);
                }
            }
            
            // Reconstruct full digit string
            digits = protectedDigits + userDigits;
            
            // Format as: +880 1XXX-XXXXXX
            let formatted = PREFIX + digits.substring(3);
            if (digits.length > 6) {
                // Insert dash after 4th position of user input (3+4 = 7, so position 7 overall)
                formatted = PREFIX + digits.substring(3, 7) + '-' + digits.substring(7);
            }
            
            this.value = formatted;
            
            // Force cursor to stay at or after "+880 " (position 5 or later)
            if (this.selectionStart < PROTECTED_LENGTH) {
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
            }
            
            // Real-time validation
            validatePhoneRealTime();
        });
        
        // Real-time validation function
        function validatePhoneRealTime() {
            const value = phoneInput.value;
            const digits = value.replace(/\D/g, '');
            const userDigits = digits.substring(3); // Everything after 880
            
            // Get or create error message element
            let errorMsg = phoneInput.nextElementSibling;
            if (!errorMsg || !errorMsg.classList.contains('phone-error-message')) {
                errorMsg = document.createElement('p');
                errorMsg.className = 'phone-error-message text-red-500 text-sm mt-1';
                phoneInput.parentNode.insertBefore(errorMsg, phoneInput.nextSibling);
            }
            
            if (digits.length === REQUIRED_DIGITS) {
                // Valid: 13 total digits (880 + 10 user)
                phoneInput.classList.remove('border-red-500');
                phoneInput.classList.add('border-green-500');
                errorMsg.textContent = '';
                errorMsg.style.display = 'none';
            } else if (userDigits.length > 0 && userDigits.length < 10) {
                // Incomplete: user has started typing
                const digitsNeeded = 10 - userDigits.length;
                phoneInput.classList.add('border-red-500');
                phoneInput.classList.remove('border-green-500');
                errorMsg.textContent = `Please enter ${digitsNeeded} more digit${digitsNeeded !== 1 ? 's' : ''}`;
                errorMsg.style.display = 'block';
            } else if (userDigits.length === 0) {
                // Empty
                phoneInput.classList.remove('border-red-500', 'border-green-500');
                errorMsg.textContent = '';
                errorMsg.style.display = 'none';
            } else if (userDigits[0] !== '1') {
                // First digit must be 1
                phoneInput.classList.add('border-red-500');
                errorMsg.textContent = 'Phone number must start with 1';
                errorMsg.style.display = 'block';
            }
        }
        
        // Prevent ANY key action that tries to delete or modify protected zone
        phoneInput.addEventListener('keydown', function(e) {
            // Always force cursor to safe position (after "+880 ")
            if (this.selectionStart < PROTECTED_LENGTH) {
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
            }
            
            // Block Backspace when at start or trying to delete prefix
            if (e.key === 'Backspace' && this.selectionStart <= PROTECTED_LENGTH) {
                e.preventDefault();
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
                return;
            }
            
            // Block Delete at protected zone
            if (e.key === 'Delete' && this.selectionStart < PROTECTED_LENGTH) {
                e.preventDefault();
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
                return;
            }
            
            // Block arrow left from entering protected zone
            if (e.key === 'ArrowLeft' && this.selectionStart <= PROTECTED_LENGTH) {
                e.preventDefault();
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
                return;
            }
            
            // Block Home key
            if (e.key === 'Home') {
                e.preventDefault();
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
                return;
            }
        });
        
        // Extra safety: lock cursor after keyup
        phoneInput.addEventListener('keyup', function(e) {
            if (this.selectionStart < PROTECTED_LENGTH) {
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
            }
        });
        
        // Prevent click into protected zone
        phoneInput.addEventListener('click', function() {
            if (this.selectionStart < PROTECTED_LENGTH) {
                this.setSelectionRange(PROTECTED_LENGTH, PROTECTED_LENGTH);
            }
        });
        
        // Restore prefix if accidentally deleted
        phoneInput.addEventListener('blur', function() {
            if (!this.value.startsWith(PREFIX)) {
                this.value = PREFIX;
            }
            validatePhoneRealTime();
        });
    }

    // Notification System
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500',
            warning: 'bg-yellow-600'
        };
        
        notification.className = `fixed top-24 right-4 ${colors[type]} text-white px-6 py-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Make showNotification globally accessible
    window.showNotification = showNotification;

    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Image Lazy Loading
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });

    // Search Functionality
    const searchInputs = document.querySelectorAll('[data-search]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetSelector = this.getAttribute('data-search');
            const items = document.querySelectorAll(targetSelector);
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                    item.classList.add('fade-in-up');
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Filter Functionality
    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            const targetSelector = this.getAttribute('data-target');
            const items = document.querySelectorAll(targetSelector);
            
            // Update active button
            filterButtons.forEach(btn => {
                btn.classList.remove('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
                btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            });
            this.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            this.classList.add('active', 'bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white');
            
            // Filter items using class instead of inline styles
            items.forEach(item => {
                const itemCategory = item.getAttribute('data-category');
                if (filter === 'all' || itemCategory === filter) {
                    item.classList.remove('filter-hidden');
                    item.classList.add('fade-in-up');
                } else {
                    item.classList.add('filter-hidden');
                    item.classList.remove('fade-in-up');
                }
            });
        });
    });

    // Modal System
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    // Close modal on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    });

    // Countdown Timer
    window.countdown = function(targetDate, elementId) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const countdownInterval = setInterval(() => {
            const now = new Date().getTime();
            const distance = new Date(targetDate).getTime() - now;
            
            if (distance < 0) {
                clearInterval(countdownInterval);
                element.innerHTML = '<span class="text-2xl">Event Started!</span>';
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            element.innerHTML = `
                <div class="grid grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-4xl font-bold">${days}</div>
                        <div class="text-sm">Days</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold">${hours}</div>
                        <div class="text-sm">Hours</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold">${minutes}</div>
                        <div class="text-sm">Minutes</div>
                    </div>
                    <div>
                        <div class="text-4xl font-bold">${seconds}</div>
                        <div class="text-sm">Seconds</div>
                    </div>
                </div>
            `;
        }, 1000);
    };

    // Initialize Swiper sliders
    if (typeof Swiper !== 'undefined') {
        // Featured Projects Slider
        new Swiper('.projects-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });

        // Leaders Carousel
        new Swiper('.leaders-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });

        // Testimonial Slider
        new Swiper('.testimonial-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            }
        });
    }

    console.log('ICT Club Website Initialized Successfully!');
});

// Role Filter Functionality
document.querySelectorAll('.role-filter').forEach(button => {
    button.addEventListener('click', function() {
        const selectedRole = this.getAttribute('data-role');
        const cards = document.querySelectorAll('.member-card');
        
        // Update active button styling
        document.querySelectorAll('.role-filter').forEach(btn => {
            if (btn.getAttribute('data-role') === selectedRole) {
                btn.classList.remove('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
                btn.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white', 'shadow-md');
            } else {
                btn.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-purple-500', 'text-white', 'shadow-md');
                btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-300');
            }
        });
        
        // Filter cards
        let visibleCount = 0;
        cards.forEach(card => {
            const cardRole = card.getAttribute('data-role');
            if (selectedRole === 'all' || cardRole === selectedRole) {
                card.style.display = 'block';
                setTimeout(() => {
                    card.style.opacity = '1';
                }, 10);
                visibleCount++;
            } else {
                card.style.opacity = '0';
                setTimeout(() => {
                    card.style.display = 'none';
                }, 300);
            }
        });
        
        // Update search counter
        document.querySelector('.search-counter').textContent = visibleCount;
    });
});

// Search Counter Update
const searchInput = document.querySelector('input[data-search]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.member-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(query)) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        document.querySelector('.search-counter').textContent = visibleCount;
    });
}

// Share Project Functionality
function shareProject(title, url) {
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).catch(err => console.log('Share failed:', err));
    } else {
        // Fallback: Copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Project link copied to clipboard!');
        });
    }
}

// FAQ Accordion Functionality
document.querySelectorAll('.faq-toggle').forEach(button => {
    button.addEventListener('click', function() {
        const content = this.nextElementSibling;
        const icon = this.querySelector('i');
        const isOpen = !content.classList.contains('hidden');
        
        // Close other FAQs
        document.querySelectorAll('.faq-content').forEach(el => {
            el.classList.add('hidden');
        });
        document.querySelectorAll('.faq-toggle i').forEach(el => {
            el.style.transform = 'rotate(0deg)';
        });
        
        // Toggle current FAQ
        if (!isOpen) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    });
});

// Skills Autocomplete Component
document.addEventListener('DOMContentLoaded', function() {
    const DEFAULT_SKILLS = [
        '3D Printing', 'Adobe XD', 'AI', 'Angular', 'Arduino', 'Authentication', 'AWS',
        'Blockchain', 'Bootstrap', 'C', 'C#', 'C++', 'Circuit Design', 'CMS', 'Communication',
        'Content Writing', 'CSS', 'Cybersecurity', 'Data Science', 'DevOps', 'Digital Marketing',
        'Django', 'Docker', 'E-commerce', 'Electronics', 'Embedded Systems', 'Express.js',
        'Figma', 'Firebase', 'Flask', 'Git', 'GitHub', 'GitLab', 'Google Cloud', 'GraphQL',
        'Graphic Design', 'Hardware Design', 'HTML', 'IoT', 'Java', 'JavaScript', 'Leadership',
        'Linux', 'Machine Learning', 'Microcontrollers', 'Mobile Development', 'MongoDB',
        'Node.js', 'Photography', 'PHP', 'Problem Solving', 'Project Management', 'Python',
        'React', 'REST API', 'Robotics', 'SEO', 'Shopify', 'Social Media', 'Soldering', 'SQL',
        'Tailwind CSS', 'TypeScript', 'UI/UX Design', 'Video Editing', 'Vue.js', 'Web Development',
        'Web3', 'WordPress', 'WooCommerce'
    ].sort();

    const MAX_SKILLS = 10;

    // Skill to icon mapping
    const SKILL_ICONS = {
        'Programming': ['Python', 'JavaScript', 'Java', 'C', 'C#', 'C++', 'PHP', 'TypeScript', 'HTML', 'CSS'],
        'Frontend': ['React', 'Angular', 'Vue.js', 'Tailwind CSS', 'Bootstrap', 'Adobe XD', 'Figma', 'UI/UX Design'],
        'Backend': ['Node.js', 'Express.js', 'Django', 'Flask', 'GraphQL', 'REST API', 'CMS'],
        'Database': ['MongoDB', 'SQL', 'Firebase'],
        'DevOps': ['Docker', 'DevOps', 'AWS', 'Google Cloud', 'Git', 'GitHub', 'GitLab'],
        'AI/ML': ['AI', 'Machine Learning', 'Data Science', 'Blockchain', 'Web3'],
        'Robotics': ['Arduino', 'Robotics', 'IoT', 'Embedded Systems', 'Microcontrollers', 'Circuit Design', 'Electronics', 'Soldering', 'Hardware Design'],
        'Creative': ['Graphic Design', 'Video Editing', 'Photography', 'Content Writing'],
        'Soft Skills': ['Leadership', 'Communication', 'Problem Solving', 'Project Management', 'SEO', 'Digital Marketing', 'Social Media', 'E-commerce', 'Authentication', 'Cybersecurity', 'Shopify', 'WooCommerce', '3D Printing'],
    };

    function getSkillIcon(skill) {
        for (const [category, skills] of Object.entries(SKILL_ICONS)) {
            if (skills.includes(skill)) {
                const icons = {
                    'Programming': 'fa-code',
                    'Frontend': 'fa-palette',
                    'Backend': 'fa-server',
                    'Database': 'fa-database',
                    'DevOps': 'fa-cogs',
                    'AI/ML': 'fa-brain',
                    'Robotics': 'fa-robot',
                    'Creative': 'fa-star',
                    'Soft Skills': 'fa-lightbulb'
                };
                return icons[category] || 'fa-tag';
            }
        }
        return 'fa-tag';
    }

    const skillsSearchInput = document.getElementById('skills-search');
    const skillsDropdown = document.getElementById('skills-dropdown');
    const selectedSkillsDiv = document.getElementById('selected-skills');
    const skillsHiddenInput = document.getElementById('skills-hidden');
    const noSkillsPlaceholder = document.getElementById('no-skills-placeholder');
    const skillsCounter = document.getElementById('skills-counter');
    const skillsProgressBar = document.getElementById('skills-progress-bar');
    const resultsCount = document.getElementById('results-count');
    const emptyState = document.getElementById('empty-state');
    
    let selectedSkills = [];
    let draggedSkill = null;

    if (!skillsSearchInput) return; // Exit if component doesn't exist

    // Render dropdown suggestions with difficulty badges
    function renderDropdown(filter = '') {
        let filtered = DEFAULT_SKILLS.filter(skill => 
            !selectedSkills.includes(skill) && 
            skill.toLowerCase().includes(filter.toLowerCase())
        );

        if (filtered.length === 0) {
            skillsDropdown.innerHTML = '<div id="dropdown-counter" class="sticky top-0 px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300">0 results found</div><div class="p-4 text-center text-gray-500 dark:text-gray-400">No skills found</div>';
            skillsDropdown.classList.remove('hidden');
            return;
        }

        const dropdownContent = filtered.map((skill, index) => {
            const icon = getSkillIcon(skill);
            return `<div class="px-4 py-2 bg-white dark:bg-gray-800 hover:bg-purple-100 dark:hover:bg-purple-900 cursor-pointer transition-colors duration-200 skills-option text-gray-900 dark:text-white flex items-center gap-2" data-skill="${skill}">
                <span class="text-gray-400 dark:text-gray-300 text-xs">${(index + 1).toString().padStart(2, '0')}.</span>
                <i class="fas ${icon} text-sm text-purple-500 dark:text-purple-400 opacity-70"></i>
                <span>${skill}</span>
            </div>`;
        }).join('');

        skillsDropdown.innerHTML = '<div id="dropdown-counter" class="sticky top-0 px-4 py-2 bg-gray-50 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-600 text-xs font-semibold text-gray-600 dark:text-gray-300"><span id="results-count">' + filtered.length + '</span> results found</div>' + dropdownContent;
        skillsDropdown.classList.remove('hidden');

        // Add click handlers to options
        document.querySelectorAll('.skills-option').forEach(option => {
            option.addEventListener('click', function() {
                addSkill(this.getAttribute('data-skill'));
            });
        });
    }

    // Add skill to selected with max limit check
    function addSkill(skill) {
        if (selectedSkills.length >= MAX_SKILLS) {
            alert(`Maximum ${MAX_SKILLS} skills allowed!`);
            return;
        }
        if (!selectedSkills.includes(skill)) {
            selectedSkills.push(skill);
            updateDisplay();
        }
        skillsSearchInput.value = '';
        renderDropdown('');
    }

    // Remove skill
    function removeSkill(skill) {
        selectedSkills = selectedSkills.filter(s => s !== skill);
        updateDisplay();
        renderDropdown(skillsSearchInput.value);
    }

    // Animate counter
    function animateCounter(from, to, duration = 400) {
        const steps = 30;
        const stepValue = (to - from) / steps;
        let current = from;
        let step = 0;

        const interval = setInterval(() => {
            current += stepValue;
            step++;
            
            if (step >= steps) {
                current = to;
                clearInterval(interval);
            }
            
            const countElement = document.getElementById('skill-count-value');
            if (countElement) {
                countElement.textContent = Math.round(current);
            }
        }, duration / steps);
    }

    // Update display with progress bar and better UI
    function updateDisplay() {
        const previousCount = document.getElementById('skill-count-value') ? parseInt(document.getElementById('skill-count-value').textContent) : 0;
        
        // Update progress bar
        const progressPercent = (selectedSkills.length / MAX_SKILLS) * 100;
        skillsProgressBar.style.width = progressPercent + '%';
        skillsCounter.textContent = selectedSkills.length + '/' + MAX_SKILLS;

        // Update hidden input
        skillsHiddenInput.value = selectedSkills.join(', ');
        
        // Update selected skills display with skill count
        if (selectedSkills.length === 0) {
            selectedSkillsDiv.innerHTML = '<div id="empty-state" class="w-full text-center py-4"><i class="fas fa-lightbulb text-3xl text-gray-400 dark:text-gray-500 mb-2 block"></i><p class="text-gray-500 dark:text-gray-400 text-sm">Select your skills and interests to showcase your expertise!</p></div>';
            return;
        }

        // Add skill count header with animated counter
        selectedSkillsDiv.innerHTML = `<div class="w-full mb-2 flex justify-between items-center">
            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Selected Skills (<span id="skill-count-value" class="text-purple-600 dark:text-purple-400 font-bold">${selectedSkills.length}</span>)</span>
        </div>`;

        // Animate counter if count changed
        if (previousCount !== selectedSkills.length) {
            animateCounter(previousCount, selectedSkills.length, 400);
        }

        const skillsContainer = document.createElement('div');
        skillsContainer.className = 'flex flex-wrap gap-2 w-full';
        
        const skillElements = selectedSkills.map((skill, index) => {
            const icon = getSkillIcon(skill);
            const div = document.createElement('span');
            div.className = 'skill-badge px-1.5 py-0.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs font-semibold rounded-full inline-flex items-center gap-0.5 hover:shadow-lg transition-all duration-300 shadow-md cursor-move animate-skill-enter';
            div.draggable = true;
            div.dataset.skill = skill;
            div.dataset.index = index;
            
            div.innerHTML = `<i class="fas ${icon} text-xs opacity-80"></i>
                ${skill}
                <button type="button" class="remove-skill hover:opacity-60 transition-opacity duration-200 ml-0.5" data-skill="${skill}">
                    <i class="fas fa-times text-xs"></i>
                </button>`;
            
            return div;
        });

        skillElements.forEach(el => skillsContainer.appendChild(el));
        selectedSkillsDiv.appendChild(skillsContainer);

        // Auto-scroll to newly added skill (last one)
        setTimeout(() => {
            const lastSkill = skillsContainer.lastElementChild;
            if (lastSkill) {
                lastSkill.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'end' });
            }
        }, 50);

        // Add remove handlers
        document.querySelectorAll('.remove-skill').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const skillEl = e.target.closest('.skill-badge');
                skillEl.classList.add('animate-skill-remove');
                setTimeout(() => {
                    removeSkill(this.getAttribute('data-skill'));
                }, 300);
            });
        });

        // Add drag handlers
        setupDragHandlers();
    }

    // Setup drag and drop handlers
    function setupDragHandlers() {
        const skillBadges = document.querySelectorAll('.skill-badge');
        
        skillBadges.forEach(badge => {
            badge.addEventListener('dragstart', function() {
                draggedSkill = this.dataset.skill;
                this.style.opacity = '0.5';
            });

            badge.addEventListener('dragend', function() {
                this.style.opacity = '1';
                draggedSkill = null;
            });

            badge.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.transform = 'scale(1.05)';
            });

            badge.addEventListener('dragleave', function() {
                this.style.transform = 'scale(1)';
            });

            badge.addEventListener('drop', function(e) {
                e.preventDefault();
                const draggedIndex = selectedSkills.indexOf(draggedSkill);
                const targetIndex = selectedSkills.indexOf(this.dataset.skill);
                
                if (draggedIndex !== targetIndex && draggedIndex !== -1) {
                    // Swap positions
                    [selectedSkills[draggedIndex], selectedSkills[targetIndex]] = [selectedSkills[targetIndex], selectedSkills[draggedIndex]];
                    updateDisplay();
                }
                this.style.transform = 'scale(1)';
            });
        });
    }

    // Focus to show all skills
    skillsSearchInput.addEventListener('focus', function() {
        renderDropdown(this.value);
    });

    // Search input listener for filtering
    skillsSearchInput.addEventListener('input', function() {
        renderDropdown(this.value);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.skills-component')) {
            skillsDropdown.classList.add('hidden');
        }
    });

    // Initialize - don't show dropdown by default
    if (noSkillsPlaceholder) {
        noSkillsPlaceholder.remove();
    }
});

// ============================================
// ADVANCED FORM VALIDATION & FEATURES
// ============================================

// Form Validation
function validateField(fieldName) {
    const field = document.getElementById('field-' + fieldName);
    const errorEl = document.getElementById('error-' + fieldName);
    let isValid = true;
    let errorMsg = '';

    if (!field) return;

    if (fieldName === 'name') {
        if (!field.value || field.value.trim().length < 2) {
            isValid = false;
            errorMsg = 'Name must be at least 2 characters';
        }
    } else if (fieldName === 'email') {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(field.value)) {
            isValid = false;
            errorMsg = 'Please enter a valid email address';
        }
    } else if (fieldName === 'phone') {
        const phoneField = document.getElementById('phone-input');
        const phoneValue = phoneField.value.replace(/\s/g, '').replace(/-/g, '');
        if (phoneValue.length !== 13 || !phoneValue.startsWith('+880')) {
            isValid = false;
            errorMsg = 'Phone must be complete (+880 1XXX-XXXXXX)';
        }
    } else if (fieldName === 'motivation') {
        if (!field.value.trim()) {
            isValid = false;
            errorMsg = 'Please tell us why you want to join';
        }
    }

    if (errorEl) {
        if (isValid) {
            errorEl.classList.add('hidden');
        } else {
            errorEl.textContent = errorMsg;
            errorEl.classList.remove('hidden');
        }
    }

    return isValid;
}

// Character Counter
window.updateCharCounter = function(fieldName, maxLength) {
    const field = document.getElementById('field-' + fieldName);
    const counter = document.getElementById(fieldName + '-counter');
    
    if (field && counter) {
        const length = field.value.length;
        counter.textContent = length + '/' + maxLength;
        
        if (length > maxLength) {
            counter.classList.add('text-red-500', 'dark:text-red-400');
            counter.classList.remove('text-purple-600', 'dark:text-purple-400');
        } else if (length >= maxLength * 0.8) {
            counter.classList.add('text-orange-500', 'dark:text-orange-400');
            counter.classList.remove('text-purple-600', 'dark:text-purple-400');
        } else {
            counter.classList.add('text-purple-600', 'dark:text-purple-400');
            counter.classList.remove('text-red-500', 'dark:text-red-400', 'text-orange-500', 'dark:text-orange-400');
        }
    }
};

// Profile Photo Preview
window.previewProfilePhoto = function() {
    const photoUrl = document.getElementById('field-profile_photo').value;
    const previewSection = document.getElementById('preview-section');
    const previewImg = document.getElementById('preview-image');
    
    if (photoUrl && photoUrl.startsWith('http')) {
        previewImg.src = photoUrl;
        previewImg.onload = function() {
            previewSection.classList.remove('hidden');
        };
        previewImg.onerror = function() {
            previewSection.classList.add('hidden');
        };
    } else {
        previewSection.classList.add('hidden');
    }
};

// Form Submission
window.submitForm = function(event) {
    console.log('submitForm called!');
    event.preventDefault();
    event.stopPropagation();
    
    const form = document.getElementById('membership-form');
    if (!form) {
        console.error('Form not found!');
        return false;
    }
    
    // Validate all required fields
    const requiredFields = ['name', 'email', 'phone', 'motivation'];
    let allValid = true;
    let firstInvalidField = null;

    for (let field of requiredFields) {
        const fieldElement = document.getElementById('field-' + field);
        if (!fieldElement) {
            console.log(`Field element not found: field-${field}`);
            continue;
        }
        
        if (!validateField(field)) {
            console.log(`Validation failed for field: ${field}`);
            allValid = false;
            if (!firstInvalidField) {
                firstInvalidField = fieldElement;
            }
        }
    }

    // Check skills
    const skillsField = document.getElementById('skills-hidden');
    if (!skillsField || !skillsField.value.trim()) {
        console.log('Skills validation failed');
        allValid = false;
        if (!firstInvalidField && skillsField) firstInvalidField = skillsField;
    }

    if (!allValid) {
        console.log('Overall validation failed, focusing on first error');
        // Auto-focus to first error
        if (firstInvalidField) {
            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalidField.focus();
        }
        return false;
    }

    console.log('All validation passed, preparing submission...');

    // Show loading state
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const submitSpinner = document.getElementById('submit-spinner');

    if (submitBtn) {
        submitBtn.disabled = true;
    }
    if (submitText) {
        submitText.classList.add('hidden');
    }
    if (submitSpinner) {
        submitSpinner.classList.remove('hidden');
    }

    // Get form data
    const formData = new FormData(form);
    
    // Log form data
    console.log('Form data being submitted:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }

    // Submit form via AJAX
    console.log('Sending POST request to /members.php...');
    fetch('/members.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        console.log('Raw response text:', text);
        
        // Try to parse as JSON
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Failed to parse JSON:', e, 'Text:', text);
            throw new Error('Invalid server response');
        }
        
        console.log('Response data:', data);
        
        if (data.success) {
            console.log('Registration successful!');
            const memberId = data.data?.member_id || 'ICT-' + new Date().getFullYear() + '001';
            
            // Show success notification
            showNotification('🎉 ' + (data.message || 'Registration successful!'), 'success');
            
            // Show success modal
            const modal = document.getElementById('success-modal');
            const memberIdDisplay = document.getElementById('member-id-display');
            if (modal && memberIdDisplay) {
                memberIdDisplay.textContent = memberId;
                modal.classList.remove('hidden');
            }
            
            // Reset form and skills
            form.reset();
            document.querySelectorAll('.skill-badge').forEach(el => el.remove());
            const emptyState = document.getElementById('empty-state');
            if (emptyState) emptyState.classList.remove('hidden');
            if (skillsField) skillsField.value = '';
        } else if (data.type === 'warning') {
            // Email already registered - show warning notification
            console.log('Email already registered warning');
            showNotification('⚠️ ' + (data.message || 'This email is already registered'), 'warning');
        } else {
            // Other errors
            console.error('Server returned error:', data.message);
            showNotification('❌ ' + (data.message || 'Registration failed'), 'error');
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        alert('❌ Error: ' + error.message + '. Please try again.');
    })
    .finally(() => {
        // Reset button state
        if (submitBtn) submitBtn.disabled = false;
        if (submitText) submitText.classList.remove('hidden');
        if (submitSpinner) submitSpinner.classList.add('hidden');
    });
    
    return false;
};

// Close Success Modal
window.closeSuccessModal = function() {
    const modal = document.getElementById('success-modal');
    modal.classList.add('hidden');
};
