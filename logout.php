<?php
require_once 'config.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $conn = getDBConnection();
    
    // Delete session from database
    $sessionId = session_id();
    $stmt = $conn->prepare("DELETE FROM user_sessions WHERE session_id = ?");
    $stmt->bind_param("s", $sessionId);
    $stmt->execute();
    $stmt->close();
    
    closeDBConnection($conn);
    
    // Clear session data
    $_SESSION = array();
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

header("Location: login.html");
exit();
?>