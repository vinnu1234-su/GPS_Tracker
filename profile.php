<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "gps_tracker");
$user_id = $_SESSION['user_id'];

// Fetch user details
$query = "SELECT name, email, role FROM users WHERE id = '$user_id'";
$result = $conn->query($query);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
    body { 
        font-family: Arial, sans-serif; 
        margin: 0; 
        padding: 0; 
        background: url('https://wallpapers.com/images/high/profile-picture-background-mm9azbcfdvtvwsp0.webp') no-repeat center center fixed; 
        background-size: cover; 
    }
    .navbar { 
        background: rgba(0, 0, 0, 0.7); 
        padding: 15px; 
        text-align: center; 
    }
    .navbar a { 
        color: white; 
        text-decoration: none; 
        padding: 15px; 
        font-size: 18px; 
    }
    .navbar a:hover { 
        background: rgba(255, 255, 255, 0.2); 
    }
    .container { 
        padding: 20px; 
        text-align: center; 
    }
    .profile-box { 
        background: rgba(255, 255, 255, 0.9); 
        padding: 20px; 
        margin: 20px auto; 
        width: 50%; 
        border-radius: 8px; 
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); 
    }
    .profile-box h2 { 
        margin-bottom: 20px; 
    }
    .profile-box p { 
        font-size: 18px; 
        margin: 10px 0; 
    }
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
        <div class="profile-box">
            <h2>My Profile</h2>
            <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            <p><strong>Role:</strong> <?php echo ucfirst($user['role']); ?></p>
        </div>
    </div>

</body>
</html>
