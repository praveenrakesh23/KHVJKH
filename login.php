<?php
session_start();
require_once 'database_connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the incoming request
error_log("Login attempt - POST data: " . print_r($_POST, true));

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    error_log("Processing login for email: " . $email);
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        error_log("Empty email or password");
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        exit;
    }
    
    try {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() == 0) {
            error_log("User not found for email: " . $email);
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }
        
        // Verify password
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("Found user: " . print_r($user, true));
        
        // Check if password is hashed (starts with $2y$)
        if (substr($user['password'], 0, 4) === '$2y$') {
            // For hashed passwords
            $passwordValid = password_verify($password, $user['password']);
            error_log("Password verification (hashed): " . ($passwordValid ? "true" : "false"));
        } else {
            // For plain text passwords (temporary solution)
            $passwordValid = ($password === $user['password']);
            error_log("Password verification (plain): " . ($passwordValid ? "true" : "false"));
            
            // Optionally update to hashed password
            if ($passwordValid) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$hashedPassword, $user['id']]);
            }
        }
        
        if ($passwordValid) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            
            error_log("Login successful for user: " . $user['email']);
            echo json_encode(['success' => true, 'message' => 'Login successful', 'user' => [
                'id' => $user['id'],
                'name' => $user['full_name'],
                'email' => $user['email']
            ]]);
        } else {
            error_log("Invalid password for user: " . $user['email']);
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
} else {
    // Not a POST request
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>