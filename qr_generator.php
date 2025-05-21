<?php
// Enable error reporting to show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

function generateAndSendQR($orderId, $userEmail, $userName) {
    try {
        // Generate QR code
        $qrCode = new QrCode($orderId);
        $qrCode->setSize(300);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Save QR code temporarily
        $qrPath = 'temp_qr/' . $orderId . '.png';
        if (!file_exists('temp_qr')) {
            if (!mkdir('temp_qr', 0777, true)) {
                throw new Exception("Failed to create temp_qr directory");
            }
        }
        
        if (!$result->saveToFile($qrPath)) {
            throw new Exception("Failed to save QR code");
        }
        
        // Send email with QR code
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
        
        // Additional settings
        $mail->Timeout = 60;
        $mail->SMTPKeepAlive = true;
        
        // Recipients
        $mail->setFrom('praveenrakeh23@gmail.com', 'Campus Canteen');
        $mail->addAddress($userEmail, $userName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Order QR Code - Campus Canteen';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <h2 style='color: #ff6b6b;'>Thank you for your order!</h2>
                <p>Dear $userName,</p>
                <p>Your order has been successfully placed. Please find your order QR code attached.</p>
                <p><strong>Order Details:</strong></p>
                <ul>
                    <li>Order ID: $orderId</li>
                    <li>Order Date: " . date('Y-m-d H:i:s') . "</li>
                </ul>
                <p>Please show this QR code at the counter to collect your order.</p>
                <p style='color: #666; font-size: 0.9em;'>This is an automated email. Please do not reply.</p>
                <p>Best regards,<br><strong>Campus Canteen Team</strong></p>
            </div>
        ";
        $mail->addAttachment($qrPath, 'order_qr.png');
        
        // Try to send the email
        if (!$mail->send()) {
            throw new Exception("Mailer Error: " . $mail->ErrorInfo);
        }
        
        // Clean up temporary QR code
        if (file_exists($qrPath)) {
            unlink($qrPath);
        }
        
        return true;
    } catch (Exception $e) {
        // Clean up temporary QR code even if email fails
        if (file_exists($qrPath)) {
            unlink($qrPath);
        }
        echo "Error: " . $e->getMessage() . "<br>";
        echo "Stack trace: " . $e->getTraceAsString() . "<br>";
        return false;
    }
}
?> 