<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => true, 'redirect' => 'admin_dashboard.php']);
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'database_connection.php';
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    try {
        if (!$conn) {
            throw new Exception("Database connection failed");
        }
        
        $stmt = $conn->prepare("SELECT id, email, password FROM admins WHERE email = ?");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 1) {
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if password is hashed (starts with $2y$)
            if (substr($admin['password'], 0, 4) === '$2y$') {
                // For hashed passwords
                $passwordValid = password_verify($password, $admin['password']);
            } else {
                // For plain text passwords
                $passwordValid = ($password === $admin['password']);
                
                // Optionally update to hashed password
                if ($passwordValid) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    $updateStmt->execute([$hashedPassword, $admin['id']]);
                }
            }
            
            if ($passwordValid) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                echo json_encode(['success' => true, 'redirect' => 'admin_dashboard.php']);
                exit();
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        exit();
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit();
    }
}

// If not a POST request, return error
echo json_encode(['success' => false, 'message' => 'Invalid request method']);
exit(); 