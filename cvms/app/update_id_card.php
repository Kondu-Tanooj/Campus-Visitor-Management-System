<?php
include 'config.php';

if (isset($_POST['sno'])) {
    $sno = $_POST['sno'];
    $rf_id = $_POST['rf_id'];
    $id_name = $_POST['id_name'];
    $regd_id = $_POST['regd_id'];

    // Update the row in the database
    $stmt = $conn->prepare("UPDATE raw_id_table SET rf_id=?, id_name=?, regd_id=? WHERE Sno=?");
    $stmt->bind_param("sssi", $rf_id, $id_name, $regd_id, $sno);

    if ($stmt->execute()) {
        echo "Row updated successfully!";
    } else {
        echo "Error updating row: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
