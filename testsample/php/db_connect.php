<?php
// ==================================================================
// File: db_connect.php
// Description: 数据库连接配置文件。
// Functionality:
// 1. 定义数据库连接参数。
// 2. 创建 MySQLi 连接对象。
// 3. 检查连接状态并设置字符集。
// ==================================================================

$servername = "localhost";
$username = "root";      // XAMPP 默认用户名
$password = "";          // XAMPP 默认密码为空
$dbname = "bookstore";   // 数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 设置字符集，防止中文乱码
$conn->set_charset("utf8mb4");
?>