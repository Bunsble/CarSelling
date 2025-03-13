<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Get form data
$name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
$phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
$message = isset($_POST['message']) ? mysqli_real_escape_string($conn, $_POST['message']) : '';
$car_id = isset($_POST['car_id']) ? intval($_POST['car_id']) : 0;
$seller_id = isset($_POST['seller_id']) ? intval($_POST['seller_id']) : 0;

// Validate required fields
if(empty($name) || empty($email) || empty($message) || $car_id === 0 || $seller_id === 0) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
    exit;
}

// Validate email format
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Get car details
$car_sql = "SELECT title FROM cars WHERE id = ?";
if($stmt = mysqli_prepare($conn, $car_sql)){
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    mysqli_stmt_execute($stmt);
    $car_result = mysqli_stmt_get_result($stmt);
    $car = mysqli_fetch_assoc($car_result);
    mysqli_stmt_close($stmt);
}

// Get seller details
$seller_sql = "SELECT email FROM users WHERE id = ?";
if($stmt = mysqli_prepare($conn, $seller_sql)){
    mysqli_stmt_bind_param($stmt, "i", $seller_id);
    mysqli_stmt_execute($stmt);
    $seller_result = mysqli_stmt_get_result($stmt);
    $seller = mysqli_fetch_assoc($seller_result);
    mysqli_stmt_close($stmt);
}

// Insert message into database
$sql = "INSERT INTO seller_messages (car_id, seller_id, name, email, phone, message) VALUES (?, ?, ?, ?, ?, ?)";
if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "iissss", $car_id, $seller_id, $name, $email, $phone, $message);
    
    if(mysqli_stmt_execute($stmt)){
        // Send email to seller
        $to = $seller['email'];
        $subject = "New inquiry about your car: " . $car['title'];
        $email_message = "You have received a new message about your car listing:\n\n";
        $email_message .= "From: " . $name . "\n";
        $email_message .= "Email: " . $email . "\n";
        if(!empty($phone)) {
            $email_message .= "Phone: " . $phone . "\n";
        }
        $email_message .= "\nMessage:\n" . $message;
        
        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        
        mail($to, $subject, $email_message, $headers);
        
        echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error sending message']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Error preparing message']);
}
?> 