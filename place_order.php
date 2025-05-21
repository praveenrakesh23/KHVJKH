<?php
// Enable error reporting to show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'database_connection.php';
require_once 'qr_generator.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        echo "Starting order process...<br>";
        
        // Start transaction
        $conn->beginTransaction();
        echo "Transaction started<br>";
        
        // Get user details
        $userId = $_SESSION['user_id'];
        echo "User ID: " . $userId . "<br>";
        
        $stmt = $conn->prepare("SELECT email, full_name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        echo "User found: " . print_r($user, true) . "<br>";
        
        // Insert order
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, payment_status) 
            VALUES (?, ?, 'Pending')
        ");
        $stmt->execute([$userId, $_POST['total_amount']]);
        $orderId = $conn->lastInsertId();
        
        echo "Order created with ID: " . $orderId . "<br>";
        
        // Insert order items
        $stmt = $conn->prepare("
            INSERT INTO order_items (order_id, item_id, quantity, price) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($_POST['items'] as $item) {
            $stmt->execute([
                $orderId,
                $item['id'],
                $item['quantity'],
                $item['price']
            ]);
        }
        
        echo "Order items inserted successfully<br>";
        
        // Generate and send QR code
        echo "Attempting to send QR code email to: " . $user['email'] . "<br>";
        if (generateAndSendQR($orderId, $user['email'], $user['full_name'])) {
            echo "QR code email sent successfully<br>";
            $conn->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Order placed successfully. QR code has been sent to your email.',
                'order_id' => $orderId
            ]);
        } else {
            throw new Exception("Failed to send QR code email");
        }
        
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error in place_order.php: " . $e->getMessage() . "<br>";
        echo "Stack trace: " . $e->getTraceAsString() . "<br>";
        echo json_encode([
            'success' => false,
            'message' => 'Error placing order: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?> 