<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.html"); 
    exit();
}

include 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username']; // Get the logged-in username

$dept_filter = "";
$where_clauses = ["1"]; // Default condition (Always true)

// If not admin, filter by department
if ($username !== "admin") {
    $dept_filter = " AND dept = '".mysqli_real_escape_string($conn, $username)."'";  
}

// Handle Filters
$filter = isset($_GET['filter']) ? mysqli_real_escape_string($conn, $_GET['filter']) : "";
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : "";
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : "";
$start_time = isset($_GET['start_time']) ? $_GET['start_time'] : "";
$end_time = isset($_GET['end_time']) ? $_GET['end_time'] : "";
$by_user = isset($_GET['by_user']) ? mysqli_real_escape_string($conn, $_GET['by_user']) : "";

// Apply Filters
if (!empty($filter)) {
    $where_clauses[] = "(dept LIKE '%$filter%' OR regd_id LIKE '%$filter%')";
}
if (!empty($start_date)) {
    $where_clauses[] = "DATE(start_time) >= '$start_date'";
}
if (!empty($end_date)) {
    $where_clauses[] = "DATE(end_time) <= '$end_date'";
}
if (!empty($start_time)) {
    $where_clauses[] = "TIME(start_time) >= '$start_time'";
}
if (!empty($end_time)) {
    $where_clauses[] = "TIME(end_time) <= '$end_time'";
}
if (!empty($by_user)) {
    $where_clauses[] = "by_user LIKE '%$by_user%'";
}

// Construct Query
$where_sql = implode(" AND ", $where_clauses);
$sql = "SELECT * FROM permission_table WHERE $where_sql $dept_filter";
$result = $conn->query($sql);


// Handle Deletion
if (isset($_GET['delete_sno'])) {
    $sno = $_GET['delete_sno'];
    $conn->query("DELETE FROM permission_table WHERE sno = '$sno'");
    header("Location: permission.php");
    exit();
}

// Handle Adding New Entry
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_entry'])) {
    $regd_id = $_POST['regd_id'];
    $dept = $_POST['dept'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $by_user = $_POST['by_user'];

    $conn->query("INSERT INTO permission_table (regd_id, dept, start_time, end_time, by_user) 
                  VALUES ('$regd_id', '$dept', '$start_time', '$end_time', '$by_user')");
    header("Location: permission.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permission Data</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
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
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            box-shadow: var(--box-shadow);
            border-radius: var(--border-radius);
        }
        
        h1 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
        }
        
        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: end;
        }
        
        .filter-form button[type="submit"] {
            grid-column: 1 / -1;
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .filter-form button[type="submit"]:hover {
            background-color: var(--secondary-color);
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        input, select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: inherit;
            transition: var(--transition);
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-add {
            background-color: var(--success-color);
            color: white;
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-back {
            background-color: #6c757d;
            color: white;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow);
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-top: 1rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            position: sticky;
            top: 0;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e9ecef;
        }
        
        #popupForm {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            border-radius: var(--border-radius);
            z-index: 1000;
        }
        
        .popup-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .popup-header h3 {
            color: var(--primary-color);
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--danger-color);
        }
        
        .popup-form {
            display: grid;
            gap: 1rem;
        }
        
        .popup-form button[type="submit"] {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .popup-form button[type="submit"]:hover {
            background-color: var(--secondary-color);
        }
        
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        @media (max-width: 768px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            th, td {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>PERMISSION MANAGEMENT</h1>

    <form class="filter-form" method="GET">
        <div class="form-group">
            <label for="filter">Search</label>
            <input type="text" id="filter" name="filter" placeholder="Filter by Regd ID or Dept" value="<?= htmlspecialchars($filter) ?>">
        </div>
        
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>">
        </div>
        
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>">
        </div>
        
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" id="start_time" name="start_time" value="<?= htmlspecialchars($start_time) ?>">
        </div>
        
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" id="end_time" name="end_time" value="<?= htmlspecialchars($end_time) ?>">
        </div>
        
        <div class="form-group">
            <label for="by_user">By User</label>
            <input type="text" id="by_user" name="by_user" placeholder="Filter by User" value="<?= htmlspecialchars($by_user) ?>">
        </div>
        
        <button type="submit">Apply Filters</button>
    </form>

    <div class="action-buttons">
        <button class="btn btn-add" onclick="openPopup()">
            <i class="fas fa-plus"></i> Add New
        </button>
        <button class="btn btn-delete" onclick="deleteRecord()">
            <i class="fas fa-trash"></i> Delete
        </button>
        <button class="btn btn-back" onclick="goBack()">
            <i class="fas fa-arrow-left"></i> Back
        </button>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Regd ID</th>
                    <th>Department</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>By</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['sno'] ?></td>
                        <td><?= $row['regd_id'] ?></td>
                        <td><?= $row['dept'] ?></td>
                        <td><?= date('M d, Y h:i A', strtotime($row['start_time'])) ?></td>
                        <td><?= date('M d, Y h:i A', strtotime($row['end_time'])) ?></td>
                        <td><?= $row['by_user'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="popupForm">
    <div class="popup-header">
        <h3>Add New Permission</h3>
        <button class="close-btn" onclick="closePopup()">&times;</button>
    </div>
    <form class="popup-form" action="permission.php" method="POST">
        <input type="hidden" name="add_entry" value="1">
        
        <div class="form-group">
            <label for="regd_id">Registration ID</label>
            <input type="text" id="regd_id" name="regd_id" placeholder="Enter Registration ID" required>
        </div>
        
        <div class="form-group">
            <label for="dept">Department</label>
            <input type="text" id="dept" name="dept" 
                   value="<?= htmlspecialchars($username !== 'admin' ? $username : '') ?>" 
                   placeholder="Department" <?= $username !== 'admin' ? 'readonly' : '' ?> required>
        </div>
        
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="datetime-local" id="start_time" name="start_time" required>
        </div>
        
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="datetime-local" id="end_time" name="end_time" required>
        </div>
        
        <input type="hidden" name="by_user" value="<?= htmlspecialchars($_SESSION['username']) ?>">
        
        <button type="submit">Submit</button>
    </form>
</div>

<div id="overlay" class="overlay" onclick="closePopup()"></div>

<script>
    function deleteRecord() {
        let sno = prompt("Enter S.No to delete:");
        if (sno) {
            window.location.href = "permission.php?delete_sno=" + sno;
        }
    }
    
    function openPopup() {
        document.getElementById("popupForm").style.display = "block";
        document.getElementById("overlay").style.display = "block";
        document.body.style.overflow = "hidden";
    }
    
    function closePopup() {
        document.getElementById("popupForm").style.display = "none";
        document.getElementById("overlay").style.display = "none";
        document.body.style.overflow = "auto";
    }
    
    function goBack() {
        window.location.href = "view_students.php";
    }
</script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</body>
</html>

<?php $conn->close(); ?>