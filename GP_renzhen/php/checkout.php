<?php
session_start();
include 'db_connect.php';

if (empty($_SESSION['cart'])) {
    header("Location: shopping.php"); // nothing in cart > shopping php
    exit();
}

//update sales count
foreach ($_SESSION['cart'] as $book_id => $quantity) {
    $id = intval($book_id);
    $qty = intval($quantity);

    // SQL count 
    $sql = "UPDATE books SET sales_count = sales_count + $qty WHERE id = $id";
    $conn->query($sql);
}

unset($_SESSION['cart']);

// success buy
header("Location: success.html");
exit();
?>