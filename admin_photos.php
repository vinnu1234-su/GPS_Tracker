<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'gps_tracker'); // Update with your database details
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filter variables
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';

// Construct query with filters
$query = "SELECT * FROM employee_photos WHERE 1";

// Apply date filter if selected
if (!empty($date_filter)) {
    $query .= " AND DATE(timestamp) = '$date_filter'";
}

// Apply location filter if selected
if (!empty($location_filter)) {
    $query .= " AND location LIKE '%$location_filter%'";
}

$query .= " ORDER BY timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Uploaded Photos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        img {
            width: 100px;
            border-radius: 5px;
        }

        .no-data {
            padding: 20px;
            font-size: 18px;
            color: red;
        }

        .filter-box {
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        input, select, button {
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        button {
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <a href="admin_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
        <h2>📸 Employee Uploaded Photos</h2>

        <!-- Filter Section -->
        <div class="filter-box">
            <form method="GET">
                <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                <input type="text" name="location" placeholder="Filter by Location" value="<?php echo htmlspecialchars($location_filter); ?>">
                <button type="submit">Apply Filters</button>
                <a href="admin_photos.php"><button type="button">Clear Filters</button></a>
            </form>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Image</th>
                    <th>Location</th>
                    <th>Date & Time</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Employee Photo"></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td><?php echo date('d M Y, h:i A', strtotime($row['timestamp'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="no-data">No images found with the selected filters.</p>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
$conn->close();
?>
