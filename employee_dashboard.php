<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f4; }
        .navbar { background: #333; padding: 15px; text-align: center; }
        .navbar a { color: white; text-decoration: none; padding: 15px; font-size: 18px; }
        .navbar a:hover { background: #555; }
        .container { padding: 20px; text-align: center; }
        .content { background: white; padding: 20px; margin-top: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="profile.php">Profile</a>
        <a href="track.php">Track</a>
        <a href="history.php">History</a>
        <a href="attendance.php">Attendance</a>
		<a href="employee_photo.php">Uploads</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Welcome, </h2>
        <div class="content">
            <p>Select an option from the menu to continue.</p>
        </div>
    </div>

</body>
</html>
