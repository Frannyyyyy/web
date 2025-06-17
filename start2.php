<?php
$host = 'localhost';
$username = 'fran';
$password = 'QueryCode2212#';
$database = 'dafac';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully!";




?>
