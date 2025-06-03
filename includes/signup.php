<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $user = "emilybl_oly";
    $pass = "Null1999!@";
    $db = "emilybl_DB_Dilly";

    // חיבור למסד נתונים
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // קבלת נתונים מהטופס
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['c_password'];  // סיסמת אימות

    // בדיקת אם הסיסמאות תואמות
    if ($password !== $confirm_password) {
        echo "<script>
                alert('Passwords do not match.');
                window.history.back();
              </script>";
        exit();
    }

    // חישוב הסיסמה המוצפנת
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // בדיקת קיום שם משתמש
    $check_sql = "SELECT * FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>
                alert('Username already exists. Please choose a different one.');
                window.history.back();
              </script>";
        exit();
    }

    // הכנסת משתמש חדש
    $sql = "INSERT INTO users (username, full_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $full_name, $email, $password_hash);

    if ($stmt->execute()) {
        echo "<script>
                alert('The account \"$username\" is registered!');
                window.location.href = 'login.php';
              </script>";
    } else {
        echo "<script>
                alert('ERROR: " . addslashes($stmt->error) . "');
                window.history.back();
              </script>";
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dilly Sign Up</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>
<body>
    <img src="../images/dillySmallLogo.png" alt="Dilly Voucher Hub" class="logo">
    
    <div class="form-container">
        <h1>Sign Up</h1>
        <form id="signup-form" action="signup.php" method="POST">
            <div class="form-group">
                <label for="username">User Name</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="c_password">Confirm Password</label>
                <input type="password" id="c_password" name="c_password" required>
            </div>
            <button type="submit">Sign Up</button>
        </form>
        <div class="links">
            <p>Already have an account? <a href="login.php">Log In</a></p>
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </div>

    <script src="../js/signup.js"></script>
</body>
</html>
