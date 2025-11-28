<?php

session_start();

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // use unset destroy $id
    unset($_SESSION['cart'][$id]); 
}

//update 
header("Location: cart.php"); 

exit();
?>