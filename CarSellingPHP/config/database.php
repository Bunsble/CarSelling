<?php
define('DB_SERVER', 'h.xron.net');
define('DB_USERNAME', 'carselling');
define('DB_PASSWORD', 'g3V8wob9ZqVW1q4ajKoD');
define('DB_NAME', 'carselling');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?> 