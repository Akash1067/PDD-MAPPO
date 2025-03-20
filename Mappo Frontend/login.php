<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'DBconfig.php'; // Include your database config

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;

    if (!empty($input['mobile_number']) && !empty($input['password'])) {
        $mobile_number = $mysqli->real_escape_string(trim($input['mobile_number']));
        $password = trim($input['password']);

        // Check if user exists
        $query = "SELECT id, username, mobile_number, password FROM signup WHERE mobile_number = ?";
        $stmt = $mysqli->prepare($query);

        if (!$stmt) {
            echo json_encode(["status" => false, "message" => "Database error: " . $mysqli->error]);
            exit;
        }

        $stmt->bind_param("s", $mobile_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Directly compare passwords (No hashing since you removed restrictions)
            if ($password === $user['password']) {
                echo json_encode([
                    "status" => true,
                    "message" => "Login successful",
                    "data" => [
                        "id" => $user['id'],
                        "username" => $user['username'],
                        "mobile_number" => $user['mobile_number']
                    ]
                ]);
            } else {
                echo json_encode(["status" => false, "message" => "Incorrect password"]);
            }
        } else {
            echo json_encode(["status" => false, "message" => "Mobile number not registered"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => false, "message" => "Mobile Number and Password are required."]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method"]);
}

$mysqli->close();
?>
