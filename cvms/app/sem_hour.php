<?php
session_start();
include 'config.php';
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.html"); 
    exit();
}

// Ensure the table exists
$tableCreateSQL = "CREATE TABLE IF NOT EXISTS semester_hour (
    semester VARCHAR(2) PRIMARY KEY, 
    hour1 VARCHAR(50), 
    hour2 VARCHAR(50), 
    hour3 VARCHAR(50), 
    hour4 VARCHAR(50), 
    hour5 VARCHAR(50), 
    hour6 VARCHAR(50)
)";
$conn->query($tableCreateSQL);

// If form submitted, insert/update semester hours
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $semester = $_POST["semester"];
    $hour1 = $_POST["hour1_start"] . " to " . $_POST["hour1_end"];
    $hour2 = $_POST["hour2_start"] . " to " . $_POST["hour2_end"];
    $hour3 = $_POST["hour3_start"] . " to " . $_POST["hour3_end"];
    $hour4 = $_POST["hour4_start"] . " to " . $_POST["hour4_end"];
    $hour5 = $_POST["hour5_start"] . " to " . $_POST["hour5_end"];
    $hour6 = $_POST["hour6_start"] . " to " . $_POST["hour6_end"];

    // Check if semester already exists
    $checkExistSQL = "SELECT semester FROM semester_hour WHERE semester='$semester'";
    $result = $conn->query($checkExistSQL);

    if ($result->num_rows > 0) {
        // Update existing semester
        $updateSQL = "UPDATE semester_hour 
                      SET hour1=?, hour2=?, hour3=?, hour4=?, hour5=?, hour6=? 
                      WHERE semester=?";
        $stmt = $conn->prepare($updateSQL);
        $stmt->bind_param("sssssss", $hour1, $hour2, $hour3, $hour4, $hour5, $hour6, $semester);
        $stmt->execute();
    } else {
        // Insert new semester hours
        $insertSQL = "INSERT INTO semester_hour (semester, hour1, hour2, hour3, hour4, hour5, hour6) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSQL);
        $stmt->bind_param("sssssss", $semester, $hour1, $hour2, $hour3, $hour4, $hour5, $hour6);
        $stmt->execute();
    }
    $stmt->close();
    header("Location: sem_hour.php"); // Redirect back
    exit();
}

// Fetch existing semester hours
$sql = "SELECT * FROM semester_hour";
$result = $conn->query($sql);
$semester_hours = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $semester_hours[$row['semester']] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Semester Hours</title>
    <style>
        body {
    font-family: 'Poppins', sans-serif;
    background: url('./images/clg.jpg') no-repeat center center fixed;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
body::before {
      content: "";
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0, 0, 0, 0.6);
      z-index: -1;
    }
.container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    width: 95%;
    max-width: 1200px; /* Adjusted width */
    background: rgba(153, 153, 153, 0.65);
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    gap: 20px;
}

/* Form and Table Containers */
.form-container, .table-container {
    width: 48%;
}

h2 {
    color: #333;
    text-align: center;
    margin-bottom: 15px;
}

/* Styling for Form Inputs */
form table {
    width: 100%;
    border-collapse: collapse;
}

form table td {
    padding: 10px;
    vertical-align: middle;
}

/* Input fields */
input[type="time"], input[type="text"], select {
    padding: 8px;
    width: calc(100% - 10px); /* Keep inputs inside the table */
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

input:focus {
    border-color: #007bff;
    outline: none;
}

/* Buttons */
.btn {
    padding: 8px 15px;
    background: green;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
    transition: 0.3s;
    width: auto; /* Button size reduced */
}

.btn:hover {
    background: darkgreen;
}

.btn-back {
    padding: 8px 15px;
    background: #333;
    color: white;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    font-size: 14px;
    transition: 0.3s;
    width: auto; /* Button size reduced */
}

.btn-back:hover {
    background: #555;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
    font-size: 14px;
}

th {
    background: #007bff;
    color: white;
}

tr:nth-child(even) {
    background: #f2f2f2;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        flex-direction: column;
        align-items: center;
    }
    .form-container, .table-container {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <!-- Left Side: Form Section -->
        <div class="form-container">
            <h2>Update Semester Hours</h2>
            <form action="sem_hour.php" method="POST">
                <label>Semester:</label>
                <input type="text" name="semester" required><br><br>

                <table>
                    <tr>
                        <th>Hour</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                    </tr>
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <tr>
                            <td>Hour <?= $i ?></td>
                            <td><input type="time" name="hour<?= $i ?>_start" required></td>
                            <td><input type="time" name="hour<?= $i ?>_end" required></td>
                        </tr>
                    <?php endfor; ?>
                </table>
                <br>
                <button type="submit" class="btn">Save</button>
                <button type="button" class="btn-back" onclick="window.location.href = 'sub&time.php'">Back</button>
            </form>
        </div>

        <!-- Right Side: Table Display -->
        <div class="table-container">
            <h2>Existing Semester Hours</h2>
            <table>
                <tr>
                    <th>Semester</th>
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
                    <th>Hour 6</th>
                </tr>
                <?php foreach ($semester_hours as $sem => $row): ?>
                    <tr>
                        <td><?= $sem ?></td>
                        <td><?= $row['hour1'] ?></td>
                        <td><?= $row['hour2'] ?></td>
                        <td><?= $row['hour3'] ?></td>
                        <td><?= $row['hour4'] ?></td>
                        <td><?= $row['hour5'] ?></td>
                        <td><?= $row['hour6'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
