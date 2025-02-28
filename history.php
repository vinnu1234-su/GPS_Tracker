<?php
session_start();
include "db_connection.php"; // Ensure this file correctly connects to the database

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access! Please log in.");
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking History</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; text-align: center; }
        
        /* Menu Bar */
        .menu-bar {
            background: #333;
            padding: 10px;
            text-align: center;
        }
        .menu-bar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            display: inline-block;
        }
        .menu-bar a:hover { background: #555; }

        table { width: 80%; margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid black; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <!-- Menu Bar -->
    <div class="menu-bar">
        <a href="profile.php">Profile</a>
        <a href="track.php">Track</a>
        <a href="history.php">History</a>
        <a href="attendance.php">Attendance</a>
		<a href="employee_photo.php">Uploads</a>
        <a href="logout.php">Logout</a>
    </div>

    <h2>Tracking History</h2>

    <table>
        <tr>
            
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Timestamp</th>
            <th>Action</th> <!-- Added Action Column -->
        </tr>

        <?php
        // Fetch tracking records along with the action type
        $sql = "SELECT id, latitude, longitude, timestamp, action FROM tracking WHERE user_id = ? ORDER BY timestamp DESC";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            
                            <td>{$row['latitude']}</td>
                            <td>{$row['longitude']}</td>
                            <td>{$row['timestamp']}</td>
                            <td style='color: " . ($row['action'] == 'Start' ? 'green' : 'red') . "; font-weight: bold;'>{$row['action']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No tracking data available</td></tr>";
            }

            $stmt->close();
        } else {
            die("Query preparation failed: " . $conn->error);
        }

        $conn->close();
        ?>

    </table>

</body>
</html>
