<?php
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
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
    <div class="auth-card">
        <img src="../IMG/slogo.png" alt="Logo" class="logo-img">
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