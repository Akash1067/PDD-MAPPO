<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile_number = $_POST['mobile_number']; // Updated field name to match the column
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $response = []; // Initialize response array

    // Check if all fields are provided
    if (empty($mobile_number) || empty($new_password) || empty($confirm_password)) {
        $response = [
            "status" => false,
            "message" => "All fields are required.",
            "data" => ["error" => "Missing required fields"]
        ];
        echo json_encode($response);
        exit;
    }

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $response = [
            "status" => false,
            "message" => "Passwords do not match.",
            "data" => ["error" => "Password mismatch"]
        ];
        echo json_encode($response);
        exit;
    }

    // Include database configuration
    require 'dbconfig.php';

    // Prepare SQL to check if the mobile number exists
    $stmt = $mysqli->prepare("SELECT id FROM signup WHERE mobile_number = ?");
    if (!$stmt) {
        $response = [
            "status" => false,
            "message" => "Failed to prepare SQL statement.",
            "data" => ["error" => $mysqli->error]
        ];
        echo json_encode($response);
        exit;
    }

    // Bind and execute
    $stmt->bind_param("s", $mobile_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mobile number exists, prepare SQL to update the password
        $update_stmt = $mysqli->prepare("UPDATE signup SET password = ? WHERE mobile_number = ?");
        if (!$update_stmt) {
            $response = [
                "status" => false,
                "message" => "Failed to prepare update statement.",
                "data" => ["error" => $mysqli->error]
            ];
            echo json_encode($response);
            exit;
        }

        // Bind and execute the update statement
        $update_stmt->bind_param("ss", $new_password, $mobile_number);
        if ($update_stmt->execute()) {
            $response = [
                "status" => true,
                "message" => "Password updated successfully.",
                "data" => ["success" => "Password updated"]
            ];
        } else {
            $response = [
                "status" => false,
                "message" => "Failed to update password.",
                "data" => ["error" => "Error updating password"]
            ];
        }

        $update_stmt->close();
    } else {
        $response = [
            "status" => false,
            "message" => "Mobile number not found.",
            "data" => ["error" => "Mobile number not registered"]
        ];
    }

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();

    echo json_encode($response);
} else {
    $response = [
        "status" => false,
        "message" => "Invalid request method. Please use POST.",
        "data" => ["error" => "Invalid request method"]
    ];
    echo json_encode($response);
}
?>
