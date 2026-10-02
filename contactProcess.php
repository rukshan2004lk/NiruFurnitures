<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    require 'PHPMailer/Exception.php';
    require 'PHPMailer/PHPMailer.php';
    require 'PHPMailer/SMTP.php';
}

// 2. Collect and sanitize input
$name    = htmlspecialchars(trim($_POST['userName'] ?? ''));
$email   = filter_var(trim($_POST['userEmail'] ?? ''), FILTER_SANITIZE_EMAIL);
$subject = htmlspecialchars(trim($_POST['subjectSelect'] ?? 'Inquiry'));
$message = nl2br(htmlspecialchars(trim($_POST['userMessage'] ?? '')));

if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields properly.']);
    exit;
}

$mail = new PHPMailer(true);

try {
    // 3. SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sandeesharukshan321@gmail.com';
    $mail->Password   = 'lqpvunpdrjoemjim'; // 16-char Google App Password (no spaces)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    // 4. Recipients
    $mail->setFrom('sandeesharukshan321@gmail.com', 'NiRu Website Contact');
    $mail->addAddress('sandeesharukshan321@gmail.com', 'NiRu Furnitures'); // Modified to the address you requested
    $mail->addReplyTo($email, $name);                            // Hitting "Reply" replies to the customer

    // 5. Content
    $mail->isHTML(true);
    $mail->Subject = "New Website Inquiry: " . $subject;
    $mail->Body    = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <h2 style='color: #8b5e3c;'>New Contact Form Submission</h2>
            <hr>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> <a href='mailto:{$email}'>{$email}</a></p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong></p>
            <div style='background-color: #f9f9f9; padding: 15px; border-radius: 6px; border: 1px solid #eee;'>
                {$message}
            </div>
        </div>
    ";

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Thank you for contacting us! We will reply to your message soon.']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
}

