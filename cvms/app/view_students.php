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
    $departments = $conn->query("SELECT DISTINCT department FROM student_details")->fetch_all(MYSQLI_ASSOC);
} else {
    $username = $conn->real_escape_string($_SESSION['username']); // Prevent SQL injection
    $departments = $conn->query("SELECT DISTINCT department FROM student_details WHERE department = '$username'")->fetch_all(MYSQLI_ASSOC);
}
$semesters = $conn->query("SELECT DISTINCT semester FROM student_details")->fetch_all(MYSQLI_ASSOC);
$batches = $conn->query("SELECT DISTINCT batch_no FROM student_details")->fetch_all(MYSQLI_ASSOC);
$sections = $conn->query("SELECT DISTINCT section FROM student_details")->fetch_all(MYSQLI_ASSOC);
$allowed = $conn->query("SELECT DISTINCT allowed FROM student_details")->fetch_all(MYSQLI_ASSOC);

// Build the SQL query with filtering logic
$filters = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['name'])) {
        $filters[] = "d.name LIKE '%" . $conn->real_escape_string($_POST['name']) . "%'";
    }
    if (!empty($_POST['department'])) {
        if ($_SESSION['username'] == "admin") {
            $filters[] = "d.department = '" . $conn->real_escape_string($_POST['department']) . "'";
        } else {
            $filters[] = "d.department LIKE '%" . $username . "%'";// Non-admins can only see their own department
        }
    }
    if (!empty($_POST['batch_no'])) {
        $filters[] = "d.batch_no = '" . $conn->real_escape_string($_POST['batch_no']) . "'";
    }
    if (!empty($_POST['regd_id'])) {
        $filters[] = "d.regd_id = '" . $conn->real_escape_string($_POST['regd_id']) . "'";
    }
    if (!empty($_POST['semester'])) {
        $filters[] = "d.semester = '" . $conn->real_escape_string($_POST['semester']) . "'";
    }
    if (!empty($_POST['section'])) {
        $filters[] = "d.section = '" . $conn->real_escape_string($_POST['section']) . "'";
    }
    if (!empty($_POST['allowed'])) {
        $filters[] = "d.allowed = '" . $conn->real_escape_string($_POST['allowed']) . "'";
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
    d.regd_id AS Register_Number,
    d.name AS Student_Name,
    d.batch_no,
    d.department,
    d.semester,
    d.section,
    d.allowed
FROM
    student_details d
$whereClause
ORDER BY
    d.regd_id ASC"; // Changed ORDER BY to regd_id (modify as needed)

$result = $conn->query($sql);


// Handle CSV download
if (isset($_POST['download_csv'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=visitor_report.csv');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Register Number', 'Name', 'Batch', 'Department', 'Semester', 'Section'));

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
    <title>Student Details</title>
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

</style>
</head>
<body>
    <h1>Student Details</h1>
    <div class="logout-container">
        <?php if ($_SESSION['username'] == "admin"): ?>
            <a href="admin.php"><button class="logout"><b><-- BACK</b></button></a>
        <?php else: ?>
            <a href="dept.php"><button class="logout"><b><-- BACK</b></button></a>
        <?php endif; ?>
    </div>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Search for names...">

        <label for="batch">Batch:</label>
        <select id="batch" name="batch">
            <option value="">All Batches</option>
            <?php foreach ($batches as $batch): ?>
                <option value="<?= htmlspecialchars($batch['BATCH']) ?>">
                    <?= htmlspecialchars($batch['BATCH']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="department">Department:</label>
        <select id="department" name="department">
            <?php if ($_SESSION['username'] == "admin"): ?>
                <option value="">All Departments</option>
                <?php foreach ($departments as $dept): ?>
                    <option value="<?= htmlspecialchars($dept['department']) ?>">
                        <?= htmlspecialchars($dept['department']) ?>
                    </option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="<?= htmlspecialchars($_SESSION['username']) ?>" selected>
                    <?= htmlspecialchars($_SESSION['username']) ?>
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
            <?php foreach ($Sections as $Section): ?>
                <option value="<?= htmlspecialchars($section['section']) ?>">
                    <?= htmlspecialchars($section['section']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="allowed">Allowed:</label>
        <select id="allowed" name="allowed">
            <option value="">YES/NO</option>
            <?php foreach ($allowed as $allowed): ?>
                <option value="<?= htmlspecialchars($allowed['allowed']) ?>">
                    <?= htmlspecialchars($allowed['allowed']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="filter">Filter</button>
        <button type="submit" name="download_csv">Download CSV</button>
    </form>

        <div style="display:flex">
        <button class="promote-btn" onclick="promoteBatch()">Promote</button>

        <button class="promote-btn" onclick="deleteBatch()">Delete</button>

        <button class="promote-btn" onclick="window.location.href='permission.php'">Permission</button>
        </div>
        <div class="std-table">
        <table id="dataTable" border="1">
            <tr>
                <th>Register Number</th>
                <th>Name</th>
                <th>Batch</th>
                <th>Department</th>
                <th>Semester</th>
                <th>Section</th>
                <th>Allowed</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['Register_Number']) ?></td>
                        <td><?= htmlspecialchars($row['Student_Name']) ?></td>
                        <td><?= htmlspecialchars($row['batch_no']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['semester']) ?></td>
                        <td><?= htmlspecialchars($row['section']) ?></td>
                        <td><?= htmlspecialchars($row['allowed']) ?></td>
                        <td>
                            <button onclick="openEditModal(
                                '<?= htmlspecialchars($row['Register_Number']) ?>',
                                '<?= htmlspecialchars($row['Student_Name']) ?>',
                                '<?= htmlspecialchars($row['batch_no']) ?>',
                                '<?= htmlspecialchars($row['department']) ?>',
                                '<?= htmlspecialchars($row['semester']) ?>',
                                '<?= htmlspecialchars($row['section']) ?>',
                                '<?= htmlspecialchars($row['allowed']) ?>'
                            )">Update</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No results found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <form id="editForm" action="update_student.php" method="POST">
                <input type="hidden" id="editRegdId" name="regd_id">
                <label for="editName">Name:</label>
                <input type="text" id="editName" name="name">
                <label for="editBatch">Batch:</label>
                <input type="text" id="editBatch" name="batch_no">
                <label for="editDepartment">Department:</label>
                <input type="text" id="editDepartment" name="department">
                <label for="editSemester">Semester:</label>
                <input type="text" id="editSemester" name="semester">
                <label for="editSection">Section:</label>
                <input type="text" id="editSection" name="section">
                <label for="editAllowed">Allowed:</label>
                <input type="text" id="editAllowed" name="allowed">
                <button type="submit">Save</button>
                <button type="button" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>
    <script>
        function promoteBatch() {
            let batch = prompt("Enter Batch:");
            let semester = prompt("Enter Current Semester:");

            if (batch && semester) {
                    
                    // Send data to PHP backend
                    let formData = new FormData();
                    formData.append("batch", batch);
                    formData.append("semester", semester);

                    fetch("promote_batch.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        alert(data); // Show success or error message
                        window.location.href = "view_students.php";
                    })
                    .catch(error => {
                        alert("Error updating semester: " + error);
                    });

            } else {
                alert("Batch or Semester cannot be empty!");
            }
        }

        function deleteBatch() {
        let batch = prompt("Enter Register Number to Delete:");

        if (batch) {
            if (confirm(`Are you sure you want to delete : ${batch}? This action cannot be undone.`)) {
                let formData = new FormData();
                formData.append("batch", batch);

                fetch("delete_regd_id.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    window.location.href = "view_students.php";
                })
                .catch(error => {
                    alert("Error deleting batch: " + error);
                });
            }
        } else {
            alert("Batch cannot be empty!");
        }
    }
    function openEditModal(regdId, name, batch, department, semester, section, allowed) {
            document.getElementById('editRegdId').value = regdId;
            document.getElementById('editName').value = name;
            document.getElementById('editBatch').value = batch;
            document.getElementById('editDepartment').value = department;
            document.getElementById('editSemester').value = semester;
            document.getElementById('editSection').value = section;
            document.getElementById('editAllowed').value = allowed;
            document.getElementById('editModal').style.display = 'flex';
        }

        // Function to close the edit modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal if clicked outside the modal content
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
    </script>

</body>
</html>

<?php
$conn->close();
?>
