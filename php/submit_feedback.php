<?php
/**
 * ==================================================================
 * File: submit_feedback.php
 * Description: 处理用户反馈表单提交。
 * Functionality:
 * 1. 接收 POST 数据。
 * 2. 清洗数据防止 SQL 注入。
 * 3. 将数据插入 'feedback' 表。
 * 4. 返回 JSON 格式的成功/失败消息。
 * ==================================================================
 */

session_start();
include 'db_connect.php'; // 确保路径正确，如果 db_connect.php 也在 php/ 下

header('Content-Type: application/json'); // 设置返回类型为 JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. 获取并清洗数据
    $name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? $conn->real_escape_string(trim($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? $conn->real_escape_string(trim($_POST['message'])) : '';

    // 2. 简单的服务器端验证
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit();
    }

    // 3. 插入数据库
    $sql = "INSERT INTO feedback (full_name, email, phone, message) VALUES ('$name', '$email', '$phone', '$message')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Thank you for your feedback!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>