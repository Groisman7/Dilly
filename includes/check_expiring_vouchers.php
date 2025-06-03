<?php
// חיבור לבסיס הנתונים
$pdo = new PDO('mysql:host=localhost;dbname=emilybl_DB_Dilly;charset=utf8', 'emilybl_oly', 'Null1999!@');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// מפתח ה-API של SendGrid
$apiKey = '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';
$url = 'https://api.sendgrid.com/v3/mail/send';

// שליפת כל המיילים שיש להם marketplace_updates = 1 וכתובת אימייל לא ריקה
$stmt = $pdo->prepare("SELECT email FROM notification_preferences WHERE marketplace_updates = 1 AND email IS NOT NULL");
$stmt->execute();
$emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

// פונקציה לשליחת מייל
function sendEmail($apiKey, $url, $toEmail, $subject, $contentText) {
    $emailData = [
        "personalizations" => [
            [
                "to" => [["email" => $toEmail]],
                "subject" => $subject
            ]
        ],
        "from" => ["email" => "Groisman98@gmail.com", "name" => "Dilly"],
        "content" => [
            ["type" => "text/plain", "value" => $contentText]
        ]
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
        echo "Email sent successfully to $toEmail\n";
    } else {
        echo "Failed to send email to $toEmail. Response: $response\n";
    }
}

// שליחת מיילים
foreach ($emails as $email) {
    sendEmail($apiKey, $url, $email, "Test Immediate Email", "This is a test email sent immediately via SendGrid.");
}
?>
