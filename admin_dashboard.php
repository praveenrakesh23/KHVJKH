<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

require_once 'database_connection.php';

// Fetch admin username
$stmt = $conn->prepare("SELECT username FROM admins WHERE id = ?");
$stmt->execute([$_SESSION['admin_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
$adminUsername = $admin['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Campus Canteen</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff6b6b;
            --secondary-color: #4ecdc4;
            --text-color: #2d3436;
            --bg-color: #f9f9f9;
            --white: #ffffff;
            --success-color: #2ecc71;
            --warning-color: #f1c40f;
            --danger-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-color);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: var(--dark-color);
            color: white;
            padding: 1rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu {
            margin-top: 2rem;
        }

        .menu-item {
            padding: 0.8rem 1rem;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            transition: background-color 0.3s ease;
        }

        .menu-item:hover, .menu-item.active {
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
        }

        .top-bar {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            color: var(--dark-color);
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .admin-username {
            font-weight: 600;
            color: var(--text-color);
        }

        .content-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--text-color);
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
            color: var(--white);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            color: var(--white);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #c0392b);
            color: var(--white);
        }

        .qr-scanner {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        #qr-video {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .order-row {
            transition: all 0.3s ease;
        }

        .order-row.paid {
            background-color: rgba(46, 204, 113, 0.1);
        }

        .order-row.fade-out {
            opacity: 0;
            transform: translateX(-100px);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 1rem 0.5rem;
            }

            .sidebar-header h2,
            .menu-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .menu-item {
                justify-content: center;
            }
            
            .content-section {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Admin Panel</h2>
        </div>
        <div class="sidebar-menu">
            <a href="#manage-products" class="menu-item active">
                <i class="fas fa-box"></i>
                <span>Manage Products</span>
            </a>
            <a href="#current-orders" class="menu-item">
                <i class="fas fa-shopping-cart"></i>
                <span>Current Orders</span>
            </a>
            <a href="#qr-scanner" class="menu-item">
                <i class="fas fa-qrcode"></i>
                <span>QR Scanner</span>
            </a>
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1 class="page-title">Admin Dashboard</h1>
            <div class="admin-info">
                <span class="admin-username">Welcome, <?php echo htmlspecialchars($adminUsername); ?></span>
            </div>
        </div>

        <!-- Manage Products Section -->
        <div id="manage-products" class="content-section">
            <h2 class="section-title">Manage Products</h2>
            <div class="table-container">
                <table id="products-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Current Orders Section -->
        <div id="current-orders" class="content-section" style="display: none;">
            <h2 class="section-title">Current Orders</h2>
            <div class="table-container">
                <table id="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Orders will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- QR Scanner Section -->
        <div id="qr-scanner" class="content-section" style="display: none;">
            <h2 class="section-title">QR Code Scanner</h2>
            <div class="qr-scanner">
                <video id="qr-video"></video>
                <div class="scanner-controls">
                    <button class="btn btn-primary" onclick="startScanner()">Start Scanner</button>
                    <button class="btn btn-danger" onclick="stopScanner()">Stop Scanner</button>
                </div>
                <p>Scan QR code to mark order as paid</p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script>
        // Handle sidebar navigation
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('click', function(e) {
                if (this.getAttribute('href') === 'logout.php') return;
                
                e.preventDefault();
                document.querySelectorAll('.menu-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                const target = this.getAttribute('href').substring(1);
                document.querySelectorAll('.content-section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById(target).style.display = 'block';

                // Load data when switching sections
                if (target === 'manage-products') {
                    loadProducts();
                } else if (target === 'current-orders') {
                    loadOrders();
                } else if (target === 'qr-scanner') {
                    startScanner();
                }
            });
        });

        // QR Scanner setup
        let codeReader = null;
        let isScannerActive = false;

        function startScanner() {
            if (isScannerActive) return;
            
            const video = document.getElementById('qr-video');
            codeReader = new ZXing.BrowserQRCodeReader();

            codeReader.getVideoInputDevices()
                .then((videoInputDevices) => {
                    if (videoInputDevices.length > 0) {
                        isScannerActive = true;
                        codeReader.decodeFromVideoDevice(videoInputDevices[0].deviceId, video, (result, err) => {
                            if (result) {
                                handleQRScan(result.text);
                            }
                            if (err && !(err instanceof ZXing.NotFoundException)) {
                                console.error(err);
                            }
                        });
                    }
                })
                .catch(err => console.error(err));
        }

        function stopScanner() {
            if (codeReader && isScannerActive) {
                codeReader.reset();
                isScannerActive = false;
                const video = document.getElementById('qr-video');
                video.srcObject = null;
            }
        }

        function handleQRScan(orderId) {
            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: 'Paid'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order marked as paid successfully!');
                    loadOrders(); // Refresh the orders list
                } else {
                    alert('Error updating order status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order status');
            });
        }

        // Fetch and display products
        function loadProducts() {
            fetch('get_products.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.querySelector('#products-table tbody');
                        tbody.innerHTML = data.products.map(product => `
                            <tr>
                                <td>${product.name}</td>
                                <td>${product.category}</td>
                                <td>₹${product.price}</td>
                                <td>${product.is_available ? 'Available' : 'Unavailable'}</td>
                                <td>
                                    <button class="btn btn-primary" onclick="editProduct(${product.id})">Edit</button>
                                    <button class="btn btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        console.error('Error loading products:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching products:', error);
                });
        }

        // Fetch and display orders
        function loadOrders() {
            fetch('get_orders.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const tbody = document.querySelector('#orders-table tbody');
                        tbody.innerHTML = data.orders.map(order => `
                            <tr data-order-id="${order.id}">
                                <td>${order.id}</td>
                                <td>${order.customer_name}</td>
                                <td>₹${order.total_amount}</td>
                                <td>${order.status}</td>
                                <td>
                                    <button class="btn btn-success" onclick="markAsCompleted(${order.id})">Mark as Completed</button>
                                </td>
                            </tr>
                        `).join('');
                    } else {
                        console.error('Error loading orders:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                });
        }

        // Mark order as completed
        function markAsCompleted(orderId) {
            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: 'completed'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadOrders(); // Refresh orders list
                } else {
                    console.error('Error updating order:', data.message);
                }
            })
            .catch(error => {
                console.error('Error updating order:', error);
            });
        }

        // Load initial data
        loadProducts();
        loadOrders();

        // Refresh orders every 30 seconds
        setInterval(loadOrders, 30000);

        // Stop scanner when leaving the page
        window.addEventListener('beforeunload', stopScanner);
    </script>
</body>
</html> 