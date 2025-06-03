<?php
header('Content-Type: application/json');

$vouchersFile = 'buyme.json';

if (!file_exists($vouchersFile)) {
    echo json_encode(['success' => false, 'message' => 'Voucher database not found.']);
    exit;
}

$vouchersData = json_decode(file_get_contents($vouchersFile), true);

$input = json_decode(file_get_contents('php://input'), true);
$code = $_GET['code'] ?? $input['code'] ?? null;

if (!$code) {
    echo json_encode(['success' => false, 'message' => 'Missing voucher code.']);
    exit;
}

function &findVoucherByCode($code, &$vouchers) {
    foreach ($vouchers as &$voucher) {
        if ($voucher['code'] === $code) {
            return $voucher; // מחזיר רפרנס
        }
    }
    $null = null;
    return $null;
}

$voucher = &findVoucherByCode($code, $vouchersData);

if (!$voucher) {
    echo json_encode(['success' => false, 'message' => 'Voucher not found.']);
    exit;
}

if (!isset($voucher['businessName'], $voucher['businessLogo'], $voucher['balance'], $voucher['expiryDate'])) {
    echo json_encode(['success' => false, 'message' => 'Missing voucher data.']);
    exit;
}

if ($voucher['status'] !== 'available') {
    echo json_encode([
        'success' => false,
        'message' => 'Voucher is already redeemed !',
        'status' => $voucher['status']
    ]);
    exit;
}

// עדכון הסטטוס לשמור שהשובר כבר נוצל
$voucher['status'] = 'redeemed';

// שמירה חזרה לקובץ
file_put_contents($vouchersFile, json_encode($vouchersData, JSON_PRETTY_PRINT));

// שליחה מלאה של כל השדות הנחוצים
echo json_encode([
    'success' => true,
    'message' => 'Voucher validated and redeemed.',
    'code' => $code,
    'businessName' => $voucher['businessName'],
    'businessLogo' => $voucher['businessLogo'],
    'balance' => $voucher['balance'],
    'expiryDate' => $voucher['expiryDate']
]);
