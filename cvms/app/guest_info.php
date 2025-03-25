
<?php
session_start();
include 'config.php'; // Ensure this file exists and contains DB connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filters
$filter_name = isset($_GET['filter_name']) ? trim($_GET['filter_name']) : "";
$filter_phone = isset($_GET['filter_phone']) ? trim($_GET['filter_phone']) : "";
$filter_vehicle = isset($_GET['filter_vehicle']) ? trim($_GET['filter_vehicle']) : "";
$filter_from_date = isset($_GET['filter_from_date']) ? $_GET['filter_from_date'] : "";
$filter_to_date = isset($_GET['filter_to_date']) ? $_GET['filter_to_date'] : "";
$filter_vehicle_no = isset($_GET['filter_vehicle_no']) ? trim($_GET['filter_vehicle_no']) : "";

// Prepare SQL query with filters
$sql = "SELECT * FROM guest_info WHERE 1=1";
$params = [];

if (!empty($filter_name)) {
    $sql .= " AND guest_name LIKE ?";
    $params[] = "%$filter_name%";
}
if (!empty($filter_phone)) {
    $sql .= " AND phone_number LIKE ?";
    $params[] = "%$filter_phone%";
}
if (!empty($filter_vehicle)) {
    $sql .= " AND vehicle = ?";
    $params[] = $filter_vehicle;
}
if (!empty($filter_vehicle_no)) {
    $sql .= " AND vehicle_number LIKE ?";
    $params[] = "%$filter_vehicle%";
}
if (!empty($filter_from_date) && !empty($filter_to_date)) {
    $sql .= " AND entry_time BETWEEN ? AND ?";
    $params[] = $filter_from_date . " 00:00:00";
    $params[] = $filter_to_date . " 23:59:59";
}

// Execute query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat("s", count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Download CSV functionality
if (isset($_GET['download_csv'])) {
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=guest_info.csv");

    $output = fopen("php://output", "w");
    fputcsv($output, ["S.No", "Guest ID", "Guest Name", "Phone", "Vehicle", "Vehicle_Number", "No. of Companions", "Companions", "Entry Time", "Exit Time"]);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Information</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: url(images/clg.jpg) no-repeat center center;
            background-size: cover;
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
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-custom {
            font-weight: bold;
            padding: 10px;
            border-radius: 8px;
        }
        .btn-back {
            background: #6c757d; color: white;
        }
        h1 {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }
        /* Table Styling */
        .table-container {
            max-height: 400px; /* Scroll limit */
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .table thead th {
            position: sticky;
            top: 0;
            background: #343a40;
            color: white;
            z-index: 2;
        }
    </style>
</head>
<body>
    <h1 class="text-center mt-3">Guest Information</h1>
    <div class="container">
        <!-- Back Button -->
        <div class="text-start mb-3">
            <a href="<?php echo ($_SESSION['username'] === 'admin') ? 'admin.php' : 'security.php'; ?>" class="btn btn-back btn-custom">Back</a>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="filter_name" class="form-control" placeholder="Guest Name" value="<?= htmlspecialchars($filter_name) ?>">
            </div>
            <div class="col-md-3">
                <input type="text" name="filter_phone" class="form-control" placeholder="Phone Number" value="<?= htmlspecialchars($filter_phone) ?>">
            </div>
            <div class="col-md-2">
                <select name="filter_vehicle" class="form-control">
                    <option value="">Vehicle (Yes/No)</option>
                    <option value="Yes" <?= ($filter_vehicle == 'Yes') ? 'selected' : '' ?>>Yes</option>
                    <option value="No" <?= ($filter_vehicle == 'No') ? 'selected' : '' ?>>No</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="filter_vehicle_no" class="form-control" placeholder="Vehicle Number" value="<?= htmlspecialchars($filter_vehicle_no) ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="filter_from_date" class="form-control" value="<?= htmlspecialchars($filter_from_date) ?>">
            </div>
            <div class="col-md-2">
                <input type="date" name="filter_to_date" class="form-control" value="<?= htmlspecialchars($filter_to_date) ?>">
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success btn-custom">Filter</button>
                <a href="guest_info.php" class="btn btn-danger btn-custom">Clear</a>
                <a href="guest_info.php?<?= http_build_query($_GET) ?>&download_csv=1" class="btn btn-primary btn-custom">Download CSV</a>
            </div>
        </form>
    </br></br>
    <div class="table-container mt-3">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>S.No</th>
                        <th>Guest ID</th>
                        <th>Guest Name</th>
                        <th>Phone</th>
                        <th>Vehicle</th>
                        <th>Vehicle Number</th>
                        <th>No. of Companions</th>
                        <th>Companions</th>
                        <th>Entry Time</th>
                        <th>Exit Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['sno']) ?></td>
                            <td><?= htmlspecialchars($row['guest_id']) ?></td>
                            <td><?= htmlspecialchars($row['guest_name']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= htmlspecialchars($row['vehicle']) ?></td>
                            <td><?= htmlspecialchars($row['vehicle_number']) ?></td>
                            <td><?= htmlspecialchars($row['no_of_companions']) ?></td>
                            <td><?= htmlspecialchars($row['names_of_companions']) ?></td>
                            <td><?= htmlspecialchars($row['entry_time']) ?></td>
                            <td><?= htmlspecialchars($row['exit_time']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    
</body>
</html>
<?php $conn->close(); ?>