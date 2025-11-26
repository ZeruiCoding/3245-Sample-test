<?php
session_start();
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]); // 从 Session 中删除
}
header("Location: cart.php"); //以此刷新页面
exit();
?>