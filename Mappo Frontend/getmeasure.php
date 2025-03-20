<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

require 'dbconfig.php';

$response = [];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $query = "SELECT id, user_id, area, perimeter, unit, place FROM measurements ORDER BY id DESC";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $measurements = [];

        while ($row = $result->fetch_assoc()) {
            $measurements[] = [
                "id" => $row["id"],
                "user_id" => $row["user_id"],
                "area" => (float)$row["area"],
                "perimeter" => (float)$row["perimeter"],
                "unit" => $row["unit"],
                "place" => $row["place"]
            ];
        }

        $response = [
            "status" => true,
            "message" => "Measurements retrieved successfully",
            "data" => $measurements
        ];
    } else {
        $response = ["status" => false, "message" => "No measurements found", "data" => []];
    }
} else {
    $response = ["status" => false, "message" => "Invalid request method"];
}

$mysqli->close();

echo json_encode($response, JSON_PRETTY_PRINT);
?>
