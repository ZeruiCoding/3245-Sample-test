<?php
// ==========================================
// 1. 初始化与数据库连接
// ==========================================
session_start();
include 'db_connect.php';

// ==========================================
// 2. 数据查询逻辑
// ==========================================
// A. 查询 New Releases (按出版日期倒序，取前4本)
$sql_new = "SELECT * FROM books ORDER BY publish_date DESC LIMIT 4";
$result_new = $conn->query($sql_new);

// B. 查询 Best Sellers (按销量倒序，取前4本)
$sql_best = "SELECT * FROM books ORDER BY sales_count DESC LIMIT 4";
$result_best = $conn->query($sql_best);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Temple - Bookstore</title>
    <style>
        /* =========================================
           1. 全局样式 (Global Reset)
           ========================================= */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #fdfbf5; font-family: "Georgia", "Garamond", serif; color: #4a4a4a; }
        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        a { text-decoration: none; transition: color 0.3s; }

        /* =========================================
           2. 头部与导航 (Header & Nav)
           ========================================= */
        header { padding-top: 20px; }
        .header-divider { border: 0; height: 1px; background-color: #e0ddd5; margin-bottom: 20px; }

        /* Logo */
        .logo-section { display: flex; justify-content: center; margin-bottom: 20px; }
        .logo-img { height: 60px; display: block; }

        /* 导航条布局 */
        .nav-bar { display: flex; justify-content: space-between; align-items: center; padding: 10px 0 30px 0; }
        
        /* 中间菜单 */
        .nav-links { display: flex; gap: 60px; flex: 1; justify-content: center; padding-left: 150px; }
        .nav-links a { color: #666; font-size: 16px; font-weight: 500; }
        .nav-links a.active, .nav-links a:hover { color: #c83a3a; }

        /* 右侧工具栏 */
        .nav-tools { display: flex; align-items: center; gap: 20px; }

        /* 搜索框 */
        .search-box { display: flex; align-items: center; border-bottom: 1px solid #dcdcdc; padding-bottom: 5px; }
        .search-box input { border: none; background: transparent; outline: none; color: #666; width: 160px; font-family: inherit; }
        .search-btn { background: none; border: none; cursor: pointer; display: flex; align-items: center; padding: 0; }
        .search-btn:hover svg { fill: #c83a3a; }

        /* 用户菜单 */
        .user-menu { font-family: Arial, sans-serif; font-size: 14px; color: #666; display: flex; align-items: center; gap: 10px; }
        .login-btn, .logout-btn { color: #4a4a4a; font-weight: bold; }
        .login-btn:hover, .logout-btn:hover { color: #c83a3a; }

        /* 购物车图标 */
        .cart-icon-wrapper { position: relative; cursor: pointer; display: flex; align-items: center; }
        .cart-img { width: 24px; height: 24px; display: block; }
        .badge { position: absolute; top: -8px; right: -8px; background-color: #c83a3a; color: white; font-size: 10px; width: 16px; height: 16px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-family: Arial, sans-serif; }

        /* =========================================
           3. 横幅与区块 (Banner & Sections)
           ========================================= */
        .banner-section { width: 100%; margin-top: 10px; display: flex; justify-content: center; }
        .banner-img { width: 100%; height: auto; display: block; }
        .banner-divider { border: 0; height: 1px; background-color: #d1cdc0; margin: 30px 0; }

        .content-section { padding: 30px 0 60px 0; text-align: left; }
        .section-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px; }
        .section-title { color: #4a3f35; font-size: 28px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .section-divider { border: 0; height: 1px; background-color: #e0ddd5; margin: 0; }
        
        .view-all-btn { display: inline-block; background-color: #c83a3a; color: white; padding: 8px 20px; font-size: 12px; font-family: Arial, sans-serif; text-transform: uppercase; letter-spacing: 1px; border-radius: 4px; }
        .view-all-btn:hover { background-color: #a62e2e; }

        /* =========================================
           4. 书籍网格与卡片 (Book Grid)
           ========================================= */
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 30px; }
        
        .book-card { position: relative; background-color: #ffffff; padding: 15px; border: 1px solid #efece5; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        
        .book-cover { width: 100%; height: 200px; object-fit: cover; margin-bottom: 15px; background-color: #f0f0f0; }
        .book-title { font-size: 15px; font-weight: bold; color: #333; margin-bottom: 5px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { font-size: 13px; color: #888; font-style: italic; margin-bottom: 10px; }
        .book-price { font-size: 16px; color: #c83a3a; font-weight: bold; font-family: Arial, sans-serif; }

        /* 标签 (New / Hot) */
        .tag { position: absolute; top: 10px; left: 10px; padding: 4px 10px; color: white; font-size: 10px; font-weight: bold; border-radius: 4px; z-index: 10; }
        .tag-new { background-color: #4CAF50; } 
        .tag-hot { background-color: #FF5722; } 

        /* =========================================
           5. 弹窗样式 (Modal)
           ========================================= */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); z-index: 1000; backdrop-filter: blur(5px); }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #fff; width: 90%; max-width: 800px; padding: 30px; z-index: 1001; border-radius: 8px; box-shadow: 0 15px 30px rgba(0,0,0,0.2); display: flex; gap: 30px; align-items: flex-start; }
        
        .hidden { display: none !important; } 
        .visible { display: flex !important; } 
        .visible-block { display: block !important; }

        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; color: #aaa; cursor: pointer; transition: color 0.3s; }
        .close-btn:hover { color: #c83a3a; }

        /* 弹窗布局 */
        .modal-left { flex: 1; }
        .modal-right { flex: 1.5; display: flex; flex-direction: column; }
        .modal-img { width: 100%; height: auto; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .modal-title { font-size: 26px; color: #4a3f35; margin-bottom: 5px; }
        .modal-author { font-size: 16px; color: #888; font-style: italic; margin-bottom: 15px; }
        .modal-price { font-size: 24px; color: #c83a3a; font-weight: bold; margin-bottom: 15px; }
        
        /* 详细信息网格 */
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #555; background-color: #f9f8f4; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #efece5; }
        .meta-item strong { color: #4a3f35; display: block; margin-bottom: 2px; }

        .modal-desc { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 25px; max-height: 120px; overflow-y: auto; padding-right: 5px; }
        .modal-desc::-webkit-scrollbar { width: 5px; }
        .modal-desc::-webkit-scrollbar-thumb { background: #ccc; border-radius: 5px; }

        /* 按钮组 */
        .modal-actions { display: flex; gap: 15px; margin-top: auto; }
        .btn-buy, .btn-cart { flex: 1; padding: 12px 0; text-align: center; border-radius: 4px; font-weight: bold; cursor: pointer; transition: all 0.3s; border: none; }
        .btn-buy { background-color: #c83a3a; color: white; }
        .btn-buy:hover { background-color: #a62e2e; }
        .btn-cart { background-color: transparent; border: 2px solid #c83a3a; color: #c83a3a; }
        .btn-cart:hover { background-color: #c83a3a; color: white; }

        /* 响应式适配 */
        @media (max-width: 768px) { .modal { flex-direction: column; width: 95%; height: 80vh; overflow-y: scroll; } .modal-img { height: 200px; } }
    </style>
</head>
<body>

    <div class="container">
        <header id="main-header">
            <div class="logo-section">
                <img src="./IMG/logo.png" alt="Logo" class="logo-img">
            </div>
            
            <hr class="header-divider">
            
            <div class="nav-bar">
                <nav class="nav-links">
                    <a href="homepage.php" class="active">Home</a>
                    <a href="shopping.php">Shopping</a>
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                </nav>
                
                <div class="nav-tools">
                    <form action="shopping.php" method="GET" class="search-box">
                        <input type="text" name="q" placeholder="Search books...">
                        <button type="submit" class="search-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#666"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                        </button>
                    </form>
                    
                    <div class="user-menu">
                        <?php if(isset($_SESSION['username'])): ?>
                            <span>Hi, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
                            <a href="logout.php" class="logout-btn">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="login-btn">Login</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="cart-icon-wrapper" onclick="window.location.href='cart.php'">
                        <img src="./IMG/cart.png" alt="Cart" class="cart-img">
                        <div class="badge" id="cart-badge">
                            <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="banner-section"><img src="./IMG/slogan.png" alt="Slogan" class="banner-img"></div>
        <hr class="banner-divider">

        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">New Releases</h2>
                <a href="shopping.php?type=new" class="view-all-btn">View All</a>
            </div>
            <div class="book-grid">
                <?php 
                if ($result_new->num_rows > 0) {
                    while($row = $result_new->fetch_assoc()) {
                        $desc = isset($row['description']) ? htmlspecialchars($row['description']) : "No description.";
                        $pub = isset($row['publisher']) ? htmlspecialchars($row['publisher']) : "Unknown";
                        $isbn = isset($row['isbn']) ? htmlspecialchars($row['isbn']) : "N/A";
                        $cat = isset($row['category']) ? htmlspecialchars($row['category']) : "General";
                        $date = isset($row['publish_date']) ? htmlspecialchars($row['publish_date']) : "N/A";
                ?>
                <div class="book-card" onclick="openModal(this)" 
                     data-id="<?php echo $row['id']; ?>"
                     data-title="<?php echo htmlspecialchars($row['title']); ?>"
                     data-author="<?php echo htmlspecialchars($row['author']); ?>"
                     data-price="<?php echo $row['price']; ?>"
                     data-image="./IMG/allknow.png" 
                     data-desc="<?php echo $desc; ?>"
                     data-publisher="<?php echo $pub; ?>"
                     data-isbn="<?php echo $isbn; ?>"
                     data-category="<?php echo $cat; ?>"
                     data-date="<?php echo $date; ?>">
                    
                    <div class="tag tag-new">NEW</div> 
                    <img src="./IMG/allknow.png" alt="Cover" class="book-cover">
                    <h3 class="book-title"><?php echo $row["title"]; ?></h3>
                    <p class="book-author"><?php echo $row["author"]; ?></p>
                    <p class="book-price">$<?php echo $row["price"]; ?></p>
                </div>
                <?php } } ?>
            </div>
        </section>

        <hr class="section-divider">

        <section class="content-section">
            <div class="section-header">
                <h2 class="section-title">Best Sellers</h2>
                <a href="shopping.php?type=hot" class="view-all-btn">View All</a>
            </div>
            <div class="book-grid">
                <?php 
                if ($result_best->num_rows > 0) {
                    while($row = $result_best->fetch_assoc()) {
                        $desc = isset($row['description']) ? htmlspecialchars($row['description']) : "No description.";
                        $pub = isset($row['publisher']) ? htmlspecialchars($row['publisher']) : "Unknown";
                        $isbn = isset($row['isbn']) ? htmlspecialchars($row['isbn']) : "N/A";
                        $cat = isset($row['category']) ? htmlspecialchars($row['category']) : "General";
                        $date = isset($row['publish_date']) ? htmlspecialchars($row['publish_date']) : "N/A";
                ?>
                <div class="book-card" onclick="openModal(this)"
                     data-id="<?php echo $row['id']; ?>"
                     data-title="<?php echo htmlspecialchars($row['title']); ?>"
                     data-author="<?php echo htmlspecialchars($row['author']); ?>"
                     data-price="<?php echo $row['price']; ?>"
                     data-image="./IMG/allknow.png"
                     data-desc="<?php echo $desc; ?>"
                     data-publisher="<?php echo $pub; ?>"
                     data-isbn="<?php echo $isbn; ?>"
                     data-category="<?php echo $cat; ?>"
                     data-date="<?php echo $date; ?>">

                    <div class="tag tag-hot">Hot</div> 
                    <img src="./IMG/allknow.png" alt="Cover" class="book-cover">
                    <h3 class="book-title"><?php echo $row["title"]; ?></h3>
                    <p class="book-author"><?php echo $row["author"]; ?></p>
                    <p class="book-price">$<?php echo $row["price"]; ?></p>
                </div>
                <?php } } ?>
            </div>
        </section>
    </div>

    <div id="modalOverlay" class="modal-overlay hidden" onclick="closeModal()"></div>
    <div id="productModal" class="modal hidden">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-left">
            <img id="m-image" src="" alt="Book Cover" class="modal-img">
        </div>
        <div class="modal-right">
            <h2 id="m-title" class="modal-title">Book Title</h2>
            <p id="m-author" class="modal-author">Author Name</p>

            <div class="meta-grid">
                <div class="meta-item"><strong>Publisher:</strong> <span id="m-publisher">-</span></div>
                <div class="meta-item"><strong>Date:</strong> <span id="m-date">-</span></div>
                <div class="meta-item"><strong>ISBN:</strong> <span id="m-isbn">-</span></div>
                <div class="meta-item"><strong>Category:</strong> <span id="m-category">-</span></div>
            </div>

            <p id="m-price" class="modal-price">$0.00</p>
            <div class="modal-desc"><p id="m-desc">Description...</p></div>
            <div class="modal-actions">
                <button class="btn-buy" onclick="buyNow()">Buy Now</button>
                <button class="btn-cart" onclick="addToCart()">Add to Cart</button>
            </div>
        </div>
    </div>

    <script>
        var currentBookId = 0; // 全局变量，记录当前弹窗的书本ID

        // 打开弹窗并填充数据
        function openModal(element) {
            currentBookId = element.getAttribute('data-id');

            // 填充文本数据
            document.getElementById('m-title').innerText = element.getAttribute('data-title');
            document.getElementById('m-author').innerText = element.getAttribute('data-author');
            document.getElementById('m-price').innerText = '$' + element.getAttribute('data-price');
            document.getElementById('m-desc').innerText = element.getAttribute('data-desc');
            
            // 填充元数据
            document.getElementById('m-publisher').innerText = element.getAttribute('data-publisher');
            document.getElementById('m-date').innerText = element.getAttribute('data-date');
            document.getElementById('m-isbn').innerText = element.getAttribute('data-isbn');
            document.getElementById('m-category').innerText = element.getAttribute('data-category');
            
            // 填充图片
            document.getElementById('m-image').src = element.getAttribute('data-image');

            // 显示
            document.getElementById('modalOverlay').classList.remove('hidden');
            document.getElementById('modalOverlay').classList.add('visible-block');
            document.getElementById('productModal').classList.remove('hidden');
            document.getElementById('productModal').classList.add('visible');
        }

        // 关闭弹窗
        function closeModal() {
            document.getElementById('modalOverlay').classList.add('hidden');
            document.getElementById('modalOverlay').classList.remove('visible-block');
            document.getElementById('productModal').classList.add('hidden');
            document.getElementById('productModal').classList.remove('visible');
        }

        // 加入购物车 (AJAX)
        function addToCart() {
            if(currentBookId == 0) return;

            var formData = new FormData();
            formData.append('book_id', currentBookId);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('cart-badge').innerText = data.total;
                    alert("Book added to cart!");
                    closeModal();
                } else {
                    alert("Error adding to cart.");
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // 立即购买
        function buyNow() {
            if(currentBookId == 0) return;

            var formData = new FormData();
            formData.append('book_id', currentBookId);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.href = 'cart.php';
                }
            });
        }
    </script>
</body>
</html>