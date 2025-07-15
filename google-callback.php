<?php
require_once 'config.php';
require_once 'db.php';

// Initialize Google Client
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);
    
    // Get user info
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();
    
    // Check if user exists in database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND provider = 'google'");
    $stmt->execute([$userInfo->email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Generate verification code
        $verificationCode = rand(100000, 999999);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (email, first_name, last_name, provider, verification_code, verified) 
                              VALUES (?, ?, ?, 'google', ?, 0)");
        $stmt->execute([
            $userInfo->email,
            $userInfo->givenName,
            $userInfo->familyName,
            $verificationCode
        ]);
        
        // Send verification email
        sendVerificationEmail($userInfo->email, $verificationCode);
        
        // Store user email in session for verification page
        $_SESSION['verify_email'] = $userInfo->email;
        header('Location: verify.php');
        exit();
    } else if ($user['verified'] == 0) {
        // User exists but not verified
        $_SESSION['verify_email'] = $userInfo->email;
        header('Location: verify.php');
        exit();
    } else {
        // User exists and is verified - log them in
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
    }
} else {
    // Handle error
    header('Location: signup.php?error=google_auth_failed');
    exit();
}

function sendVerificationEmail($email, $code) {
    $subject = "EcoWaste - Verify Your Email";
    $message = "Your verification code is: $code\n\nEnter this code on the verification page to complete your signup.";
    $headers = "From: no-reply@ecowaste.com";
    
    mail($email, $subject, $message, $headers);
}
?>