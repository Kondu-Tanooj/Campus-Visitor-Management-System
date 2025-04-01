<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.html"); 
    exit();
}
$server_ip = $_SERVER['SERVER_ADDR'];
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cam Server</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: url(images/clg.jpg) no-repeat center center/cover;
            position: relative;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        h1 {
            color: white;
            font-size: 2.5rem;
            margin-top: 1rem;
            text-align: center;
        }
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding: 2rem;
            align-items: flex-start;
        }
        .btn, .logout-btn {
            width: 200px;
            height: 50px;
            font-size: 1.2rem;
            color: white;
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid white;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn:hover, .logout-btn:hover { background: rgba(255, 255, 255, 0.4); transform: translateY(-5px); }
        .logout-btn { background: red; }
        .logout-btn:hover { background: darkred; }
    </style>
</head>
<body>
    <h1><?= strtoupper($_SESSION['username']); ?> - Cam Server</h1>
    <div class="sidebar">
        <button class="btn" onclick="openCapture()">Capture</button>
        <button class="btn" onclick="openTrainServer()">Train Model</button>
        <button class="btn" onclick="openStartServer()">Start Capturing</button>
        <button class="btn" onclick="openStopServer()">Stop Capturing</button>
        <button class="logout-btn" onclick="window.location.href='admin.php'">Back</button>
    </div>

    <script>
        // Inject PHP server IP dynamically into JavaScript
        const serverIP = "<?= $server_ip ?>";

        function openCapture() {
            window.location.href = `https://${serverIP}/cvms/app/capture.php`;
        }

        function openTrainServer() {
            window.location.href = `https://${serverIP}:5001/train`;
            setTimeout(() => {
                window.location.href = `https://${serverIP}/cvms/app/cam.php`;
            }, 10000);
        }

        function openStartServer() {
            window.location.href = `https://${serverIP}:5001/recognize`;
            setTimeout(() => {
                window.location.href = `https://${serverIP}/cvms/app/cam.php`;
            }, 10000);
        }

        function openStopServer() {
            window.location.href = `https://${serverIP}:5001/stop`;
            setTimeout(() => {
                window.location.href = `https://${serverIP}/cvms/app/cam.php`;
            }, 10000);
        }
    </script>
</body>
</html>
