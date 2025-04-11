<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Honeypot (should be empty)
    if (!empty($_POST['website'])) {
        die("Bot detected.");
    }

    // Math CAPTCHA (e.g., 3 + 5 = 8)
    if (trim($_POST['captcha']) !== '8') {
        die("Captcha failed. Please try again.");
    }

    // Clean input
    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = isset($_POST['subject']) ? htmlspecialchars(trim($_POST['subject'])) : 'Contact Form Message';
    $message = htmlspecialchars(trim($_POST['message']));

    // Simple spam filters
    if (preg_match('/http|https|tinyurl|bit\.ly/i', $message)) {
        die("⚠️ Links are not allowed in messages.");
    }
    if (preg_match('/[а-яА-ЯЁё]/u', $name)) {
        die("⚠️ Please use English characters only.");
    }

    if (!empty($name) && !empty($email) && !empty($message)) {
        $yourEmail = "info@digicley.com";         // ✅ Hostinger email
        $yourPassword = "Digicley@1231";          // 🔐 Your Hostinger email password

        // Send to you
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.hostinger.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $yourEmail;
            $mail->Password   = $yourPassword;
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom($yourEmail, 'Website Contact');
            $mail->addAddress($yourEmail);
            $mail->addReplyTo($email, $name);
            $mail->Subject = "New Contact Form: $subject";
            $mail->Body    = "Name: $name\nEmail: $email\nMessage:\n$message";
            $mail->send();

            // Auto-reply
            $reply = new PHPMailer(true);
            $reply->isSMTP();
            $reply->Host       = 'smtp.hostinger.com';
            $reply->SMTPAuth   = true;
            $reply->Username   = $yourEmail;
            $reply->Password   = $yourPassword;
            $reply->SMTPSecure = 'tls';
            $reply->Port       = 587;

            $reply->setFrom($yourEmail, 'Digicley Team');
            $reply->addAddress($email, $name);
            $reply->Subject = "Thanks for contacting us!";
            $reply->Body    = "Hi $name,\n\nThanks for your message! We'll get back to you shortly.\n\n- Digicley Team";

            $reply->send();

            echo "✅ Your message has been sent. A confirmation email was sent to you.";
        } catch (Exception $e) {
            echo "❌ Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "⚠️ All fields are required.";
    }
} else {
    echo "❌ Invalid request method.";
}
?>
