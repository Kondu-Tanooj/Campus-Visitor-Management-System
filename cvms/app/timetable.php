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
    $_SESSION['selected_section'] = $_POST['section'];  // Store selected section

    // Table name includes semester & section
    $tableName = $_SESSION['username'] . "_" . $_SESSION['selected_semester'] . "_" . $_SESSION['selected_section'] . "_timetable";

    // ✅ Create table if not exists
    $createTableSQL = "CREATE TABLE IF NOT EXISTS $tableName (
        day VARCHAR(10) PRIMARY KEY,
        hour1 VARCHAR(50) DEFAULT 'CLASS',
        hour2 VARCHAR(50) DEFAULT 'CLASS',
        hour3 VARCHAR(50) DEFAULT 'CLASS',
        hour4 VARCHAR(50) DEFAULT 'CLASS',
        hour5 VARCHAR(50) DEFAULT 'CLASS',
        hour6 VARCHAR(50) DEFAULT 'CLASS'
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

// ✅ Handle timetable update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update'])) {
    $tableName = $_SESSION['username'] . "_" . $_SESSION['selected_semester'] . "_" . $_SESSION['selected_section'] . "_timetable";
    // $days = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];


    $stmt = $conn->prepare("UPDATE $tableName SET hour1=?, hour2=?, hour3=?, hour4=?, hour5=?, hour6=? WHERE day=?");

    foreach ($days as $day) {
        $hour1 = $_POST["{$day}_hour1"] ?? "CLASS";
        $hour2 = $_POST["{$day}_hour2"] ?? "CLASS";
        $hour3 = $_POST["{$day}_hour3"] ?? "CLASS";
        $hour4 = $_POST["{$day}_hour4"] ?? "CLASS";
        $hour5 = $_POST["{$day}_hour5"] ?? "CLASS";
        $hour6 = $_POST["{$day}_hour6"] ?? "CLASS";
        
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Timetable</title>
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
        .container {
            width: 95%;
            max-width: 1200px;
            background: rgba(153, 153, 153, 0.65);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            gap: 20px;
            text-align: center;
        }
        select, button {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
            background: white;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background: #28a745;
            color: white;
        }
        .btn {
            padding: 10px 15px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background: darkgreen;
        }
        .back-btn {
            position: absolute;
            top: 10px;
            left: 10px;
            background: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            border-radius: 5px;
        }
        .back-btn:hover {
            background: darkred;
        }
        select {
            width: 150px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Back Button -->
    <button onclick="window.location.href = 'sub&time.php'" class="back-btn">← Back</button>

    <h2>Select Semester</h2>
    <form method="POST">
        <!-- Semester Dropdown -->
        <select name="semester" required>
            <option disabled selected>-- Select Semester --</option>
            <?php foreach ($semesterOptions as $sem) : ?>
                <option value="<?= $sem ?>" <?= isset($_SESSION['selected_semester']) && $_SESSION['selected_semester'] == $sem ? 'selected' : '' ?>>
                    <?= $sem ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Section Dropdown -->
        <select name="section" required>
            <option disabled selected>-- Select Section --</option>
            <?php foreach ($sectionOptions as $sec) : ?>
                <option value="<?= $sec ?>" <?= isset($_SESSION['selected_section']) && $_SESSION['selected_section'] == $sec ? 'selected' : '' ?>>
                    <?= $sec ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="show" class="btn">Show Timetable</button>
    </form>

    <?php if (isset($_SESSION['selected_semester']) && isset($_SESSION['selected_section'])) : ?>
    <h2>Update Timetable for <?= $_SESSION['selected_semester'] ?>-Semester, Section <?= $_SESSION['selected_section'] ?></h2>
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
                            <td>
                                <select name="<?= $day ?>_hour<?= $i ?>">
                                    <option value="CLASS" <?= isset($timetable[$day]) && $timetable[$day]["hour$i"] == "CLASS" ? "selected" : "" ?>>CLASS</option>
                                    <option value="NO CLASS" <?= isset($timetable[$day]) && $timetable[$day]["hour$i"] == "NO CLASS" ? "selected" : "" ?>>NO CLASS</option>
                                </select>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endforeach; ?>
            </table>
            <br>
            <button type="submit" name="update" class="btn">Update Timetable</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
