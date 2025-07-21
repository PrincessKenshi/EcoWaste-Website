<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        die("Email is required");
    }
    
    $conn = getDBConnection();
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("If this email exists in our system, we'll send a reset link");
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token in database
    $tokenStmt = $conn->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) 
                               VALUES (?, ?, ?)");
    $tokenStmt->bind_param("iss", $user['user_id'], $resetToken, $expiresAt);
    $tokenStmt->execute();
    $tokenStmt->close();
    
    closeDBConnection($conn);
    
    // TODO: Send password reset email
    $resetLink = "https://yourapp.com/reset_password_form.php?token=$resetToken";
    
    // For testing, you can output the link
    echo "Password reset link: $resetLink";
} else {
    header("Location: forgot_password.html");
    exit();
}
?>