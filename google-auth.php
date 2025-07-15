<?php
require_once 'config.php';

// Initialize Google Client
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");
$client->addScope("profile");

// Generate authentication URL
$authUrl = $client->createAuthUrl();

// Redirect to Google's authentication page
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));
exit();
?>