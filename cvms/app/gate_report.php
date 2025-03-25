<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../index.html');
    exit();
}

// Database connection
include 'config.php';

// Fetch distinct filter options
if ($_SESSION['username'] == "admin") {
    $username='admin';
    $departments = $conn->query("SELECT DISTINCT department FROM main_entry_table")->fetch_all(MYSQLI_ASSOC);
} else {
    $username = $conn->real_escape_string($_SESSION['username']); // Prevent SQL injection
    $departments = $conn->query("SELECT DISTINCT department FROM main_entry_table WHERE department = '$username'")->fetch_all(MYSQLI_ASSOC);
}
$semesters = $conn->query("SELECT DISTINCT semester FROM main_entry_table")->fetch_all(MYSQLI_ASSOC);
$batches = $conn->query("SELECT DISTINCT batch FROM main_entry_table")->fetch_all(MYSQLI_ASSOC);


// Get today's date
$today = date('Y-m-d');

// Get the earliest IN_Time from the entry table
$earliest_in_time_query = "SELECT MIN(in_time) AS earliest_in_time FROM main_entry_table";
$earliest_in_time_result = $conn->query($earliest_in_time_query);
$earliest_in_time_row = $earliest_in_time_result->fetch_assoc();
$earliest_in_time = $earliest_in_time_row['earliest_in_time'] ?? $today;
// Build the SQL query with filtering logic
$filters = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['department'])) {
        if ($_SESSION['username'] == "admin") {
            $filters[] = "d.department = '" . $conn->real_escape_string($_POST['department']) . "'";
        } else {
            $filters[] = "d.department LIKE '%" . $username . "%'";// Non-admins can only see their own department
        }
    }
    if (!empty($_POST['batch'])) {
        $filters[] = "d.batch = '" . $conn->real_escape_string($_POST['batch']) . "'";
    }
    if (!empty($_POST['regd_id'])) {
        $filters[] = "d.regd_no LIKE '%" . $conn->real_escape_string($_POST['regd_id']) . "%'";
    }
    if (!empty($_POST['semester'])) {
        $filters[] = "d.semester = '" . $conn->real_escape_string($_POST['semester']) . "'";
    }
    if (!empty($_POST['in_time'])) {
        $filters[] = "d.in_time >= '" . $conn->real_escape_string($_POST['in_time']) . "'";
    }
    if (!empty($_POST['out_time'])) {
        $filters[] = "d.out_time <= '" . $conn->real_escape_string($_POST['out_time']) . "'";
    }
}
// If the user is admin, show all data, else restrict by department
if ($_SESSION['username'] === 'admin') {
    $whereClause = count($filters) > 0 ? 'WHERE ' . implode(' AND ', $filters) : 'WHERE 1=1';
} else {
    $filters[] = "d.department = '" . $conn->real_escape_string($_SESSION['username']) . "'"; // Restrict by department for non-admins
    $whereClause = 'WHERE ' . implode(' AND ', $filters);
}

$sql = "
SELECT
    d.regd_no AS Register_Number,
    d.department,
    d.semester,
    d.batch,
    d.in_time,
    d.out_time
FROM
    main_entry_table d
$whereClause
ORDER BY
    d.in_time DESC";

$result = $conn->query($sql);

// Handle CSV download
if (isset($_POST['download_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=visitor_report.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Register Number', 'Department', 'Semester', 'Batch', 'In Time', 'Out Time'));

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }
    }

    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visitor Report</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            min-height: 100vh;
            width: 100%;
            background: #f7f7f7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        h1 {
            margin: 20px 0;
            font-size: 36px;
            color: #333;
        }
        form {
            margin: 20px auto;
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        label {
            margin-right: 10px;
            font-size: 16px;
        }
        input, select, button {
            padding: 10px;
            margin: 10px 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        input:focus, select:focus {
            border-color: #007bff;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 20px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .logout-container {
            width: 90%;
            text-align: left;
            margin: 20px;
        }
        .logout {
            background-color: #c00000;
            border-radius: 100px;
            color: white;
            padding: 10px 30px;
            text-align: center;
            text-decoration: none;
            font-size: 20px;
            cursor: pointer;
            transition: all 250ms ease-in-out;
            border: none;
        }
        .logout:hover {
            background-color: #ff1c1c;
        }
    </style>
</head>
<body>

    <div class="logout-container">
        <?php if ($_SESSION['username'] == "admin"): ?>
            <a href="admin.php"><button class="logout"><b><-- BACK</b></button></a>
        <?php else: ?>
            <a href="dept.php"><button class="logout"><b><-- BACK</b></button></a>
        <?php endif; ?>
    </div>

    <h1>COllege Gate Entry Report</h1>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="regd_id" placeholder="Search for ID...">

        <label for="department">Department:</label>
        <select id="department" name="department">
            <option value="">All Departments</option>
            <?php foreach ($departments as $dept): ?>
                <option value="<?= htmlspecialchars($dept['department']) ?>">
                    <?= htmlspecialchars($dept['department']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="batch">Batch:</label>
        <select id="batch" name="batch">
            <option value="">All Batches</option>
            <?php foreach ($batches as $batch): ?>
                <option value="<?= htmlspecialchars($batch['batch']) ?>">
                    <?= htmlspecialchars($batch['batch']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="in_time">In Time:</label>
        <input type="date" id="in_time" name="in_time" value="<?= htmlspecialchars($earliest_in_time) ?>">

        <label for="out_time">Out Time:</label>
        <input type="date" id="out_time" name="out_time" value="<?= htmlspecialchars($today) ?>">

        <button type="submit" name="filter">Filter</button>
        <button type="submit" name="download_csv">Download CSV</button>
    </form>

    <table id="dataTable">
        <tr>
            <th>Register Number</th>
            <th>Department</th>
            <th>Batch</th>
            <th>In Time</th>
            <th>Out Time</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['Register_Number']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['batch']) ?></td>
                    <td><?= htmlspecialchars($row['in_time']) ?></td>
                    <td><?= htmlspecialchars($row['out_time']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No results found</td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>

<?php
$conn->close();
?>