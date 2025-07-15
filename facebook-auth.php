<?php
require_once 'config.php';

// Initialize Facebook SDK
$fb = new Facebook\Facebook([
    'app_id' => FACEBOOK_APP_ID,
    'app_secret' => FACEBOOK_APP_SECRET,
    'default_graph_version' => 'v12.0',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl(FACEBOOK_REDIRECT_URI, $permissions);

header('Location: ' . $loginUrl);
exit();
?>