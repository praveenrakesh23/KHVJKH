<?php
require_once('qrcode.php');

session_start();
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    header('HTTP/1.0 400 Bad Request');
    exit('Order ID required');
}

// Create QR code
$qr = new QRCode($orderId);
$qrImageUrl = $qr->generate();

// Redirect to the generated QR code
header('Location: ' . $qrImageUrl);
exit();
?> 