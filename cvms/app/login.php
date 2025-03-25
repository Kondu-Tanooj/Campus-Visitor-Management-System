<?php
session_start(); // Start session
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = trim($_POST["username"]);
    $pass = trim($_POST["password"]);
    
    if (!empty($user) && !empty($pass)) {
        // Using prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT username, password, role FROM credentials WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($pass,$row["password"])) {
                $_SESSION["username"] = $row["username"];
                $_SESSION["role"] = $row["role"];
                $_SESSION['admin_logged_in'] = true;
                
                // Redirect based on role
                if ($row["role"] == "admin") {
                    header("Location: admin.php");
                } elseif ($row["role"] == "security") {
                    header("Location: security.php");
                } elseif ($row["role"] == "dept") {
                    header("Location: dept.php");
                }
                exit();
            } else {
                echo "<script>alert('Invalid password!'); window.location.href='../index.html';</script>";

            }
        } else {
            echo "<script>alert('Invalid username!'); window.location.href='../index.html';</script>";

        }
        $stmt->close();
    } else {
        echo "All fields are required!";
    }
}
?>
