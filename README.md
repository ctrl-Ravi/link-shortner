# Link Shortener with Ad Pages

This is a PHP-based link shortener system with monetization through ad pages. It allows you to create short links that redirect users through a series of ad pages before reaching the final destination.

## Features

- Admin panel for creating and managing short links
- Customizable number of ad pages (1-3) before final redirection
- Click tracking for all links
- Easy Adsterra ad integration
- Mobile-friendly responsive design
- Protection against bots
- Progress bar and security verification visualization
- Skip waiting button with additional ad monetization
- Social sharing options
- Returning visitor detection

## Requirements

- PHP 7.0 or higher
- MySQL 5.6 or higher
- Web server with mod_rewrite enabled (Apache recommended)

## Installation on Hostinger

For detailed Hostinger installation instructions, see [hostinger-setup.md](hostinger-setup.md).

Quick steps:
1. Create a MySQL database on Hostinger
2. Update database credentials in `db.php`
3. Upload all files to your Hostinger hosting
4. Ensure .htaccess is properly uploaded
5. Visit your domain to initialize the system
6. Log in to the admin panel with username `pelupa` and password `Ravi@1327#2613`

## General Installation

1. **Upload Files**:
   Upload all files to your web hosting directory (usually public_html).

2. **Database Setup**:
   - Create a new MySQL database
   - Edit the `db.php` file with your database credentials:
     ```php
     $host = "localhost";
     $username = "your_database_username";
     $password = "your_database_password";
     $database = "your_database_name";
     ```

3. **Server Configuration**:
   Make sure your server has mod_rewrite enabled and .htaccess file support.

4. **First Run**:
   - Visit your domain in a web browser
   - The database tables will be created automatically
   - The admin user will be created with:
     - Username: `pelupa`
     - Password: `Ravi@1327#2613`

5. **Admin Login**:
   - Go to `/admin.php`
   - Login with the credentials above

## Adsterra Integration

To integrate Adsterra ads:

1. Sign up for an Adsterra account at [adsterra.com](https://adsterra.com)
2. Create ad units for your site
3. The ad codes are already integrated in the following files:
   - `ad-page.php` - Main ad display during countdown
   - `ad-redirect.php` - Ad display when skipping the countdown

See [adsterra-integration.md](adsterra-integration.md) for detailed instructions on customizing ad placements.

## Security Considerations

- Keep your PHP and MySQL installations up to date
- Consider adding HTTPS to your site for better security
- The admin credentials are securely stored with password hashing

## Customization

- Edit `style.css` to change the appearance
- Modify the `ad-page.php` template to customize the ad pages
- Adjust the countdown timer by modifying the `secondsLeft` variable in JavaScript

## Troubleshooting

- If short links don't work, check if mod_rewrite is enabled
- Make sure the .htaccess file was uploaded correctly
- Check your PHP and MySQL error logs for any issues
- Verify database connection settings in db.php

## License

This software is provided as-is without warranty. You are free to modify and use it for personal or commercial purposes. 
