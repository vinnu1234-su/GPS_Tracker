<?php
session_start();
$conn = new mysqli("localhost", "root", "", "gps_tracker");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users (both Admin & Employee)
$users_query = "SELECT id, name FROM users";
$users_result = $conn->query($users_query);

// Fetch selected user details
$selected_user = null;
if (isset($_POST['selected_user'])) {
    $selected_user_id = $_POST['selected_user'];
    $user_query = "SELECT * FROM users WHERE id='$selected_user_id'";
    $result = $conn->query($user_query);
    if ($result && $result->num_rows > 0) {
        $selected_user = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Profile</title>
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
            padding: 30px;
            margin: 50px auto;
            width: 40%;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }
        .profile-box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .profile-box label {
            font-size: 18px;
            font-weight: bold;
            color: #444;
        }
        .profile-box input, .profile-box select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .profile-box input[type="submit"] {
            background: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            transition: 0.3s;
        }
        .profile-box input[type="submit"]:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="employee_details.php">Employee Details</a>
        <a href="edit_profiles.php">Edit Profiles</a>
        <a href="add_new_profile.php">Add New Profile</a>
        <a href="view_employees.php">View Employees</a>
		<a href="view_attendance.php">View Attendance</a>
		<a href="admin_photos.php">View Photos</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <div class="profile-box">
            <h2>Edit User Profile</h2>

            <form method="post" action="">
                <label>Select User:</label>
                <select name="selected_user" onchange="this.form.submit()">
                    <option value="">-- Select a User --</option>
                    <?php while ($user = $users_result->fetch_assoc()) { ?>
                        <option value="<?php echo $user['id']; ?>" <?php echo (isset($selected_user) && $selected_user['id'] == $user['id']) ? 'selected' : ''; ?>>
                            <?php echo $user['name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </form>

            <?php if ($selected_user): ?>
            <form method="post" action="update_user.php">
                <input type="hidden" name="user_id" value="<?php echo $selected_user['id']; ?>">

                <label>Name:</label>
                <input type="text" name="name" value="<?php echo $selected_user['name']; ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo $selected_user['email']; ?>" required>

                <label>Role:</label>
                <select name="role">
                    <option value="employee" <?php echo ($selected_user['role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                    <option value="admin" <?php echo ($selected_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>

                <label>New Password (Leave blank to keep current password):</label>
                <input type="password" name="password">

                <input type="submit" name="update_profile" value="Update">
            </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
