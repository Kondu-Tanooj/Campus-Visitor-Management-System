<?php
session_start();
include 'config.php'; // Ensure this file exists

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch available guest IDs
$guestIDs = [];
$result = $conn->query("SELECT guest_id FROM guest_id_status WHERE available = 'Yes'");
while ($row = $result->fetch_assoc()) {
    $guestIDs[] = $row['guest_id'];
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guest_id = $_POST['guest_id'];
    $guest_name = $_POST['guest_name'];
    $phone_number = $_POST['phone_number'];
    $vehicle = $_POST['vehicle'];
    $vehicle_number = !empty($_POST['vehicle_number']) ? $_POST['vehicle_number'] : null;
    $no_of_companions = $_POST['no_of_companions'];
    $names_of_companions = !empty($_POST['names_of_companions']) ? implode(", ", $_POST['names_of_companions']) : null;

    // Insert data into guest_info table
    $query = "INSERT INTO guest_info (guest_id, guest_name, phone_number, vehicle, vehicle_number, no_of_companions, names_of_companions)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssis", $guest_id, $guest_name, $phone_number, $vehicle, $vehicle_number, $no_of_companions, $names_of_companions);

    if ($stmt->execute()) {
        // Update guest_id_status table to mark guest_id as unavailable
        $updateQuery = "UPDATE guest_id_status SET available = 'No' WHERE guest_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("s", $guest_id);
        $updateStmt->execute();
        $updateStmt->close();

        echo "<script>alert('Guest added successfully!'); window.location.href = window.location.href;</script>";
        exit();
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Entry Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        
        body {
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
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
        .card {
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 600px;
            padding: 2rem;
            animation: fadeIn 0.5s ease-in-out;
        }
        .card-header {
            background: linear-gradient(135deg, #4b7bec, #6e8efb);
            color: white;
            text-align: center;
            font-size: 1.75rem;
            font-weight: 600;
            border-radius: 12px 12px 0 0;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6e8efb;
            box-shadow: 0 0 8px rgba(110, 142, 251, 0.3);
        }
        .btn-custom {
            width: 100%;
            border-radius: 10px;
            font-size: 1.1rem;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .btn-success {
            background: linear-gradient(135deg, #4b7bec, #6e8efb);
            border: none;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
        }
        #companion-fields input {
            transition: all 0.3s ease-in-out;
            margin-bottom: 0.5rem;
        }
        .alert {
            border-radius: 10px;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            Guest Entry Form
        </div>
        <div class="card-body">
            <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>$error_message</div>"; } ?>

            <form method="POST" action="" onsubmit="return validateCompanionNames()">
                <div class="mb-3">
                    <label for="guest_id" class="form-label">Guest ID</label>
                    <select class="form-select" name="guest_id" required>
                        <option value="">Select Guest ID</option>
                        <?php foreach ($guestIDs as $id) { echo "<option value='$id'>$id</option>"; } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="guest_name" class="form-label">Guest Name</label>
                    <input type="text" class="form-control" name="guest_name" required>
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="phone_number" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Vehicle</label><br>
                    <input type="radio" name="vehicle" value="Yes" onclick="toggleVehicleFields(true)"> Yes
                    <input type="radio" name="vehicle" value="No" onclick="toggleVehicleFields(false)" checked> No
                </div>

                <div class="mb-3" id="vehicle-details" style="display: none;">
                    <label for="vehicle_number" class="form-label">Vehicle Number</label>
                    <input type="text" class="form-control" name="vehicle_number">
                </div>

                <div class="mb-3">
                    <label for="no_of_companions" class="form-label">No. of Companions</label>
                    <input type="number" class="form-control" name="no_of_companions" id="no_of_companions" min="0" onchange="generateCompanionFields()">
                </div>

                <div class="mb-3" id="companion-fields"></div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-custom" onclick="goBack()">Back</button>
                    <button type="submit" class="btn btn-success btn-custom">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleVehicleFields(show) {
        document.getElementById('vehicle-details').style.display = show ? 'block' : 'none';
    }

    function generateCompanionFields() {
        let count = document.getElementById('no_of_companions').value;
        let container = document.getElementById('companion-fields');
        container.innerHTML = '';

        for (let i = 1; i <= count; i++) {
            let input = document.createElement('input');
            input.type = 'text';
            input.name = 'names_of_companions[]';
            input.placeholder = `Companion ${i} Name`;
            input.className = 'form-control mb-2';
            input.required = true;
            container.appendChild(input);
        }
    }

    function validateCompanionNames() {
        let count = document.getElementById('no_of_companions').value;
        let companions = document.getElementsByName('names_of_companions[]');

        if (count > 0) {
            for (let i = 0; i < companions.length; i++) {
                if (companions[i].value.trim() === '') {
                    alert(`Companion ${i + 1} Name is required`);
                    return false;
                }
            }
        }
        return true;
    }

    function goBack() {
        window.location.href = 'security.php';
    }
</script>

</body>
</html>