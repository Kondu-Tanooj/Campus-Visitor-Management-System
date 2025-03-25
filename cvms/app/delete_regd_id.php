<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $batch = $_POST["batch"];

    if (!empty($batch)) {
        $sql = "DELETE FROM student_details WHERE regd_id='$batch'";

        if ($conn->query($sql) === TRUE) {
            echo " $batch successfully deleted!";
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Field cannot be empty!";
    }
}

$conn->close();
?>
