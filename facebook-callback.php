<?php
require_once 'config.php';
require_once 'db.php';

// Initialize Facebook SDK
$fb = new Facebook\Facebook([
    'app_id' => FACEBOOK_APP_ID,
    'app_secret' => FACEBOOK_APP_SECRET,
    'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
    $response = $fb->get('/me?fields=id,name,email', $accessToken);
    $userInfo = $response->getGraphUser();
    
    // Check if user exists in database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND provider = 'facebook'");
    $stmt->execute([$userInfo->getEmail()]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Generate verification code
        $verificationCode = rand(100000, 999999);
        
        // Split name into first and last
        $nameParts = explode(' ', $userInfo->getName(), 2);
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? $nameParts[1] : '';
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (email, first_name, last_name, provider, verification_code, verified) 
                              VALUES (?, ?, ?, 'facebook', ?, 0)");
        $stmt->execute([
            $userInfo->getEmail(),
            $firstName,
            $lastName,
            $verificationCode
        ]);
        
        // Send verification email
        sendVerificationEmail($userInfo->getEmail(), $verificationCode);
        
        // Store user email in session for verification page
        $_SESSION['verify_email'] = $userInfo->getEmail();
        header('Location: verify.php');
        exit();
    } else if ($user['verified'] == 0) {
        // User exists but not verified
        $_SESSION['verify_email'] = $userInfo->getEmail();
        header('Location: verify.php');
        exit();
    } else {
        // User exists and is verified - log them in
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
    }
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // Handle error
    header('Location: signup.php?error=facebook_auth_failed');
    exit();
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // Handle error
    header('Location: signup.php?error=facebook_auth_failed');
    exit();
}

function sendVerificationEmail($email, $code) {
    $subject = "EcoWaste - Verify Your Email";
    $message = "Your verification code is: $code\n\nEnter this code on the verification page to complete your signup.";
    $headers = "From: no-reply@ecowaste.com";
    
    mail($email, $subject, $message, $headers);
}
?>