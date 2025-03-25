<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['regd_id'])) {
    $regd_id = trim($_POST['regd_id']);
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $batch_no = trim($_POST['batch_no']);
    $semester = trim($_POST['semester']);
    $section = trim($_POST['section']);

    // Check if the student exists
    $check_sql = "SELECT * FROM student_details WHERE regd_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $regd_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update student details securely
        $update_sql = "UPDATE student_details SET name = ?, department = ?, batch_no = ?, semester = ?, section = ? WHERE regd_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssisss", $name, $department, $batch_no, $semester, $section, $regd_id);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Student updated successfully!'); 
                    window.location.href = 'view_students.php';
                  </script>";
        } else {
            echo "<script>alert('Error updating student: " . $stmt->error . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Student not found! Please check the Registration ID.'); window.history.back();</script>";
    }
    
    $stmt->close();
}
$conn->close();
?>
