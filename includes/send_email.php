<?php
// התחברות למסד הנתונים
$pdo = new PDO('mysql:host=localhost;dbname=emilybl_DB_Dilly;charset=utf8', 'emilybl_oly', 'Null1999!@');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// פרטי SendGrid
$apiKey = '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';
$url = 'https://api.sendgrid.com/v3/mail/send';

echo "=== התחלת שליחת מיילים ===\n\n";

// ============================================
// פונקציה 1: שליחת מיילים כלליים (marketplace_updates = 1)
// ============================================
echo "1. שליחת מיילים כלליים למשתמשים שהסכימו לקבל עדכונים...\n";

$templateId1 = 'd-facd6856014e4fc89c9107f50493d592'; // Template ID הקיים שלך

// שליפת משתמשים עם marketplace_updates = 1
$stmt = $pdo->prepare("
    SELECT email, username 
    FROM notification_preferences 
    WHERE email IS NOT NULL AND marketplace_updates = 1
");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $email = $user['email'];
    $username = $user['username'] ?? 'there';
    
    $emailData = [
        "from" => [
            "email" => "Groisman98@gmail.com",
            "name" => "Dilly"
        ],
        "personalizations" => [
            [
                "to" => [["email" => $email]],
                "dynamic_template_data" => [
                    "username" => $username
                ]
            ]
        ],
        "template_id" => $templateId1
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ מייל כללי נשלח ל-$email\n";
    } else {
        echo "❌ שגיאה בשליחת מייל כללי ל-$email. קוד: $httpCode. תגובה: $response\n";
    }
    
    sleep(1); // השהיה קטנה
}

echo "\nסיום שליחת מיילים כלליים\n\n";

// ============================================
// פונקציה 2: התראות פקיעת שוברים (voucher_expiry = 1)
// ============================================
echo "2. שליחת התראות פקיעת שוברים...\n";

$templateId2 = 'd-703e97d557a6420284fd58fb4643706b'; // Template ID לפקיעת שוברים

// חישוב תאריך - שבוע מהיום
$oneWeekFromNow = date('Y-m-d', strtotime('+7 days'));

// שליפת שוברים שפגים בעוד שבוע + פרטי המשתמש
$stmt = $pdo->prepare("
    SELECT 
        uv.CODE,
        uv.businessName,
        uv.businessLogo,
        uv.balance,
        uv.expiryDate,
        uv.username,
        np.email
    FROM user_vouchers uv
    JOIN notification_preferences np ON uv.username = np.username
    WHERE DATE(uv.expiryDate) = :expiryDate 
    AND np.email IS NOT NULL
    AND np.voucher_expiry = 1
");

$stmt->execute(['expiryDate' => $oneWeekFromNow]);
$expiringVouchers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "נמצאו " . count($expiringVouchers) . " שוברים שפגים בעוד שבוע\n";

foreach ($expiringVouchers as $voucher) {
    $email = $voucher['email'];
    $username = $voucher['username'] ?? 'שם לא זמין';
    
    $emailData = [
        "from" => [
            "email" => "Groisman98@gmail.com",
            "name" => "Dilly"
        ],
        "personalizations" => [
            [
                "to" => [["email" => $email]],
                "dynamic_template_data" => [
                    "username" => $username,
                    "voucher_code" => $voucher['CODE'],
                    "business_name" => $voucher['businessName'],
                    "business_logo" => $voucher['businessLogo'],
                    "balance" => $voucher['balance'],
                    "expiry_date" => date('d/m/Y', strtotime($voucher['expiryDate'])),
                    "days_left" => "7"
                ]
            ]
        ],
        "template_id" => $templateId2
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $apiKey",
        "Content-Type: application/json"
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode >= 200 && $httpCode < 300) {
        echo "✅ התראת פקיעה נשלחה ל-$email עבור שובר {$voucher['CODE']} של {$voucher['businessName']}\n";
    } else {
        echo "❌ שגיאה בשליחת התראת פקיעה ל-$email. קוד: $httpCode. תגובה: $response\n";
    }
    
    sleep(1); // השהיה קטנה
}

echo "\nסיום שליחת התראות פקיעה\n";
echo "\n=== סיום כל המיילים ===\n";
?>
