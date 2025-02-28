<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gps_tracker");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Encrypt password

    $query = "SELECT * FROM users WHERE email=? AND password=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['name']; // Now storing 'username' correctly
        $_SESSION['role'] = $row['role'];

        // Redirect based on role
        if ($row['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: employee_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GPS Tracker</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; padding: 0;">
    <div style="background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center;">
        <h2 style="color: #4CAF50; font-size: 28px; margin-bottom: 20px;">Login to GPS Tracker</h2>

        <?php if (isset($error)) echo "<p style='color: red; font-size: 14px;'>$error</p>"; ?>

        <form action="" method="POST">
            <label for="email" style="display: block; font-size: 14px; margin-bottom: 8px; font-weight: bold;">Email</label>
            <input type="email" name="email" required style="width: 100%; padding: 12px; font-size: 16px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box;">

            <label for="password" style="display: block; font-size: 14px; margin-bottom: 8px; font-weight: bold;">Password</label>
            <input type="password" name="password" required style="width: 100%; padding: 12px; font-size: 16px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box;">

            <button type="submit" style="width: 100%; padding: 12px; background-color: #4CAF50; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s ease; text-transform: uppercase;">
                Login
            </button>
        </form>
    </div>
</body>
</html>
