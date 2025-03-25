<?php
session_start();
include 'config.php'; // Ensure this file exists

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF validation failed!");
    }

    if (isset($_POST['add_guest_id'])) {
        $guest_id = trim($_POST['guest_id']);

        // Validate input
        if (empty($guest_id)) {
            echo "<script>alert('Enter a valid Guest ID!'); window.location.href='guest_id.php';</script>";
            exit;
        }

        // Check if guest ID already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM guest_id_status WHERE guest_id = ?");
        $stmt->bind_param("s", $guest_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "<script>alert('Guest ID already exists!'); window.location.href='guest_id.php';</script>";
            exit;
        }

        // Insert new guest ID
        $stmt = $conn->prepare("INSERT INTO guest_id_status (guest_id, available) VALUES (?, 'Yes')");
        $stmt->bind_param("s", $guest_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Guest Added Successfully!'); window.location.href='guest_id.php';</script>";
            exit;
    }

    if (isset($_POST['delete_guest_id'])) {
        $guest_id = trim($_POST['guest_id']);

        // Validate input
        if (empty($guest_id)) {
            echo "<script>alert('Enter a valid Guest ID to delete!'); window.location.href='guest_id.php';</script>";
            exit;
        }

        // Check if guest ID exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM guest_id_status WHERE guest_id = ?");
        $stmt->bind_param("s", $guest_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            echo "<script>alert('Guest ID not found!'); window.location.href='guest_id.php';</script>";
            exit;
        }

        // Delete guest ID
        $stmt = $conn->prepare("DELETE FROM guest_id_status WHERE guest_id = ?");
        $stmt->bind_param("s", $guest_id);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Guest ID Deleted Successfully'); window.location.href='guest_id.php';</script>";
            exit;
    }
}

// Fetch guest IDs
$result = $conn->query("SELECT * FROM guest_id_status ORDER BY sno ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest ID Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            margin-top: 50px;
        }
        .table-container {
            width: 70%;
            margin: 20px auto;
            max-height: 400px;
            overflow-y: auto;
            border-radius: 10px;
            background: white;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #343a40;
            color: white;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        button {
            padding: 12px 20px;
            margin: 10px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s, transform 0.2s;
        }
        button:hover {
            transform: scale(1.05);
        }
        .add { background-color: rgb(106, 235, 136); color: white; }
        .delete { background-color: #dc3545; color: white; }
        .back { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    <h2>Guest ID Status</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>S.No</th>
                <th>Guest ID</th>
                <th>Available</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['sno']); ?></td>
                <td><?php echo htmlspecialchars($row['guest_id']); ?></td>
                <td><?php echo htmlspecialchars($row['available']); ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <button class="add" onclick="addGuestId()">Add Guest ID</button>
    <button class="delete" onclick="deleteGuestId()">Delete Guest ID</button>
    <button class="back" onclick="goBack()">Back</button>

    <script>
        function addGuestId() {
            let guest_id = prompt("Enter Guest ID to add:");
            if (!guest_id || guest_id.trim() === "") {
                alert("Enter a valid Guest ID!");
                return;
            }

            let form = document.createElement("form");
            form.method = "POST";
            form.innerHTML = `
                <input type="hidden" name="add_guest_id">
                <input type="hidden" name="guest_id" value="${guest_id}">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function deleteGuestId() {
            let guest_id = prompt("Enter Guest ID to delete:");
            if (!guest_id || guest_id.trim() === "") {
                alert("Enter a valid Guest ID!");
                return;
            }

            let form = document.createElement("form");
            form.method = "POST";
            form.innerHTML = `
                <input type="hidden" name="delete_guest_id">
                <input type="hidden" name="guest_id" value="${guest_id}">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function goBack() {
            window.location.href = 'admin.php';
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
