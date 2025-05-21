<?php
require_once 'database_connection.php';

try {
    $stmt = $conn->query("SELECT id, full_name, email FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Users in database:\n";
    if (count($users) > 0) {
        foreach ($users as $user) {
            echo "ID: " . $user['id'] . ", Name: " . $user['full_name'] . ", Email: " . $user['email'] . "\n";
        }
    } else {
        echo "No users found in the database.\n";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 