<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

try {
    // Sample order data in JSON format
    $orderData = [
        'order_id' => 123,
        'user_id' => 1,
        'total_amount' => 150.00,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ];

    $qrCode = new QrCode(
        json_encode($orderData), // Convert order data to JSON string
        new Encoding('UTF-8'),
        ErrorCorrectionLevel::High,
        300,
        10,
        RoundBlockSizeMode::None,
        new Color(0, 0, 0),
        new Color(255, 255, 255)
    );

    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    header('Content-Type: ' . $result->getMimeType());
    echo $result->getString();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 