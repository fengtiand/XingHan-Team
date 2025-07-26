<?php
// Docker环境数据库配置文件
// 从环境变量获取数据库配置，如果没有则使用默认值
$db_host = getenv('DB_HOST') ?: 'mysql';
$db_name = getenv('DB_NAME') ?: 'xinghan_team';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: 'xinghan123';

// 创建数据库连接
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // 设置时区
    $pdo->exec("SET time_zone = '+08:00'");
    
} catch(PDOException $e) {
    error_log("数据库连接失败: " . $e->getMessage());
    die("数据库连接失败，请检查配置");
}

// 兼容原有的mysqli连接方式
$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$connection) {
    error_log("MySQL连接失败: " . mysqli_connect_error());
    die("数据库连接失败");
}
mysqli_set_charset($connection, "utf8mb4");
?>