<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecowaste');
define('DB_USER', 'root');
define('DB_PASS', '');

// Google OAuth credentials
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI', 'http://yourdomain.com/google-callback.php');

// Facebook OAuth credentials
define('FACEBOOK_APP_ID', 'YOUR_FACEBOOK_APP_ID');
define('FACEBOOK_APP_SECRET', 'YOUR_FACEBOOK_APP_SECRET');
define('FACEBOOK_REDIRECT_URI', 'http://yourdomain.com/facebook-callback.php');

// Initialize database connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Include Google and Facebook SDKs
require_once 'vendor/autoload.php';
?>