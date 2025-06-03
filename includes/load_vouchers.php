<?php
session_set_cookie_params(86400); // יום אחד
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username'];

// התחברות למסד נתונים
$host = "localhost";
$user = "emilybl_oly";
$pass = "Null1999!@";
$db = "emilybl_DB_Dilly";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

// בדיקה אם רוצים רק שוברים למכירה
$onlySellable = isset($_GET['onlySellable']) && $_GET['onlySellable'] == '1';
// בדיקה אם רוצים להחריג את המשתמש הנוכחי
$excludeSelf = isset($_GET['excludeSelf']) && $_GET['excludeSelf'] == '1';

$sql = "SELECT id, code, businessName, businessLogo, balance, expiryDate, sellable, username FROM user_vouchers WHERE 1";
$params = [];
$types = "";

// תנאי: שוברים למכירה
if ($onlySellable) {
    $sql .= " AND sellable = 1";
}

// תנאי: להחריג את המשתמש הנוכחי
if ($excludeSelf) {
    $sql .= " AND username != ?";
    $params[] = $username;
    $types .= "s";
}

// תנאי: להחזיר רק את השוברים של המשתמש (אם excludeSelf לא סומן)
if (!$excludeSelf) {
    $sql .= " AND username = ?";
    $params[] = $username;
    $types .= "s";
}

// הכנה וביצוע
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// שליחה חזרה ללקוח
$vouchers = [];
while ($row = $result->fetch_assoc()) {
    $vouchers[] = $row;
}

echo json_encode(['success' => true, 'vouchers' => $vouchers]);
$conn->close();
?>
