<?php
/**
 * ==================================================================
 * File: add_to_cart.php
 * Description: 处理“加入购物车”的后端逻辑接口。
 * Principle: 
 * 1. 这是一个纯逻辑文件，不输出 HTML，只输出 JSON 数据供前端 JS 使用。
 * 2. 利用 $_SESSION['cart'] 数组来存储购物车数据。
 * 数据结构为: Key(书本ID) => Value(购买数量)。
 * ==================================================================
 */

// 1. 开启 Session
// 必须在脚本最开始调用，以便访问服务器端存储的用户会话数据。
session_start();

// 2. 初始化购物车
// 如果这是用户第一次添加商品，Session 中还没有 'cart' 数组，需要先创建一个空数组。
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// 3. 处理请求
// 检查前端是否通过 POST 方法发送了 'book_id'
if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // --- 更新购物车逻辑 ---
    // 检查该书本 ID 是否已经存在于购物车数组中
    if (isset($_SESSION['cart'][$book_id])) {
        // 场景 A: 书本已存在 -> 数量 +1
        $_SESSION['cart'][$book_id]++;
    } else {
        // 场景 B: 书本不存在 -> 添加新条目，初始数量设为 1
        $_SESSION['cart'][$book_id] = 1;
    }

    // 4. 计算总数量
    // array_sum() 会将数组中所有的 Value (即每本书的数量) 相加，得到购物车总件数。
    // 这个数字将用于更新前端 Header 上的红色角标。
    $total_items = array_sum($_SESSION['cart']);

    // 5. 返回响应
    // 将结果封装成关联数组，并转换为 JSON 格式输出。
    // 前端 JS 接收到这个 JSON 后，会解析 'status' 和 'total' 来更新界面。
    echo json_encode([
        'status' => 'success', 
        'total' => $total_items
    ]);
} else {
    // 如果没有接收到 book_id，返回错误状态
    echo json_encode(['status' => 'error']);
}
?>