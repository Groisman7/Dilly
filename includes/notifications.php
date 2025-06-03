<?php
session_start();

$host = "localhost";
$user = "emilybl_oly";
$pass = "Null1999!@";
$db = "emilybl_DB_Dilly";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// קבלת שם המשתמש מה-SESSION
$username = $_SESSION['username'] ?? null;
if (!$username) {
    header("Location: login.php");
    exit();
}

// שליפת האימייל של המשתמש מטבלת users
$getEmailSql = "SELECT email FROM users WHERE username = ?";
$stmt = $conn->prepare($getEmailSql);
$stmt->bind_param("s", $username);
$stmt->execute();
$emailResult = $stmt->get_result();
$email = null;
if ($row = $emailResult->fetch_assoc()) {
    $email = $row['email'];
}

// בדיקת שליחה של הטופס
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voucherExpiry = isset($_POST['voucherExpiry']) ? 1 : 0;
    $voucherSold = isset($_POST['voucherSold']) ? 1 : 0;
    $marketplaceUpdates = isset($_POST['marketplaceUpdates']) ? 1 : 0;

    // בדיקה אם יש ערך קיים
    $checkSql = "SELECT * FROM notification_preferences WHERE username = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // עדכון
        $updateSql = "UPDATE notification_preferences SET 
            voucher_expiry = ?, 
            voucher_sold = ?, 
            marketplace_updates = ?, 
            email = ?
            WHERE username = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("iiiss", $voucherExpiry, $voucherSold, $marketplaceUpdates, $email, $username);
    } else {
        // הכנסת ערך חדש
        $insertSql = "INSERT INTO notification_preferences 
            (username, voucher_expiry, voucher_sold, marketplace_updates, email)
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("siiiss", $username, $voucherExpiry, $voucherSold, $marketplaceUpdates, $email);
    }

    if ($stmt->execute()) {
        $message = "Preferences saved successfully!";
    } else {
        $message = "Error saving preferences: " . $conn->error;
    }
}

// שליפת ערכים קודמים (אם קיימים)
$preferences = [
    'voucher_expiry' => 1,
    'voucher_sold' => 1,
    'marketplace_updates' => 0,
];

$sql = "SELECT * FROM notification_preferences WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $preferences = $row;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notification Preferences</title>
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/notifications.css">
    <link rel="stylesheet" href="../css/profile.css">
</head>
<body>
    <img src="../images/dillySmallLogo.png" alt="Dilly Voucher Hub" class="logo">

<div class="min-h-screen bg-main text-primary font-sans">
    <div class="container mx-auto flex justify-between items-center">
        <div class="flex items-center">
            <a href="profile.php" class="back-button mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
                Back to Profile
            </a>
        </div>
    </div>

    <div class="container mx-auto py-8">
        <div class="notification-section">
            <h2 class="text-2xl font-semibold mb-6">Email Preferences</h2>

            <form id="notificationForm" method="POST" class="space-y-6">
                <div class="notification-option">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="voucherExpiry" <?= $preferences['voucher_expiry'] ? 'checked' : '' ?>>
                        <span>Voucher Expiry Alerts</span>
                    </label>
                    <p class="text-gray-600 ml-7">Get notified when your vouchers are about to expire in 7 days</p>
                </div>

                <div class="notification-option">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="voucherSold" <?= $preferences['voucher_sold'] ? 'checked' : '' ?>>
                        <span>Notification of a successfully sold voucher</span>
                    </label>
                    <p class="text-gray-600 ml-7">Get notified when your voucher is sold and the money is on its way</p>
                </div>

                <div class="notification-option">
                    <label class="flex items-center space-x-3">
                        <input type="checkbox" name="marketplaceUpdates" <?= $preferences['marketplace_updates'] ? 'checked' : '' ?>>
                        <span>Dilly Zone Updates</span>
                    </label>
                    <p class="text-gray-600 ml-7">Get notified about new vouchers in the DillyZone every day</p>
                </div>

                <!--<div class="email-settings">-->
                <!--    <label for="emailFrequency" class="block font-medium mb-2">Email Frequency</label>-->
                <!--    <select id="emailFrequency" name="emailFrequency" class="w-full p-2 border rounded">-->
                <!--        <option value="immediate" <?= $preferences['emailFrequency'] === 'immediate' ? 'selected' : '' ?>>Immediate</option>-->
                <!--        <option value="daily" <?= $preferences['emailFrequency'] === 'daily' ? 'selected' : '' ?>>Daily</option>-->
                <!--        <option value="weekly" <?= $preferences['emailFrequency'] === 'weekly' ? 'selected' : '' ?>>Weekly Summary</option>-->
                <!--    </select>-->
                <!--</div>-->

                <div class="form-actions">
                    <button type="submit" class="primary-button">Save Preferences</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-main py-8 text-center">
        <p class="text-primary-light">© 2025 Dilly. All rights reserved.</p>
    </footer>
</div>
</body>
</html>
