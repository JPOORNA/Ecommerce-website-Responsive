<?php
session_start();
include '../includes/db_config.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form data
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validate inputs
    if (empty($name) || empty($phone) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect back with an error (handle display on index.php as needed)
        header("Location: index.php?error=invalid_input");
        exit();
    }
    
    // --- Save to Database ---
    $stmt = $conn->prepare("INSERT INTO subscriptions (name, phone, email, created_at) VALUES (:name, :phone, :email, NOW())");
    $stmt->bindValue(':name', $name);
    $stmt->bindValue(':phone', $phone);
    $stmt->bindValue(':email', $email);
    $stmt->execute();
    
    // --- Send Email ---
    $to      = 'poorna.inbox.610@gmail.com';
    $subject = 'New Subscription/Contact Details';
    $message = "You have received a new subscription/contact:\n\n";
    $message .= "Name: $name\n";
    $message .= "Phone: $phone\n";
    $message .= "Email: $email\n";
    
    // Set a valid sender email address for your domain
    $headers = "From: no-reply@shopease.com\r\n";
    
    // Send the email (error handling can be enhanced if needed)
    mail($to, $subject, $message, $headers);
    
    // Redirect to homepage with a success flag
    header("Location: index.php?subscribed=1");
    exit();
} else {
    // If not a POST request, redirect to homepage
    header("Location: index.php");
    exit();
}
?>
