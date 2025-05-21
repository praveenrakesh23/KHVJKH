<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'database_connection.php';

try {
    // Get today's orders
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT o.order_id, o.total_amount, o.payment_status, o.order_date, u.full_name as customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE DATE(o.order_date) = ?
        ORDER BY o.order_date DESC
    ");

    $stmt->execute([$today]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the orders array
    $formattedOrders = array_map(function($row) {
        return [
            'id' => $row['order_id'],
            'customer_name' => $row['customer_name'],
            'total_amount' => $row['total_amount'],
            'status' => $row['payment_status'],
            'created_at' => $row['order_date']
        ];
    }, $orders);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'orders' => $formattedOrders]);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error fetching orders',
        'error' => $e->getMessage()
    ]);
}
?> 