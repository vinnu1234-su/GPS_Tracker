<?php
// update_location.php

// Assuming you have established a connection to the database
$conn = new mysqli("localhost", "root", "", "gps_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from the POST request
$user_id = $_POST['user_id'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

// Insert or update the employee's location in the tracking table
$query = "INSERT INTO tracking (user_id, latitude, longitude) 
          VALUES ('$user_id', '$latitude', '$longitude')
          ON DUPLICATE KEY UPDATE latitude='$latitude', longitude='$longitude'";

if ($conn->query($query) === TRUE) {
    echo "Location updated successfully!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
