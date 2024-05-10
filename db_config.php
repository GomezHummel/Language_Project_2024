<?php
// Database connection parameters
$hostname = 'localhost';
$username = 'root'; // Default username for XAMPP
$password = ''; // Default password for XAMPP

// Function to establish connection to a specific database
function connectToDatabase($database) {
    global $hostname, $username, $password;
    // Create connection
    $conn = new mysqli($hostname, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>