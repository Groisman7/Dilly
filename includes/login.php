<?php
session_set_cookie_params(86400); // יום אחד
session_start();

// פרטי התחברות למסד הנתונים
$host = "localhost";
$user = "emilybl_oly";
$pass = "Null1999!@";
$db = "emilybl_DB_Dilly";

// התחברות למסד
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// בדיקה אם הטופס נשלח
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // קבלת שם משתמש וסיסמה מהטופס
    $username = $_POST['username'];
    $password = $_POST['password'];

    // חיפוש המשתמש במסד
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // בדיקה אם נמצא משתמש
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // בדיקת סיסמה
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $message = "Welcome back, $username!";
            $message_class = "success";
            header("refresh:1.5;url=../index.php"); // מעבר לעמוד הבית אחרי 2 שניות
        } else {
            $message = "Incorrect password!";
            $message_class = "error";
        }
    } else {
        $message = "Username not found!";
        $message_class = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dilly Log In</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        /* עיצוב הודעת הצלחה */
        .success {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        /* עיצוב הודעת שגיאה */
        .error {
            background-color: #f44336;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <img src="../images/dillySmallLogo.png" alt="Dilly Voucher Hub" class="logo">
    
    <div class="form-container">
        <h1>Log In</h1>

        <!-- הצגת הודעת הצלחה או שגיאה -->
        <?php if (isset($message)): ?>
            <div class="<?php echo $message_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form id="signin-form" action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Log In</button>
        </form>
        <div class="links">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            <p><a href="../index.php">Back to Home</a></p>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>
</html>
