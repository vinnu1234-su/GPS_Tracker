<?php
session_start();
include "db_connection.php"; // Ensure this file exists and connects to the database

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
    <title>Live GPS Tracking</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        
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

        /* Page Content */
        h2 { text-align: center; margin: 20px 0; }
        #map { height: 400px; width: 90%; margin: auto; border: 2px solid black; }
        .controls { text-align: center; margin-top: 10px; }
        button { padding: 10px 15px; margin: 5px; font-size: 16px; cursor: pointer; }
        #message { font-size: 18px; font-weight: bold; text-align: center; }
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

    <h2>Live GPS Tracking</h2>
    <div id="map"></div>
    <div class="controls">
        <button id="start">Start Tracking</button>
        <button id="stop">Stop Tracking</button>
    </div>
    <p id="message"></p>

    <script>
        var map = L.map('map').setView([0, 0], 13);
var tracking = false;
var marker, polyline, watchID;

// Load OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

$("#start").click(function () {
    tracking = true;
    $("#message").text("Tracking Started!").css("color", "green");
    polyline = L.polyline([], { color: 'blue' }).addTo(map);
    startTracking("Start"); // Send "Start" action to the server
});

$("#stop").click(function () {
    tracking = false;
    $("#message").text("Tracking Stopped!").css("color", "red");
    if (watchID) navigator.geolocation.clearWatch(watchID); // Stop GPS tracking
    sendLocation("Stop"); // Send "Stop" action to server
});

function startTracking(action) {
    if (!tracking) return;

    if (navigator.geolocation) {
        watchID = navigator.geolocation.watchPosition(function (position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            if (!marker) {
                marker = L.marker([lat, lng]).addTo(map);
            } else {
                marker.setLatLng([lat, lng]);
            }

            polyline.addLatLng([lat, lng]);
            map.setView([lat, lng], 13);

            sendLocation("Start"); // Continuously update position with "Start" action

        }, function (error) {
            console.log("Error getting location: " + error.message);
        }, { enableHighAccuracy: true });
    } else {
        alert("Geolocation is not supported by your browser.");
    }
}

function sendLocation(action) {
    if (!navigator.geolocation) return;

    navigator.geolocation.getCurrentPosition(function (position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        $.post("save_location.php", { 
            user_id: <?= $user_id ?>, 
            lat: lat, 
            lng: lng, 
            action: action 
        }, function (response) {
            console.log(response);
        });

    }, function (error) {
        console.log("Error getting location: " + error.message);
    }, { enableHighAccuracy: true });
}

    </script>

</body>
</html>
