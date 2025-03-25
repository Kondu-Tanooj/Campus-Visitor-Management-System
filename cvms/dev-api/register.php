<?php
require_once "config.php"; // Include the database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_REQUEST['sensor1'])) {
    $sensor1 = $_REQUEST['sensor1'];

    // Check if the RF_ID already exists in raw_id_table
    $stmt_check = $conn->prepare("SELECT id_name FROM raw_id_table WHERE rf_id = ?");
    $stmt_check->bind_param("s", $sensor1);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // If RF_ID exists, fetch id_name
        $row = $result_check->fetch_assoc();
        $id_name = $row['id_name'];
    } else {
        // Insert a new record
        $stmt_insert = $conn->prepare("INSERT INTO raw_id_table (rf_id) VALUES (?)");
        $stmt_insert->bind_param("s", $sensor1);
        if ($stmt_insert->execute()) {
            // Get the auto-incremented Sno
            $new_sno = $conn->insert_id;
            $id_name = "ID_" . $new_sno;

            // Update the newly inserted row with id_name
            $stmt_update = $conn->prepare("UPDATE raw_id_table SET id_name = ? WHERE Sno = ?");
            $stmt_update->bind_param("si", $id_name, $new_sno);
            $stmt_update->execute();
            $stmt_update->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to insert new RFID"]);
            exit;
        }
        $stmt_insert->close();
    }
    $stmt_check->close();

    // Return the id_name
    echo json_encode(["status" => "success", "id_name" => $id_name]);
}

$conn->close();
?>
