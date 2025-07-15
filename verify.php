<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - EcoWaste</title>
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="logo-image">
            <a href="landingpage.html">
                <img src="images/ecowaste_logo.png" alt="EcoWaste Logo" class="logo-img">
            </a>
        </div>
    </header>
    
    <div class="signup-container">
        <div class="logo">
            <i class="fas fa-recycle"></i>
        </div>
        
        <h1>Verify Your Email</h1>
        <p class="verify-instructions">We've sent a 6-digit verification code to your email address. Please enter it below to complete your signup.</p>
        
        <form id="verify-form" action="verify-process.php" method="post">
            <div class="form-group">
                <label for="verification-code">Verification Code</label>
                <input type="text" id="verification-code" name="verification_code" required maxlength="6">
            </div>
            
            <button type="submit" class="signup-btn">Verify Account</button>
            
            <div class="resend-code">
                <p>Didn't receive a code? <a href="resend-code.php">Resend code</a></p>
            </div>
        </form>
    </div>
</body>
</html>