<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: ../index.html"); 
  exit();
}

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
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
    body {
      display: flex;
      flex-direction: column;
      height: 100vh;
      background: url(images/clg.jpg) no-repeat center center;
      background-size: cover;
      overflow: hidden;
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
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
    }
    .sidebar {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
      padding: 2rem;
      align-items: flex-start;
      z-index: 10;
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
      transition: background 0.3s, transform 0.3s;
    }
    .btn:hover, .logout-btn:hover {
      background: rgba(255, 255, 255, 0.4);
      transform: translateY(-5px);
    }
    .logout-btn {
      background: red;
      margin-top: auto;
    }
    .logout-btn:hover {
      background: darkred;
    }

    /* Popup Styling */
    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 75vw;
      max-width: 600px;
      height: auto;
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
      text-align: center;
      display: none;
      opacity: 0;
      transition: opacity 0.4s ease-in-out, transform 0.3s ease-in-out;
      z-index: 9999;
    }
    .popup.show {
      display: block;
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }
    .popup .close-btn {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 1.5rem;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .popup .close-btn:hover {
      transform: scale(1.2);
    }

    /* Form styling */
    form {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-top: 10px;
    }
    input, select, button {
      width: 100%;
      padding: 10px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }

.popup-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
    }

    .popup-buttons button {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        transition: 0.3s;
    }

    .popup-buttons button:hover {
        background-color: #0056b3;
    }

    .popup-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 15px;
    }

    .popup-buttons button {
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        background-color: #007bff;
        color: white;
        transition: 0.3s;
    }

    .popup-buttons button:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }

    th {
        background-color: #007bff;
        color: white;
    }
    

  </style>
</head>
<!-- PHP and head section remains the same until the body -->
<body>
  <h1><?= strtoupper($_SESSION['username']); ?> - Cam Server</h1>
  <div class="sidebar">
    <button class="btn" onclick="opencam()">Capture</button>
    <button class="btn" onclick="openTrainServer()">Train Model</button>
    <button class="btn" onclick="openStartServer()">Start Capturing</button>
    <button class="btn" onclick="openStopServer()">Stop Capturing</button>
    <button class="logout-btn" onclick="window.location.href='admin.php'">Back</button>
  </div>
  
  <!-- Capture Popup -->
  <div id="capture-popup" class="popup">
    <div class="popup-content">
      <span class="close-btn" onclick="stopCamera()">&times;</span>
      <iframe id="capture-frame" src="http://localhost:5001/register" width="100%" height="400px" frameborder="0" allow="camera;"></iframe>
    </div>
  </div>

<script>
    // Declare videoStream globally
    let videoStream = null;

    function openTrainServer() {
        window.location.href = "http://localhost:5001/train";
        setTimeout(() => {
        window.location.href = "http://localhost/cvms/app/cam.php"; // Redirect after 10 seconds
    }, 10000);
    }
    
    function openStartServer() {
        window.location.href = "http://localhost:5001/recognize";
        setTimeout(() => {
        window.location.href = "http://localhost/cvms/app/cam.php"; // Redirect after 10 seconds
    }, 10000);
        
    }
    
    function openStopServer() {
        window.location.href = "http://localhost:5001/stop";
        setTimeout(() => {
        window.location.href = "http://localhost/cvms/app/cam.php"; // Redirect after 10 seconds
    }, 10000);
    }
    
    async function opencam() {
        try {
            // Request camera access and store in global variable
            videoStream = await navigator.mediaDevices.getUserMedia({ video: true });
            console.log("Camera access granted.");
            
            // Open the popup after access is granted
            openPopup("capture-popup");
            
        } catch (error) {
            console.error("Camera access denied:", error);
            alert("Please allow camera access to continue.");
        }
    }
    
    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            console.log("Camera access stopped.");
            videoStream = null;
        }
        closePopup("capture-popup");
        window.location.href = "/cvms/app/cam.php";
    }
    
    function openPopup(id) {
        // Close all popups first
        document.querySelectorAll('.popup').forEach(popup => {
            popup.classList.remove("show");
            popup.style.display = "none";
        });

        // Open the selected popup
        const popup = document.getElementById(id);
        if (popup) {
            popup.classList.add("show");
            popup.style.display = "block";
        }
    }

    function closePopup(id) {
        const popup = document.getElementById(id);
        if (popup) {
            popup.classList.remove("show");
            popup.style.display = "none";
        }
    }

</script>
</body>
</html>