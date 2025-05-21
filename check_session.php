<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $loggedIn = true;
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : "Guest";
    $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : "No email available";
} else {
    $loggedIn = false;
}
?>
