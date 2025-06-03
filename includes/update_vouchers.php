<?php
session_set_cookie_params(86400); // יום אחד
session_start();

$host = "localhost";
$user = "emilybl_oly";
$pass = "Null1999!@";
$db = "emilybl_DB_Dilly";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters(update_vouchers.php)']);
    exit;
}

$id = intval($data['id']);
$newUsername = $_SESSION['username'];
$sellable = intval($data['sellable']);

$stmt = $conn->prepare("UPDATE user_vouchers SET sellable = ?, username = ? WHERE id = ?");
$stmt->bind_param("isi", $sellable, $newUsername, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update voucher']);
}

$stmt->close();
$conn->close();
?>
