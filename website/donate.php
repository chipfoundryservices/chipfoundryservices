<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '/opt/bitnami/nginx/html/vendor/autoload.php';

// Database connection
$host = 'localhost';
$dbname = 'chipfoundry';
$username = 'root';
$password = 'fOm7eS:DyRW0';

// Get visitor info
$visitor_ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
$visitor_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$timestamp = date('Y-m-d H:i:s');

// Save to database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("INSERT INTO donation_requests (ip_address, user_agent) VALUES (?, ?)");
    $stmt->execute([$visitor_ip, $visitor_agent]);
} catch (PDOException $e) {
    // Continue even if DB fails
}

// Send email via Gmail
$mail = new PHPMailer(true);

try {
    // Gmail SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'chipfoundryservices@gmail.com';
    $mail->Password   = 'ypdgoyswsbztbbfc';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('chipfoundryservices@gmail.com', 'Chip Foundry Services');
    $mail->addAddress('chipfoundryservices@gmail.com', 'Support');

    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Donation Inquiry - Chip Foundry Services';
    $mail->Body    = "A visitor has expressed interest in donating!\n\n";
    $mail->Body   .= "Timestamp: $timestamp\n";
    $mail->Body   .= "IP Address: $visitor_ip\n";
    $mail->Body   .= "Browser: $visitor_agent\n\n";
    $mail->Body   .= "Please follow up with payment details.";

    $mail->send();
    
    echo json_encode(['success' => true, 'message' => 'Thank you for your interest in donating! We have received your request and will contact you shortly with payment details.']);
    
} catch (Exception $e) {
    // Log error but still show success to user
    file_put_contents('/opt/bitnami/nginx/html/mail_errors.log', date('Y-m-d H:i:s') . " - " . $mail->ErrorInfo . "\n", FILE_APPEND);
    echo json_encode(['success' => true, 'message' => 'Thank you for your interest! We will contact you shortly with payment details.']);
}
?>
