# Installation Guide for Campus Canteen System

## 1. Install Composer
1. Download Composer from https://getcomposer.org/download/
2. Follow the installation instructions for your operating system
3. Verify installation by running `composer --version` in your terminal

## 2. Install Required Packages
1. Open terminal/command prompt
2. Navigate to your project directory:
   ```bash
   cd /path/to/your/project
   ```
3. Initialize Composer:
   ```bash
   composer init
   ```
   - Press Enter for default values
   - Type "yes" when asked if you want to define dependencies

4. Install PHPMailer:
   ```bash
   composer require phpmailer/phpmailer
   ```

5. Install QR Code library:
   ```bash
   composer require endroid/qr-code
   ```

## 3. Verify Installation
1. Run the check_installation.php file in your browser
2. You should see:
   - All extensions loaded
   - Composer autoloader found
   - PHPMailer installed
   - QR Code library installed

## 4. Test Email Functionality
1. After installation, run test_email.php in your browser
2. You should see SMTP debug output and either:
   - Success message if email sent
   - Error message if something went wrong

## Troubleshooting
If you encounter any issues:
1. Make sure Composer is installed correctly
2. Check if you have write permissions in your project directory
3. Verify your PHP version is compatible (PHP 7.4 or higher)
4. Ensure all required PHP extensions are enabled 