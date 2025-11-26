<?php
// 1. 连接数据库
include 'db_connect.php';

$message = "";

// 2. 处理表单提交逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据 (使用 real_escape_string 防止 SQL 注入)
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $price = $_POST['price'];
    $publisher = $conn->real_escape_string($_POST['publisher']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $category = $conn->real_escape_string($_POST['category']);
    $publish_date = $_POST['publish_date'];
    $description = $conn->real_escape_string($_POST['description']);
    
    // 图片文件名 (默认使用 allknow.png，你也可以手动输入其他文件名)
    $cover_image = !empty($_POST['cover_image']) ? $conn->real_escape_string($_POST['cover_image']) : 'allknow.png';
    
    // 销量 (新书默认为 0)
    $sales_count = 0;

    // 3. 插入数据库
    $sql = "INSERT INTO books (title, author, price, publisher, isbn, category, publish_date, description, cover_image, sales_count) 
            VALUES ('$title', '$author', '$price', '$publisher', '$isbn', '$category', '$publish_date', '$description', '$cover_image', '$sales_count')";

    if ($conn->query($sql) === TRUE) {
        $message = "✅ Book added successfully! <a href='index.php'>Go to Homepage</a>";
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
    <style>
        /* 复用之前的风格 */
        body { background-color: #fdfbf5; font-family: "Georgia", serif; color: #4a4a4a; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border: 1px solid #efece5; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { text-align: center; color: #4a3f35; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; font-family: inherit; box-sizing: border-box; border-radius: 4px; }
        textarea { height: 100px; resize: vertical; }
        
        .btn { width: 100%; padding: 12px; background-color: #c83a3a; color: white; border: none; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 20px; transition: background 0.3s; }
        .btn:hover { background-color: #a62e2e; }
        
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; text-align: center; }
        .alert a { color: #155724; font-weight: bold; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #666; text-decoration: none; }
        .back-link:hover { color: #c83a3a; }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add New Book</h2>

        <?php if($message != ""): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_add_book.php">
            <div class="form-group">
                <label>Book Title</label>
                <input type="text" name="title" required placeholder="e.g. The Great Adventure">
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" required placeholder="e.g. J.K. Rowling">
            </div>

            <div class="form-group" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" required placeholder="19.99">
                </div>
                <div style="flex: 1;">
                    <label>Category</label>
                    <input type="text" name="category" required placeholder="Fiction">
                </div>
            </div>

            <div class="form-group" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>Publisher</label>
                    <input type="text" name="publisher" placeholder="Publisher Name">
                </div>
                <div style="flex: 1;">
                    <label>ISBN</label>
                    <input type="text" name="isbn" placeholder="978-...">
                </div>
            </div>

            <div class="form-group">
                <label>Publish Date</label>
                <input type="date" name="publish_date" required value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Short summary of the book..."></textarea>
            </div>

            <div class="form-group">
                <label>Image Filename (in ./IMG/ folder)</label>
                <input type="text" name="cover_image" value="allknow.png">
                <small style="color: #999;">Default: allknow.png</small>
            </div>

            <button type="submit" class="btn">Add Book</button>
        </form>

        <a href="homepage.php" class="back-link">← Back to Bookstore</a>
    </div>

</body>
</html>