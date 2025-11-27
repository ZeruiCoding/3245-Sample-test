<?php
// ==================================================================
// File: checkout.php
// Description: 处理结账逻辑。
// Functionality:
// 1. 检查购物车是否为空。
// 2. 遍历购买列表，更新 'books' 表中的 sales_count。
// 3. 清空购物车 Session。
// 4. 跳转至购买成功页面。
// ==================================================================

session_start();
include 'db_connect.php';

// 1. 安全检查
if (empty($_SESSION['cart'])) {
    header("Location: shopping.php"); // 没东西买什么？回去购物！
    exit();
}

// 2. 更新销量
foreach ($_SESSION['cart'] as $book_id => $quantity) {
    $id = intval($book_id);
    $qty = intval($quantity);

    // SQL: 销量 = 原销量 + 本次购买量
    $sql = "UPDATE books SET sales_count = sales_count + $qty WHERE id = $id";
    $conn->query($sql);
}

// 3. 清空购物车
unset($_SESSION['cart']);

// 4. 跳转成功页
header("Location: success.html");
exit();
?>