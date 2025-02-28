<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'gps_tracker'); // Update with your DB credentials
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle form submission (Saving to DB)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if action and image data are present
    if (isset($_POST['action']) && isset($_POST['image'])) {
        $action = $_POST['action'];
        $imageData = $_POST['image'];

        // Clean the base64 image data (remove data URL prefix)
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        // Decode the base64 string
        $imageName = 'attendance_' . time() . '.png';
        $filePath = 'uploads/' . $imageName;
        $imageData = base64_decode($imageData);

        // Save the image to the server
        file_put_contents($filePath, $imageData);

        // Insert record into the database
        $stmt = $conn->prepare("INSERT INTO attendance (action, timestamp, image) VALUES (?, NOW(), ?)");
        $stmt->bind_param('ss', $action, $imageName);
        $stmt->execute();
        $stmt->close();

// Return JSON response
echo json_encode(['message' => 'Attendance recorded successfully.']);
exit;

        
    }
}

// Fetch attendance records from the database
$query = "SELECT * FROM attendance ORDER BY timestamp DESC";
$result = $conn->query($query);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    text-align: center;
    margin: 0;
    padding: 0;
}

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

/* Header Styles */
h1 {
    color: #4CAF50;
    margin-top: 20px;
}

/* Button Styles */
button {
    padding: 10px 20px;
    margin: 10px;
    font-size: 16px;
    cursor: pointer;
    border: none;
    border-radius: 5px;
    background-color: #4CAF50;
    color: white;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #45a049;
}

button.hidden {
    display: none;
}

/* Camera Styles */
#camera {
    width: 100%;
    max-width: 500px;
    border: 1px solid #ddd;
    margin-top: 20px;
    border-radius: 10px;
}

/* Snapshot Styles */
#snapshot {
    display: none;
}

/* Table Styles */
table {
    width: 80%;
    margin-top: 30px;
    border-collapse: collapse;
    margin-left: auto;
    margin-right: auto;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

th, td {
    padding: 12px 20px;
    text-align: center;
    border: 1px solid #ddd;
}

th {
    background-color: #4CAF50;
    color: white;
}

td {
    background-color: #f9f9f9;
}

/* Image Style */
td img {
    width: 100px;
    border-radius: 5px;
}

/* Page Layout */
.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
}

h2 {
    margin-top: 40px;
    color: #333;
}

/* Media Queries for Mobile Responsiveness */
@media (max-width: 768px) {
    table {
        width: 100%;
        margin-top: 20px;
    }

    button {
        width: 100%;
        font-size: 18px;
    }

    #camera {
        max-width: 100%;
    }
}

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
    <h1>Attendance Page</h1>
    <button id="loginBtn">Login</button>
    <button id="logoutBtn" class="hidden">Logout</button>
    <br><br>
    <button id="submitBtn" class="hidden">Submit</button>

    <video id="camera" class="hidden" autoplay></video>
    <canvas id="snapshot" class="hidden"></canvas>
	<!-- Message Container -->
<div id="message" style="color: green; font-size: 18px; margin-top: 10px;"></div>


    <!-- Attendance Table -->
    <h2>Attendance Records</h2>
    <table>
        <thead>
            <tr>
                <th>Action</th>
                <th>Time</th>
                <th>Photo</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Attendance Photo" width="100"></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No attendance records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        const loginBtn = document.getElementById('loginBtn');
        const logoutBtn = document.getElementById('logoutBtn');
        const submitBtn = document.getElementById('submitBtn');
        const camera = document.getElementById('camera');
        const snapshot = document.getElementById('snapshot');

        let videoStream;
        let currentAction = '';  // Track the current action (login/logout)

        // Start camera
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    videoStream = stream;
                    camera.srcObject = stream;
                    camera.classList.remove('hidden');
                })
                .catch(err => {
                    console.error("Error accessing camera:", err);
                });
        }

        // Stop camera
        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                camera.classList.add('hidden');
            }
        }

        // Take snapshot
        function takeSnapshot() {
            const ctx = snapshot.getContext('2d');
            snapshot.width = camera.videoWidth;
            snapshot.height = camera.videoHeight;
            ctx.drawImage(camera, 0, 0, snapshot.width, snapshot.height);
            snapshot.classList.remove('hidden');
            const imageData = snapshot.toDataURL('image/png'); // Base64 image data
            return imageData;
        }

        // Save to the database (backend call)
        // Save to the database (backend call)
function saveAttendance(action, imageData) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('image', imageData);

    fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('message').textContent = data.message; // Show message on page
        stopCamera();
        submitBtn.classList.add('hidden');
        loginBtn.classList.remove('hidden');
        logoutBtn.classList.add('hidden');
        setTimeout(() => location.reload(), 2000); // Reload page after 2 seconds
    })
    .catch(error => {
        console.error("Error saving data:", error);
        document.getElementById('message').style.color = "red";
        document.getElementById('message').textContent = "Error saving data!";
    });
}


        // Login Button click
        loginBtn.addEventListener('click', () => {
            currentAction = 'login';
            startCamera();
            loginBtn.classList.add('hidden');
            logoutBtn.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
        });

        // Logout Button click
        logoutBtn.addEventListener('click', () => {
            currentAction = 'logout';
            startCamera();
            logoutBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        });

        // Submit Button click
        submitBtn.addEventListener('click', () => {
            if (!currentAction) {
                alert("Please select login or logout first.");
                return;
            }
            const imageData = takeSnapshot(); // Capture the image from the camera
            saveAttendance(currentAction, imageData); // Save the image and action to the database
            snapshot.classList.add('hidden'); // Hide the snapshot after submission
        });
    </script>
</body>
</html>
