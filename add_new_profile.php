<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gps_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php"); // Redirect if not logged in as admin
    exit();
}

// Handle form submission for adding a new employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Encrypt password
    $role = $_POST['role'];

    // Validate the inputs
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('Please fill in all fields.');</script>";
    } else {
        // Insert the new employee into the database
        $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Employee added successfully!');</script>";
            echo "<script>window.location = 'admin_dashboard.php';</script>";
        } else {
            echo "Error adding employee.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Employee Profile</title>
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
        }

        .container {
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="email"], input[type="password"], select, button {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add New Employee</h2>

        <!-- Form to add new employee -->
        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter employee's name" required><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter employee's email" required><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter employee's password" required><br>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="admin">Admin</option>
                <option value="employee">Employee</option>
            </select><br>

            <button type="submit" name="add_profile">Add Employee</button>
        </form>
    </div>

</body>
</html>
