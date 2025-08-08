<?php
// Database connection configuration
$host = 'localhost';       // Database host
$dbname = 'child_welfare'; // Database name
$username = 'root';        // Database username
$password = '';            // Database password (leave empty for XAMPP)

// Create a new database connection
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection failure
    die("Connection failed: " . $e->getMessage());
}
?>
