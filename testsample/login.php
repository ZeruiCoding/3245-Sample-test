<?php
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
    <style>
        body { background-color: #fdfbf5; font-family: "Georgia", serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; color: #4a4a4a; }
        .auth-card { background: white; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #efece5; text-align: center; }
        .logo-img { height: 50px; margin-bottom: 20px; }
        h2 { margin-bottom: 20px; color: #4a3f35; }
        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px; }
        input { width: 100%; padding: 10px; border: 1px solid #ccc; font-family: inherit; box-sizing: border-box; }
        .btn { width: 100%; padding: 10px; background-color: #c83a3a; color: white; border: none; cursor: pointer; font-size: 16px; margin-top: 10px; }
        .btn:hover { background-color: #a62e2e; }
        .link { margin-top: 15px; font-size: 14px; }
        .link a { color: #c83a3a; text-decoration: none; }
        .error { color: red; font-size: 14px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <img src="./IMG/logowhite.png" alt="Logo" class="logo-img">
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