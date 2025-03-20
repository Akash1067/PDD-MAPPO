<?php
// Allow cross-origin requests (for development; restrict for production)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Include database configuration
require_once 'DBconfig.php';

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form-data input
    $input = $_POST;

    if (
        !empty($input['username']) &&
        !empty($input['mobile_number']) &&
        !empty($input['password'])
    ) {
        $username = $mysqli->real_escape_string(trim($input['username']));
        $mobile_number = $mysqli->real_escape_string(trim($input['mobile_number']));
        $password = trim($input['password']);

        // Validate the mobile number (10-15 digits)
        if (!preg_match('/^[0-9]{10,15}$/', $mobile_number)) {
            echo json_encode([
                "status" => false,
                "message" => "Invalid mobile number. Please provide a 10-15 digit mobile number.",
                "data" => "Invalid mobile number"
            ]);
            exit;
        }

        // Check if the mobile number already exists
        $checkQuery = "SELECT id FROM signup WHERE mobile_number = '$mobile_number'";
        $checkResult = $mysqli->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            echo json_encode([
                "status" => false,
                "message" => "Mobile number already registered",
                "data" => "Duplicate entry"
            ]);
        } else {
            // Insert new user (plaintext password for now)
            $insertQuery = "INSERT INTO signup (username, mobile_number, password, created_at) VALUES ('$username', '$mobile_number', '$password', NOW())";
            if ($mysqli->query($insertQuery)) {
                echo json_encode([
                    "status" => true,
                    "message" => "Registration successful",
                    "data" => "User registered successfully"
                ]);
            } else {
                echo json_encode([
                    "status" => false,
                    "message" => "Failed to register user: " . $mysqli->error,
                    "data" => "Database error"
                ]);
            }
        }
    } else {
        echo json_encode([
            "status" => false,
            "message" => "All fields (username, mobile_number, password) are required",
            "data" => "Missing fields"
        ]);
    }
} else {
    echo json_encode([
        "status" => false,
        "message" => "Invalid request method",
        "data" => "Request not allowed"
    ]);
}

// Close the database connection
$mysqli->close();
?>
