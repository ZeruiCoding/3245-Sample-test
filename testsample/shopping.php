<?php
session_start();
include 'db_connect.php';

// ==========================================
// 1. 获取所有分类 (用于生成侧边栏)
// ==========================================
$cat_sql = "SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != ''";
$cat_result = $conn->query($cat_sql);

// ==========================================
// 2. 处理筛选逻辑
// ==========================================
// 初始化查询条件
$where_clauses = [];
$order_by = "ORDER BY id DESC"; // 默认排序
$page_title = "All Books";
$current_filter = ""; // 用于前端高亮显示当前选中的菜单

// A. 搜索功能 (优先级最高)
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_key = $conn->real_escape_string($_GET['q']);
    $where_clauses[] = "(title LIKE '%$search_key%' OR author LIKE '%$search_key%' OR isbn LIKE '%$search_key%')";
    $page_title = "Search Results for: \"" . htmlspecialchars($_GET['q']) . "\"";
    $current_filter = "search";
} 
// B. 特殊集合筛选 (New / Hot)
elseif (isset($_GET['type'])) {
    $type = $_GET['type'];
    if ($type == 'new') {
        // 筛选最近 30 天出版的书
        $where_clauses[] = "publish_date > DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $page_title = "New Releases";
        $current_filter = "new";
    } elseif ($type == 'hot') {
        // 筛选销量大于 1000 的书，并按销量排序
        $where_clauses[] = "sales_count > 1000";
        $order_by = "ORDER BY sales_count DESC";
        $page_title = "Best Sellers";
        $current_filter = "hot";
    }
}
// C. 分类筛选
elseif (isset($_GET['category'])) {
    $cat = $conn->real_escape_string($_GET['category']);
    $where_clauses[] = "category = '$cat'";
    $page_title = "Category: " . htmlspecialchars($cat);
    $current_filter = $cat; // 用于高亮分类
}

// ==========================================
// 3. 构建最终 SQL
// ==========================================
$sql = "SELECT * FROM books";

// 如果有筛选条件，拼接 WHERE
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

// 拼接排序
$sql .= " " . $order_by;

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Knowledge Temple</title>
    <style>
        /* 复用并扩展样式 */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: #fdfbf5; font-family: "Georgia", "Garamond", serif; color: #4a4a4a; }
        .container { width: 100%; max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* Header & Nav */
        header { padding-top: 20px; }
        .logo-section { display: flex; justify-content: center; margin-bottom: 20px; }
        .logo-img { height: 60px; display: block; }
        .header-divider { border: 0; height: 1px; background-color: #e0ddd5; margin-bottom: 20px; }
        .nav-bar { display: flex; justify-content: space-between; align-items: center; padding: 10px 0 30px 0; }
        .nav-links { display: flex; gap: 60px; flex: 1; justify-content: center; padding-left: 150px; }
        .nav-links a { text-decoration: none; color: #666; font-size: 16px; font-weight: 500; transition: color 0.3s; }
        .nav-links a.active, .nav-links a:hover { color: #c83a3a; }
        .nav-tools { display: flex; align-items: center; gap: 20px; }
        
        .search-box { display: flex; align-items: center; border-bottom: 1px solid #dcdcdc; padding-bottom: 5px; }
        .search-box input { border: none; background: transparent; outline: none; color: #666; width: 160px; font-family: inherit; }
        .search-btn { background: none; border: none; cursor: pointer; display: flex; align-items: center; padding: 0; }
        .search-btn:hover svg { fill: #c83a3a; }
        .cart-icon-wrapper { position: relative; cursor: pointer; display: flex; align-items: center; }
        .cart-img { width: 24px; height: 24px; display: block; }
        .badge { position: absolute; top: -8px; right: -8px; background-color: #c83a3a; color: white; font-size: 10px; width: 16px; height: 16px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-family: Arial, sans-serif; }
        .user-menu { font-family: Arial, sans-serif; font-size: 14px; color: #666; display: flex; align-items: center; gap: 10px; }
        .login-btn, .logout-btn { text-decoration: none; color: #4a4a4a; font-weight: bold; }
        .login-btn:hover, .logout-btn:hover { color: #c83a3a; }
        
        .section-divider { border: 0; height: 1px; background-color: #e0ddd5; margin: 0; }

        /* =========================================
           [新增] 左右分栏布局样式
           ========================================= */
        .shop-layout {
            display: flex;
            margin-top: 40px;
            gap: 40px;
            margin-bottom: 60px;
        }

        /* 左侧侧边栏 */
        .sidebar {
            width: 250px;
            flex-shrink: 0; /* 防止被压缩 */
            padding-right: 20px;
            border-right: 1px solid #efece5;
        }

        .filter-group { margin-bottom: 30px; }
        .filter-title { font-size: 18px; font-weight: bold; color: #4a3f35; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; }
        .filter-list { list-style: none; }
        .filter-list li { margin-bottom: 10px; }
        
        .filter-link {
            text-decoration: none;
            color: #666;
            font-size: 15px;
            transition: color 0.2s;
            display: block;
            padding: 5px 0;
        }
        .filter-link:hover { color: #c83a3a; padding-left: 5px; } /* Hover 动效 */
        
        /* 选中状态 */
        .filter-link.active {
            color: #c83a3a;
            font-weight: bold;
            padding-left: 5px;
            border-left: 3px solid #c83a3a;
            padding-left: 10px;
        }

        /* 右侧商品区 */
        .product-area { flex: 1; }
        .page-header { margin-bottom: 30px; display: flex; justify-content: space-between; align-items: baseline; }
        .page-title { font-size: 28px; color: #4a3f35; }
        .result-count { color: #888; font-size: 14px; }

        /* 书本网格 (复用) */
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 30px; }
        .book-card { position: relative; background-color: #ffffff; padding: 15px; border: 1px solid #efece5; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; display: flex; flex-direction: column; align-items: center; text-align: center; }
        .book-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .book-cover { width: 100%; height: 200px; object-fit: cover; margin-bottom: 15px; background-color: #f0f0f0; }
        .book-title { font-size: 15px; font-weight: bold; color: #333; margin-bottom: 5px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .book-author { font-size: 13px; color: #888; font-style: italic; margin-bottom: 10px; }
        .book-price { font-size: 16px; color: #c83a3a; font-weight: bold; font-family: Arial, sans-serif; }
        
        .tag { position: absolute; top: 10px; left: 10px; padding: 4px 10px; color: white; font-size: 10px; font-weight: bold; border-radius: 4px; z-index: 10; }
        .tag-new { background-color: #4CAF50; } 
        .tag-hot { background-color: #FF5722; } 

        /* Modal Styles */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); z-index: 1000; backdrop-filter: blur(5px); }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #fff; width: 90%; max-width: 800px; padding: 30px; z-index: 1001; border-radius: 8px; box-shadow: 0 15px 30px rgba(0,0,0,0.2); display: flex; gap: 30px; align-items: flex-start; }
        .hidden { display: none !important; } .visible { display: flex !important; } .visible-block { display: block !important; }
        .close-btn { position: absolute; top: 15px; right: 20px; font-size: 28px; font-weight: bold; color: #aaa; cursor: pointer; transition: color 0.3s; }
        .close-btn:hover { color: #c83a3a; }
        .modal-left { flex: 1; }
        .modal-right { flex: 1.5; display: flex; flex-direction: column; }
        .modal-img { width: 100%; height: auto; object-fit: cover; border-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .modal-title { font-size: 26px; color: #4a3f35; margin-bottom: 5px; }
        .modal-author { font-size: 16px; color: #888; font-style: italic; margin-bottom: 15px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #555; background-color: #f9f8f4; padding: 15px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #efece5; }
        .meta-item strong { color: #4a3f35; display: block; margin-bottom: 2px; }
        .modal-price { font-size: 24px; color: #c83a3a; font-weight: bold; margin-bottom: 15px; }
        .modal-desc { font-size: 14px; color: #666; line-height: 1.6; margin-bottom: 25px; max-height: 120px; overflow-y: auto; padding-right: 5px; }
        .modal-actions { display: flex; gap: 15px; margin-top: auto; }
        .btn-buy, .btn-cart { flex: 1; padding: 12px 0; text-align: center; border-radius: 4px; font-weight: bold; cursor: pointer; transition: all 0.3s; border: none; }
        .btn-buy { background-color: #c83a3a; color: white; }
        .btn-buy:hover { background-color: #a62e2e; }
        .btn-cart { background-color: transparent; border: 2px solid #c83a3a; color: #c83a3a; }
        .btn-cart:hover { background-color: #c83a3a; color: white; }

        @media (max-width: 768px) { 
            .shop-layout { flex-direction: column; } 
            .sidebar { width: 100%; border-right: none; border-bottom: 1px solid #efece5; padding-bottom: 20px; }
            .modal { flex-direction: column; width: 95%; height: 80vh; overflow-y: scroll; } .modal-img { height: 200px; } 
        }
    </style>
</head>
<body>

    <div class="container">
        <header>
            <div class="logo-section"><img src="./IMG/logo.png" alt="Logo" class="logo-img"></div>
            <hr class="header-divider">
            <div class="nav-bar">
                <nav class="nav-links">
                    <a href="homepage.php">Home</a>
                    <a href="shopping.php" class="active">Shopping</a>
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                </nav>
                <div class="nav-tools">
                    <form action="shopping.php" method="GET" class="search-box">
                        <input type="text" name="q" placeholder="Search..." value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                        <button type="submit" class="search-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#666"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
                        </button>
                    </form>
                    <div class="user-menu">
                        <?php if(isset($_SESSION['username'])): ?>
                            <span>Hi, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span><a href="logout.php" class="logout-btn">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="login-btn">Login</a>
                        <?php endif; ?>
                    </div>
                    <div class="cart-icon-wrapper"><img src="./IMG/cart.png" alt="Cart" class="cart-img"><div class="badge">0</div></div>
                </div>
            </div>
        </header>

        <hr class="section-divider">

        <div class="shop-layout">
            
            <aside class="sidebar">
                
                <div class="filter-group">
                    <h3 class="filter-title">Collections</h3>
                    <ul class="filter-list">
                        <li>
                            <a href="shopping.php" class="filter-link <?php echo ($current_filter == '') ? 'active' : ''; ?>">All Books</a>
                        </li>
                        <li>
                            <a href="shopping.php?type=new" class="filter-link <?php echo ($current_filter == 'new') ? 'active' : ''; ?>">New Releases</a>
                        </li>
                        <li>
                            <a href="shopping.php?type=hot" class="filter-link <?php echo ($current_filter == 'hot') ? 'active' : ''; ?>">Best Sellers</a>
                        </li>
                    </ul>
                </div>

                <div class="filter-group">
                    <h3 class="filter-title">Categories</h3>
                    <ul class="filter-list">
                        <?php 
                        if ($cat_result && $cat_result->num_rows > 0) {
                            while($cat_row = $cat_result->fetch_assoc()) {
                                $c_name = $cat_row['category'];
                                $is_active = ($current_filter == $c_name) ? 'active' : '';
                                echo "<li><a href='shopping.php?category=" . urlencode($c_name) . "' class='filter-link $is_active'>" . htmlspecialchars($c_name) . "</a></li>";
                            }
                        } else {
                            echo "<li style='color:#999; font-size:13px;'>No categories found</li>";
                        }
                        ?>
                    </ul>
                </div>

            </aside>

            <main class="product-area">
                
                <div class="page-header">
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                    <span class="result-count">
                        <?php echo $result->num_rows; ?> books found
                        <?php if($current_filter != ''): ?>
                            <a href="shopping.php" style="font-size:12px; margin-left:10px; color:#c83a3a;">(Clear Filters)</a>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="book-grid">
                    <?php 
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // 数据准备
                            $desc = isset($row['description']) ? htmlspecialchars($row['description']) : "No description.";
                            $pub = isset($row['publisher']) ? htmlspecialchars($row['publisher']) : "Unknown";
                            $isbn = isset($row['isbn']) ? htmlspecialchars($row['isbn']) : "N/A";
                            $cat = isset($row['category']) ? htmlspecialchars($row['category']) : "General";
                            $date = isset($row['publish_date']) ? htmlspecialchars($row['publish_date']) : "N/A";

                            // 标签逻辑
                            $is_new = (strtotime($row['publish_date']) > strtotime('-30 days'));
                            $is_hot = ($row['sales_count'] > 1000);
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
                        
                        <?php if ($is_new): ?><div class="tag tag-new">NEW</div>
                        <?php elseif ($is_hot): ?><div class="tag tag-hot">HOT</div>
                        <?php endif; ?>

                        <img src="./IMG/allknow.png" alt="Cover" class="book-cover">
                        <h3 class="book-title"><?php echo $row["title"]; ?></h3>
                        <p class="book-author"><?php echo $row["author"]; ?></p>
                        <p class="book-price">$<?php echo $row["price"]; ?></p>
                    </div>
                    <?php } } else { echo "<p style='width:100%; color:#888;'>No books found.</p>"; } ?>
                </div>

            </main>
        </div>
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
                <button class="btn-buy" onclick="alert('Proceed to Checkout')">Buy Now</button>
                <button class="btn-cart" onclick="addToCart()">Add to Cart</button>
            </div>
        </div>
    </div>

    <script>
        function openModal(element) {
            const title = element.getAttribute('data-title');
            const author = element.getAttribute('data-author');
            const price = element.getAttribute('data-price');
            const image = element.getAttribute('data-image');
            const desc = element.getAttribute('data-desc');
            const publisher = element.getAttribute('data-publisher');
            const date = element.getAttribute('data-date');
            const isbn = element.getAttribute('data-isbn');
            const category = element.getAttribute('data-category');

            document.getElementById('m-title').innerText = title;
            document.getElementById('m-author').innerText = author;
            document.getElementById('m-price').innerText = '$' + price;
            document.getElementById('m-image').src = image;
            document.getElementById('m-desc').innerText = desc;
            document.getElementById('m-publisher').innerText = publisher;
            document.getElementById('m-date').innerText = date;
            document.getElementById('m-isbn').innerText = isbn;
            document.getElementById('m-category').innerText = category;

            document.getElementById('modalOverlay').classList.remove('hidden');
            document.getElementById('modalOverlay').classList.add('visible-block');
            document.getElementById('productModal').classList.remove('hidden');
            document.getElementById('productModal').classList.add('visible');
        }

        function closeModal() {
            document.getElementById('modalOverlay').classList.add('hidden');
            document.getElementById('modalOverlay').classList.remove('visible-block');
            document.getElementById('productModal').classList.add('hidden');
            document.getElementById('productModal').classList.remove('visible');
        }

        function addToCart() { alert("Added to cart!"); }
    </script>
</body>
</html>