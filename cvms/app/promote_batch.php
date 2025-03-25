<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $batch = $_POST["batch"];
    $semester = $_POST["semester"];

    if (!empty($batch) && !empty($semester)) {
        $sql = "UPDATE student_details SET semester='$semester' WHERE batch_no ='$batch'";
        
        if ($conn->query($sql) === TRUE) {
            echo "Batch $batch successfully promoted to Semester $semester!";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Batch and Semester cannot be empty!";
    }
}

$conn->close();
?>
