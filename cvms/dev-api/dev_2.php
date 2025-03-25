<?php
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set response headers
header('Content-Type: text/plain');

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    die("Invalid request method.");
}

// Validate and sanitize inputs
$sensor1 = trim($_POST['sensor1'] ?? '');
$devname = trim($_POST['devname'] ?? '');

if (empty($sensor1) || empty($devname)) {
    http_response_code(400);
    die("Missing required parameters.");
}

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Start transaction
    $conn->begin_transaction();

    // 1. Validate Device
    $device_type = null;
    $stmt = $conn->prepare("SELECT type FROM devices_table WHERE dev_ID = ?");
    $stmt->bind_param("s", $devname);
    if (!$stmt->execute()) {
        throw new Exception("Device validation failed.");
    }
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $conn->rollback();
        die("Invalid Device");
    }
    $device_type = $result->fetch_assoc()['type'];
    $stmt->close();

    // 2. Log the scan
    $stmt = $conn->prepare("INSERT INTO raw_id_dev_2_table (rf_id, device_name, Time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $sensor1, $devname);
    if (!$stmt->execute()) {
        throw new Exception("Failed to log scan.");
    }
    $stmt->close();

    // 3. Check RFID registration
    $regd_no = null;
    $stmt = $conn->prepare("SELECT id_name, regd_id FROM raw_id_table WHERE rf_id = ?");
    $stmt->bind_param("s", $sensor1);
    if (!$stmt->execute()) {
        throw new Exception("RFID lookup failed.");
    }
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $conn->commit();
        echo "Invalid ID";
        die("Invalid ID");
    }
    
    $row = $result->fetch_assoc();
    $regd_no = $row['regd_id'];
    $stmt->close();

    // 4. Check guest status
    $stmt = $conn->prepare("SELECT available FROM guest_id_status WHERE guest_id = ?");
    $stmt->bind_param("s", $regd_no);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $guest_data = $result->fetch_assoc();
            $stmt->close();
            $conn->commit();
            die($guest_data['available'] === 'No' ? "Guest" : "Invalid Guest");
        }
    }
    $stmt->close();

    // 5. Check permission
    $stmt = $conn->prepare("SELECT 1 FROM permission_table WHERE regd_id = ? AND start_time <= NOW() AND end_time >= NOW() LIMIT 1");
    $stmt->bind_param("s", $regd_no);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $stmt->close();
            $conn->commit();
            die("Permitted");
        }
    }
    $stmt->close();

    // 6. Get student details
    $student = null;
    $stmt = $conn->prepare("SELECT department, semester, section FROM student_details WHERE regd_id = ?");
    $stmt->bind_param("s", $regd_no);
    if (!$stmt->execute()) {
        throw new Exception("Student lookup failed.");
    }
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $conn->commit();
        die("User Not Allowed");
    }
    
    $student = $result->fetch_assoc();
    $stmt->close();

    // 7. Check current hour
    $hour_data = null;
    $stmt = $conn->prepare("SELECT CASE 
            WHEN TIME(NOW()) BETWEEN STR_TO_DATE(SUBSTRING_INDEX(hour1, ' to ', 1), '%H:%i') 
                                AND STR_TO_DATE(SUBSTRING_INDEX(hour1, ' to ', -1), '%H:%i') THEN 'hour1'
            WHEN TIME(NOW()) BETWEEN STR_TO_DATE(SUBSTRING_INDEX(hour2, ' to ', 1), '%H:%i') 
                                AND STR_TO_DATE(SUBSTRING_INDEX(hour2, ' to ', -1), '%H:%i') THEN 'hour2'
            WHEN TIME(NOW()) BETWEEN STR_TO_DATE(SUBSTRING_INDEX(hour3, ' to ', 1), '%H:%i') 
                                AND STR_TO_DATE(SUBSTRING_INDEX(hour3, ' to ', -1), '%H:%i') THEN 'hour3'
            WHEN TIME(NOW()) BETWEEN STR_TO_DATE(SUBSTRING_INDEX(hour4, ' to ', 1), '%H:%i') 
                                AND STR_TO_DATE(SUBSTRING_INDEX(hour4, ' to ', -1), '%H:%i') THEN 'hour4'
            WHEN TIME(NOW()) BETWEEN STR_TO_DATE(SUBSTRING_INDEX(hour5, ' to ', 1), '%H:%i') 
                                AND STR_TO_DATE(SUBSTRING_INDEX(hour5, ' to ', -1), '%H:%i') THEN 'hour5'
            WHEN TIME(NOW()) BETWEEN STR_TO_DATE(SUBSTRING_INDEX(hour6, ' to ', 1), '%H:%i') 
                                AND STR_TO_DATE(SUBSTRING_INDEX(hour6, ' to ', -1), '%H:%i') THEN 'hour6'
            ELSE 'NO CLASS' END AS current_hour 
        FROM semester_hour WHERE semester = ?");
    $stmt->bind_param("s", $student['semester']);
    if (!$stmt->execute()) {
        throw new Exception("Hour lookup failed.");
    }
    $result = $stmt->get_result();
    $hour_data = $result->fetch_assoc();
    $stmt->close();

    if ($hour_data['current_hour'] === 'NO CLASS') {
        $conn->commit();
        die("NO CLASS");
    }

    // 8. Check timetable
    $subject_table = $conn->real_escape_string(
        $student['department'] . "_" . 
        $student['semester'] . "_" . 
        $student['section'] . "_timetable"
    );
    
    $day_column = strtolower(date('l'));
    $hour_column = $hour_data['current_hour'];
    
    // First check main timetable
    $stmt = $conn->prepare("SELECT $hour_column AS subject FROM $subject_table WHERE day = ?");
    $stmt->bind_param("s", $day_column);
    if (!$stmt->execute()) {
        throw new Exception("Timetable lookup failed.");
    }
    $result = $stmt->get_result();
    $subject_data = $result->fetch_assoc();
    $stmt->close();

    if ($subject_data['subject'] != "CLASS") {
        $conn->commit();
        die("NO CLASS");
    }

    // Check subject timetable if main says "CLASS"
    $subject_table = $conn->real_escape_string(
        $student['department'] . "_" . 
        $student['semester'] . "_" . 
        $student['section'] . "_subject_timetable"
    );
    
    $stmt = $conn->prepare("SELECT $hour_column AS subject FROM $subject_table WHERE day = ?");
    $stmt->bind_param("s", $day_column);
    if (!$stmt->execute()) {
        throw new Exception("Subject timetable lookup failed.");
    }
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $conn->commit();
        die("NO CLASS");
    }
    
    $subject_data = $result->fetch_assoc();
    $subject = $subject_data['subject'];
    $stmt->close();

    // 9. Record attendance
    $stmt = $conn->prepare("INSERT INTO report_dev_entry_table 
        (regd_id, department, semester, section, subject, time) 
        VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", 
        $regd_no, 
        $student['department'], 
        $student['semester'], 
        $student['section'], 
        $subject
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to record attendance.");
    }
    
    $stmt->close();
    $conn->commit();
    echo "CLASS";

} catch (Exception $e) {
    if (isset($conn) && $conn->in_transaction) {
        $conn->rollback();
    }
    error_log("RFID System Error: " . $e->getMessage());
    http_response_code(500);
    die("System error occurred.");
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}