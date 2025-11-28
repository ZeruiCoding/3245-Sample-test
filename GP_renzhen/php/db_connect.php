<?php
$servername = "localhost";
$username = "root";    
$password = "";          
$dbname = "bookstore";  

$conn = new mysqli($servername, $username, $password, $dbname);

// success connect
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// use for chinese
$conn->set_charset("utf8mb4");
?>