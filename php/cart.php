<?php
// ==================================================================
// File: cart.php
// Description: 购物车页面，展示用户已添加的商品。
// Functionality:
// 1. 读取 $_SESSION['cart'] 获取用户选择的商品ID和数量。
// 2. 根据 ID 列表从数据库查询书籍详细信息。
// 3. 动态计算每本书的小计和整个购物车的总价。
// ==================================================================

session_start(); // 开启 Session 以访问购物车数据
include 'db_connect.php'; // 连接数据库

// 计算购物车中商品的总数量 (用于显示 Header 角标等，虽在此页面主要展示列表)
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
    // --- 检查购物车状态 ---
    if (empty($_SESSION['cart'])) {
        // 情况 A: 购物车为空
        echo "<div class='empty-cart'>
                <h2>Your cart is empty.</h2>
                <a href='shopping.php' class='btn btn-checkout' style='margin-top:20px;'>Go Shopping</a>
              </div>";
    } else {
        // 情况 B: 购物车有商品
        
        // 1. 获取所有书本 ID (例如: array(1, 5, 8) -> string "1,5,8")
        $ids = array_keys($_SESSION['cart']);
        $ids_string = implode(',', $ids);

        // 2. SQL 查询: 根据 ID 列表一次性获取所有书本详情
        $sql = "SELECT * FROM books WHERE id IN ($ids_string)";
        $result = $conn->query($sql);
        
        $grand_total = 0; // 初始化总价
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
            // 3. 遍历查询结果，渲染每一行
            while($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $qty = $_SESSION['cart'][$id]; // 从 Session 获取数量
                $subtotal = $row['price'] * $qty; // 计算小计
                $grand_total += $subtotal; // 累加总价

                // 动态图片处理
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