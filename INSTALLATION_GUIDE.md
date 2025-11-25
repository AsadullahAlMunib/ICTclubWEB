# ICT Club Website - Installation & Deployment Guide

## Prerequisites
- PHP 8.0 or higher
- Web server (Apache, Nginx, or similar)
- Write permissions on server for data folder

## Installation Steps

### 1. Upload Files to Server
- Upload all files to your web server's public_html or www directory
- Maintain the same folder structure

### 2. Set Folder Permissions
```bash
# SSH into your server and run:
chmod 755 data/
chmod 644 data/*.json

# If you get permission errors, try:
chmod 777 data/
chown -R www-data:www-data data/  # Replace www-data with your server user
```

### 3. Verify Installation
- Visit `https://your-domain.com` - should see home page
- Visit `https://your-domain.com/members` - should see members page
- Visit `https://your-domain.com/blog` - should see blog page

### 4. Test Registration System
1. Go to Members page
2. Click "Join Our Club"
3. Fill out the form and submit
4. Should see success notification and modal with Member ID

### 5. Test Subscribe System
1. Go to home page
2. Scroll to newsletter section
3. Enter email and click subscribe
4. Should see success notification

---

## File Structure

```
your-site/
├── index.php                 (Home page)
├── about.php                 (About page)
├── members.php               (Members & registration)
├── projects.php              (Projects page)
├── events.php                (Events page)
├── blog/
│   └── [slug].php           (Individual blog posts)
├── contact.php               (Contact page)
├── includes/
│   ├── header.php           (Site header)
│   ├── footer.php           (Site footer)
│   ├── config.php           (Configuration)
│   └── functions.php        (Helper functions)
├── assets/
│   ├── css/
│   │   └── style.css        (Main styles)
│   └── js/
│       └── main.js          (Main JavaScript)
├── data/
│   ├── registrations.json   (Member registrations)
│   ├── subscribers.json     (Newsletter subscribers)
│   ├── contacts.json        (Contact submissions)
│   ├── members.json         (Club members)
│   ├── blog.json            (Blog posts)
│   ├── events.json          (Events)
│   └── projects.json        (Projects)
└── images/                  (Website images)
```

---

## Configuration

Edit `includes/config.php` to customize:
- `SITE_NAME` - Your club name
- `SITE_EMAIL` - Admin email for notifications
- Other settings as needed

---

## Troubleshooting

See `DEPLOYMENT_TROUBLESHOOTING.md` for common issues and solutions.

---

## Support

If registration isn't working:
1. Check `DEPLOYMENT_TROUBLESHOOTING.md`
2. Verify data folder permissions
3. Check your hosting's error logs
4. Contact your hosting provider if mail() isn't working

