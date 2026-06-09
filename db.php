<?php
$host = "127.0.0.1";   // TCP instead of localhost
$user = "root";
$password = "root";    // your MySQL password
$database = "guef_system";
$port = 3306;          // default MySQL port

$conn = mysqli_connect($host, $user, $password, $database, $port);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>