<?php

session_start();

// initilize
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];

    // Check if the book ID already exists in the shopping cart array
    if (isset($_SESSION['cart'][$book_id])) {
        // if exit , +1
        $_SESSION['cart'][$book_id]++;
    } else {
        // not exist set 1
        $_SESSION['cart'][$book_id] = 1;
    }

    // calculate cart amount
    $total_items = array_sum($_SESSION['cart']);

    echo json_encode([
        'status' => 'success', 
        'total' => $total_items
    ]);
} else {
    // x receive id > error
    echo json_encode(['status' => 'error']);
}
?>