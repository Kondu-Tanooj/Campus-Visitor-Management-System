<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.html"); 
    exit();
}

include './config.php'; // Database connection

// Directory where images will be stored
$trainingDir = "flask_server/training-images/";

// Ensure the main training directory exists
if (!is_dir($trainingDir)) {
    mkdir($trainingDir, 0775, true);
    chmod($trainingDir, 0775);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['image']) && !empty($_POST['student_name'])) {
    $regdId = trim($_POST['student_name']); // Student Registration Number

    // Check if student exists in the database
    $stmt = $conn->prepare("SELECT * FROM student_details WHERE regd_id = ?");
    $stmt->bind_param("s", $regdId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found! Please enter a valid registration number.']);
        exit();
    }
    
    // If student exists, proceed with folder creation and image saving
    $studentFolder = $trainingDir . $regdId . "/";

    if (!is_dir($studentFolder)) {
        mkdir($studentFolder, 0775, true);
        chmod($studentFolder, 0775);
    }

    // Get sequential file name
    $files = glob($studentFolder . "*.jpg");
    $imageCount = count($files) + 1;
    $filePath = $studentFolder . "{$regdId}_{$imageCount}.jpg";

    // Decode base64 image and save it
    $imageParts = explode(";base64,", $_POST['image']);
    $imageBase64 = base64_decode($imageParts[1]);

    if (file_put_contents($filePath, $imageBase64)) {
        echo json_encode(['status' => 'success', 'message' => 'Image saved successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save image.']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Image Capture</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --dark-color: #212529;
            --light-color: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background-color: #f5f7fa;
            color: #333;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h2 {
            color: var(--dark-color);
            text-align: center;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }
        
        .container {
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        #status {
            padding: 0.75rem 1rem;
            margin: 1rem 0;
            border-radius: var(--border-radius);
            font-weight: 500;
            text-align: center;
            background-color: #e9ecef;
            color: #495057;
        }
        
        .error {
            background-color: #fff0f3;
            color: #d00000;
            border-left: 4px solid #d00000;
        }
        
        .success {
            background-color: #e6fcf5;
            color: #099268;
            border-left: 4px solid #099268;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto 1.5rem;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            background-color: #000;
        }
        
        video {
            width: 100%;
            display: block;
        }
        
        .controls {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.75rem;
            margin: 1.5rem 0;
        }
        
        input, select {
            width: 100%;
            max-width: 400px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: var(--border-radius);
            margin: 0 auto 1rem;
            display: block;
            transition: var(--transition);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        button {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        button:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        
        #startButton {
            background-color: var(--primary-color);
            color: white;
        }
        
        #startButton:hover:not(:disabled) {
            background-color: var(--secondary-color);
        }
        
        #captureButton {
            background-color: var(--success-color);
            color: white;
        }
        
        #captureButton:hover:not(:disabled) {
            background-color: #38b6db;
        }
        
        #stopButton {
            background-color: var(--danger-color);
            color: white;
        }
        
        #stopButton:hover:not(:disabled) {
            background-color: #e5177e;
        }
        
        .back-button {
            background-color: var(--dark-color);
            color: white;
        }
        
        .back-button:hover {
            background-color: #343a40;
        }
        
        .switch-camera-button {
            background-color: #7209b7;
            color: white;
        }
        
        .switch-camera-button:hover:not(:disabled) {
            background-color: #5a08a0;
        }
        
        canvas {
            display: none;
        }
        
        #capturedImage {
            max-width: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin: 1.5rem auto;
            display: block;
            border: 1px solid #dee2e6;
        }
        
        #uploadForm {
            display: none;
        }
        
        .preview-container {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        #cameraSelect {
            display: none;
            margin-bottom: 1rem;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .container {
                padding: 1.5rem;
            }
            
            .controls {
                flex-direction: column;
                align-items: center;
            }
            
            button {
                width: 100%;
                max-width: 300px;
            }
        }
        
        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Flash animation for capture */
        .flash {
            animation: flash 0.5s ease-out;
        }
        
        @keyframes flash {
            0% { opacity: 1; }
            50% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Preview animation */
        .preview-animation {
            animation: previewFade 2s ease-in-out;
        }
        
        @keyframes previewFade {
            0% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(0.95); }
            100% { opacity: 0; transform: scale(0.9); display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Image Capture System</h2>
        
        <div id="status">Tap "Start Camera" to begin capturing student images</div>
        
        <select id="cameraSelect" disabled>
            <option value="">Select Camera</option>
        </select>
        
        <div class="video-container">
            <video id="videoElement" playsinline autoplay></video>
        </div>
        
        <input type="text" id="student_name" placeholder="Enter Student Registration Number" required>
        
        <div class="controls">
            <button id="startButton">
                <span class="button-icon">📷</span>
                Start Camera
            </button>
            <button id="captureButton" disabled>
                <span class="button-icon">⏺️</span>
                Capture Image
            </button>
            <button id="stopButton" disabled>
                <span class="button-icon">⏹️</span>
                Stop Camera
            </button>
            <button id="switchCameraButton" class="switch-camera-button" disabled>
                <span class="button-icon">🔄</span>
                Switch Camera
            </button>
            <button class="back-button" onclick="goBack()">
                <span class="button-icon">←</span>
                Back to Dashboard
            </button>
        </div>
        
        <canvas id="canvas"></canvas>
        
        <form id="uploadForm" method="POST">
            <input type="hidden" name="image" id="imageData">
            <input type="hidden" name="student_name" id="hiddenStudentName">
        </form>
    </div>
    
    <script>
        function goBack() {
            <?php if ($_SESSION['username'] === 'admin') { ?>
                window.location.href = 'cam.php';
            <?php } else { ?>
                window.location.href = 'dept.php';
            <?php } ?>
        }
        
        const video = document.getElementById('videoElement');
        const startBtn = document.getElementById('startButton');
        const stopBtn = document.getElementById('stopButton');
        const captureBtn = document.getElementById('captureButton');
        const switchCameraBtn = document.getElementById('switchCameraButton');
        const cameraSelect = document.getElementById('cameraSelect');
        const statusDiv = document.getElementById('status');
        const studentNameInput = document.getElementById('student_name');
        const canvas = document.getElementById('canvas');
        const capturedImage = document.getElementById('capturedImage');
        const videoContainer = document.querySelector('.video-container');
        
        let stream = null;
        let isCameraActive = false;
        let cameras = [];
        let currentCameraIndex = 0;
        
        // Show status message
        function showStatus(message, type = '') {
            statusDiv.textContent = message;
            statusDiv.className = type;
        }
        
        // Flash effect when capturing image
        function flashEffect() {
            videoContainer.classList.add('flash');
            setTimeout(() => {
                videoContainer.classList.remove('flash');
            }, 500);
        }
        
        // Show preview of captured image
        function showPreview(imageDataUrl) {
            capturedImage.src = imageDataUrl;
            capturedImage.style.display = 'block';
            capturedImage.classList.remove('preview-animation');
            void capturedImage.offsetWidth; // Trigger reflow
            capturedImage.classList.add('preview-animation');
            
            setTimeout(() => {
                capturedImage.style.display = 'none';
            }, 2000);
        }
        
        // Get available cameras
        async function getCameras() {
            try {
                const devices = await navigator.mediaDevices.enumerateDevices();
                cameras = devices.filter(device => device.kind === 'videoinput');
                
                if (cameras.length > 1) {
                    cameraSelect.innerHTML = '<option value="">Select Camera</option>';
                    cameras.forEach((camera, index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        option.text = camera.label || `Camera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });
                    cameraSelect.disabled = false;
                    switchCameraBtn.disabled = false;
                } else {
                    cameraSelect.style.display = 'none';
                    switchCameraBtn.disabled = true;
                }
            } catch (err) {
                console.error('Error enumerating devices:', err);
                cameraSelect.style.display = 'none';
                switchCameraBtn.disabled = true;
            }
        }
        
        // Initialize camera
        async function startCamera(cameraIndex = 0) {
            try {
                // Stop any existing stream
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                startBtn.innerHTML = '<span class="spinner"></span> Starting...';
                startBtn.disabled = true;
                
                const constraints = { 
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        deviceId: cameras.length > 0 ? { exact: cameras[cameraIndex].deviceId } : undefined,
                        facingMode: cameras.length === 0 ? 'user' : undefined
                    }, 
                    audio: false 
                };
                
                stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                video.srcObject = stream;
                isCameraActive = true;
                currentCameraIndex = cameraIndex;
                showStatus("Camera is active. Position the student's face in the frame.", "success");
                startBtn.disabled = true;
                stopBtn.disabled = false;
                captureBtn.disabled = false;
                
                if (cameras.length > 1) {
                    cameraSelect.value = cameraIndex;
                    cameraSelect.style.display = 'block';
                    switchCameraBtn.disabled = false;
                }
                
                startBtn.innerHTML = '<span class="button-icon">📷</span> Camera Started';
                studentNameInput.focus();
            } catch (err) {
                isCameraActive = false;
                showStatus(`Error: ${err.message}`, "error");
                startBtn.innerHTML = '<span class="button-icon">📷</span> Start Camera';
                startBtn.disabled = false;
                
                if (cameras.length > 1) {
                    // Try the next camera if available
                    const nextIndex = (cameraIndex + 1) % cameras.length;
                    if (nextIndex !== cameraIndex) {
                        showStatus(`Trying next camera...`, "");
                        setTimeout(() => startCamera(nextIndex), 500);
                    }
                }
            }
        }
        
        // Stop camera
        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
                isCameraActive = false;
                showStatus("Camera stopped. Tap 'Start Camera' to begin again.");
                startBtn.disabled = false;
                stopBtn.disabled = true;
                captureBtn.disabled = true;
                switchCameraBtn.disabled = true;
                cameraSelect.style.display = 'none';
                startBtn.innerHTML = '<span class="button-icon">📷</span> Start Camera';
            }
        }
        
        // Switch to next camera
        function switchCamera() {
            if (!isCameraActive || cameras.length < 2) return;
            
            const nextIndex = (currentCameraIndex + 1) % cameras.length;
            startCamera(nextIndex);
        }
        
        // Capture image
        function captureImage() {
            if (!studentNameInput.value.trim()) {
                showStatus("Please enter a registration number before capturing.", "error");
                studentNameInput.focus();
                return;
            }
            
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageDataUrl = canvas.toDataURL('image/jpg');
            showPreview(imageDataUrl);
            
            // Visual feedback
            flashEffect();
            showStatus("Saving captured image...");
            
            // Save image via AJAX
            saveImage(imageDataUrl, studentNameInput.value.trim());
        }
        
        // AJAX function to save image
        function saveImage(imageData, studentName) {
            const formData = new FormData();
            formData.append('image', imageData);
            formData.append('student_name', studentName);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showStatus(data.message, data.status);
            })
            .catch(error => {
                showStatus("Error saving image: " + error.message, "error");
            });
        }
        
        // Event listeners
        startBtn.addEventListener('click', () => {
            getCameras().then(() => startCamera(0));
        });
        stopBtn.addEventListener('click', stopCamera);
        captureBtn.addEventListener('click', captureImage);
        switchCameraBtn.addEventListener('click', switchCamera);
        cameraSelect.addEventListener('change', (e) => {
            if (e.target.value) {
                startCamera(parseInt(e.target.value));
            }
        });
        
        // Auto-focus camera button when page loads
        window.addEventListener('DOMContentLoaded', () => {
            startBtn.focus();
            
            // Clear any existing stream when page reloads
            window.addEventListener('beforeunload', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        });
    </script>
</body>
</html>