<?php
include 'config.php'; // Include database connection file
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if regd_id is set in POST request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['regd_id'])) {
    $regd_id = $conn->real_escape_string($_POST['regd_id']);

    $sql = "INSERT INTO cam_capture_table (regd_id) VALUES ('$regd_id')";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Invalid request!";
}

$conn->close();
?>
