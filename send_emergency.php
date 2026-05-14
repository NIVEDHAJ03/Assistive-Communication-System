<?php
session_start();
include "db.php";

/* PHPMailer includes */
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if(!isset($_SESSION['user_id'])){
    die("Not logged in");
}

$current_user = $_SESSION['user_id'];

/* ===============================
   GET LOCATION FROM FORM
=============================== */
$latitude  = $_POST['latitude']  ?? '';
$longitude = $_POST['longitude'] ?? '';

/* ===============================
   AI PRIORITY CHECK
=============================== */
$check = $conn->query("
SELECT COUNT(*) as total
FROM emergency_alerts
WHERE user_id='$current_user'
AND created_at >= NOW() - INTERVAL 10 MINUTE
");

$count = $check->fetch_assoc()['total'];
$priority = ($count > 2) ? "High Priority" : "Normal";

/* ===============================
   INSERT EMERGENCY (WITH LOCATION)
=============================== */
$conn->query("
INSERT INTO emergency_alerts 
(user_id, type, status, latitude, longitude, created_at)
VALUES 
('$current_user', '$priority', 'Pending', '$latitude', '$longitude', NOW())
");

/* ===============================
   SEND MESSAGE TO ADMIN
=============================== */
$admin = $conn->query("SELECT id FROM users WHERE role='admin' LIMIT 1")->fetch_assoc();
$admin_id = $admin['id'];

$msg = "Emergency Alert! User ID $current_user needs immediate help.";

$conn->query("
INSERT INTO messages (sender_id, receiver_id, message)
VALUES ('$current_user', '$admin_id', '$msg')
");

/* ===============================
   EMAIL SECTION
=============================== */
$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'dramauniverse143@gmail.com';
    $mail->Password   = 'fvtifgwxoddkirhz'; // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('dramauniverse143@gmail.com', 'Assistive System');
    $mail->addAddress('dramauniverse143@gmail.com');

    $mail->Subject = "Emergency Alert!";
    $mail->Body    = "User ID $current_user triggered emergency.\nPriority: $priority\nLocation: https://www.google.com/maps?q=$latitude,$longitude";

    $mail->send();

} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo;
}

/* ===============================
   SMS SECTION - TWILIO
=============================== */

$account_sid = "YOUR_SID";
$auth_token  = "YOUR_AUTH_TOKEN";
$twilio_number = "+12512200344";
$admin_phone   = "+919944894651";

$data = [
    'From' => $twilio_number,
    'To'   => $admin_phone,
    'Body' => "Emergency Alert! User $current_user - $priority - https://maps.google.com/?q=$latitude,$longitude"
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/$account_sid/Messages.json");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$account_sid:$auth_token");

$response = curl_exec($ch);

curl_close($ch);

/* ===============================
   REDIRECT
=============================== */
header("Location: dashboard.php");
exit();
?>