<?php
session_start();

// Database credentials
$host = "localhost"; // Change if needed
$dbname = "cvms";
$username = "root";  // keep your user
$password = "";      // Keep your passowrd

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
