<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');    // Default XAMPP username
define('DB_PASS', '');        // Default XAMPP password (empty)
define('DB_NAME', 'ecowaste_db');

function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>