<?php
// connect.php
$host = 'localhost';
$username = 'root';
$port = '3307';
$password = '';
$dbname = 'logit';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, port: $port );

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
