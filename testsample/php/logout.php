<?php
/**
 * ==================================================================
 * File: logout.php
 * Description: 处理用户登出逻辑。
 * Principle: 
 * 1. 恢复现有 Session。
 * 2. 彻底销毁 Session 数据。
 * 3. 将用户重定向回首页。
 * ==================================================================
 */

// 1. 开启 Session
// 必须先开启才能找到当前用户的 Session ID，从而进行销毁操作。
session_start();

// 2. 销毁 Session
// session_destroy() 会删除服务器端存储的所有关于当前会话的数据。
// 这意味着 $_SESSION 数组被清空，用户登录状态失效。
session_destroy(); 

// 3. 重定向
// 登出后，通常将用户送回首页或登录页。
header("Location: homepage.php"); 

// 4. 终止脚本
exit();
?>