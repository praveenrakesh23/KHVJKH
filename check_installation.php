<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Checking PHP version: " . PHP_VERSION . "<br>";
echo "Checking required extensions:<br>";

$required_extensions = ['openssl', 'pdo', 'pdo_mysql', 'json'];
foreach ($required_extensions as $ext) {
    echo $ext . ": " . (extension_loaded($ext) ? "Loaded" : "Not loaded") . "<br>";
}

echo "<br>Checking Composer autoloader:<br>";
if (file_exists('vendor/autoload.php')) {
    echo "Composer autoloader found<br>";
    
    // Check PHPMailer
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        echo "PHPMailer is installed<br>";
    } else {
        echo "PHPMailer is NOT installed<br>";
    }
    
    // Check QR Code library
    if (class_exists('Endroid\QrCode\QrCode')) {
        echo "QR Code library is installed<br>";
    } else {
        echo "QR Code library is NOT installed<br>";
    }
} else {
    echo "Composer autoloader NOT found<br>";
    echo "Please run: composer require phpmailer/phpmailer endroid/qr-code<br>";
}
?> 