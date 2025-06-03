<?php
session_set_cookie_params(86400); // יום אחד
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$username = $_SESSION['username'];

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!$data || !isset($data['code'], $data['businessName'], $data['businessLogo'], $data['balance'], $data['expiryDate'])) {
    echo json_encode(['success' => false, 'message' => 'Missing voucher data']);
    exit;
}

$host = "localhost";
$user = "emilybl_oly";
$pass = "Null1999!@";
$db = "emilybl_DB_Dilly";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

$code = $data['code'];
$businessName = $data['businessName'];
$businessLogo = $data['businessLogo'];
$balance = $data['balance'];
$expiryDate = $data['expiryDate'];


$sql = "INSERT INTO user_vouchers (username, code, businessName, businessLogo, balance, expiryDate) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssds", $username, $code, $businessName, $businessLogo, $balance, $expiryDate);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$conn->close();
?>




