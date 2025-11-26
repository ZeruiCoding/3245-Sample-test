<?php
$servername = "localhost";
$username = "root";      // XAMPP 默认用户名
$password = "";          // XAMPP 默认密码为空
$dbname = "bookstore";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 设置字符集，防止中文乱码 (虽然我们主要是英文，但以防万一)
$conn->set_charset("utf8mb4");
?>