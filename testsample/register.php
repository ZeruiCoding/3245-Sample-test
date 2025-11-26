<?php
// 开启 Session (虽然注册页面不一定立马需要，但为了习惯建议加上)
session_start();
include 'db_connect.php';

$message = "";

// 检查表单是否提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // 1. 检查用户名是否已存在
    $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $message = "Error: Username or Email already exists!";
    } else {
        // 2. 密码加密 (非常重要！永远不要存储明文密码)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 3. 插入数据库
        $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

        if ($conn->query($sql) === TRUE) {
            // 注册成功，跳转到登录页
            header("Location: login.php"); 
            exit();
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Knowledge Temple</title>
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
        <h2>Create Account</h2>
        
        <?php if($message): ?>
            <p class="error"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        
        <p class="link">Already have an account? <a href="login.php">Login here</a></p>
        <p class="link"><a href="homepage.php">Back to Home</a></p>
    </div>
</body>
</html>