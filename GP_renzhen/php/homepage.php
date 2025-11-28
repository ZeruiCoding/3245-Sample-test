<?php
session_start();
include 'db_connect.php';

$hot_threshold = 1000; 
$threshold_sql = "SELECT sales_count FROM books ORDER BY sales_count DESC LIMIT 1 OFFSET 9";
$threshold_result = $conn->query($threshold_sql);

if ($threshold_result && $threshold_result->num_rows > 0) {
    $row_t = $threshold_result->fetch_assoc();
    $hot_threshold = $row_t['sales_count'];
    if ($hot_threshold < 10) $hot_threshold = 10; 
}

$sql_all = "SELECT * FROM books ORDER BY publish_date DESC";
$result_all = $conn->query($sql_all);

$new_releases_list = [];
$best_sellers_list = [];

if ($result_all->num_rows > 0) {
    while ($row = $result_all->fetch_assoc()) {
        $final_tag = "";
        if (!empty($row['manual_tag']) && $row['manual_tag'] != 'NULL') {
            if ($row['manual_tag'] == 'NEW') $final_tag = "NEW";
            elseif ($row['manual_tag'] == 'HOT') $final_tag = "HOT";
        } else {
            if ($row['sales_count'] >= $hot_threshold && $row['sales_count'] > 0) {
                $final_tag = "HOT";
            } elseif (strtotime($row['publish_date']) > strtotime('-30 days')) {
                $final_tag = "NEW";
            }
        }

        if ($final_tag == 'NEW') {
            $new_releases_list[] = $row;
        } elseif ($final_tag == 'HOT') {
            $best_sellers_list[] = $row;
        }
    }
}

$display_new = array_slice($new_releases_list, 0, 5);
usort($best_sellers_list, function($a, $b) { return $b['sales_count'] - $a['sales_count']; });
$display_hot = array_slice($best_sellers_list, 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Temple - Bookstore</title>
    <link rel="stylesheet" href="../css/style.css"> 
</head>
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
                        <button type="submit" class="search-btn">
                            <img src="../IMG/search.png" alt="Search" class="search-icon">
                        </button>
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
                    $img_file = !empty($row['cover_image']) ? $row['cover_image'] : 'allknow.png';
                    $img_src = "../IMG/bookimg/" . htmlspecialchars($img_file);
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
                    $img_file = !empty($row['cover_image']) ? $row['cover_image'] : 'allknow.png';
                    $img_src = "../IMG/bookimg/" . htmlspecialchars($img_file);
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
            <div class="section-header" style="justify-content: center; margin-bottom: 30px;">
                <h2 class="section-title">About Us</h2>
            </div>
            <div class="about-content">
                <h3 class="about-subtitle js-typewriter" data-text='"Knowledge is the Oasis in the Desert"'></h3>
                <p class="about-text js-typewriter" data-text="At Knowledge Temple, we believe that in the vast desert of life, knowledge is the only oasis that never dries up. Our core mission is simple yet profound: to encourage everyone to read more. We strive to provide a spiritual sanctuary where every soul can find nourishment and strength through the power of words."></p>
                <p class="about-text js-typewriter" data-text="We have designed a user-friendly platform to support your reading journey. You can easily use our Search function to find specific titles, browse through curated Categories, or explore our latest New Releases and Best Sellers. We are here to make your quest for wisdom as effortless as possible."></p>
                <img src="../IMG/slogo.png" alt="Logo" class="about-logo">
            </div>
        </section>

        <hr class="section-divider">

        <section class="content-section team-section">
            <div class="section-header" style="justify-content: center; margin-bottom: 60px;">
                <h2 class="section-title">Meet Our Team</h2>
            </div>
            
            <div class="team-container">
                
                <div class="team-member">
                    <div class="member-img-box">
                        <img src="../IMG/mem1.png" alt="Lai Chun Hei" class="member-img">
                    </div>
                    <div class="member-info">
                        <span class="member-role">Team Leader & Planner</span>
                        <h3 class="member-name">Lai Chun Hei</h3>
                        <p class="member-desc">
                            As the team leader, Lai is responsible for the overall project planning and task allocation. He also spearheaded the design and implementation of the book card components, ensuring a consistent and appealing product display.
                        </p>
                    </div>
                </div>

                <div class="team-member reverse">
                    <div class="member-img-box">
                        <img src="../IMG/mem2.png" alt="Chan Hoi Sui" class="member-img">
                    </div>
                    <div class="member-info">
                        <span class="member-role">Lead Developer</span>
                        <h3 class="member-name">Chan Hoi Sui</h3>
                        <p class="member-desc">
                            The core architect of the website. Chan handled the majority of the coding and design work, with a specialized focus on database management and the homepage's functionality and aesthetics.
                        </p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-img-box">
                        <img src="../IMG/mem3.png" alt="Tang Wai Lam" class="member-img">
                    </div>
                    <div class="member-info">
                        <span class="member-role">Cart System Developer</span>
                        <h3 class="member-name">Tang Wai Lam</h3>
                        <p class="member-desc">
                            Tang is the mind behind the seamless shopping experience. He was in charge of designing and coding the Shopping Cart interface, ensuring that the checkout process is intuitive and user-friendly.
                        </p>
                    </div>
                </div>

                <div class="team-member reverse">
                    <div class="member-img-box">
                        <img src="../IMG/mem4.png" alt="Zhang KunTing" class="member-img">
                    </div>
                    <div class="member-info">
                        <span class="member-role">Authentication Specialist</span>
                        <h3 class="member-name">Zhang KunTing</h3>
                        <p class="member-desc">
                            Zhang focused on the user security and access control. He designed and developed the Login and Registration pages, providing a secure gateway for our users to access their personal accounts.
                        </p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-img-box">
                        <img src="../IMG/mem5.png" alt="Wong Ting Yik" class="member-img">
                    </div>
                    <div class="member-info">
                        <span class="member-role">Data Analyst & Content Curator</span>
                        <h3 class="member-name">Wong Ting Yik</h3>
                        <p class="member-desc">
                            Wong played a crucial role in content management. He was responsible for collecting detailed book data and crafting the introductory content for the homepage, giving the bookstore its rich and informative substance.
                        </p>
                    </div>
                </div>

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
                    <div class="info-item">
                        <img src="../IMG/map.png" alt="Location" class="info-icon">
                        <span class="info-text">Knowledge Temple, Book Street<br>Hong Kong</span>
                    </div>
                    <div class="info-item">
                        <img src="../IMG/phone.png" alt="Phone" class="info-icon">
                        <span class="info-text">1234 5678</span>
                    </div>
                    <div class="info-item">
                        <img src="../IMG/mail.png" alt="Email" class="info-icon">
                        <span class="info-text"><a href="mailto:speed@common.cpce.edu.hk">speed@common.cpce.edu.hk</a></span>
                    </div>
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