# Registration & Subscribe System - Deployment Troubleshooting Guide

## Common Issues & Solutions

### 1. **"File Permission Denied" Error**
**Problem**: The data folder doesn't have write permissions

**Solution**: Run these commands on your web server:
```bash
# Give write permissions to data folder
chmod 755 data/
chmod 644 data/*.json

# Or use more permissive settings if needed
chmod 777 data/
```

### 2. **"Failed to save registration" Error**
**Problem**: The system can't write to JSON files

**Causes**:
- Wrong permissions on data folder (see above)
- Data folder doesn't exist
- File ownership issues (Apache/Nginx user doesn't own files)

**Solution**:
```bash
# Check permissions
ls -la data/

# Fix ownership (replace www-data with your web server user)
chown -R www-data:www-data data/
chmod -R 755 data/

# Verify JSON files are readable/writable
ls -la data/*.json
```

### 3. **Email Not Being Sent**
**Problem**: Emails are not sent (but registration might still save)

**Solutions**:
- Check if mail() function is enabled on your server
- Ask your hosting provider to enable PHP mail()
- Look for postfix/sendmail service running
- Check error logs: `/var/log/mail.log`

### 4. **Empty Response or JSON Errors**
**Problem**: Form submission returns error or empty response

**Solutions**:
- Check PHP error logs: `/var/log/php-errors.log` or your hosting's error logs
- Look in `data/subscribers.json` and `data/registrations.json` for write errors
- Check if `includes/config.php` and `includes/functions.php` exist

### 5. **Path Issues (Relative Paths)**
**Problem**: Files not found when using relative paths

**Solution**: 
The code uses `__DIR__` which is absolute and should work. If not:
- Verify all files are uploaded correctly to the same structure
- Check that `includes/` folder exists
- Verify `data/` folder exists

---

## Step-by-Step Verification

### On Your Web Server, Run:

```bash
# 1. Check if data folder exists and has correct permissions
ls -la data/

# 2. Test if PHP can write to data folder
php -r "file_put_contents('data/test.json', json_encode(['test' => 'works'])); echo 'Write test successful';"

# 3. Check PHP error logs
tail -f /var/log/php-errors.log

# 4. Check Apache/Nginx error logs
tail -f /var/log/apache2/error.log  # For Apache
tail -f /var/log/nginx/error.log    # For Nginx

# 5. Verify file structure
find . -name "*.json" -o -name "functions.php"
```

---

## JSON Files Location

Make sure these files exist in `data/` folder:
- `registrations.json` - Member registrations
- `subscribers.json` - Newsletter subscribers  
- `contacts.json` - Contact form submissions
- `members.json` - Club members
- `blog.json` - Blog posts
- `events.json` - Events
- `projects.json` - Projects

---

## Enabling Error Logging

Add this to the top of `members.php` and `index.php` for debugging:

```php
// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/debug.log');
error_reporting(E_ALL);

// Log all errors to debug.log
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
    return false;
});
```

---

## Quick Test

After uploading, test with this URL:
```
https://your-domain.com/members.php
```

Should show the members page. If you get a white page:
- Check your hosting's error logs
- Look for PHP syntax errors
- Verify all files were uploaded correctly

---

## Hosting Provider Checklist

☐ PHP version 8.0 or higher installed  
☐ Write permissions on data folder  
☐ Mail() function enabled or SMTP configured  
☐ All PHP files uploaded correctly  
☐ Data folder writable by web server user  
☐ Error logs accessible to debug issues  

---

## Still Having Issues?

1. **Check debug.log file** in your website root for errors
2. **Test permissions** by creating a test file
3. **Contact hosting provider** if mail() is disabled
4. **Verify file structure** - all files in correct locations

