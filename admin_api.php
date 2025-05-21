<?php
session_start();
header('Content-Type: application/json');
require_once('config.php');

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get database connection
$conn = getDBConnection();
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_products':
        $sql = "SELECT * FROM menu_items ORDER BY category, name";
        $result = $conn->query($sql);
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        echo json_encode($products);
        break;

    case 'add_product':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $sql = "INSERT INTO menu_items (name, description, price, category, image) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdss", 
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['image']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'update_product':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $sql = "UPDATE menu_items 
                SET name = ?, description = ?, price = ?, category = ?, image = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", 
            $data['name'],
            $data['description'],
            $data['price'],
            $data['category'],
            $data['image'],
            $data['id']
        );
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'delete_product':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $sql = "DELETE FROM menu_items WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $data['id']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    case 'get_orders':
        $sql = "SELECT o.*, 
                GROUP_CONCAT(CONCAT(m.name, ':', oi.quantity, ':', oi.price) SEPARATOR '|') as items
                FROM orders o
                LEFT JOIN order_items oi ON o.order_id = oi.order_id
                LEFT JOIN menu_items m ON oi.item_id = m.id
                GROUP BY o.order_id
                ORDER BY o.order_date DESC";
        
        $result = $conn->query($sql);
        $orders = [];
        
        while ($row = $result->fetch_assoc()) {
            // Process items string into array
            $itemsArray = [];
            if ($row['items']) {
                $items = explode('|', $row['items']);
                foreach ($items as $item) {
                    list($name, $quantity, $price) = explode(':', $item);
                    $itemsArray[] = [
                        'name' => $name,
                        'quantity' => (int)$quantity,
                        'price' => (float)$price
                    ];
                }
            }
            $row['items'] = $itemsArray;
            $orders[] = $row;
        }
        
        echo json_encode($orders);
        break;

    case 'update_order_status':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $data['status'], $data['order_id']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $conn->error]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

$conn->close();
?> 