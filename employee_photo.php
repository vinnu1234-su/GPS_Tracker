<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'gps_tracker'); // Update with your DB credentials
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Handle form submission (Saving Image to DB)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['location']) && (isset($_POST['image']) || isset($_FILES['uploaded_image']))) {
        $location = $_POST['location'];
        $imageName = '';

        // Handling Camera Captured Image
        if (isset($_POST['image'])) {
            $imageData = $_POST['image'];
            $imageData = str_replace('data:image/png;base64,', '', $imageData);
            $imageData = str_replace(' ', '+', $imageData);
            $imageData = base64_decode($imageData);

            $imageName = 'photo_' . time() . '.png';
            $filePath = 'uploads/' . $imageName;
            file_put_contents($filePath, $imageData);
        }

        // Handling Uploaded Image
        if (isset($_FILES['uploaded_image']) && $_FILES['uploaded_image']['error'] == 0) {
            $imageName = 'upload_' . time() . '_' . $_FILES['uploaded_image']['name'];
            $filePath = 'uploads/' . $imageName;
            move_uploaded_file($_FILES['uploaded_image']['tmp_name'], $filePath);
        }

        // Save to database
        if (!empty($imageName)) {
            $stmt = $conn->prepare("INSERT INTO employee_photos (location, image, timestamp) VALUES (?, ?, NOW())");
            $stmt->bind_param('ss', $location, $imageName);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Fetch images from the database
$query = "SELECT * FROM employee_photos ORDER BY timestamp DESC";
$result = $conn->query($query);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Photo Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        select, button, input {
            margin: 10px;
            padding: 10px;
            font-size: 16px;
        }

        video, canvas {
            width: 100%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin: 10px 0;
        }

        .photo-gallery img {
            width: 100px;
            margin: 5px;
            border-radius: 5px;
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
<a href="employee_dashboard.php" class="back-btn">⬅ Back to Dashboard</a>
    <div class="container">
        <h2>Upload or Capture Photo</h2>
        <form id="photoForm" method="POST" enctype="multipart/form-data">
            <label for="location">Select Location:</label>
            <select name="location" id="location">
                <option value="Office">Office</option>
                <option value="Client Visit">Client Visit</option>
                <option value="Site">Site</option>
            </select>
            <br>

            <button type="button" id="openCamera">Take Picture</button>
            <button type="button" id="uploadPhoto">Upload Picture</button>

            <br>
            <video id="camera" class="hidden" autoplay></video>
            <canvas id="snapshot" class="hidden"></canvas>

            <input type="file" name="uploaded_image" id="uploaded_image" class="hidden" accept="image/*">
            
            <input type="hidden" name="image" id="imageData">
            <br>
            <button type="submit">Submit</button>
        </form>

        <h2>Photo History</h2>
        <div class="photo-gallery">
            <?php while ($row = $result->fetch_assoc()): ?>
                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Employee Photo">
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        const openCameraBtn = document.getElementById('openCamera');
        const uploadPhotoBtn = document.getElementById('uploadPhoto');
        const camera = document.getElementById('camera');
        const snapshot = document.getElementById('snapshot');
        const imageDataInput = document.getElementById('imageData');
        const fileInput = document.getElementById('uploaded_image');
        let videoStream;

        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(stream => {
                    videoStream = stream;
                    camera.srcObject = stream;
                    camera.classList.remove('hidden');
                })
                .catch(err => console.error("Camera access error:", err));
        }

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                camera.classList.add('hidden');
            }
        }

        function takeSnapshot() {
            const ctx = snapshot.getContext('2d');
            snapshot.width = camera.videoWidth;
            snapshot.height = camera.videoHeight;
            ctx.drawImage(camera, 0, 0, snapshot.width, snapshot.height);
            const imageData = snapshot.toDataURL('image/png');
            imageDataInput.value = imageData;
            snapshot.classList.remove('hidden');
        }

        openCameraBtn.addEventListener('click', () => {
            startCamera();
            setTimeout(() => takeSnapshot(), 3000);
        });

        uploadPhotoBtn.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                stopCamera();
                snapshot.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
