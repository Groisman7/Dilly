<?php
session_start();
$username = $_SESSION['username'] ?? null;
$logoutMessage = $_SESSION['logout_message'] ?? null;
if ($logoutMessage) {
    unset($_SESSION['logout_message']);
}

function getAuthenticatedUrl($targetUrl, $loginUrl = "includes/login.php") {
    global $username;
    return $username ? $targetUrl : $loginUrl;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dilly Home Page</title>
    <link rel="stylesheet" href="css/home.css">
    <style>
        /* עיצוב הודעת אימות */
        .auth-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #f44336;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 9999;
            font-size: 16px;
            animation: fadeOut 3s forwards;
        }

        /* עיצוב הודעת יציאה */
        .logout-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 9999;
            font-size: 16px;
            animation: fadeOut 3s forwards;
        }

        @keyframes fadeOut {
            0% {opacity: 1;}
            70% {opacity: 1;}
            100% {opacity: 0; display: none;}
        }
    </style>
    <script>
        function checkAuthAndRedirect(targetUrl, event) {
            <?php if (!$username): ?>
                event.preventDefault();
                showAuthMessage('You need to log in to access this page.');
                setTimeout(function() {
                    window.location.href = 'includes/login.php';
                }, 3000);
            <?php endif; ?>
        }

        function showAuthMessage(message) {
            const oldMessage = document.querySelector('.auth-message');
            if (oldMessage) oldMessage.remove();

            const messageDiv = document.createElement('div');
            messageDiv.className = 'auth-message';
            messageDiv.textContent = message;
            document.body.appendChild(messageDiv);
        }
    </script>
</head>
<body>

<nav>
    <div class="container nav-content auth-buttons">
        <?php if ($logoutMessage): ?>
            <div class="logout-message">
                <?php echo htmlspecialchars($logoutMessage); ?>
            </div> 
        <?php endif; ?>

        <?php if ($username): ?>
            <span>Hello, <?php echo htmlspecialchars($username); ?>!</span>
        <?php else: ?>
            <div class="logSign">
                <a href="includes/login.php" class="login-btn">Log In</a>
                <a href="includes/signup.php" class="signup-btn">Sign Up</a>
            </div>
        <?php endif; ?>

        <div class="nav-links">
            <a href="index.php" class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline></svg> Home</a>
            <a href="<?php echo getAuthenticatedUrl('includes/dillyZone.php'); ?>" class="nav-link" onclick="checkAuthAndRedirect('includes/dillyZone.php', event)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"></path>
                      <path d="M3 6h18"></path>
                      <path d="M16 10a4 4 0 0 1-8 0"></path></svg>  DillyZone</a>
            <a href="<?php echo getAuthenticatedUrl('includes/profile.php'); ?>" class="nav-link" onclick="checkAuthAndRedirect('includes/profile.php', event)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                      <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle></svg> Profile</a>
            <?php if ($username): ?>
                     <a href="includes/logout.php" class="nav-link logout-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                          <polyline points="16 17 21 12 16 7"></polyline>
                          <line x1="21" y1="12" x2="9" y2="12"></line> </svg> Log Out </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<header class="hero container">
    <img src="images/dillyBigLogo.png" alt="Dilly Voucher Hub" class="bigLogo">
    <h1>Manage Your Vouchers Smartly</h1>
    <p>Add, track, and trade vouchers with ease. Never miss an expiration again.</p>
</header>

<section class="features container">
    <div class="feature-card">
        <img src="images/coupon.gif" width="20%"> 
        <h3>Track Vouchers</h3>
        <p>Keep all your vouchers in one place. See expiration dates and details instantly.</p>
        <div class="feature-stats">
            <p>Active Vouchers: <span class="active-count">12</span></p>
            <p>Next Expiring: <span class="expiring-date">Apr 30, 2025</span></p>
        </div>
        <a href="<?php echo getAuthenticatedUrl('includes/profile.php'); ?>" class="feature-button view-vouchers" onclick="checkAuthAndRedirect('includes/profile.php', event)">
            View all my vouchers 
        </a>
    </div>

    <div class="feature-card">
        <img src="images/trading.gif" width="20%"> 
        <h3>Trade Vouchers</h3>
        <p>Buy, sell, and exchange vouchers securely with other users.</p>
        <div class="feature-stats">
            <p>Active Users: <span class="users-count">1.2K</span></p>
            <p>Popular: <span class="popular-voucher">50% Off at Store XYZ</span></p>
        </div>
        <a href="<?php echo getAuthenticatedUrl('includes/dillyZone.php'); ?>" class="feature-button start-exchange" onclick="checkAuthAndRedirect('includes/dillyZone.php', event)">
            Start exchange
        </a>
    </div>

    <div class="feature-card">
        <img src="images/notification.gif" width="20%"> 
        <h3>Get Notifications</h3>
        <p>Receive smart alerts before your vouchers expire.</p>
        <div class="feature-stats">
            <p>Recommended: <span class="alert-recommendation">7 days before expiry</span></p>
            <p class="stats-highlight">Users with alerts save 85% more!</p>
        </div>
        <a href="<?php echo getAuthenticatedUrl('includes/notifications.html'); ?>" class="feature-button setup-alerts" onclick="checkAuthAndRedirect('includes/notifications.html', event)">
            Set up personal alerts
        </a>
    </div>
</section>

<section class="video-section">
    <h2>How Dilly can help you?</h2>
    <div class="video-wrapper">
        <video width="560" height="315" controls>
            <source src="images/dillyVideo.mp4" type="video/mp4">
        </video>
    </div>
</section>

<section class="cta">
    <h2>Ready to Manage Your Vouchers?</h2>
    <p>Sign up now and take control of your voucher collection.</p>
    <a href="includes/signup.php"><button>Get Started</button></a>
</section>

<footer>
    <p>© 2025 Dilly. All rights reserved.</p>
</footer>

<script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
<script src="js/home.js"></script>

</body>
</html>
