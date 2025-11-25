# ICT Club Website

## Overview
The ICT Club website is a high-end platform built with PHP and Tailwind CSS, designed to provide comprehensive club management, interactive elements, stunning animations, and a fully functional blog system. Its purpose is to showcase club activities, manage members, feature projects, host events, and publish blog content with premium author profiles. The project aims to deliver a world-class user experience with a focus on modern design and robust functionality.

## User Preferences
- Mobile-first design approach
- No disabled or empty visual states
- Clean, professional aesthetics with premium touches
- Vibrant blue-purple-pink gradient color scheme
- Dark mode support with proper visibility
- Smooth, continuous animations with organic easing
- Professional spacing and typography hierarchy
- Enterprise-grade UI components
- Complete information display (nothing hidden)

## System Architecture

### UI/UX Decisions
The website features a modern and premium design aesthetic with:
- **Color Scheme**: Vibrant blue-purple-pink gradients.
- **Design Patterns**: Glass morphism effects with backdrop blur, smooth scrolling, hover effects, parallax scrolling, animated counters, interactive forms.
- **Typography**: Premium typography with proper hierarchy using Google Fonts (Inter, Poppins).
- **Responsiveness**: Fully responsive, mobile-first design optimized for all devices.
- **Dark Mode**: Professional aesthetics with a toggle for dark/light mode.
- **Animations**: Extensive use of animations for hero sections, navigation, scroll-triggered effects (AOS), particle backgrounds, and blob animations.

### Technical Implementations
- **Backend**: PHP 8.2+ for server-side logic, form validation, data handling, and utility functions.
- **Frontend**: Tailwind CSS (CDN) for utility-first styling and Alpine.js for lightweight interactivity.
- **JavaScript Libraries**: Particles.js for animated backgrounds, AOS for scroll animations, Swiper.js for carousels.
- **Data Management**: All data (members, projects, events, blog posts) is stored and managed using JSON files.
- **Favicon**: SVG favicon embedded as a data URI for instant loading, with a fallback `favicon.ico`.
- **Meta Tags**: Dynamic Open Graph and Twitter Card meta tags for enhanced social sharing and SEO.
- **Email Protection**: JavaScript `obfuscateEmail()` function to protect displayed email addresses from spam.
- **Form Handling**: PHP for form processing, validation, sanitization, and AJAX-ready JSON responses. JavaScript functions for loading states (`setFormLoading()`) and success messages (`showSuccessMessage()`).
- **Iconography**: Font Awesome 6 for consistent and professional icons.

### Feature Specifications
- **Home Page**: Animated hero, live statistics, featured projects slider, upcoming events, team highlights, announcements, quick contact.
- **About Us**: Interactive club history timeline, mission/vision, achievement stats, leadership team, facilities, roadmap, testimonials.
- **Members Page**: Executive committee showcase, live search member directory, join club form, skills display, alumni network, member spotlight.
- **Projects Page**: Filterable project portfolio, project details modals, GitHub integration, collaboration showcases.
- **Events Page**: Calendar, registration system, past events gallery, workshop schedules, competition announcements.
- **Blog System**: Responsive listing with search and category filters, individual post pages with author info, related articles, time-to-read estimation.
- **Counting Animations**: Smooth scroll-triggered counting animations for statistics and achievements. Fully robust for web server deployment with standalone animation initialization functions.
- **Author Profile (Premium)**: Sticky sidebar, fixed premium navigation, gradient glow effects, comprehensive author info (skills, social links, featured articles), professional typography, full dark mode support, responsive design.
- **Contact Page**: Contact form, executive contacts, social media integration, interactive map, FAQ, newsletter subscription.

## Technical Improvements (Latest)
- **Enhanced Registration Form** (Nov 24, 2025): Streamlined form with essential features. Features:
  - **Character Counters**: Live character counters for motivation and bio (0-300) with color-coded warnings (informational only)
  - **Success Modal**: Confirmation screen displaying generated member ID
  - **Auto-focus to Errors**: Smooth scroll to first validation error when submission fails
  - **Loading State**: Submit button shows spinner and disables during form processing
  - **Profile Photo Preview**: Real-time image preview appears only when valid image URL is entered
  - **Form Validation**: Comprehensive validation for name (2+ chars), email (valid format), phone (complete format), motivation (required, any length)
- **Advanced Profile Section** (Nov 24, 2025): Optional collapsible profile enhancement for registrations. Features:
  - **Collapsible dropdown**: Toggle with smooth Alpine.js animations
  - **Profile photo URL**: Optional direct image link field
  - **Bio field**: Optional self-description textarea (up to 300 chars)
  - **Social media links**: 8 platforms (GitHub, LinkedIn, Twitter, Facebook, Instagram, Website, Discord, Telegram)
  - **All optional**: Users can fill any combination or skip entirely
  - **Data storage**: All fields saved to registrations.json with null checks
  - **Email integration**: Admin gets notified of all Advanced Profile data when provided
  - **Display-ready**: Data structure prepared for member profile pages
- **Skills Autocomplete Component** (Nov 24, 2025): Implemented searchable skills input with 55+ default skills. Features:
  - **Curated skill list**: 55+ pre-defined skills (programming languages, frameworks, tools, soft skills, robotics & DIY)
  - **Real-time search**: Filter skills as you type
  - **Tag system**: Click skills to add them, shows as removable tags with "×" button
  - **Selected skills display**: Visual cards showing chosen skills with gradient styling
  - **Hidden input sync**: Automatically formats skills as comma-separated for form submission
  - **Autocomplete dropdown**: Shows filtered matches, hides used skills to prevent duplicates
  - **Click-to-add**: One-click skill selection from dropdown
  - **Remove with cross**: Click × to remove any added skill
  - **Focus/blur handling**: Dropdown closes when clicking outside
- **Phone Input Validation System** (Nov 24, 2025): Implemented enterprise-grade Bangladesh mobile number formatting. Features:
  - **Immutable prefix**: "+880 " cannot be edited or deleted (completely locked)
  - **Fixed format**: Total 13 digits (+880 + 1XXX-XXXXXX = 10 user digits)
  - **Format display**: +880 1234-567890 with automatic dash insertion after 4 digits
  - **Cursor control**: Always positioned after space, cannot move before prefix
  - **Protection**: Blocks backspace/delete/arrow keys in protected zone
  - **Real-time validation**: Visual feedback (green border when valid, red when incomplete)
  - **Smart error messages**: Shows exactly how many digits needed
  - **First digit enforcement**: Must be 1 (silently rejects other starting digits)
- **Animation System Refactor** (Nov 23, 2025): Moved counting animation functions outside DOMContentLoaded event listener for robust web server compatibility. Functions now initialize independently with multiple fallback timing mechanisms (immediate, 500ms, 1000ms delays). Ensures "Our Achievements" and "Statistics Dashboard" animations work on all servers.
- **Form Submission & Notification Fix** (Nov 25, 2025): Fixed duplicate notification bar showing on registration. Issues resolved:
  - **Problem**: Success registration was showing "email already registered" warning instead of success message
  - **Solution**: Updated JavaScript to prevent generic form handler from interfering with membership form submission. Made submitForm() exclusive handler for membership form using event.stopPropagation()
  - **Behavior**: Now properly checks email FIRST before registration:
    - If email exists → shows warning notification, no registration saved
    - If email new → shows success notification + modal with generated Member ID (ICT-YYYYNNN format)
  - **Backend validation**: PHP checks for duplicate emails (case-insensitive) before generating Member ID
  - **Form elements**: Success modal displays generated member ID, form auto-resets on success
  - **Member ID Format**: Changed from ICT-2025-012 to ICT-2025012 (format: ICT-YYYYNNN where NNN is 3-digit sequential number)

## External Dependencies
- **Tailwind CSS**: Utilized via CDN for utility-first styling and grid system.
- **Alpine.js**: Integrated for declarative JavaScript functionality.
- **Particles.js**: Used for creating animated backgrounds.
- **AOS (Animate On Scroll)**: For scroll-triggered animations.
- **Swiper.js**: Implemented for carousels and sliders.
- **Font Awesome 6**: Provides a comprehensive set of icons.
- **Google Fonts**: Inter and Poppins are used for typography.