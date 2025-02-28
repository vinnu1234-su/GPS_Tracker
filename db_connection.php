<?php
$host = "localhost";  // Change if using a different host
$user = "root";       // Change if your database has a username
$pass = "";           // Change if your database has a password
$db_name = "gps_tracker"; // Make sure this database exists

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
