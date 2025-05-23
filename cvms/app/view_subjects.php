<?php
session_start();
include 'config.php';
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.html"); 
    exit();
}
// ✅ Fetch available semesters from 'semester_hour' table
$semesterOptions = [];
$semQuery = "SELECT DISTINCT semester FROM semester_hour";
$result = $conn->query($semQuery);
while ($row = $result->fetch_assoc()) {
    $semesterOptions[] = $row['semester'];
}

// ✅ Fetch available sections from 'department_sections' table
$username = $_SESSION['username'];
$sectionOptions = [];
$sectionQuery = "SELECT DISTINCT section FROM department_sections WHERE department='$username'";
$result = $conn->query($sectionQuery);
while ($row = $result->fetch_assoc()) {
    $sectionOptions[] = $row['section'];
}

// ✅ Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['show'])) {
    $_SESSION['selected_semester'] = $_POST['semester'];
    $_SESSION['selected_section'] = $_POST['section'];

    // Updated table name format
    $tableName = $_SESSION['username'] . "_" . $_SESSION['selected_semester'] . "_" . $_SESSION['selected_section'] . "_subject_timetable";

    // ✅ Create table if not exists
    $createTableSQL = "CREATE TABLE IF NOT EXISTS $tableName (
        day VARCHAR(10) PRIMARY KEY,
        hour1 VARCHAR(50) DEFAULT NULL,
        hour2 VARCHAR(50) DEFAULT NULL,
        hour3 VARCHAR(50) DEFAULT NULL,
        hour4 VARCHAR(50) DEFAULT NULL,
        hour5 VARCHAR(50) DEFAULT NULL,
        hour6 VARCHAR(50) DEFAULT NULL
    )";
    $conn->query($createTableSQL);

    // Allowed days
    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    // ✅ Ensure each day exists in the table
    foreach ($days as $day) {
        $checkExistSQL = "SELECT day FROM $tableName WHERE day='$day'";
        $result = $conn->query($checkExistSQL);
        if ($result->num_rows === 0) {
            $insertSQL = "INSERT INTO $tableName (day) VALUES ('$day')";
            $conn->query($insertSQL);
        }
    }

    // ✅ Fetch existing timetable
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);
    $timetable = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $timetable[$row['day']] = $row;
        }
    }
}

// ✅ Handle CSV Import
if (isset($_POST['import'])) {
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if ($handle !== false) {
            fgetcsv($handle); // Skip header row

            // Ensure table name is generated correctly
            $tableName = $_SESSION['username'] . "_" . $_SESSION['selected_semester'] . "_" . $_SESSION['selected_section'] . "_subject_timetable";

            // ✅ Check if table exists before inserting data
            $checkTableSQL = "SHOW TABLES LIKE '$tableName'";
            $tableExists = $conn->query($checkTableSQL);
            if ($tableExists->num_rows == 0) {
                echo "<script>alert('Error: Timetable table does not exist. Select semester and section first!');</script>";
                exit();
            }

            $successCount = 0;
            $errorCount = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // ✅ Trim values to remove spaces and set empty values as NULL
                $day = trim($data[0]);
                $hour1 = !empty(trim($data[1])) ? trim($data[1]) : NULL;
                $hour2 = !empty(trim($data[2])) ? trim($data[2]) : NULL;
                $hour3 = !empty(trim($data[3])) ? trim($data[3]) : NULL;
                $hour4 = !empty(trim($data[4])) ? trim($data[4]) : NULL;
                $hour5 = !empty(trim($data[5])) ? trim($data[5]) : NULL;
                $hour6 = !empty(trim($data[6])) ? trim($data[6]) : NULL;

                // ✅ Debugging: Print each row to check data
                // echo "Day: $day, Hour1: $hour1, Hour2: $hour2, ...<br>";

                // ✅ Ensure that day exists in DB before updating
                $checkDaySQL = "SELECT COUNT(*) FROM $tableName WHERE day=?";
                $stmtCheck = $conn->prepare($checkDaySQL);
                $stmtCheck->bind_param("s", $day);
                $stmtCheck->execute();
                $stmtCheck->bind_result($rowCount);
                $stmtCheck->fetch();
                $stmtCheck->close();

                if ($rowCount > 0) {
                    // ✅ Update only if the day exists in DB
                    $sql = "UPDATE $tableName SET 
                            hour1=?, hour2=?, hour3=?, hour4=?, hour5=?, hour6=? 
                            WHERE day=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssss", $hour1, $hour2, $hour3, $hour4, $hour5, $hour6, $day);

                    if ($stmt->execute()) {
                        $successCount++;
                    } else {
                        $errorCount++;
                    }
                } else {
                    echo "<script>alert('Warning: Day \"$day\" not found in DB. Check CSV format.');</script>";
                    $errorCount++;
                }
            }
            fclose($handle);

            // ✅ Display Success or Error Alert
            if ($successCount > 0) {
                echo "<script>alert('CSV Data Imported Successfully! Updated $successCount rows.');</script>";
            }
            if ($errorCount > 0) {
                echo "<script>alert('Some rows failed to update: $errorCount errors.');</script>";
            }
        } else {
            echo "<script>alert('Error opening CSV file.');</script>";
        }
    } else {
        echo "<script>alert('No file selected or upload error.');</script>";
    }
}


// ✅ Handle timetable update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $tableName = $_SESSION['username'] . "_" . $_SESSION['selected_semester'] . "_" . $_SESSION['selected_section'] . "_subject_timetable";
    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

    $stmt = $conn->prepare("UPDATE $tableName SET hour1=?, hour2=?, hour3=?, hour4=?, hour5=?, hour6=? WHERE day=?");

    foreach ($days as $day) {
        $hour1 = $_POST["{$day}_hour1"] ?? NULL;
        $hour2 = $_POST["{$day}_hour2"] ?? NULL;
        $hour3 = $_POST["{$day}_hour3"] ?? NULL;
        $hour4 = $_POST["{$day}_hour4"] ?? NULL;
        $hour5 = $_POST["{$day}_hour5"] ?? NULL;
        $hour6 = $_POST["{$day}_hour6"] ?? NULL;
        
        $stmt->bind_param("sssssss", $hour1, $hour2, $hour3, $hour4, $hour5, $hour6, $day);
        $stmt->execute();
    }

    $stmt->close();
    echo "<script>alert('Timetable updated successfully!'); window.location.href='".$_SERVER['PHP_SELF']."';</script>";
    exit();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Timetable</title>
    <style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

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
    position: relative;
    overflow: hidden;
}

body::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.6);
    z-index: -1;
}

/* Glassmorphism Effect */
.container {
    width: 95%;
    max-width: 1500px;
    background: rgba(255, 255, 255, 0.15);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(12px);
    text-align: center;
    color: white;
    overflow: hidden;
    max-height: 85vh; /* Increased height to fit the table */
}

/* Back Button (Top Left) */
.back-btn {
    position: absolute;
    top: 15px;
    left: 15px;
    background: red;
    color: white;
    padding: 10px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
    transition: 0.3s ease-in-out;
}

.back-btn:hover {
    background: darkred;
}

/* Dropdowns and Buttons */
select, button {
    padding: 12px;
    margin: 8px;
    font-size: 16px;
    border-radius: 8px;
    border: none;
    outline: none;
    transition: 0.3s ease-in-out;
}

/* Dropdown Fix: Ensure text is visible */
select {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    border: 1px solid white;
    appearance: none;
    padding-right: 30px;
    cursor: pointer;
}

select option {
    background: white;
    color: black;
}

/* Button Styling */
button {
    background: #28a745;
    color: white;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #1e7e34;
}

/* Table Container */
.table-container {
    max-height: 70vh; /* Increased height to fit the table */
    overflow-y: auto;
    margin-top: 20px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.9);
    padding: 10px;
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.9);
}

th, td {
    padding: 15px;
    border: 1px solid #ddd;
    text-align: center;
    color: black;
    white-space: nowrap;
}

th {
    background: #28a745;
    color: white;
}

tr:nth-child(even) {
    background: rgba(0, 0, 0, 0.05);
}

tr:hover {
    background: rgba(40, 167, 69, 0.1);
}

/* Responsive Adjustments */
@media screen and (max-width: 768px) {
    .container {
        width: 90%;
        padding: 15px;
    }

    th, td {
        padding: 10px;
        font-size: 14px;
    }

    select, button {
        padding: 10px;
        font-size: 14px;
    }

    .table-container {
        overflow-x: auto;
    }
}


    </style>
</head>
<body>
<button class="back-btn" onclick="window.location.href = 'sub&time.php'">⬅ Back</button>

<div class="container">
    <h2>Select Semester</h2>
    <form method="POST">
        <select name="semester" required>
            <option disabled selected>-- Select Semester --</option>
            <?php foreach ($semesterOptions as $sem) : ?>
                <option value="<?= $sem ?>" <?= isset($_SESSION['selected_semester']) && $_SESSION['selected_semester'] == $sem ? 'selected' : '' ?>>
                    <?= $sem ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="section" required>
            <option disabled selected>-- Select Section --</option>
            <?php foreach ($sectionOptions as $sec) : ?>
                <option value="<?= $sec ?>" <?= isset($_SESSION['selected_section']) && $_SESSION['selected_section'] == $sec ? 'selected' : '' ?>>
                    <?= $sec ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="show">Show Timetable</button>
    </form>

    <?php if (isset($_SESSION['selected_semester']) && isset($_SESSION['selected_section'])) : ?>
        <h2>Update Timetable</h2>
        <form method="POST">
            <table>
                <tr>
                    <th>Day</th>
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
                    <th>Hour 6</th>
                </tr>
                <?php foreach ($days as $day) : ?>
                    <tr>
                        <td><?= $day ?></td>
                        <?php for ($i = 1; $i <= 6; $i++) : ?>
                            <td><input type="text" name="<?= $day ?>_hour<?= $i ?>" value="<?= $timetable[$day]["hour$i"] ?? '' ?>"></td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button type="submit" name="update">Update Timetable</button>
        </form>

        <!-- CSV Import Form -->
        <h2>Import Timetable (CSV)</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv" required>
            <button type="submit" name="import">Import</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
