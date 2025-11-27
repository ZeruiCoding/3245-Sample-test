<?php
session_start();
include 'db_connect.php';

// ------------------------------------------------------------------
// 1. 定义分类结构 (用于侧边栏和逻辑判断)
// ------------------------------------------------------------------
$categories_structure = [
    "Languages" => ["Chinese", "English", "Japanese", "Korean", "Arabic", "Italian", "Spanish", "Thai", "French"],
    "Children" => ["Fairy Tales", "Picture Books"],
    "Fiction" => ["Wuxia", "Fantasy", "Romance", "Horror", "History"],
    "Classics" => ["Eastern", "Western"]
];

// ------------------------------------------------------------------
// 2. 计算 HOT 门槛
// ------------------------------------------------------------------
$hot_threshold = 1000; 
$threshold_sql = "SELECT sales_count FROM books ORDER BY sales_count DESC LIMIT 1 OFFSET 9";
$threshold_result = $conn->query($threshold_sql);
if ($threshold_result && $threshold_result->num_rows > 0) {
    $row_t = $threshold_result->fetch_assoc();
    $hot_threshold = $row_t['sales_count'];
    if ($hot_threshold < 10) $hot_threshold = 10; 
}

// ------------------------------------------------------------------
// 3. 处理筛选逻辑 (GET 参数)
// ------------------------------------------------------------------
$where_clauses = [];
$order_by = "ORDER BY id DESC";
$page_title = "All Books";
$current_main = ""; 
$current_sub = "";  
$current_filter = ""; 

// A. 关键词搜索
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_key = $conn->real_escape_string($_GET['q']);
    $where_clauses[] = "(title LIKE '%$search_key%' OR author LIKE '%$search_key%' OR isbn LIKE '%$search_key%')";
    $page_title = "Search: \"" . htmlspecialchars($_GET['q']) . "\"";
    $current_filter = "search";
} 
// B. 特殊集合 (NEW/HOT)
elseif (isset($_GET['type'])) {
    $type = $_GET['type'];
    if ($type == 'new') {
        $where_clauses[] = "publish_date > DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $page_title = "New Releases";
        $current_filter = "new";
    } elseif ($type == 'hot') {
        $where_clauses[] = "sales_count > 1000";
        $order_by = "ORDER BY sales_count DESC";
        $page_title = "Best Sellers";
        $current_filter = "hot";
    }
} 
// C. 一级分类筛选
elseif (isset($_GET['parent'])) {
    $parent = $conn->real_escape_string($_GET['parent']);
    $where_clauses[] = "parent_category = '$parent'";
    $page_title = "Category: " . htmlspecialchars($parent);
    $current_main = $parent;
} 
// D. 二级分类筛选
elseif (isset($_GET['category'])) {
    $cat = $conn->real_escape_string($_GET['category']);
    $where_clauses[] = "category = '$cat'";
    $page_title = "Category: " . htmlspecialchars($cat);
    $current_sub = $cat;
    // 反向查找所属大类以展开菜单
    foreach ($categories_structure as $p => $subs) {
        if (in_array($cat, $subs)) {
            $current_main = $p; 
            break; 
        }
    }
}

// ------------------------------------------------------------------
// 4. 执行查询
// ------------------------------------------------------------------
$sql = "SELECT * FROM books";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}
$sql .= " " . $order_by;
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Knowledge Temple</title>
    <link rel="stylesheet" href="../css/style.css"> </head>
<body>

    <header id="main-header">
        <div class="logo-section"><img src="../IMG/logobig.png" alt="Logo" class="logo-img"></div>
        <hr class="header-divider">
        <div class="header-inner">
            <div class="nav-bar">
                <a href="homepage.php" class="small-logo-link"><img src="../IMG/slogo.png" alt="Logo" class="small-logo"></a>
                <nav class="nav-links">
                    <a href="homepage.php">Home</a>
                    <a href="shopping.php" class="active">Shopping</a>
                    <a href="homepage.php#aboutUsSection">About Us</a>
                    <a href="homepage.php#feedbackSection">Contact</a>
                </nav>
                <div class="nav-tools">
                    <form action="shopping.php" method="GET" class="search-box">
                        <input type="text" name="q" placeholder="Search books...">
                        <button type="submit" class="search-btn"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#666"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></button>
                    </form>
                    <div class="user-menu">
                        <?php if(isset($_SESSION['username'])): ?>
                            <span>Hi, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span><a href="logout.php" class="logout-btn">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="login-btn">Login</a>
                        <?php endif; ?>
                    </div>
                    <div class="cart-icon-wrapper" onclick="window.location.href='cart.php'">
                        <img src="../IMG/cart.png" alt="Cart" class="cart-img">
                        <div class="badge" id="cart-badge"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="header-placeholder"></div>

    <div class="container">
        <hr class="section-divider">
        <div class="shop-layout">
            
            <aside class="sidebar">
                <div class="filter-group">
                    <h3 class="filter-title">Collections</h3>
                    <ul class="filter-list" style="padding-left:0; list-style:none;">
                        <li><a href="shopping.php" class="sub-link <?php echo ($current_filter == '') ? 'active' : ''; ?>">All Books</a></li>
                        <li><a href="shopping.php?type=new" class="sub-link <?php echo ($current_filter == 'new') ? 'active' : ''; ?>">New Releases</a></li>
                        <li><a href="shopping.php?type=hot" class="sub-link <?php echo ($current_filter == 'hot') ? 'active' : ''; ?>">Best Sellers</a></li>
                    </ul>
                </div>

                <div class="filter-group">
                    <h3 class="filter-title">Categories</h3>
                    <div class="cat-list">
                        <?php foreach($categories_structure as $parent_name => $sub_names): ?>
                            <?php 
                                $is_parent_active = ($current_main == $parent_name);
                                $is_open = $is_parent_active ? 'open' : '';
                            ?>
                            <div class="cat-parent">
                                <a href="shopping.php?parent=<?php echo urlencode($parent_name); ?>" class="cat-parent-link <?php echo $is_parent_active ? 'active' : ''; ?>"><?php echo $parent_name; ?></a>
                                <ul class="sub-list <?php echo $is_open; ?>">
                                    <?php foreach($sub_names as $sub_name): ?>
                                        <?php $is_sub_active = ($current_sub == $sub_name) ? 'active' : ''; ?>
                                        <li><a href="shopping.php?category=<?php echo urlencode($sub_name); ?>" class="sub-link <?php echo $is_sub_active; ?>"><?php echo $sub_name; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>

            <main class="product-area">
                <div class="page-header">
                    <h1 class="page-title"><?php echo $page_title; ?></h1>
                    <span class="result-count">
                        <?php echo $result->num_rows; ?> books found
                        <?php if($current_filter != '' || $current_main != '' || $current_sub != ''): ?>
                            <a href="shopping.php" style="font-size:12px; margin-left:10px; color:#c83a3a;">(Clear Filters)</a>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="book-grid">
                    <?php if ($result->num_rows > 0) { while($row = $result->fetch_assoc()) {
                        // 动态图片 & 标签逻辑同首页
                        $img_file = !empty($row['cover_image']) ? $row['cover_image'] : 'allknow.png';
                        $img_src = "../IMG/bookimg/" . htmlspecialchars($img_file);
                        $desc = isset($row['description']) ? htmlspecialchars($row['description']) : "No description.";
                        $pub = isset($row['publisher']) ? htmlspecialchars($row['publisher']) : "Unknown";
                        $isbn = isset($row['isbn']) ? htmlspecialchars($row['isbn']) : "N/A";
                        $cat = isset($row['category']) ? htmlspecialchars($row['category']) : "General";
                        $parent_cat = isset($row['parent_category']) ? htmlspecialchars($row['parent_category']) : "Other";
                        $date = isset($row['publish_date']) ? htmlspecialchars($row['publish_date']) : "N/A";

                        $display_tag = "";
                        if (!empty($row['manual_tag']) && $row['manual_tag'] != 'NULL') {
                            if ($row['manual_tag'] == 'NEW') $display_tag = "NEW";
                            elseif ($row['manual_tag'] == 'HOT') $display_tag = "HOT";
                        } else {
                            if ($row['sales_count'] >= $hot_threshold && $row['sales_count'] > 0) {
                                $display_tag = "HOT";
                            } elseif (strtotime($row['publish_date']) > strtotime('-30 days')) {
                                $display_tag = "NEW";
                            }
                        }
                    ?>
                    <div class="book-card" onclick="openModal(this)" 
                         data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-author="<?php echo htmlspecialchars($row['author']); ?>" data-price="<?php echo $row['price']; ?>" data-image="<?php echo $img_src; ?>" data-desc="<?php echo $desc; ?>" data-publisher="<?php echo $pub; ?>" data-isbn="<?php echo $isbn; ?>" data-category="<?php echo $cat; ?>" data-parent="<?php echo $parent_cat; ?>" data-date="<?php echo $date; ?>">
                        <?php if ($display_tag == 'NEW'): ?><div class="tag tag-new">NEW</div><?php elseif ($display_tag == 'HOT'): ?><div class="tag tag-hot">HOT</div><?php endif; ?>
                        <img src="<?php echo $img_src; ?>" alt="Cover" class="book-cover">
                        <h3 class="book-title"><?php echo $row["title"]; ?></h3>
                        <p class="book-author"><?php echo $row["author"]; ?></p>
                        <p class="book-price">$<?php echo $row["price"]; ?></p>
                    </div>
                    <?php } } else { echo "<p style='width:100%; color:#888;'>No books found.</p>"; } ?>
                </div>
            </main>
        </div>
    </div>

    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="homepage.php">Home</a></li>
                    <li><a href="#aboutUsSection">About Us</a></li>
                    <li><a href="#feedbackSection">Contact Us</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Book Categories</h3>
                <ul class="footer-links">
                    <li><a href="shopping.php?parent=Languages">Languages</a></li>
                    <li><a href="shopping.php?parent=Children">Children</a></li>
                    <li><a href="shopping.php?parent=Fiction">Fiction</a></li>
                    <li><a href="shopping.php?parent=Classics">Classics</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Contact Us</h3>
                <div class="footer-contact-item"><img src="../IMG/phone.png" class="footer-icon"><span>1234 5678</span></div>
                <div class="footer-contact-item"><img src="../IMG/mail.png" class="footer-icon"><a href="mailto:speed@common.cpce.edu.hk">speed@common.cpce.edu.hk</a></div>
            </div>
        </div>
    </footer>

    <div id="toast-notification">Item added to cart</div>

    <div id="modalOverlay" class="modal-overlay hidden" onclick="closeModal()" style="display:none;"></div>
    <div id="productModal" class="modal hidden" style="display:none;">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-left"><img id="m-image" src="" alt="Cover" class="modal-img"></div>
        <div class="modal-right">
            <h2 id="m-title" class="modal-title"></h2>
            <p id="m-author" class="modal-author"></p>
            <p id="m-price" class="modal-price"></p>
            <div class="meta-grid">
                <div class="meta-item"><strong>Publisher:</strong> <span id="m-publisher"></span></div>
                <div class="meta-item"><strong>Date:</strong> <span id="m-date"></span></div>
                <div class="meta-item"><strong>ISBN:</strong> <span id="m-isbn"></span></div>
                <div class="meta-item"><strong>Main Category:</strong> <span id="m-parent"></span></div>
                <div class="meta-item"><strong>Sub Category:</strong> <span id="m-category"></span></div>
            </div>
            <div class="modal-desc"><p id="m-desc"></p></div>
            <div class="modal-actions">
                <button class="btn-buy" onclick="buyNow()">Buy Now</button>
                <button class="btn-cart" onclick="addToCart()">Add to Cart</button>
            </div>
        </div>
    </div>

    <script src="../js/script.js"></script>
</body>
</html>