<?php

include 'db_connect.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title          = $conn->real_escape_string($_POST['title']);
    $author         = $conn->real_escape_string($_POST['author']);
    $price          = $_POST['price'];
    $publisher      = $conn->real_escape_string($_POST['publisher']);
    $isbn           = $conn->real_escape_string($_POST['isbn']);
    $publish_date   = $_POST['publish_date'];
    $description    = $conn->real_escape_string($_POST['description']);
    
    $parent_category = $conn->real_escape_string($_POST['parent_category']);
    $category       = $conn->real_escape_string($_POST['category']);
    
    // default: allknow.png
    $cover_image    = !empty($_POST['cover_image']) ? $conn->real_escape_string($_POST['cover_image']) : 'allknow.png';
    $sales_count    = 0; 

    // tag
    $manual_tag     = !empty($_POST['manual_tag']) ? $conn->real_escape_string($_POST['manual_tag']) : "NULL";

    if ($manual_tag === "NULL") {
        $sql = "INSERT INTO books (title, author, price, publisher, isbn, parent_category, category, publish_date, description, cover_image, sales_count, manual_tag) 
                VALUES ('$title', '$author', '$price', '$publisher', '$isbn', '$parent_category', '$category', '$publish_date', '$description', '$cover_image', '$sales_count', NULL)";
    } else {
        $sql = "INSERT INTO books (title, author, price, publisher, isbn, parent_category, category, publish_date, description, cover_image, sales_count, manual_tag) 
                VALUES ('$title', '$author', '$price', '$publisher', '$isbn', '$parent_category', '$category', '$publish_date', '$description', '$cover_image', '$sales_count', '$manual_tag')";
    }

    if ($conn->query($sql) === TRUE) {
        $message = " Book added successfully! <a href='homepage.php'>Go to Homepage</a>";
    } else {
        $message = " Error: " . $sql . "<br>" . $conn->error;
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
                    <option value="">Auto (Based on Date/Sales)</option> 
                    <option value="NEW">Force "NEW"</option>            
                    <option value="HOT">Force "HOT"</option>            
                    <option value="NONE">No Tag (Hide)</option>         </select>
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