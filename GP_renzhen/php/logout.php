<?php
session_start();

session_destroy(); 

//logout to
header("Location: homepage.php"); 

exit();
?>