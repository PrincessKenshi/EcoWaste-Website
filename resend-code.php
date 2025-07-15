<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['verify_email']) || empty($_SESSION['verify_email'])) {
    header('Location: signup.php');
    exit();
}

// Generate new verification code
$newCode = rand(100000, 999999);
$email = $_SESSION['verify_email'];

// Update code in database
$stmt = $pdo->prepare("UPDATE users SET verification_code = ? WHERE email = ?");
$stmt->execute([$newCode, $email]);

// Send new verification email
$subject = "EcoWaste - New Verification Code";
$message = "Your new verification code is: $newCode\n\nEnter this code on the verification page to complete your signup.";
$headers = "From: no-reply@ecowaste.com";

mail($email, $subject, $message, $headers);

header('Location: verify.php?resend=success');
exit();
?>