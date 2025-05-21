<?php
require_once('config.php');

$conn = getDBConnection();
if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?> 