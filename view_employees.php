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

// Search functionality
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';

// Modify the query to include search functionality
$query = "
    SELECT u.id, u.name, u.email, t.latitude, t.longitude 
    FROM users u
    LEFT JOIN tracking t ON u.id = t.user_id
    WHERE u.role = 'employee' 
    AND (u.name LIKE '%$searchTerm%' OR u.email LIKE '%$searchTerm%')
    ORDER BY t.timestamp DESC
";

$result = $conn->query($query);

// Check if the query executed successfully
if (!$result) {
    die("Error executing query: " . $conn->error);
}

$employees = [];

while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees | GPS Tracker</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }

        nav {
            background-color: #333;
            overflow: hidden;
        }

        nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 16px;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        #map {
            height: 500px;
            width: 100%;
        }

        .search-container {
            margin: 20px 0;
            text-align: center;
        }

        .search-container input {
            padding: 10px;
            width: 250px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .search-container button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #45a049;
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
        <h2>Employees' Locations</h2>

        <!-- Search Bar -->
        <div class="search-container">
            <form method="POST" action="">
                <input type="text" name="search" placeholder="Search by name or email" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit">Search</button>
            </form>
        </div>

        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
	// This function will fetch the latest employee locations and update the map
function updateEmployeeLocations() {
    $.ajax({
        url: 'get_employee_locations.php', // This PHP script will fetch the latest locations
        type: 'GET',
        success: function(data) {
            // Parse the response data (assumed to be in JSON format)
            var employees = JSON.parse(data);

            // Remove all current markers from the map
            map.eachLayer(function(layer) {
                if (layer instanceof L.Marker) {
                    map.removeLayer(layer);
                }
            });

            // Add updated markers to the map
            employees.forEach(function(employee) {
                if (employee.latitude && employee.longitude) {
                    var marker = L.marker([employee.latitude, employee.longitude])
                        .addTo(map)
                        .bindPopup("<b>" + employee.name + "</b><br>Email: " + employee.email);
                }
            });
        }
    });
}

// Call the update function periodically (every 5 seconds)
setInterval(updateEmployeeLocations, 5000);

        var map = L.map('map').setView([20.5937, 78.9629], 5); // Initial map view set to India

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Adding markers for employees with locations fetched from the database
        <?php foreach ($employees as $employee): ?>
            // Check if the latitude and longitude are available
            <?php if ($employee['latitude'] && $employee['longitude']): ?>
                var marker = L.marker([<?php echo $employee['latitude']; ?>, <?php echo $employee['longitude']; ?>])
                    .addTo(map)
                    .bindPopup("<b><?php echo $employee['name']; ?></b><br>Email: <?php echo $employee['email']; ?>");
            <?php endif; ?>
        <?php endforeach; ?>
    </script>

</body>
</html>
