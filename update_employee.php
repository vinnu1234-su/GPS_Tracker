<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gps_tracker");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $conn->real_escape_string($_POST['user_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);

    // Update user details
    $query = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$user_id'";
    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Employee profile updated successfully!'); window.location='edit_profiles.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
