<?php
// ==================================================================
// File: homepage.php
// Description: 网站首页。
// Functionality:
// 1. 动态计算 'HOT' 标签的销量门槛 (Top 10)。
// 2. 获取所有书籍，并应用混合标签逻辑 (Manual > Auto)。
// 3. 筛选出 'NEW' 和 'HOT' 列表，分别展示前 5 本。
// ==================================================================

session_start();
include 'db_connect.php';

// ------------------------------------------------------------------
// 1. 计算 HOT 门槛逻辑
// ------------------------------------------------------------------
// 原理：找出全站销量排名第 10 的那本书的销量数，作为成为 HOT 的最低标准。
$hot_threshold = 1000; // 默认保底值
$threshold_sql = "SELECT sales_count FROM books ORDER BY sales_count DESC LIMIT 1 OFFSET 9";
$threshold_result = $conn->query($threshold_sql);

if ($threshold_result && $threshold_result->num_rows > 0) {
    $row_t = $threshold_result->fetch_assoc();
    $hot_threshold = $row_t['sales_count'];
    // 防止新店门槛过低（例如都是0），强制至少卖出10本才算 HOT
    if ($hot_threshold < 10) $hot_threshold = 10; 
}

// ------------------------------------------------------------------
// 2. 数据查询与标签筛选
// ------------------------------------------------------------------
$sql_all = "SELECT * FROM books ORDER BY publish_date DESC"; // 默认按日期倒序
$result_all = $conn->query($sql_all);

$new_releases_list = []; // 存放最终判定为 NEW 的书
$best_sellers_list = []; // 存放最终判定为 HOT 的书

if ($result_all->num_rows > 0) {
    while ($row = $result_all->fetch_assoc()) {
        $final_tag = "";
        
        // --- 优先级 A: 手动标签 (Manual Tag) ---
        if (!empty($row['manual_tag']) && $row['manual_tag'] != 'NULL') {
            if ($row['manual_tag'] == 'NEW') $final_tag = "NEW";
            elseif ($row['manual_tag'] == 'HOT') $final_tag = "HOT";
        } 
        // --- 优先级 B: 自动逻辑 (Auto Logic) ---
        else {
            // 销量优先：如果销量达标，算 HOT
            if ($row['sales_count'] >= $hot_threshold && $row['sales_count'] > 0) {
                $final_tag = "HOT";
            } 
            // 日期其次：30天内算 NEW
            elseif (strtotime($row['publish_date']) > strtotime('-30 days')) {
                $final_tag = "NEW";
            }
        }

        // 根据判定结果分流
        if ($final_tag == 'NEW') $new_releases_list[] = $row;
        elseif ($final_tag == 'HOT') $best_sellers_list[] = $row;
    }
}

// 截取前 5 本展示
$display_new = array_slice($new_releases_list, 0, 5);

// Best Sellers 列表需要按销量重新降序排列（因为原数据是按日期排的）
usort($best_sellers_list, function($a, $b) { return $b['sales_count'] - $a['sales_count']; });
$display_hot = array_slice($best_sellers_list, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Temple - Bookstore</title>
    <link rel="stylesheet" href="../css/style.css"> </head>
<body>

    <header id="main-header">
        <div class="logo-section"><img src="../IMG/logobig.png" alt="Logo" class="logo-img"></div>
        <hr class="header-divider">
        <div class="header-inner">
            <div class="nav-bar">
                <a href="homepage.php" class="small-logo-link"><img src="../IMG/slogo.png" alt="Logo" class="small-logo"></a>
                
                <nav class="nav-links">
                    <a href="homepage.php" class="active">Home</a>
                    <a href="shopping.php">Shopping</a>
                    <a href="#aboutUsSection">About Us</a>
                    <a href="#feedbackSection">Contact</a>
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
        <div class="banner-section"><img src="../IMG/slogan.png" alt="Slogan" class="banner-img"></div>
        <hr class="banner-divider">

        <section class="content-section">
            <div class="section-header"><h2 class="section-title">New Releases</h2><a href="shopping.php?type=new" class="view-all-btn">View All</a></div>
            <div class="book-grid">
                <?php if (!empty($display_new)) { foreach ($display_new as $row) {
                    // 动态图片路径
                    $img_file = !empty($row['cover_image']) ? $row['cover_image'] : 'allknow.png';
                    $img_src = "../IMG/" . htmlspecialchars($img_file);
                    // 数据准备
                    $desc = isset($row['description']) ? htmlspecialchars($row['description']) : "No description.";
                    $pub = isset($row['publisher']) ? htmlspecialchars($row['publisher']) : "Unknown";
                    $isbn = isset($row['isbn']) ? htmlspecialchars($row['isbn']) : "N/A";
                    $cat = isset($row['category']) ? htmlspecialchars($row['category']) : "General";
                    $parent_cat = isset($row['parent_category']) ? htmlspecialchars($row['parent_category']) : "Other";
                    $date = isset($row['publish_date']) ? htmlspecialchars($row['publish_date']) : "N/A";
                ?>
                <div class="book-card" onclick="openModal(this)" 
                     data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-author="<?php echo htmlspecialchars($row['author']); ?>" data-price="<?php echo $row['price']; ?>" data-image="<?php echo $img_src; ?>" data-desc="<?php echo $desc; ?>" data-publisher="<?php echo $pub; ?>" data-isbn="<?php echo $isbn; ?>" data-category="<?php echo $cat; ?>" data-parent="<?php echo $parent_cat; ?>" data-date="<?php echo $date; ?>">
                    <div class="tag tag-new">NEW</div> 
                    <img src="<?php echo $img_src; ?>" alt="Cover" class="book-cover">
                    <h3 class="book-title"><?php echo $row["title"]; ?></h3>
                    <p class="book-author"><?php echo $row["author"]; ?></p>
                    <p class="book-price">$<?php echo $row["price"]; ?></p>
                </div>
                <?php } } else { echo "<p>No new releases found.</p>"; } ?>
            </div>
        </section>

        <hr class="section-divider">

        <section class="content-section">
            <div class="section-header"><h2 class="section-title">Best Sellers</h2><a href="shopping.php?type=hot" class="view-all-btn">View All</a></div>
            <div class="book-grid">
                <?php if (!empty($display_hot)) { foreach ($display_hot as $row) {
                    // 同样的动态逻辑
                    $img_file = !empty($row['cover_image']) ? $row['cover_image'] : 'allknow.png';
                    $img_src = "../IMG/" . htmlspecialchars($img_file);
                    $desc = isset($row['description']) ? htmlspecialchars($row['description']) : "No description.";
                    $pub = isset($row['publisher']) ? htmlspecialchars($row['publisher']) : "Unknown";
                    $isbn = isset($row['isbn']) ? htmlspecialchars($row['isbn']) : "N/A";
                    $cat = isset($row['category']) ? htmlspecialchars($row['category']) : "General";
                    $parent_cat = isset($row['parent_category']) ? htmlspecialchars($row['parent_category']) : "Other";
                    $date = isset($row['publish_date']) ? htmlspecialchars($row['publish_date']) : "N/A";
                ?>
                <div class="book-card" onclick="openModal(this)"
                     data-id="<?php echo $row['id']; ?>" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-author="<?php echo htmlspecialchars($row['author']); ?>" data-price="<?php echo $row['price']; ?>" data-image="<?php echo $img_src; ?>" data-desc="<?php echo $desc; ?>" data-publisher="<?php echo $pub; ?>" data-isbn="<?php echo $isbn; ?>" data-category="<?php echo $cat; ?>" data-parent="<?php echo $parent_cat; ?>" data-date="<?php echo $date; ?>">
                    <div class="tag tag-hot">HOT</div> 
                    <img src="<?php echo $img_src; ?>" alt="Cover" class="book-cover">
                    <h3 class="book-title"><?php echo $row["title"]; ?></h3>
                    <p class="book-author"><?php echo $row["author"]; ?></p>
                    <p class="book-price">$<?php echo $row["price"]; ?></p>
                </div>
                <?php } } else { echo "<p>No best sellers found.</p>"; } ?>
            </div>
        </section>

        <hr class="section-divider">

        <section class="about-section" id="aboutUsSection">
            <div class="section-header" style="justify-content: center; margin-bottom: 30px;"><h2 class="section-title">About Us</h2></div>
            <div class="about-content">
                <h3 class="about-subtitle js-typewriter" data-text='"Where Wisdom Finds Its Sanctuary"'></h3>
                <p class="about-text js-typewriter" data-text="Welcome to Knowledge Temple. Our name is not chosen by chance; it reflects our deepest belief that books are the sacred vessels of human wisdom. Just as a temple provides a sanctuary for the soul, we aspire to provide a sanctuary for the mind."></p>
                <p class="about-text js-typewriter" data-text="Founded with a singular mission, we aim to bridge cultures through languages—from Eastern Classics to Western Philosophy. Whether you are seeking the thrill of a new Novel or the enlightenment of History, Knowledge Temple is dedicated to curating the finest collection for seekers of truth and beauty."></p>
                <img src="../IMG/slogo.png" alt="Logo" class="about-logo">
            </div>
        </section>

        <hr class="section-divider">

        <section class="content-section team-section">
            <div class="section-header" style="justify-content: center; margin-bottom: 60px;"><h2 class="section-title">Meet Our Team</h2></div>
            <div class="team-container">
                <div class="team-member"><div class="member-img-box"><img src="../IMG/mem1.png" alt="Member 1" class="member-img"></div><div class="member-info"><span class="member-role">Leader</span><h3 class="member-name">Chan Hoi Sui</h3><p class="member-desc">"Books are the quietest and most constant of friends." Eleanor founded Knowledge Temple with a vision to create a sanctuary for bibliophiles. With 20 years of experience in rare manuscript collection, she curates our Classics section.</p></div></div>
                <div class="team-member reverse"><div class="member-img-box"><img src="../IMG/mem2.png" alt="Member 2" class="member-img"></div><div class="member-info"><span class="member-role">Chief Editor</span><h3 class="member-name">Arthur Blackwood</h3><p class="member-desc">Arthur oversees our Fiction department. A published novelist himself, he has an uncanny ability to spot the next big bestseller before it hits the shelves. He believes every story deserves a listener.</p></div></div>
                <div class="team-member"><div class="member-img-box"><img src="../IMG/mem3.png" alt="Member 3" class="member-img"></div><div class="member-info"><span class="member-role">Children's Literature Specialist</span><h3 class="member-name">Lily Chen</h3><p class="member-desc">Lily brings magic to our Children's section. She organizes our weekly storytelling hours and carefully selects picture books that inspire imagination and kindness in young minds.</p></div></div>
                <div class="team-member reverse"><div class="member-img-box"><img src="../IMG/mem4.png" alt="Member 4" class="member-img"></div><div class="member-info"><span class="member-role">Language Expert</span><h3 class="member-name">Hiroshi Tanaka</h3><p class="member-desc">Fluent in five languages, Hiroshi manages our diverse Language learning collection. He is dedicated to breaking down cultural barriers and helping our customers find the perfect tools to master new tongues.</p></div></div>
                <div class="team-member"><div class="member-img-box"><img src="../IMG/mem5.jpg" alt="Member 5" class="member-img"></div><div class="member-info"><span class="member-role">Technology Lead</span><h3 class="member-name">Sarah O'Connor</h3><p class="member-desc">Sarah ensures your online shopping experience is seamless. She built the digital infrastructure of Knowledge Temple, combining traditional aesthetics with modern e-commerce efficiency.</p></div></div>
            </div>
        </section>

        <section class="feedback-section" id="feedbackSection">
            <div class="feedback-wrapper">
                <div class="feedback-form-container">
                    <div class="feedback-header"><h2 class="feedback-title">Feedback & Contact</h2></div>
                    <form id="feedbackForm" class="feedback-form">
                        <input type="text" name="name" class="pill-input" placeholder="Full Name" required>
                        <input type="email" name="email" class="pill-input" placeholder="Email address" required>
                        <input type="tel" name="phone" class="pill-input" placeholder="Phone number">
                        <textarea name="message" class="pill-input" placeholder="Your message or suggestions..." required></textarea>
                        <button type="submit" class="send-btn"> SEND MESSAGE 
                        <svg viewBox="0 0 24 24"><path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/></svg></button>
                    </form>
                </div>
                <div class="contact-info-container">
                    <h3 class="contact-header">Contact Info</h3>
                    <div class="info-item"><img src="../IMG/map.png" alt="Location" class="info-icon"><span class="info-text">Knowledge Temple, Book Street<br>Hong Kong</span></div>
                    <div class="info-item"><img src="../IMG/phone.png" alt="Phone" class="info-icon"><span class="info-text">1234 5678</span></div>
                    <div class="info-item"><img src="../IMG/mail.png" alt="Email" class="info-icon"><span class="info-text"><a href="mailto:speed@common.cpce.edu.hk">speed@common.cpce.edu.hk</a></span></div>
                </div>
            </div>
        </section>

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