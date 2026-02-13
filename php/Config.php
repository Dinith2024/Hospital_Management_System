<?php
$host = "localhost";
$user = "root";  // default user
$pass = "";      // default password is empty
$db   = "carelink";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>