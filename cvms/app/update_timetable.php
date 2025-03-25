<?php
session_start();
include 'config.php';

$username = $_SESSION['username'];

// Fetch available semesters and sections dynamically
$semesters = range(1, 8);
$sectionQuery = "SELECT DISTINCT section FROM department_sections WHERE department='$username'";
$sectionResult = $conn->query($sectionQuery);

$sections = [];
while ($row = $sectionResult->fetch_assoc()) {
    $sections[] = $row['section'];
}

// Handle timetable update
if (isset($_POST['update'])) {
    $semester = $_POST['semester'];
    $section = $_POST['section'];

    $tableName = "${username}_${semester}_${section}_subject_timetable";
    
    foreach ($_POST['timetable'] as $day => $hours) {
        $stmt = $conn->prepare("UPDATE $tableName SET hour1=?, hour2=?, hour3=?, hour4=?, hour5=?, hour6=? WHERE day=?");
        $stmt->bind_param("sssssss", $hours[0], $hours[1], $hours[2], $hours[3], $hours[4], $hours[5], $day);
        $stmt->execute();
        $stmt->close();
    }
    
    echo "<script>alert('Timetable updated successfully!'); window.location.href='update_timetable.php';</script>";
}

// Load existing timetable
$timetable = [];
if (isset($_POST['load_timetable'])) {
    $semester = $_POST['semester'];
    $section = $_POST['section'];

    $tableName = "${username}_${semester}_${section}_subject_timetable";
    
    $query = "SELECT * FROM $tableName";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $timetable[$row['day']] = array_slice($row, 1); // Skip the 'day' column
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Timetable</title>
</head>
<body>
    <button onclick="history.back()">Go Back</button>
    <h2>Update Timetable</h2>

    <form method="POST">
        <label for="semester">Select Semester:</label>
        <select name="semester" required>
            <option value="" disabled selected>Select Semester</option>
            <?php foreach ($semesters as $sem) { ?>
                <option value="<?php echo $sem; ?>" <?php if (isset($semester) && $semester == $sem) echo 'selected'; ?>>
                    <?php echo $sem . " Semester"; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <label for="section">Select Section:</label>
        <select name="section" required>
            <option value="" disabled selected>Select Section</option>
            <?php foreach ($sections as $sec) { ?>
                <option value="<?php echo $sec; ?>" <?php if (isset($section) && $section == $sec) echo 'selected'; ?>>
                    <?php echo $sec; ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <button type="submit" name="load_timetable">Show Timetable</button>
    </form>

    <?php if (!empty($timetable)) { ?>
        <form method="POST">
            <input type="hidden" name="semester" value="<?php echo $semester; ?>">
            <input type="hidden" name="section" value="<?php echo $section; ?>">

            <table border="1">
                <tr>
                    <th>Day</th>
                    <th>Hour 1</th>
                    <th>Hour 2</th>
                    <th>Hour 3</th>
                    <th>Hour 4</th>
                    <th>Hour 5</th>
                    <th>Hour 6</th>
                </tr>
                <?php foreach ($timetable as $day => $hours) { ?>
                    <tr>
                        <td><?php echo $day; ?></td>
                        <?php for ($i = 0; $i < 6; $i++) { ?>
                            <td><input type="text" name="timetable[<?php echo $day; ?>][]" value="<?php echo $hours[$i]; ?>"></td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </table>
            <br>
            <button type="submit" name="update">Update Timetable</button>
        </form>
    <?php } ?>
</body>
</html>
