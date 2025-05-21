<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['user_name'] : '';

// Redirect if not logged in
if (!$loggedIn) {
    header('Location: index.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'campus_canteen');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch orders with items for the current user
$userId = $_SESSION['user_id'];
$sql = "SELECT o.*, 
        GROUP_CONCAT(CONCAT(m.name, ':', oi.quantity) SEPARATOR '|') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN menu_items m ON oi.item_id = m.id
        WHERE o.user_id = ?
        GROUP BY o.order_id
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Campus Canteen</title>
    <style type="text/css">
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #4ecdc4;
            --text-color: #2d3436;
            --bg-color: #f9f9f9;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            padding-top: 80px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: var(--white);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            text-decoration: none;
        }

        .orders-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .page-title {
            font-size: 2rem;
            color: var(--text-color);
            margin-bottom: 2rem;
            text-align: center;
        }

        .order-card {
            background-color: var(--white);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .order-id {
            font-size: 1.1rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .order-date {
            color: #666;
            font-size: 0.9rem;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .order-total {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .status-Pending {
            background-color: #ffeaa7;
            color: #fdcb6e;
        }

        .status-Paid {
            background-color: #55efc4;
            color: #00b894;
        }

        .status-Failed {
            background-color: #ff7675;
            color: var(--white);
        }

        .no-orders {
            text-align: center;
            padding: 3rem;
            background-color: var(--white);
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .no-orders h3 {
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
            margin-top: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .order-header, .order-footer {
                flex-direction: column;
                gap: 0.5rem;
                align-items: flex-start;
            }
        }

        .order-items {
            margin: 1rem 0;
            padding: 1rem;
            background-color: rgba(255, 107, 107, 0.05);
            border-radius: 8px;
        }

        .item-list {
            list-style: none;
            padding: 0;
        }

        .item-list li {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px dashed #eee;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .item-list li:last-child {
            border-bottom: none;
        }

        .item-quantity {
            background-color: var(--primary-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">Campus Canteen</a>
    </nav>

    <div class="orders-container">
        <h1 class="page-title">My Orders</h1>
        
        <?php if (empty($orders)): ?>
            <div class="no-orders">
                <h3>No orders found</h3>
                <p>Looks like you haven't placed any orders yet.</p>
                <a href="index.php" class="btn btn-primary">Browse Menu</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></span>
                        <span class="order-date"><?php echo date('F j, Y, g:i a', strtotime($order['order_date'])); ?></span>
                    </div>

                    <?php if ($order['items']): ?>
                        <div class="order-items">
                            <ul class="item-list">
                                <?php
                                $items = explode('|', $order['items']);
                                foreach ($items as $item) {
                                    list($name, $quantity) = explode(':', $item);
                                    ?>
                                    <li>
                                        <span><?php echo htmlspecialchars($name); ?></span>
                                        <?php if ($quantity > 1): ?>
                                            <span class="item-quantity">x<?php echo $quantity; ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="order-footer">
                        <div class="order-total">
                            Total: ₹<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                        <div class="order-status status-<?php echo htmlspecialchars($order['payment_status']); ?>">
                            <?php echo htmlspecialchars($order['payment_status']); ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any additional JavaScript functionality here
    });
    </script>
</body>
</html>
