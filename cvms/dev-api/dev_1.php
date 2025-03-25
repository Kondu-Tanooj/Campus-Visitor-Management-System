<?php
include 'config.php'; // Include database connection file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone for both PHP and MySQL
date_default_timezone_set('Asia/Kolkata');

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
    die("Error: Invalid Input");
}

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set MySQL timezone
    $conn->query("SET time_zone = '+05:30'");

    // Start transaction
    $conn->begin_transaction();

    // 1. Validate Device
    $stmt = $conn->prepare("SELECT type FROM devices_table WHERE dev_ID = ?");
    $stmt->bind_param("s", $devname);
    if (!$stmt->execute()) {
        throw new Exception("Device validation failed.");
    }
    $device_result = $stmt->get_result();
    if ($device_result->num_rows === 0) {
        $conn->rollback();
        die("Invalid Device");
    }
    $stmt->close();

    // 2. Log the scan
    $stmt = $conn->prepare("INSERT INTO raw_id_dev_1_table (rf_id, device_name, Time) VALUES (?, ?, NOW())");
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
        // Log invalid ID
        $stmt = $conn->prepare("INSERT INTO invalid_id_table (rf_id, device_name, time) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $sensor1, $devname);
        $stmt->execute();
        $stmt->close();
        $conn->commit();
        die("Invalid ID");
    }
    
    $row = $result->fetch_assoc();
    $regd_no = $row['regd_id'];
    $stmt->close();

    // 4. Check student status
    $stmt = $conn->prepare("SELECT department, semester, section, batch_no, allowed FROM student_details WHERE regd_id = ?");
    $stmt->bind_param("s", $regd_no);
    if (!$stmt->execute()) {
        throw new Exception("Student lookup failed.");
    }
    $student_result = $stmt->get_result();
    
    if ($student_result->num_rows > 0) {
        $student = $student_result->fetch_assoc();
        $stmt->close();
        
        if (strtoupper($student['allowed']) !== "YES") {
            $conn->commit();
            die("NO4");
        }

        // 5. Check face detection (within last 10 seconds)
        $stmt = $conn->prepare("SELECT time FROM cam_capture_table WHERE regd_id = ? ORDER BY time DESC LIMIT 1");
        $stmt->bind_param("s", $regd_no);
        if (!$stmt->execute()) {
            throw new Exception("Face detection check failed.");
        }
        $cam_result = $stmt->get_result();
        
        if ($cam_result->num_rows === 0 || (time() - strtotime($cam_result->fetch_assoc()['time'])) > 10) {
            $stmt->close();
            $conn->commit();
            die("Face Not Detected");
        }
        $stmt->close();

        // 6. Handle entry/exit logic
        $stmt = $conn->prepare("SELECT sno, in_time, out_time FROM main_entry_table WHERE regd_no = ? ORDER BY sno DESC LIMIT 1");
        $stmt->bind_param("s", $regd_no);
        if (!$stmt->execute()) {
            throw new Exception("Entry lookup failed.");
        }
        $last_entry_result = $stmt->get_result();
        
        if ($last_entry_result->num_rows > 0) {
            $last_entry = $last_entry_result->fetch_assoc();
            
            if (is_null($last_entry['out_time'])) {
                // Record exit
                $stmt = $conn->prepare("UPDATE main_entry_table SET out_time = NOW() WHERE sno = ?");
                $stmt->bind_param("i", $last_entry['sno']);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to record exit.");
                }
                $response = "EXIT";
            } else {
                // Record new entry
                $stmt = $conn->prepare("INSERT INTO main_entry_table (regd_no, department, semester, batch, in_time) VALUES (?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssis", $regd_no, $student['department'], $student['semester'], $student['batch_no']);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to record entry.");
                }
                $response = "YES";
            }
            
            // Clear face detection record
            $stmt = $conn->prepare("DELETE FROM cam_capture_table WHERE regd_id = ?");
            $stmt->bind_param("s", $regd_no);
            $stmt->execute();
            
            $stmt->close();
            $conn->commit();
            die($response);
        } else {
            // First entry for this student
            $stmt = $conn->prepare("INSERT INTO main_entry_table (regd_no, department, semester, batch, in_time) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssis", $regd_no, $student['department'], $student['semester'], $student['batch_no']);
            if (!$stmt->execute()) {
                throw new Exception("Failed to record first entry.");
            }
            
            // Clear face detection record
            $stmt = $conn->prepare("DELETE FROM cam_capture_table WHERE regd_id = ?");
            $stmt->bind_param("s", $regd_no);
            $stmt->execute();
            
            $stmt->close();
            $conn->commit();
            die("YES");
        }
    }

    // 7. Handle guest logic
    $stmt = $conn->prepare("SELECT available FROM guest_id_status WHERE guest_id LIKE ?");
    $stmt->bind_param("s", $regd_no);
    if (!$stmt->execute()) {
        throw new Exception("Guest status check failed.");
    }
    $guest_status_result = $stmt->get_result();
    
    if ($guest_status_result->num_rows > 0) {
        $guest_status = $guest_status_result->fetch_assoc();
        $stmt->close();
        
        if (strtoupper($guest_status['available']) === "NO") {
            $stmt = $conn->prepare("SELECT sno, entry_time, exit_time FROM guest_info WHERE guest_id = ? ORDER BY sno DESC LIMIT 1");
            $stmt->bind_param("s", $regd_no);
            if (!$stmt->execute()) {
                throw new Exception("Guest info lookup failed.");
            }
            $guest_info_result = $stmt->get_result();
            
            if ($guest_info_result->num_rows > 0) {
                $guest_info = $guest_info_result->fetch_assoc();
                
                if (is_null($guest_info['exit_time'])) {
                    // Record guest exit
                    $stmt = $conn->prepare("UPDATE guest_info SET exit_time = NOW() WHERE sno = ?");
                    $stmt->bind_param("i", $guest_info['sno']);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to record guest exit.");
                    }
                    
                    // Update guest status
                    $stmt = $conn->prepare("UPDATE guest_id_status SET available = 'YES' WHERE guest_id = ?");
                    $stmt->bind_param("s", $regd_no);
                    if (!$stmt->execute()) {
                        throw new Exception("Failed to update guest status.");
                    }
                    
                    $stmt->close();
                    $conn->commit();
                    die("Guest EXIT");
                } else {
                    $stmt->close();
                    $conn->commit();
                    die("Guest Already Exited");
                }
            } else {
                $stmt->close();
                $conn->commit();
                die("Guest Entry Not Found");
            }
        } else {
            $stmt->close();
            $conn->commit();
            die("Guest Not Allowed");
        }
    }

    // If we get here, it's an invalid ID
    $stmt = $conn->prepare("INSERT INTO invalid_id_table (rf_id, device_name, time) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $sensor1, $devname);
    $stmt->execute();
    $stmt->close();
    $conn->commit();
    die("Invalid ID");

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