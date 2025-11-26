<?php
session_start();

// 初始化购物车 Session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// 接收前端传来的数据
if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // 逻辑：如果购物车里已经有这本书，数量+1；如果没有，数量设为1
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]++;
    } else {
        $_SESSION['cart'][$book_id] = 1;
    }

    // 计算当前购物车总数量
    $total_items = array_sum($_SESSION['cart']);

    // 返回 JSON 数据给前端 JS
    echo json_encode(['status' => 'success', 'total' => $total_items]);
} else {
    echo json_encode(['status' => 'error']);
}
?>