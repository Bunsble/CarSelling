<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get form data
$reply_to_email = isset($_POST['reply_to_email']) ? mysqli_real_escape_string($conn, $_POST['reply_to_email']) : '';
$reply_to_name = isset($_POST['reply_to_name']) ? mysqli_real_escape_string($conn, $_POST['reply_to_name']) : '';
$subject = isset($_POST['subject']) ? mysqli_real_escape_string($conn, $_POST['subject']) : '';
$message = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : '';

// Validate required fields
if(empty($reply_to_email) || empty($message) || empty($subject)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Validate email format
if(!filter_var($reply_to_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Prepare email
$to = $reply_to_email;
$from = $_SESSION['email'];
$headers = "From: " . $from . "\r\n";
$headers .= "Reply-To: " . $from . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send email
if(mail($to, $subject, $message, $headers)) {
    // Log the reply in the database
    $sql = "INSERT INTO message_replies (user_id, recipient_email, recipient_name, subject, message) VALUES (?, ?, ?, ?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)){
        mysqli_stmt_bind_param($stmt, "issss", $_SESSION['id'], $reply_to_email, $reply_to_name, $subject, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    echo json_encode(['success' => true, 'message' => 'Reply sent successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error sending reply']);
}
?> 