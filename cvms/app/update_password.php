<?php
session_start();
if (!isset($_SESSION)) {
    session_start();
}

include 'config.php';
// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.html"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST["current_password"]);
    $new_password = trim($_POST["new_password"]);

    if (!isset($_SESSION["username"])) {
        echo "<script>alert('User not logged in!');</script>";
        exit();
    }

    $username = trim($_POST["department_id"]);

    // Fetch the current password from the database
    $stmt = $conn->prepare("SELECT password FROM credentials WHERE username = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($db_password);
    $stmt->fetch();
    $stmt->close();

    // Check if the current password matches
    if (!password_verify($current_password,$db_password)) {
        echo "<script>alert('Incorrect current password!');window.location.href='../index.html';</script>";
        exit();
    }
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    // Update the password
    $stmt = $conn->prepare("UPDATE credentials SET password = ? WHERE username = ?");
    if (!$stmt) {
        die("Error preparing update statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $hashed_password, $username);
    if ($stmt->execute()) {
        echo "<script>alert('Password updated successfully!'); window.location.href='../index.html';</script>";
    } else {
        echo "<script>alert('Error updating password!');</script>";
    }
    $stmt->close();
}
?>
