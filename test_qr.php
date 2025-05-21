<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Create QR code
$qrCode = QrCode::create('Test QR Code Content')
    ->setSize(300)
    ->setMargin(10);

// Generate PNG
$writer = new PngWriter();
$result = $writer->write($qrCode);

// Output the QR code directly to browser
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
?> 