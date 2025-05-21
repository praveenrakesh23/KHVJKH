<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['user_name'] : '';
$userEmail = $loggedIn ? $_SESSION['user_email'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Canteen</title>
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
        }

        .nav-buttons {
            display: flex;
            align-items: center;
            gap: 1rem;
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

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
            color: var(--white);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #ff8787, var(--primary-color));
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color), #66c2a5);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #66c2a5, var(--secondary-color));
        }

        .cart-icon, .user-profile-icon {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .cart-icon:hover, .user-profile-icon:hover {
            background-color: rgba(255, 107, 107, 0.1);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
            color: var(--white);
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            display: none;
            z-index: 1000;
            overflow: hidden;
        }

        .user-dropdown.active {
            display: block;
        }

        .user-dropdown-item {
            padding: 1rem;
            color: var(--text-color);
            text-decoration: none;
            display: block;
            transition: all 0.3s ease;
        }

        .user-dropdown-item:hover {
            background-color: rgba(255, 107, 107, 0.1);
        }

        .user-info {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .user-info h4 {
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .user-info p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--white);
            padding: 0 1rem;
            margin-top: 60px;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero p {
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: var(--white);
            padding: 3rem;
            border-radius: 16px;
            width: 90%;
            max-width: 400px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .auth-form {
            display: none;
            flex-direction: column;
            gap: 1.2rem;
        }

        .auth-form.active {
            display: flex;
        }

        .auth-form h2 {
            text-align: center;
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .auth-form input {
            padding: 1rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .auth-form input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }

        .auth-form p {
            text-align: center;
            margin-top: 1rem;
        }

        .auth-form a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-form a:hover {
            text-decoration: underline;
        }

        .close {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
            font-size: 1.8rem;
            cursor: pointer;
            color: #666;
            transition: all 0.3s ease;
        }

        .close:hover {
            color: var(--primary-color);
            transform: rotate(90deg);
        }

        .menu-section {
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .menu-section h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
            color: var(--text-color);
        }

        .menu-filters {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            border: 2px solid var(--primary-color);
            border-radius: 25px;
            background: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            color: var(--text-color);
        }

        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .filter-btn.active {
            background: linear-gradient(135deg, var(--primary-color), #ff8787);
            color: var(--white);
            border: none;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .menu-item {
            background-color: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-8px);
        }

        .menu-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .menu-item-content {
            padding: 1.5rem;
        }

        .menu-item-content h3 {
            margin-bottom: 0.8rem;
            font-size: 1.3rem;
            color: var(--text-color);
        }

        .menu-item-content p {
            color: #666;
            margin-bottom: 1.2rem;
            line-height: 1.5;
        }

        .price {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.4rem;
            margin-bottom: 1rem;
        }

        .button-group {
            display: flex;
            gap: 0.5rem;
        }

        .button-group .btn {
            flex: 1;
            font-size: 0.85rem;
            padding: 0.6rem 1rem;
        }
        .user-profile-icon:hover .user-dropdown {
            display: block; /* Show dropdown on hover */
        }

        .cart-sidebar {
            position: fixed;
            right: -400px;
            top: 0;
            width: 400px;
            height: 100vh;
            background-color: var(--white);
            box-shadow: -4px 0 15px rgba(0, 0, 0, 0.1);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1500;
        }

        .cart-sidebar.active {
            right: 0;
        }

        .cart-header {
            padding: 1.5rem;
            border-bottom: 2px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-header h3 {
            font-size: 1.5rem;
            color: var(--text-color);
        }

        .cart-items {
            padding: 1.5rem;
            height: calc(100vh - 180px);
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            gap: 1.2rem;
            padding: 1.2rem;
            border-bottom: 1px solid #eee;
            background-color: #fff;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: transform 0.3s ease;
        }

        .cart-item:hover {
            transform: translateX(-5px);
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-details h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: var(--text-color);
        }

        .cart-item-details p {
            color: var(--primary-color);
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
        }

        .cart-footer {
            padding: 1.5rem;
            border-top: 2px solid #eee;
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: var(--white);
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.2rem;
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--text-color);
        }

        .form-error {
            color: var(--primary-color);
            font-size: 0.9rem;
            margin-top: -0.5rem;
            display: none;
        }

        @media (max-width: 768px) {
            .hero {
                height: 70vh;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .cart-sidebar {
                width: 100%;
                right: -100%;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 1rem;
            }
           
            .nav-buttons {
                gap: 0.5rem;
            }
           
            .btn {
                padding: 0.6rem 1rem;
                font-size: 0.8rem;
            }
           
            .hero h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">Campus Canteen</div>
        <div class="nav-buttons" id="navButtons">
        <?php if ($loggedIn): ?>
    <div class="user-profile-icon" id="userProfile">
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'/%3E%3Ccircle cx='12' cy='7' r='4'/%3E%3C/svg%3E" alt="Profile">
        <div class="user-dropdown" id="userDropdown" style="display: none;">
            <div class="user-info">
                <h4><?php echo htmlspecialchars($userName); ?></h4>
                <?php if ($userEmail): ?>
                    <p><?php echo htmlspecialchars($userEmail); ?></p>
                <?php endif; ?>
            </div>
            <a href="myorders.php" class="user-dropdown-item">My Orders</a>
            <a href="account_settings.php" class="user-dropdown-item">Account Settings</a>
            <a href="logout.php" class="user-dropdown-item">Logout</a>
        </div>
    </div>
<?php else: ?>
    <button onclick="showAuth('login')" class="btn">Login</button>
    <button onclick="showAuth('signup')" class="btn btn-primary">Sign Up</button>
    <button onclick="showAdminAuth()" class="btn btn-secondary">Admin Login</button>
<?php endif; ?>
    
            <div class="cart-icon" onclick="toggleCart()">
                <span class="cart-count">0</span>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='8' cy='21' r='1'/%3E%3Ccircle cx='19' cy='21' r='1'/%3E%3Cpath d='M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12'/%3E%3C/svg%3E" alt="Cart">
            </div>
        </div>
    </nav>

    <div class="hero">
        <h1>Welcome to Campus Canteen</h1>
        <p>Delicious food at your fingertips</p>
    </div>

    <div id="authModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeAuth()">&times;</span>
        <form id="loginForm" class="auth-form" method="post">
            <h2>Login</h2>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="form-error" id="loginError"></div>
            <button type="submit" class="btn btn-primary">Login</button>
            <p>Don't have an account? <a href="#" onclick="switchAuth('signup')">Sign up</a></p>
        </form>
        <form id="signupForm" class="auth-form" method="post" style="display: none;">
            <h2>Sign Up</h2>
            <input type="text" name="fullName" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <div class="form-error" id="signupError"></div>
            <button type="submit" class="btn btn-primary">Sign Up</button>
            <p>Already have an account? <a href="#" onclick="switchAuth('login')">Login</a></p>
        </form>
    </div>
</div>

    <div id="adminAuthModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeAdminAuth()">&times;</span>
            <form id="adminLoginForm" class="auth-form active" method="post">
                <h2>Admin Login</h2>
                <input type="email" name="email" placeholder="Admin Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <div class="form-error" id="adminLoginError"></div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>

    <main class="menu-section">
        <h2>Today's Menu</h2>
        <div class="menu-filters">
    <button class="filter-btn active" data-category="all" onclick="filterMenu('all', event)">All</button>
    <button class="filter-btn" data-category="breakfast" onclick="filterMenu('breakfast', event)">Breakfast</button>
    <button class="filter-btn" data-category="lunch" onclick="filterMenu('lunch', event)">Lunch</button>
    <button class="filter-btn" data-category="snacks" onclick="filterMenu('snacks', event)">Snacks</button>
    <button class="filter-btn" data-category="beverages" onclick="filterMenu('beverages', event)">Beverages</button>
</div>
        <div class="menu-grid" id="menuGrid">
            <!-- Menu items will be populated by JavaScript -->
        </div>
    </main>

    <div id="cart" class="cart-sidebar">
        <div class="cart-header">
            <h3>Your Cart</h3>
            <button class="close" onclick="toggleCart()">&times;</button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Cart items will be populated by JavaScript -->
        </div>
        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span id="cartTotal">₹0.00</span>
            </div>
            <button class="btn btn-primary" onclick="checkout()">Checkout</button>
        </div>
    </div>
    <script type="text/javascript">
    // Cart state
    let cart = [];
    let menuItems = []; // Global variable to store menu items

    // User state
    let currentUser = <?php echo $loggedIn ? json_encode([
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ]) : 'null'; ?>;

    // DOM Elements
    const menuGrid = document.getElementById('menuGrid');
    const authModal = document.getElementById('authModal');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const cartSidebar = document.getElementById('cart');
    const cartItemsContainer = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartCount = document.querySelector('.cart-count');
    const userDropdown = document.getElementById('userDropdown');
    const loginError = document.getElementById('loginError');
    const signupError = document.getElementById('signupError');

    // Fetch menu items from the database
    async function fetchMenuItems() {
        try {
            const response = await fetch('fetch_menu_items.php');
            menuItems = await response.json(); // Store fetched items in the global variable
            if (Array.isArray(menuItems) && menuItems.length > 0) {
                initializeMenu(menuItems);
            } else {
                console.error('No menu items found or invalid response:', menuItems);
            }
        } catch (error) {
            console.error('Error fetching menu items:', error);
        }
    }

    // Initialize the menu with fetched data
    function initializeMenu(items) {
        menuGrid.innerHTML = ''; // Clear existing content

        items.forEach(item => {
            const menuItem = createMenuItem(item);
            menuGrid.appendChild(menuItem);
        });
    }

    // Create a menu item card
    function createMenuItem(item) {
        const div = document.createElement('div');
        div.className = 'menu-item';
        div.innerHTML = `
            <img src="${item.image}" alt="${item.name}">
            <div class="menu-item-content">
                <h3>${item.name}</h3>
                <p>${item.description}</p>
                <div class="price">₹${item.price}</div>
                <div class="button-group">
                    <button class="btn btn-primary" onclick="addToCart(${item.id})">Add to Cart</button>
                    <button class="btn btn-primary" onclick="buyNow(${item.id})">Buy Now</button>
                </div>
            </div>
        `;
        return div;
    }

    // Filter menu items
function filterMenu(category, event) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active'); // Highlight the active button

    menuGrid.innerHTML = ''; // Clear existing content
    const filteredItems = category === 'all'
        ? menuItems
        : menuItems.filter(item => item.category === category);

    filteredItems.forEach(item => {
        const menuItem = createMenuItem(item);
        menuGrid.appendChild(menuItem);
    });
}

    // Add item to cart
    function addToCart(itemId) {
        const item = menuItems.find(item => item.id == itemId); // Use == to compare string IDs
        if (item) {
            cart.push(item);
            updateCart();
            toggleCart();
        }
    }

    // Buy now
    function buyNow(itemId) {
        if (!currentUser) {
            alert('Please login to continue');
            showAuth('login');
            return;
        }

        const item = menuItems.find(item => item.id == itemId); // Use == to compare string IDs
        if (item) {
            cart = [item]; // Set cart to only this item
            updateCart();
            checkout();
        }
    }

    // Remove item from cart
    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCart();
    }

    // Update cart UI
    function updateCart() {
        cartItemsContainer.innerHTML = '';
        let total = 0;

        cart.forEach((item, index) => {
            const cartItem = document.createElement('div');
            cartItem.className = 'cart-item';
            cartItem.innerHTML = `
                <img src="${item.image}" alt="${item.name}">
                <div class="cart-item-details">
                    <h4>${item.name}</h4>
                    <p>₹${item.price}</p>
                    <button class="btn" onclick="removeFromCart(${index})">Remove</button>
                </div>
            `;
            cartItemsContainer.appendChild(cartItem);
            total += parseFloat(item.price); // Ensure price is treated as a number
        });

        cartTotal.textContent = `₹${total.toFixed(2)}`; // Format total to 2 decimal places
        cartCount.textContent = cart.length;
    }

    // Toggle cart sidebar
    function toggleCart() {
        cartSidebar.classList.toggle('active');
    }

    // Checkout
    function checkout() {
        if (cart.length === 0) {
            alert('Your cart is empty!');
            return;
        }

        if (!currentUser) {
            alert('Please login to checkout');
            showAuth('login');
            return;
        }

        // Calculate total
        const total = cart.reduce((sum, item) => sum + parseFloat(item.price), 0);

        // Prepare order data
        const orderData = {
            user_id: currentUser.id,
            total_amount: total,
            items: cart.map(item => ({
                item_id: item.id,
                quantity: 1, // You can modify this if you implement quantity
                price: item.price
            }))
        };

        // Send order to server
        fetch('process_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order placed successfully!');
                cart = []; // Clear cart
                updateCart(); // Update cart UI
                toggleCart(); // Close cart sidebar
            } else {
                alert(data.message || 'Failed to place order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while placing your order');
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const profileIcon = document.getElementById("userProfile");
        const userDropdown = document.getElementById("userDropdown");

        if (profileIcon && userDropdown) {
            profileIcon.addEventListener("mouseenter", function () {
                userDropdown.style.display = "block";
            });

            profileIcon.addEventListener("mouseleave", function () {
                userDropdown.style.display = "none";
            });
        }
    });

    

    // Fetch and display menu items on page load
    document.addEventListener('DOMContentLoaded', fetchMenuItems);

    // Show the authentication modal
function showAuth(type) {
    const authModal = document.getElementById('authModal');
    if (type === 'login') {
        authModal.querySelector('#loginForm').style.display = 'block';
        authModal.querySelector('#signupForm').style.display = 'none';
    } else if (type === 'signup') {
        authModal.querySelector('#loginForm').style.display = 'none';
        authModal.querySelector('#signupForm').style.display = 'block';
    }
    authModal.style.display = 'block'; // Show the modal
}

// Close the authentication modal
function closeAuth() {
    const authModal = document.getElementById('authModal');
    authModal.style.display = 'none';
}

// Switch between login and signup forms
function switchAuth(type) {
    showAuth(type);
}

// Handle login form submission
document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    const formData = new FormData(this);
    
    console.log('Attempting login with:', Object.fromEntries(formData));
    
    fetch('login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Login response:', data);
        if (data.success) {
            // Handle successful login
            alert(data.message); // Show success message
            location.reload(); // Reload the page or redirect
        } else {
            // Show error message
            document.getElementById('loginError').textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loginError').textContent = 'An error occurred. Please try again.';
    });
});

// Handle signup form submission
document.getElementById('signupForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission
    const formData = new FormData(this);
    
    fetch('signup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Handle successful signup
            alert(data.message); // Show success message
            switchAuth('login'); // Switch to login form
        } else {
            // Show error message
            document.getElementById('signupError').textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('signupError').textContent = 'An error occurred. Please try again.';
    });
});

// Show the admin authentication modal
function showAdminAuth() {
    const adminAuthModal = document.getElementById('adminAuthModal');
    adminAuthModal.style.display = 'block';
}

// Close the admin authentication modal
function closeAdminAuth() {
    const adminAuthModal = document.getElementById('adminAuthModal');
    adminAuthModal.style.display = 'none';
}

// Handle admin login form submission
document.getElementById('adminLoginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    
    console.log('Attempting admin login with:', Object.fromEntries(formData));
    
    fetch('admin_login.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Admin login response:', data);
        if (data.success) {
            // Handle successful login
            alert(data.message);
            window.location.href = 'admin_dashboard.php';
        } else {
            // Show error message
            document.getElementById('adminLoginError').textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('adminLoginError').textContent = 'An error occurred. Please try again.';
    });
});

</script>
</BODY>
</html>