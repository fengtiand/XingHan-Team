<?php
/**
 * =========================================================================
 * 
 *                      XingHan-Team 官网程序 
 * =========================================================================
 * 
 * @package     XingHan-Team Official Website
 * @author      XingHan Development Team
 * @copyright   Copyright (c) 2024, XingHan-Team
 * @link        https://www.ococn.cn
 * @since       Version 1.5.0
 * @filesource  By 奉天
 * 
 * =========================================================================
 * 
 * XingHan-Team 星涵网络工作室官方网站管理系统
 * 版权所有 (C) 2024 XingHan-Team。保留所有权利。
 * 
 * 本软件受著作权法和国际公约的保护。
 * 
 * 感谢您选择 XingHan-Team 的产品。如有任何问题或建议，请联系我们。
 * 
 * =========================================================================
 */
session_start();
$db_config_file = '../db_connect.php';
$install_lock_file = '../install.lock';
if (file_exists($install_lock_file)) {
    die('网站已经安装。如需重新安装，请删除 install.lock 文件。');
}
if (file_exists($db_config_file)) {
    unlink($db_config_file);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_user = $_POST['admin_user'];
    $admin_pass = $_POST['admin_pass'];
    $conn = new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_error) {
        $_SESSION['error'] = "数据库连接失败: " . $conn->connect_error;
        header('Location: index.php?step=2');
        exit;
    }
    $conn->set_charset("UTF-8");
    if (!$conn->select_db($db_name)) {
        $_SESSION['error'] = "无法选择数据库: " . $conn->error;
        header('Location: index.php?step=2');
        exit;
    }
    $config_content = "<?php\n";
    $config_content .= "ini_set('default_charset', 'UTF-8');\n";
    $config_content .= "\$conn = new mysqli('$db_host', '$db_user', '$db_pass', '$db_name');\n";
    $config_content .= "if (\$conn->connect_error) {\n";
    $config_content .= "    die(\"连接失败: \" . \$conn->connect_error);\n";
    $config_content .= "}\n";
    $config_content .= "\$conn->set_charset(\"utf8mb4\");\n";
    $config_content .= "?>";

    if (file_put_contents('../db_connect.php', $config_content) === false) {
        $_SESSION['error'] = "无法创建 db_connect.php 文件。请确保有写入权限。";
        header('Location: index.php?step=2');
        exit;
    }
    $sql = file_get_contents('install.sql');
    if ($conn->multi_query($sql) === TRUE) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    } else {
        $_SESSION['error'] = "创建数据库表失败: " . $conn->error;
        header('Location: index.php?step=2');
        exit;
    }
    $stmt = $conn->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $admin_user);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashed_password, $admin_user);
    } else {
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $admin_user, $hashed_password);
    }
    if ($stmt->execute()) {
        $lock_file = '../install.lock';
        if (file_put_contents($lock_file, date('Y-m-d H:i:s')) !== false) {
            $success_message = "安装成功！请删除 install 文件夹以确保安全。";
            if (file_exists($lock_file)) {
                echo '<!DOCTYPE html>
                <html lang="zh-CN">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>安装完成</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>
                    <div class="container mt-5">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">安装完成！</h4>
                            <p>' . $success_message . '</p>
                            <hr>
                            <p class="mb-0">网站已经安装。如需重新安装，请删除 install.lock 文件。</p>
                        </div>
                        <div class="mt-3">
                            <a href="../index.php" class="btn btn-primary">访问网站首页</a>
                            <a href="../admin/login.php" class="btn btn-secondary">访问管理后台</a>
                        </div>
                    </div>
                </body>
                </html>';
                exit;
            } else {
                $_SESSION['error'] = "无法创建安装锁定文件，请检查目录权限。";
                header('Location: index.php?step=2');
                exit;
            }
        } else {
            $_SESSION['error'] = "无法创建安装锁定文件，请检查目录权限。";
            header('Location: index.php?step=2');
            exit;
        }
    } else {
        $_SESSION['error'] = "创建或更新管理员用户失败: " . $stmt->error;
        header('Location: index.php?step=2');
        exit;
    }
    $stmt->close();

    $conn->close();
}