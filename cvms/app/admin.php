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
  <title>Admin Dashboard</title>
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
      max-width: 500px;
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

    /* Popup Styling - New ID Section */
#new-id-popup {
  width: 80vw;
  max-width: 700px;
  height: auto;
  background: rgba(255, 255, 255, 0.95);
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
  text-align: center;
  display: none;
  opacity: 0;
  transition: opacity 0.4s ease-in-out, transform 0.3s ease-in-out;
  z-index: 9999;
}

#new-id-popup.show {
  display: block;
  opacity: 1;
  transform: translate(-50%, -50%) scale(1);
}

/* Close button */
#new-id-popup .close-btn {
  position: absolute;
  top: 10px;
  right: 15px;
  font-size: 1.8rem;
  font-weight: bold;
  cursor: pointer;
  transition: transform 0.2s, color 0.2s;
  color: #333;
}

#new-id-popup .close-btn:hover {
  transform: scale(1.2);
  color: #d9534f;
}

/* Search Input */
#searchInput {
  width: 95%;
  padding: 10px;
  font-size: 1rem;
  margin-bottom: 15px;
  border: 2px solid #007bff;
  border-radius: 5px;
  outline: none;
  transition: border 0.3s;
}

#searchInput:focus {
  border: 2px solid #0056b3;
}

/* Table Styling */
.table-wrapper {
  max-height: 300px; /* Adjust height as needed */
  overflow-y: auto;
  overflow-x: auto;
  border: 1px solid #ccc;
  border-radius: 5px;
}

#idTable {
  width: 100%;
  min-width: 600px;
  border-collapse: collapse;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

#idTable thead {
  background: #007bff;
  color: white;
  text-align: left;
}

#idTable th, #idTable td {
  padding: 12px;
  border: 1px solid #ddd;
}

#idTable tbody tr:nth-child(even) {
  background: #f9f9f9;
}

#idTable tbody tr:hover {
  background: #f1f1f1;
  transition: 0.3s;
}

/* Responsive Design */
@media (max-width: 768px) {
  #new-id-popup {
    width: 90vw;
    padding: 1.5rem;
  }

  #idTable th, #idTable td {
    padding: 8px;
    font-size: 0.9rem;
  }
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
  </style>
</head>
<body>

  <h1>Librarian Dashboard</h1>
  <div class="sidebar">
    <button class="btn" onclick="openPopup('students-popup')">Manage Students</button>
    <button class="btn" onclick="openPopup('guests-popup')">Manage Guest</button>
    <button class="btn" onclick="openPopup('new-id-popup')">ID Cards Data</button>
    <button class="btn" onclick="openPopup('reports-popup')">Reports</button>
    <button class="btn" onclick="window.location.href='cam.php'">Cam Server</button>
    <button class="btn" onclick="openPopup('settings-popup')">Settings</button>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </div>

  <div class="popup" id="guests-popup">
    <span class="close-btn" onclick="closePopup('guests-popup')">&times;</span>
    <h2>Guest Data Managing Section</h2>
    
    <div class="popup-buttons">
        <button onclick="window.location.href='guest_info.php'"> Guest Entry Report</button>
        <button onclick="window.location.href='guest_id.php'">Guest ID</button>
    </div>
</div>


 <!-- Popups -->
<div class="popup" id="new-id-popup">
    <span class="close-btn" onclick="closePopup('new-id-popup')">&times;</span>
    <h2>ID Card Section</h2>

    <!-- Search and Filter Input -->
    <input type="text" id="searchInput" onkeyup="filterTable_1()" placeholder="Search by ID, Name, or Regd ID">

    <!-- Table -->
  <!-- Table Wrapper for Scrollbar -->
<div class="table-wrapper">
    <table border="1" id="idTable">
        <thead>
            <tr>
                <th>S.No</th>
                <th>RFID</th>
                <th>ID Name</th>
                <th>Regd ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'config.php';
            $query = "SELECT * FROM raw_id_table ORDER BY Sno ASC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['Sno'] . "</td>";
                    echo "<td contenteditable='true' class='editable' data-column='rf_id' data-id='" . $row['Sno'] . "'>" . $row['rf_id'] . "</td>";
                    echo "<td contenteditable='true' class='editable' data-column='id_name' data-id='" . $row['Sno'] . "'>" . $row['id_name'] . "</td>";
                    echo "<td contenteditable='true' class='editable' data-column='regd_id' data-id='" . $row['Sno'] . "'>" . $row['regd_id'] . "</td>";
                    echo "<td><button onclick='updateRow(" . $row['Sno'] . ")'>Update</button></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </div>
        <!-- CSV Import Section -->
        <h3>Import CSV File</h3>
    <form action="import_csv.php" method="post" enctype="multipart/form-data">
        <p style="color: red;">Only two columns are accepted: <b>ID Name</b> and <b>Regd ID</b></p>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <button type="submit" name="import">Upload</button>
    </form>
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
    <h2>Update Password</h2></br>

    <!-- Admin Password Update Form -->
    <h3>Admin Password Update</h3>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="user_type" value="admin">
        <input type="hidden" name="department_id" value="admin">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Update Admin Password</button>
    </form>

    <hr></br>

    <!-- Department Password Update Form -->
    <h3>Department Login Password Update</h3>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="user_type" value="department">
        <select name="department_id" required>
            <option value="">Select Department</option>
            <option value="cse">CSE</option>
            <option value="it">IT</option>
            <option value="de">DE</option>
            <option value="ece">ECE</option>
            <option value="eee">EEE</option>
            <option value="mech">MECH</option>
            <option value="mba">MBA</option>
            <option value="civil">CIVIL</option>
            <option value="chem">CHEM</option>
        </select>
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Update Department Password</button>
    </form>
    <hr></br>

     <!-- Admin Password Update Form -->
     <h3>security Password Update</h3>
    <form action="update_password.php" method="POST">
        <input type="hidden" name="user_type" value="security">
        <input type="hidden" name="department_id" value="security">
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <button type="submit">Update Security Password</button>
    </form>

    <hr>
  </div>

  <script>

function openPopup(id) {
    document.querySelectorAll('.popup').forEach(popup => popup.classList.remove("show"));
    document.getElementById(id).classList.add("show");
}

function closePopup(id) {
    document.getElementById(id).classList.remove("show");
}


function showAddStudentForm() {
        openPopup("add-student-popup");
    }


function filterTable_1() {
    let input = document.getElementById("searchInput").value.toUpperCase();
    let table = document.getElementById("idTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let tdArray = tr[i].getElementsByTagName("td");
        let match = false;

        for (let j = 0; j < tdArray.length - 1; j++) { // Exclude action column
            if (tdArray[j] && tdArray[j].innerText.toUpperCase().includes(input)) {
                match = true;
                break;
            }
        }
        tr[i].style.display = match ? "" : "none";
    }
}

function updateRow(sno) {
    let row = document.querySelector(`td[data-id='${sno}']`).parentNode;
    let rf_id = row.cells[1].innerText;
    let id_name = row.cells[2].innerText;
    let regd_id = row.cells[3].innerText;

    let formData = new FormData();
    formData.append("sno", sno);
    formData.append("rf_id", rf_id);
    formData.append("id_name", id_name);
    formData.append("regd_id", regd_id);

    fetch("update_id_card.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Show success/failure message
    })
    .catch(error => console.error("Error:", error));
}

  </script>

</body>
</html>
