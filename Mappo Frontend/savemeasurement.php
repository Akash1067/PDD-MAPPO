<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

require 'dbconfig.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['user_id'], $_POST['area'], $_POST['perimeter'], $_POST['unit'], $_POST['place'])) {
        $user_id = $_POST['user_id'];
        $area = $_POST['area'];
        $perimeter = $_POST['perimeter'];
        $unit = $_POST['unit'];
        $place = $_POST['place'];

        // Insert measurement
        $stmt = $mysqli->prepare("INSERT INTO measurements (user_id, area, perimeter, unit, place) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iddss", $user_id, $area, $perimeter, $unit, $place);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => true,
                "message" => "Measurement saved successfully",
                "data" => [
                    "id" => $stmt->insert_id,
                    "user_id" => $user_id,
                    "area" => $area,
                    "perimeter" => $perimeter,
                    "unit" => $unit,
                    "place" => $place
                ]
            ]);
        } else {
            echo json_encode(["status" => false, "message" => "Failed to save measurement"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => false, "message" => "Missing required fields"]);
    }
} else {
    echo json_encode(["status" => false, "message" => "Invalid request method"]);
}
$mysqli->close();
?>
