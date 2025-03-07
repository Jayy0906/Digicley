<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = htmlspecialchars($_POST['first-name']);
    $email = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['msg']);

    // Validate the input
    if (!empty($name) && !empty($email) && !empty($message)) {
        $to = "info@digicley.com"; // Replace with your email address
        $subject = "New Contact Form Submission"; // Set a subject for the email
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $mail_subject = "New Contact Form Submission";
        $body = "Name: $name\nEmail: $email\nMessage:\n$message";

        // Send the email to your email address
        if (mail($to, $mail_subject, $body, $headers)) {
            // Auto-response email to the user
            $user_subject = "Thank You for Contacting Us!";
            $user_message = "Hi $name,\n\nThank you for reaching out to us. We have received your message:\n\n$message\n\nWe will get back to you shortly.\n\nBest Regards,\nYour Company Name";
            $user_headers = "From: info@digicley.com\r\n";

            // Send the thank you email to the user
            if (mail($email, $user_subject, $user_message, $user_headers)) {
                echo "Your message has been sent successfully! A confirmation email has also been sent to your address.";
            } else {
                echo "Message sent, but there was an error sending the confirmation email.";
            }
        } else {
            echo "There was an error sending your message. Please try again later.";
        }
    } else {
        echo "All fields are required. Please fill out the form completely.";
    }
} else {
    echo "Invalid request method.";
}
?>
