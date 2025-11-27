<?php
// ==================================================================
// File: admin_add_book.php
// Description: 后台管理页面，用于向数据库添加新书籍。
// Functionality:
// 1. 连接数据库。
// 2. 处理 POST 请求，接收并清洗表单数据。
// 3. 执行 SQL INSERT 语句，将新书信息写入 'books' 表。
// 4. 提供包含 JS 二级联动的 HTML 表单。
// ==================================================================

include 'db_connect.php'; // 引入数据库连接配置
$message = ""; // 用于存储操作反馈信息（成功或失败）

// ------------------------------------------------------------------
// PHP 逻辑区：处理表单提交
// ------------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- 1. 数据接收与清洗 ---
    // 使用 real_escape_string 防止 SQL 注入攻击
    $title          = $conn->real_escape_string($_POST['title']);
    $author         = $conn->real_escape_string($_POST['author']);
    $price          = $_POST['price']; // 数字类型通常不需要转义，但在 SQL 中仍建议处理
    $publisher      = $conn->real_escape_string($_POST['publisher']);
    $isbn           = $conn->real_escape_string($_POST['isbn']);
    $publish_date   = $_POST['publish_date'];
    $description    = $conn->real_escape_string($_POST['description']);
    
    // 分类信息 (Parent & Sub Category)
    $parent_category = $conn->real_escape_string($_POST['parent_category']);
    $category       = $conn->real_escape_string($_POST['category']);
    
    // 图片处理：如果用户没填，默认使用 'allknow.png'
    $cover_image    = !empty($_POST['cover_image']) ? $conn->real_escape_string($_POST['cover_image']) : 'allknow.png';
    $sales_count    = 0; // 新书默认销量为 0

    // 标签处理：获取管理员手动选择的标签 ('NEW', 'HOT', 'NONE' 或 'NULL')
    $manual_tag     = !empty($_POST['manual_tag']) ? $conn->real_escape_string($_POST['manual_tag']) : "NULL";

    // --- 2. 构建 SQL 语句 ---
    // 注意：如果 manual_tag 是字符串 "NULL"，在 SQL 中不应加引号，表示数据库的 NULL 值
    if ($manual_tag === "NULL") {
        $sql = "INSERT INTO books (title, author, price, publisher, isbn, parent_category, category, publish_date, description, cover_image, sales_count, manual_tag) 
                VALUES ('$title', '$author', '$price', '$publisher', '$isbn', '$parent_category', '$category', '$publish_date', '$description', '$cover_image', '$sales_count', NULL)";
    } else {
        // 如果有具体标签值，则作为字符串插入
        $sql = "INSERT INTO books (title, author, price, publisher, isbn, parent_category, category, publish_date, description, cover_image, sales_count, manual_tag) 
                VALUES ('$title', '$author', '$price', '$publisher', '$isbn', '$parent_category', '$category', '$publish_date', '$description', '$cover_image', '$sales_count', '$manual_tag')";
    }

    // --- 3. 执行查询并反馈 ---
    if ($conn->query($sql) === TRUE) {
        $message = "✅ Book added successfully! <a href='homepage.php'>Go to Homepage</a>";
    } else {
        $message = "❌ Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New Book</title>
    <link rel="stylesheet" href="../css/admin.css"> <script src="../js/admin.js"></script> </head>
<body>

    <div class="container">
        <h2>Add New Book</h2>
        
        <?php if($message != ""): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_add_book.php">
            
            <div class="form-group">
                <label>Book Title</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" required>
            </div>

            <div class="form-group" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Main Category</label>
                    <select name="parent_category" id="parent_cat" onchange="updateSubCategories()" required>
                        <option value="">Select...</option>
                        <option value="Languages">Languages</option>
                        <option value="Children">Children</option>
                        <option value="Fiction">Fiction</option>
                        <option value="Classics">Classics</option>
                    </select>
                </div>
                <div style="flex: 1;">
                    <label>Sub Category</label>
                    <select name="category" id="sub_cat" required>
                        <option value="">Select Main Category first</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
                <div style="flex: 1;">
                    <label>Publisher</label>
                    <input type="text" name="publisher">
                </div>
            </div>

            <div class="form-group" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>ISBN</label>
                    <input type="text" name="isbn">
                </div>
                <div style="flex: 1;">
                    <label>Publish Date</label>
                    <input type="date" name="publish_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Manual Tag (Optional)</label>
                <select name="manual_tag">
                    <option value="">Auto (Based on Date/Sales)</option> <option value="NEW">Force "NEW"</option>            <option value="HOT">Force "HOT"</option>            <option value="NONE">No Tag (Hide)</option>         </select>
                <small style="color: #888; font-size: 12px;">"Auto" means system decides based on Top 10 sales or Date.</small>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>

            <div class="form-group">
                <label>Image Filename</label>
                <input type="text" name="cover_image" value="allknow.png">
            </div>

            <button type="submit" class="btn">Add Book</button>
        </form>
        
        <a href="homepage.php" class="back-link">← Back to Bookstore</a>
    </div>

</body>
</html>