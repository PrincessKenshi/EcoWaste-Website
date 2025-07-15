<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['verify_email']) || empty($_SESSION['verify_email'])) {
    header('Location: signup.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verificationCode = trim($_POST['verification_code']);
    $email = $_SESSION['verify_email'];
    
    // Check if code matches
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND verification_code = ?");
    $stmt->execute([$email, $verificationCode]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Mark user as verified
        $stmt = $pdo->prepare("UPDATE users SET verified = 1 WHERE email = ?");
        $stmt->execute([$email]);
        
        // Log user in
        $_SESSION['user_id'] = $user['id'];
        unset($_SESSION['verify_email']);
        
        header('Location: dashboard.php');
        exit();
    } else {
        header('Location: verify.php?error=invalid_code');
        exit();
    }
} else {
    header('Location: verify.php');
    exit();
}
?>