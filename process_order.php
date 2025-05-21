<?php
session_start();
require_once 'database_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to place order']);
    exit;
}

// Get JSON data from request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data received']);
    exit;
}

try {
    // Start transaction
    $conn->beginTransaction();

    // Insert into orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_status, order_date) VALUES (?, ?, 'paid', NOW())");
    $stmt->execute([$_SESSION['user_id'], $data['total_amount']]);
    
    // Get the order ID
    $orderId = $conn->lastInsertId();

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($data['items'] as $item) {
        $stmt->execute([
            $orderId,
            $item['item_id'],
            $item['quantity'],
            $item['price']
        ]);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $orderId
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order: ' . $e->getMessage()
    ]);
}
?>