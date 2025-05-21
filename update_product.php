<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

require_once 'database_connection.php';

try {
    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Invalid input data');
    }

    // Validate required fields
    if (!isset($data['id']) || !isset($data['name']) || !isset($data['price']) || 
        !isset($data['category']) || !isset($data['description'])) {
        throw new Exception('Missing required fields');
    }

    // Prepare update query
    $stmt = $conn->prepare("UPDATE menu_items SET 
        name = ?, 
        price = ?, 
        category = ?, 
        description = ?, 
        is_available = ? 
        WHERE id = ?");
    
    $stmt->bind_param("sdssii", 
        $data['name'],
        $data['price'],
        $data['category'],
        $data['description'],
        $data['is_available'],
        $data['id']
    );
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        throw new Exception('Failed to update product');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 