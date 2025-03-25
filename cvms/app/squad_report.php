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
$username = $_SESSION['username'];
$isAdmin = ($username === 'admin');

// Fetch departments, semesters, and sections
$departments = [];
$semesters = [];
$sections = [];

if ($isAdmin) {
    $departments = $conn->query("SELECT DISTINCT department FROM report_dev_entry_table")->fetch_all(MYSQLI_ASSOC);
} else {
    $stmt = $conn->prepare("SELECT DISTINCT department FROM report_dev_entry_table WHERE department = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $departments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$semesters = $conn->query("SELECT DISTINCT semester FROM report_dev_entry_table")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT DISTINCT section FROM report_dev_entry_table")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT DISTINCT subject FROM report_dev_entry_table")->fetch_all(MYSQLI_ASSOC);

// Get today's date
$today = date('Y-m-d');

// Get the earliest IN_Time from the entry table
$earliest_in_time_query = "SELECT MIN(time) AS earliest_in_time FROM report_dev_entry_table";
$earliest_in_time_result = $conn->query($earliest_in_time_query);
$earliest_in_time = $earliest_in_time_result->fetch_assoc()['earliest_in_time'] ?? $today;

// Build the SQL query with filtering logic
$filters = [];
$params = [];
$types = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['regd_id'])) {
        $filters[] = "d.regd_id LIKE ?";
        $params[] = '%' . $conn->real_escape_string($_POST['regd_id']) . '%';
        $types .= 's';
    }
    if (!empty($_POST['department'])) {
        $filters[] = "d.department = ?";
        $params[] = $conn->real_escape_string($_POST['department']);
        $types .= 's';
    }
    if (!empty($_POST['semester'])) {
        $filters[] = "d.semester = ?";
        $params[] = $conn->real_escape_string($_POST['semester']);
        $types .= 's';
    }
    if (!empty($_POST['section'])) {
        $filters[] = "d.section = ?";
        $params[] = $conn->real_escape_string($_POST['section']);
        $types .= 's';
    }
    if (!empty($_POST['subject'])) {
        $filters[] = "d.subject = ?";
        $params[] = $conn->real_escape_string($_POST['subject']);
        $types .= 's';
    }
    if (!empty($_POST['in_time'])) {
        $filters[] = "d.time >= ?";
        $params[] = $conn->real_escape_string($_POST['in_time']);
        $types .= 's';
    }
    if (!empty($_POST['out_time'])) {
        $filters[] = "d.time <= ?";
        $params[] = $conn->real_escape_string($_POST['out_time']);
        $types .= 's';
    }
}

// Restrict by department for non-admins
if (!$isAdmin) {
    $filters[] = "d.department = ?";
    $params[] = $username;
    $types .= 's';
}

$whereClause = $filters ? 'WHERE ' . implode(' AND ', $filters) : '';

// Prepare and execute the main query
$sql = "
    SELECT
        d.regd_id AS Register_Number,
        d.department,
        d.semester,
        d.section,
        d.subject,
        d.time
    FROM
        report_dev_entry_table d
    $whereClause
    ORDER BY
        d.regd_id ASC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Handle CSV download
if (isset($_POST['download_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=visitor_report.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Register Number', 'Department', 'Semester', 'Section', 'Subject', 'Time'));

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
    <title>College Squad Report</title>
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
        background: url(images/clg.jpg) no-repeat center center;
        background-size: cover;
        overflow: hidden;
        position: relative;
        justify-content: center;
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
        margin-top: 1rem;
        font-size: 36px;
        color: #FF0000;
        text-align: center;
    }
    form {
        margin: 20px auto;
        text-align: center;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 1200px;
    }
    label {
        margin-right: 10px;
        font-size: 16px;
    }
    input, select, button {
        padding: 8px;
        margin: 8px 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    input:focus, select:focus {
        border-color: #007bff;
        outline: none;
    }
    button {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 14px;
    }
    button:hover {
        background-color: #0056b3;
    }
    table {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        border-collapse: collapse;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    th, td {
        padding: 10px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        font-size: 14px;
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
        max-width: 1200px;
        text-align: left;
        margin: 20px 0;
    }
    .logout {
        background-color: #c00000;
        border-radius: 100px;
        color: white;
        padding: 8px 20px;
        text-align: center;
        text-decoration: none;
        font-size: 16px;
        cursor: pointer;
        transition: all 250ms ease-in-out;
        border: none;
    }
    .logout:hover {
        background-color: #ff1c1c;
    }
    .promote-btn {
        background-color: red;
        color: white;
        padding: 8px 16px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        border-radius: 5px;
        margin: 8px;
    }
    .promote-btn:hover {
        background-color: darkred;
    }
    .std-table {
        width: 90%;
        max-width: 1200px;
        margin: 20px auto;
        overflow-x: auto;
    }
    .std-table table {
        width: 100%;
    }
    .std-table th, .std-table td {
        padding: 8px 12px;
    }
    .std-table input[type="text"] {
        width: 100%;
        padding: 6px;
        box-sizing: border-box;
        font-size: 14px;
    }
    .std-table button {
        padding: 6px 10px;
        font-size: 12px;
    }
    .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        .modal-content input {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .modal-content button {
            padding: 8px 16px;
            margin: 8px 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .modal-content button[type="submit"] {
            background-color: #007bff;
            color: white;
        }
        .modal-content button[type="button"] {
            background-color: #ccc;
            color: black;
        }
        .table-container {
    width: 90%;
    max-width: 1200px;
    margin: 20px auto;
    border-radius: 10px;
    overflow-x: auto; /* Enable horizontal scrolling */
    overflow-y: auto; /* Enable vertical scrolling */
    max-height: 400px; /* Set a max height for vertical scrolling */
    background: #fff;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.table-container table {
    width: 100%;
    border-collapse: collapse;
}

.table-container th, .table-container td {
    padding: 10px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    font-size: 14px;
}

.table-container th {
    background-color: #007bff;
    color: white;
    position: sticky;
    top: 0;
    z-index: 2;
}

    </style>
</head>
<body>
    <h1>College Squad Report</h1>
    <div class="logout-container">
        <a href="<?= $isAdmin ? 'admin.php' : 'dept.php' ?>"><button class="logout"><b><-- BACK</b></button></a>
    </div>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="regd_id" placeholder="Search for ID...">

        <label for="department">Department:</label>
        <select id="department" name="department">
            <?php if ($isAdmin): ?>
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept['department']) ?>">
                        <?= htmlspecialchars($dept['department']) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="<?= htmlspecialchars($username) ?>" selected>
                    <?= htmlspecialchars($username) ?>
                </option>
            <?php endif; ?>
        </select>

        <label for="semester">Semester:</label>
        <select id="semester" name="semester">
            <option value="">All Semesters</option>
            <?php foreach ($semesters as $semester): ?>
                <option value="<?= htmlspecialchars($semester['semester']) ?>">
                    <?= htmlspecialchars($semester['semester']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="section">Section:</label>
        <select id="section" name="section">
            <option value="">All Sections</option>
            <?php foreach ($sections as $section): ?>
                <option value="<?= htmlspecialchars($section['section']) ?>">
                    <?= htmlspecialchars($section['section']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="subject">Subject:</label>
        <select id="subject" name="subject">
            <option value="">All Subjects</option>
            <?php foreach ($subjects as $subject): ?>
                <option value="<?= htmlspecialchars($subject['subject']) ?>">
                    <?= htmlspecialchars($subject['subject']) ?>
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

    <div class="table-container">
        <table id="dataTable" border="1">
            <tr>
                <th>Register Number</th>
                <th>Department</th>
                <th>Semester</th>
                <th>Section</th>
                <th>Subject</th>
                <th>Time</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Register_Number']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['semester']) ?></td>
                        <td><?= htmlspecialchars($row['section']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No results found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>