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
  <title><?= strtoupper($_SESSION['username']); ?> Dashboard</title>
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
<body>

  <h1><?= strtoupper($_SESSION['username']); ?> Dashboard</h1>
  <div class="sidebar">
    <button class="btn" onclick="openPopup('students-popup')">Manage Students</button>
    <button class="btn" onclick="window.location.href='sub&time.php'">Periods Data</button>
    <button class="btn" onclick="openPopup('reports-popup')">Reports</button>
    <button class="btn" onclick="openCapture()">Capture</button>
    <button class="btn" onclick="openPopup('settings-popup')">Settings</button>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>

  <div class="popup" id="students-popup">
    <span class="close-btn" onclick="closePopup('students-popup')">&times;</span>
    <h2>Students Data Managing Section</h2>
    
    <div class="popup-buttons">
        <button onclick="window.location.href='view_students.php'">View Students Data</button>
        <button onclick="showAddStudentForm()">Add New Student</button>
    </div>
</div>

<!-- Add Student Popup -->
<div class="popup" id="add-student-popup">
    <span class="close-btn" onclick="closePopup('add-student-popup')">&times;</span>
    <h2>Add New Student</h2>

    <form action="add_student.php" method="POST">
        <label for="regd_id">Register ID:</label>
        <input type="text" id="regd_id" name="regd_id" required>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="department">Department:</label>
        <?php 
        if (isset($_SESSION['username']) && $_SESSION['username'] == "admin"): ?>
            <input type="text" id="department" name="department" required>
        <?php else: ?>
            <input type="text" id="department" name="department" 
                  value="<?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '' ?>" 
                  readonly required>
        <?php endif; ?>

        <label for="batch_no">Batch No:</label>
        <input type="text" id="batch_no" name="batch_no" required>

        <label for="semester">Semester:</label>
        <input type="text" id="semester" name="semester" required>

        <label for="section">Section:</label>
        <input type="text" id="section" name="section" required>

        <button type="submit">Add Student</button>
    </form>
    <hr>

    <!-- CSV Import Form -->
    <form action="import_students.php" method="POST" enctype="multipart/form-data">
        <label for="csv_file">Import CSV File:</label>
        <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
        <button type="submit" name="import">Import CSV</button>
    </form>

</div>

<div class="popup" id="reports-popup">
    <span class="close-btn" onclick="closePopup('reports-popup')">&times;</span>
    <h2>Reports Section</h2>      
     <div class="popup-buttons">
          <button onclick="window.location.href='gate_report.php'">Entry Gate Report</button>
          <button onclick="window.location.href='squad_report.php'">College Squad Report</button>
      </div>
</div>

  <!-- Settings Popup -->
  <div class="popup" id="settings-popup">
    <span class="close-btn" onclick="closePopup('settings-popup')">&times;</span>
    <h2>Update Password</h2>

    <!-- Admin Password Update Form -->
    <h3>Password Update</h3>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="user_type" value="admin">
        <input type="hidden" name="department_id" value="<?= $_SESSION['username']; ?>">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Update Password</button>
    </form>
  </div>
  
<script>

    function openPopup(id) {
        // Close all popups first
        document.querySelectorAll('.popup').forEach(popup => {
            popup.classList.remove("show");
            popup.style.display = "none"; // Ensure all popups are hidden
        });

        // Open the selected popup
        const popup = document.getElementById(id);
        if (popup) {
            popup.classList.add("show");
            popup.style.display = "block"; // Make sure it's visible
        }
    }

    function closePopup(id) {
        const popup = document.getElementById(id);
        if (popup) {
            popup.classList.remove("show");
            popup.style.display = "none"; // Hide it properly
        }
    }

    function showAddStudentForm() {
        openPopup("add-student-popup");
    }
    const serverIP = "<?= $server_ip ?>";

        function openCapture() {
            window.location.href = `https://${serverIP}/cvms/app/capture.php`;
        }



</script>

</body>
</html>
