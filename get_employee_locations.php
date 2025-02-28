<?php
// get_employee_locations.php

// Assuming you have established a connection to the database
$conn = new mysqli("localhost", "root", "", "gps_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the latest employee locations
$query = "
    SELECT u.name, u.email, t.latitude, t.longitude 
    FROM users u
    LEFT JOIN tracking t ON u.id = t.user_id
    WHERE u.role = 'employee'
    ORDER BY t.timestamp DESC
";

$result = $conn->query($query);

if ($result) {
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    echo json_encode($employees); // Return data as JSON
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
