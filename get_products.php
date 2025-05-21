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
    // Get all menu items
    $stmt = $conn->prepare("
        SELECT id, name, price, category, description, image
        FROM menu_items
        ORDER BY category, name
    ");

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the products array
    $formattedProducts = array_map(function($row) {
        return [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'category' => $row['category'],
            'description' => $row['description'],
            'image_path' => $row['image']
        ];
    }, $products);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'products' => $formattedProducts]);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error fetching products',
        'error' => $e->getMessage()
    ]);
}
?> 