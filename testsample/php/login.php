<?php
// ==================================================================
// File: login.php
// Description: 用户登录页面。
// Functionality:
// 1. 提供登录表单。
// 2. 验证用户名和密码。
// 3. 登录成功后设置 Session 并跳转首页。
// ==================================================================

session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // 1. 在数据库中查找用户
    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // 2. 验证密码 (将输入的密码与数据库中的 Hash 比对)
        if (password_verify($password, $row['password'])) {
            // 登录成功！设置 Session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            
            // 跳转回主页
            header("Location: homepage.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Knowledge Temple</title>
    <link rel="stylesheet" href="../css/login.css"> </head>
<body>
    <div class="auth-card">
        <img src="../IMG/slogo.png" alt="Logo" class="logo-img">
        <h2>Welcome Back</h2>
        
        <?php if($message): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        
        <p class="link">New here? <a href="register.php">Create an account</a></p>
        <p class="link"><a href="homepage.php">Back to Home</a></p>
    </div>
</body>
</html>