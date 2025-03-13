<?php
session_start();
require_once 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $surname = mysqli_real_escape_string($conn, $_POST['surname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: index.php#contact");
        exit();
    }
    
    // Insert into database
    $sql = "INSERT INTO contact_messages (name, surname, email, subject, message) 
            VALUES (?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $surname, $email, $subject, $message);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Message sent successfully!";
    } else {
        $_SESSION['error'] = "Error sending message. Please try again.";
    }
    
    $stmt->close();
    header("Location: index.php#contact");
    exit();
}
?> 