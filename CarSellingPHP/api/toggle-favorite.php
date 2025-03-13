<?php
session_start();
require_once '../config/database.php';

// Set header to JSON
header('Content-Type: application/json');

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$car_id = isset($data['car_id']) ? intval($data['car_id']) : 0;
$user_id = $_SESSION['id'];

if($car_id === 0){
    echo json_encode(['success' => false, 'message' => 'Invalid car ID']);
    exit;
}

// Check if car exists
$check_car = "SELECT id FROM cars WHERE id = ?";
if($stmt = mysqli_prepare($conn, $check_car)){
    mysqli_stmt_bind_param($stmt, "i", $car_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) === 0){
        echo json_encode(['success' => false, 'message' => 'Car not found']);
        mysqli_stmt_close($stmt);
        exit;
    }
    mysqli_stmt_close($stmt);
}

// Check if favorite already exists
$check_favorite = "SELECT id FROM favorites WHERE user_id = ? AND car_id = ?";
if($stmt = mysqli_prepare($conn, $check_favorite)){
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $car_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if(mysqli_stmt_num_rows($stmt) > 0){
        // Favorite exists, remove it
        $delete_sql = "DELETE FROM favorites WHERE user_id = ? AND car_id = ?";
        if($delete_stmt = mysqli_prepare($conn, $delete_sql)){
            mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $car_id);
            $success = mysqli_stmt_execute($delete_stmt);
            mysqli_stmt_close($delete_stmt);
            
            echo json_encode(['success' => $success, 'action' => 'removed']);
        }
    } else {
        // Favorite doesn't exist, add it
        $insert_sql = "INSERT INTO favorites (user_id, car_id) VALUES (?, ?)";
        if($insert_stmt = mysqli_prepare($conn, $insert_sql)){
            mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $car_id);
            $success = mysqli_stmt_execute($insert_stmt);
            mysqli_stmt_close($insert_stmt);
            
            echo json_encode(['success' => $success, 'action' => 'added']);
        }
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?> 