<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gps_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); // If not logged in as admin, redirect to login
    exit();
}

$query = "SELECT * FROM users";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | GPS Tracker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
            margin-bottom: 20px;
        }

        nav {
            background-color: #333;
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        nav a {
            display: inline-block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 16px;
            width: 150px;
            text-align: center;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        .container {
            width: 80%;
            max-width: 1000px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table td {
            background-color: #f9f9f9;
        }

        .button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
        }

        .button:hover {
            background-color: #45a049;
        }

        .welcome {
            font-size: 24px;
            color: #333;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <header>
        <h2>Admin Dashboard</h2>
    </header>

    <nav>
        <a href="employee_details.php">Employee Details</a>
        <a href="edit_profiles.php">Edit Profiles</a>
        <a href="add_new_profile.php">Add New Profile</a>
        <a href="view_employees.php">View Employees</a>
		<a href="view_attendance.php">View Attendance</a>
		<a href="admin_photos.php">View Photos</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="container">
        <div class="welcome">
            <h2>Welcome, Admin</h2>
        </div>
    </div>

</body>
</html>
