<?php
define('DB_HOST', 'localhost'); // Change if needed
define('DB_USER', 'root'); // Your database username
define('DB_PASS', ''); // Your database password (empty for XAMPP by default)
define('DB_NAME', 'Mappo'); // Your database name

// Create connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die(json_encode([
        "status" => false,
        "message" => "Database connection failed: " . $mysqli->connect_error,
        "data" => []
    ]));
}
?>
