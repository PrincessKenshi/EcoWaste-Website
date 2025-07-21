<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $fullName = $_POST['full-name'] ?? '';
    $firstName = $_POST['first-name'] ?? '';
    $middleName = $_POST['middle-name'] ?? '';
    $lastName = $_POST['last-name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm-password'] ?? '';
    $contactNumber = $_POST['contact-number'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zipCode = $_POST['zip-code'] ?? '';
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || 
        empty($confirmPassword) || empty($contactNumber) || empty($address) || 
        empty($city) || empty($zipCode)) {
        die("All required fields must be filled");
    }
    
    if ($password !== $confirmPassword) {
        die("Passwords do not match");
    }
    
    if (strlen($password) < 8) {
        die("Password must be at least 8 characters long");
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Connect to database
    $conn = getDBConnection();
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        die("Email already registered");
    }
    $stmt->close();
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, first_name, middle_name, last_name, 
                          contact_number, address, city, zip_code) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $email, $passwordHash, $firstName, $middleName, $lastName, 
                     $contactNumber, $address, $city, $zipCode);
    
    if ($stmt->execute()) {
        // Return success response
        http_response_code(200);
        exit();
    } else {
        die("Error: " . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: signup.html");
    exit();
}
?>