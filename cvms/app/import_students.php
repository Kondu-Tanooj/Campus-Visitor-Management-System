<?php
session_start();
include 'config.php'; // Database connection

if (isset($_POST['import'])) {
    if ($_FILES['csv_file']['error'] === 0) {
        $filename = $_FILES['csv_file']['tmp_name'];
        $file = fopen($filename, "r");

        // Skip the first row (header)
        fgetcsv($file);

        while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
            if($data[2]==$_SESSION['username'] || $_SESSION['username']=="admin"){
            $regd_id = $conn->real_escape_string($data[0]);
            $name = $conn->real_escape_string($data[1]);
            $department = $conn->real_escape_string($data[2]);
            $batch_no = $conn->real_escape_string($data[3]);
            $semester = $conn->real_escape_string($data[4]);
            $section = $conn->real_escape_string($data[5]);

            $sql = "INSERT INTO students (regd_id, name, department, batch_no, semester, section) 
                    VALUES ('$regd_id', '$name', '$department', '$batch_no', '$semester', '$section')";

            if (!$conn->query($sql)) {
                echo "Error inserting record: " . $conn->error;
            }
            }
        }

        fclose($file);
        echo "Data imported successfully!";
    } else {
        echo "File upload error!";
    }
}
?>
