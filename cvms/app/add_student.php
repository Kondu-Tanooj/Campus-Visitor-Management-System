<?php
session_start();
include 'config.php';

// Ensure database connection is established
if (!isset($conn)) {
    die("Database connection failed. Please check config.php.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regd_id = $conn->real_escape_string($_POST['regd_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $department = $conn->real_escape_string($_POST['department']);
    $batch_no = $conn->real_escape_string($_POST['batch_no']);
    $semester = $conn->real_escape_string($_POST['semester']);
    $section = $conn->real_escape_string($_POST['section']);

    // Check if student already exists
    $check_sql = "SELECT * FROM student_details WHERE regd_id = '$regd_id'";
    $check_result = $conn->query($check_sql);
    if ($check_result->num_rows > 0) {
        echo "<script>alert('Student already exists!'); window.history.back();</script>";
        exit;
    }

    // Insert student details
    $sql = "INSERT INTO student_details (regd_id, name, department, batch_no, semester, section) 
            VALUES ('$regd_id', '$name', '$department', '$batch_no', '$semester', '$section')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Student added successfully!'); 
                window.location.href = '" . ($_SESSION["username"] == "admin" ? "admin.php" : "dept.php") . "';
              </script>";
    } else {
        echo "<script>alert('Failed to add student. Please try again.'); window.history.back();</script>";
    }

    $conn->close();
}
?>
