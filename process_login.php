<?php
session_start();
require_once 'config.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Invalid CSRF token';
    header('Location: login.php');
    exit();
}

// Validate inputs
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || empty($password)) {
    $_SESSION['error_message'] = 'Invalid email or password';
    header('Location: login.php');
    exit();
}

// Database check (example using PDO)
try {
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        // Successful login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Remember me functionality
        if (isset($_POST['remember'])) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + 60 * 60 * 24 * 30; // 30 days
            
            setcookie('remember_token', $token, $expiry, '/');
            
            // Store token in database
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
        }
        
        header('Location: homepage.php');
        exit();
    } else {
        $_SESSION['error_message'] = 'Invalid email or password';
        header('Location: login.php');
        exit();
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['error_message'] = 'Database error';
    header('Location: login.php');
    exit();
}
?>