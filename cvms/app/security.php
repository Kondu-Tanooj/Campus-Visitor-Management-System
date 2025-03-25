<?php
session_start(); // Start session

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Security Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Arial', sans-serif; }
    body { display: flex; flex-direction: column; align-items: flex-start; height: 100vh; background: url(images/clg.jpg) no-repeat center center; background-size: cover; overflow: hidden; position: relative; padding-left: 2rem; }
    body::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); z-index: -1; }
    h1 { color: white; font-size: 2.5rem; margin-top: 2rem; text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8); text-align: center; align-self: center; }
    .button-container { display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem; align-items: flex-start; }
    .btn { width: 180px; height: 50px; font-size: 1rem; color: white; background: rgba(255, 255, 255, 0.2); border: 2px solid white; border-radius: 8px; cursor: pointer; transition: background 0.3s, transform 0.3s; }
    .btn:hover { background: rgba(255, 255, 255, 0.4); transform: translateY(-5px); }
    .popup { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(0.9); width: 800px; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); text-align: center; display: none; opacity: 0; transition: opacity 0.4s, transform 0.4s; }
    .popup.show { display: block; opacity: 1; transform: translate(-50%, -50%) scale(1); }
    .popup .close-btn { position: absolute; top: 10px; right: 15px; font-size: 1.2rem; cursor: pointer; transition: transform 0.2s; }
    .popup .close-btn:hover { transform: scale(1.2); }
    .table-container { max-height: 450px; overflow-y: auto; margin-top: 1rem; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background-color: #f4f4f4; }
    .delete-btn, .modify-btn, .view-btn { padding: 5px 10px; color: white; border: none; cursor: pointer; border-radius: 5px; transition: background 0.3s; }
    .delete-btn { background: red; }
    .delete-btn:hover { background: darkred; }
    .modify-btn { background: #007bff; }
    .modify-btn:hover { background: #0056b3; }
    .view-btn { background: #28a745; }
    .view-btn:hover { background: #218838; }
    .form-group { margin: 1rem 0; text-align: left; }
    .form-group label { font-weight: bold; }
    .form-group input, .form-group textarea { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
    .confirm-btn { margin-top: 15px; padding: 12px 20px; font-size: 1rem; background: #002060; color: white; border: 2px solid white; cursor: pointer; border-radius: 5px; transition: background 0.3s, transform 0.2s; }
    .confirm-btn:hover { background: #001040; transform: scale(1.05); }
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
  </style>
</head>
<body>
  <h1>Security Dashboard</h1>
  <div class="button-container">
    <button class="btn" id="guests" onclick="window.location.href='guest_info.php'">Guests</button>
    <button class="btn" id="assign-id" onclick="window.location.href='assign_id.php'">Assign ID</button>
    <button class="btn" onclick="openPopup('settings-popup')">Settings</button>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>

  <!-- Settings Popup -->
  <div class="popup" id="settings-popup">
    <span class="close-btn" onclick="closePopup('settings-popup')">&times;</span>
    <h2>Update Password</h2>

    <!-- Admin Password Update Form -->
    <h3>Password Update</h3>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="user_type" value="security">
        <input type="hidden" name="department_id" value="security">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Update Password</button>
    </form>
  </div>

  <div class="popup" id="assign-popup">
    <span class="close-btn" onclick="closePopup('assign-popup')">&times;</span>
    <h2>Assign ID</h2>
    <div class="form-group"><label>Guest Name:</label><input type="text" id="guest-name"></div>
    <div class="form-group"><label>Phone Number:</label><input type="text" id="phone-number"></div>
    <div class="form-group"><label>Time Of Entry:</label><input type="text" id="entry-time" readonly><button onclick="setCurrentTime()">Set Time</button></div>
    <div class="form-group"><label>Purpose of Visit:</label><textarea id="purpose"></textarea></div>
    <button class="confirm-btn" onclick="addGuest()">Confirm</button>
  </div>

  <div class="popup" id="guests-popup">
    <span class="close-btn" onclick="closePopup('guests-popup')">&times;</span>
    <h2>Guest Information</h2>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>S. NO.</th>
            <th>Guest Name</th>
            <th>Phone Number</th>
            <th>Guest ID</th>
            <th>Entry Time</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="guest-table"></tbody>
      </table>
    </div>
  </div>

  <script>
    let guestCount = 1;
    
    function openPopup(popupId) {
      closeAllPopups();
      document.getElementById(popupId).classList.add("show");
    }

    function closePopup(popupId) {
      document.getElementById(popupId).classList.remove("show");
    }

    function closeAllPopups() {
      document.querySelectorAll(".popup").forEach(popup => popup.classList.remove("show"));
    }

    function setCurrentTime() {
      let now = new Date();
      document.getElementById("entry-time").value = now.toLocaleTimeString();
    }

    function addGuest() {
      let name = document.getElementById("guest-name").value;
      let phone = document.getElementById("phone-number").value;
      let time = document.getElementById("entry-time").value;
      let description = document.getElementById("purpose").value;
      
      if (!name || !phone || !time || !description) {
        alert("Please fill all fields.");
        return;
      }

      let row = document.getElementById("guest-table").insertRow();
      row.innerHTML = `<td>${guestCount++}</td><td>${name}</td><td>${phone}</td><td></td><td>${time}</td><td>${description}</td>
        <td><button class="modify-btn">Modify</button> <button class="delete-btn" onclick="this.parentElement.parentElement.remove()">Delete</button> <button class="view-btn">View</button></td>`;
      
      closePopup("assign-popup");
    }
  </script>
</body>
</html>
