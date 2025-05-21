<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "Starting email test...<br>";

try {
    $mail = new PHPMailer(true);
    
    // Enable debugging
    $mail->SMTPDebug = 3;
    $mail->Debugoutput = function($str, $level) {
        echo "SMTP Debug: $str<br>";
    };
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'praveenrakeh23@gmail.com';
    $mail->Password = 'wojmozxrncvczcrv';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Recipients
    $mail->setFrom('praveenrakeh23@gmail.com', 'Campus Canteen');
    $mail->addAddress('praveenrakeh23@gmail.com', 'Test User');
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Campus Canteen';
    $mail->Body = 'This is a test email from the Campus Canteen system.';
    
    echo "Attempting to send email...<br>";
    $mail->send();
    echo "Email sent successfully!<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString() . "<br>";
}
?> 