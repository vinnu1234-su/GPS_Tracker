<?php
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $latitude = $_POST['lat'];
    $longitude = $_POST['lng'];
    $action = $_POST['action']; // Get action type (Start or Stop)

    if (!in_array($action, ['Start', 'Stop'])) {
        die("Invalid action!");
    }

    // Insert tracking data with action type
    $sql = "INSERT INTO tracking (user_id, latitude, longitude, timestamp, action) 
            VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idds", $user_id, $latitude, $longitude, $action);

    if ($stmt->execute()) {
        echo "Location saved with action: $action";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
