<?php
session_start(); 
include 'db_connect.php';

// show amount
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Knowledge Temple</title>
    <link rel="stylesheet" href="../css/cart.css"> </head>
<body>

<div class="container">
    <header>
        <a href="homepage.php"><img src="../IMG/logobig.png" alt="Logo" class="logo-img"></a>
        <nav class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="shopping.php">Continue Shopping</a>
        </nav>
    </header>

    <h1 class="cart-title">Shopping Cart</h1>

    <?php
    if (empty($_SESSION['cart'])) {
        // empty cart
        echo "<div class='empty-cart'>
                <h2>Your cart is empty.</h2>
                <a href='shopping.php' class='btn btn-checkout' style='margin-top:20px;'>Go Shopping</a>
              </div>";
    } else {
        $ids = array_keys($_SESSION['cart']);
        $ids_string = implode(',', $ids);

        // use id to get detail info
        $sql = "SELECT * FROM books WHERE id IN ($ids_string)";
        $result = $conn->query($sql);
        
        $grand_total = 0; 
    ?>

    <table class="cart-table">
        <thead>
            <tr>
                <th width="50%">Product</th>
                <th width="15%">Price</th>
                <th width="15%">Quantity</th>
                <th width="20%">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $qty = $_SESSION['cart'][$id]; // amount
                $subtotal = $row['price'] * $qty; 
                $grand_total += $subtotal; // sum

                $img_file = !empty($row['cover_image']) ? $row['cover_image'] : 'allknow.png';
                $img_src = "../IMG/bookimg/" . htmlspecialchars($img_file);
            ?>
            <tr>
                <td>
                    <div class="product-col">
                        <img src="<?php echo $img_src; ?>" alt="Cover" class="cart-img">
                        <div>
                            <div class="cart-book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <small style="color:#888;"><?php echo htmlspecialchars($row['author']); ?></small>
                            <a href="cart_remove.php?id=<?php echo $id; ?>" class="remove-link">Remove</a>
                        </div>
                    </div>
                </td>
                <td class="price-col">$<?php echo $row['price']; ?></td>
                <td style="font-family: Arial;"><?php echo $qty; ?></td>
                <td class="price-col">$<?php echo number_format($subtotal, 2); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="total-price">
        Grand Total: $<?php echo number_format($grand_total, 2); ?>
    </div>

    <div class="actions">
        <a href="shopping.php" class="btn btn-continue">Continue Shopping</a>
        <form action="checkout.php" method="POST" style="display:inline;">
             <button type="submit" class="btn btn-checkout">Proceed to Checkout</button>
        </form>
    </div>

    <?php } ?>

</div>

</body>
</html>