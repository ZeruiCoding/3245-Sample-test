<?php
session_start();
include 'db_connect.php';

// 计算购物车商品总数
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Knowledge Temple</title>
    <style>
        /* 复用基础样式 */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #fdfbf5; font-family: "Georgia", serif; color: #4a4a4a; }
        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        
        /* Header 样式 (简化版) */
        header { padding: 20px 0; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0ddd5; }
        .logo-img { height: 50px; }
        .nav-links a { text-decoration: none; color: #666; margin-left: 20px; }
        .nav-links a:hover { color: #c83a3a; }

        /* 购物车表格样式 */
        .cart-title { margin: 30px 0; font-size: 28px; color: #4a3f35; }
        
        .cart-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .cart-table th, .cart-table td { padding: 20px; text-align: left; border-bottom: 1px solid #eee; }
        .cart-table th { background-color: #f9f8f4; color: #4a3f35; font-weight: bold; }
        
        .product-col { display: flex; align-items: center; gap: 15px; }
        .cart-img { width: 60px; height: 80px; object-fit: cover; border-radius: 4px; }
        .cart-book-title { font-weight: bold; color: #333; }
        
        .price-col { color: #c83a3a; font-weight: bold; font-family: Arial; }
        .total-price { font-size: 20px; font-weight: bold; color: #c83a3a; text-align: right; margin-top: 20px; }
        
        .actions { text-align: right; margin-top: 30px; }
        .btn { padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block; cursor: pointer; border: none; }
        .btn-continue { background: #eee; color: #666; margin-right: 10px; }
        .btn-checkout { background: #c83a3a; color: white; }
        .btn-checkout:hover { background: #a62e2e; }

        /* 空购物车提示 */
        .empty-cart { text-align: center; padding: 50px; color: #888; }
        .remove-link { color: #999; font-size: 12px; text-decoration: underline; margin-left: 10px; }
        .remove-link:hover { color: red; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <a href="index.php"><img src="./IMG/logo.png" alt="Logo" class="logo-img"></a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="shopping.php">Continue Shopping</a>
        </nav>
    </header>

    <h1 class="cart-title">Shopping Cart</h1>

    <?php
    // 检查购物车是否为空
    if (empty($_SESSION['cart'])) {
        echo "<div class='empty-cart'>
                <h2>Your cart is empty.</h2>
                <a href='shopping.php' class='btn btn-checkout' style='margin-top:20px;'>Go Shopping</a>
              </div>";
    } else {
        // 如果不为空，从数据库获取书籍详情
        // 1. 获取所有书本 ID (例如: 1, 5, 8)
        $ids = array_keys($_SESSION['cart']);
        $ids_string = implode(',', $ids);

        // 2. 查询数据库
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
                $qty = $_SESSION['cart'][$id];
                $subtotal = $row['price'] * $qty;
                $grand_total += $subtotal;
            ?>
            <tr>
                <td>
                    <div class="product-col">
                        <img src="./IMG/allknow.png" alt="Cover" class="cart-img">
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
        <button class="btn btn-checkout" onclick="alert('Checkout function coming soon!')">Proceed to Checkout</button>
    </div>

    <?php } ?>

</div>

</body>
</html>