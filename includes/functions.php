<?php
// Utility functions for ICT Club website

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email address
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Send email using PHP mail function
 */
function send_email($to, $subject, $message, $from = SITE_EMAIL) {
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Load JSON data from file
 */
function load_json_data($filename) {
    $filepath = __DIR__ . '/../data/' . $filename;
    if (file_exists($filepath)) {
        $json = file_get_contents($filepath);
        return json_decode($json, true);
    }
    return [];
}

/**
 * Save JSON data to file
 */
function save_json_data($filename, $data) {
    $filepath = __DIR__ . '/../data/' . $filename;
    
    // Ensure data directory exists
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    $json = json_encode($data, JSON_PRETTY_PRINT);
    $result = file_put_contents($filepath, $json);
    
    // Log any write errors
    if ($result === false) {
        error_log("Failed to write to $filepath. Check folder permissions.");
    }
    
    return $result;
}

/**
 * Generate random token
 */
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Format date
 */
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Get time ago string
 */
function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    
    $minutes = round($seconds / 60);
    $hours = round($seconds / 3600);
    $days = round($seconds / 86400);
    $weeks = round($seconds / 604800);
    $months = round($seconds / 2629440);
    $years = round($seconds / 31553280);
    
    if ($seconds <= 60) {
        return "just now";
    } else if ($minutes <= 60) {
        return "$minutes minutes ago";
    } else if ($hours <= 24) {
        return "$hours hours ago";
    } else if ($days <= 7) {
        return "$days days ago";
    } else if ($weeks <= 4.3) {
        return "$weeks weeks ago";
    } else if ($months <= 12) {
        return "$months months ago";
    } else {
        return "$years years ago";
    }
}

/**
 * Truncate text
 */
function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

/**
 * Generate slug from text
 */
function generate_slug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Get file extension
 */
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is image
 */
function is_image_file($filename) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    return in_array(get_file_extension($filename), $allowed_extensions);
}

/**
 * Create success response
 */
function success_response($message, $data = []) {
    return [
        'success' => true,
        'message' => $message,
        'data' => $data
    ];
}

/**
 * Create error response
 */
function error_response($message, $errors = []) {
    return [
        'success' => false,
        'message' => $message,
        'errors' => $errors
    ];
}

/**
 * Create warning response
 */
function warning_response($message, $data = []) {
    return [
        'success' => false,
        'message' => $message,
        'type' => 'warning',
        'data' => $data
    ];
}

/**
 * Return JSON response
 */
function json_response($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
