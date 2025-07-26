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
 * @since       Version 1.0.0
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
$force_install = isset($_GET['force']) && $_GET['force'] == '1';

if (file_exists($install_lock_file)) {
    die('网站已经安装。如需重新安装，请删除 install.lock 文件。');
}

if (file_exists($db_config_file)) {
    unlink($db_config_file);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db_host = $_POST['db_host'];
    $db_port = $_POST['db_port'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $admin_user = $_POST['admin_user'];
    $admin_pass = $_POST['admin_pass'];

    $conn = new mysqli($db_host, $db_user, $db_pass, '', $db_port);
    if ($conn->connect_error) {
        $_SESSION['error'] = "数据库连接失败: " . $conn->connect_error;
        header('Location: index.php?step=2');
        exit;
    }

    if (!$conn->select_db($db_name)) {
        if (!$conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci")) {
            $_SESSION['error'] = "创建数据库失败: " . $conn->error;
            header('Location: index.php?step=2');
            exit;
        }
        $conn->select_db($db_name);
    }

    $tables_result = $conn->query("SHOW TABLES");
    if ($tables_result->num_rows > 0 && !$force_install) {

        die('
        <!DOCTYPE html>
        <html lang="zh-CN">
        <head>
            <meta charset="UTF-8">
            <title>数据库表已存在</title>
            <link href="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">数据库中已存在数据表！</h4>
                    <p>数据库 "' . htmlspecialchars($db_name) . '" 中已存在数据表，这可能是之前的安装残留。</p>
                    <hr>
                    <div class="mt-3">
                        <form action="install.php?force=1" method="post">
                            <input type="hidden" name="db_host" value="' . htmlspecialchars($db_host) . '">
                            <input type="hidden" name="db_port" value="' . htmlspecialchars($db_port) . '">
                            <input type="hidden" name="db_name" value="' . htmlspecialchars($db_name) . '">
                            <input type="hidden" name="db_user" value="' . htmlspecialchars($db_user) . '">
                            <input type="hidden" name="db_pass" value="' . htmlspecialchars($db_pass) . '">
                            <input type="hidden" name="admin_user" value="' . htmlspecialchars($admin_user) . '">
                            <input type="hidden" name="admin_pass" value="' . htmlspecialchars($admin_pass) . '">
                            <button type="submit" class="btn btn-danger me-2">强制安装（清空现有数据表）</button>
                            <a href="index.php?step=2" class="btn btn-secondary">返回修改</a>
                        </form>
                    </div>
                    <p class="mt-3">
                        <small class="text-muted">警告：强制安装将删除该数据库中的所有现有数据表，请谨慎操作！</small>
                    </p>
                </div>
            </div>
        </body>
        </html>
        ');
    }

    if ($force_install) {
        $tables_result = $conn->query("SHOW TABLES");
        while ($table = $tables_result->fetch_array()) {
            $table_name = $table[0];
            $conn->query("DROP TABLE IF EXISTS `$table_name`");
        }
    }

    $conn->set_charset("UTF-8");

    $config_content = "<?php\n";
    $config_content .= "ini_set('default_charset', 'UTF-8');\n";
    $config_content .= "\$conn = new mysqli('$db_host', '$db_user', '$db_pass', '$db_name', $db_port);\n";
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
        $update_stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $update_stmt->bind_param("ss", $hashed_password, $admin_user);
        $success = $update_stmt->execute();
        $update_stmt->close();
    } else {
        $hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);
        $insert_stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $insert_stmt->bind_param("ss", $admin_user, $hashed_password);
        $success = $insert_stmt->execute();
        $insert_stmt->close();
    }

    if ($success) {

        $lock_file = '../install.lock';
        if (file_put_contents($lock_file, date('Y-m-d H:i:s')) !== false) {
            $success_message = $force_install ?
                "强制安装成功！原数据表已被清空并重新安装。" :
                "安装成功！请删除 install 文件夹以确保安全。";

            if (file_exists($lock_file)) {
                echo '<!DOCTYPE html>
                <html lang="zh-CN">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>安装完成</title>
                    <link href="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body>
                    <div class="container mt-5">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">安装完成！</h4>
                            <p>' . $success_message . '</p>
                            <hr>
                            <div class="mt-3">
                                <a href="../index.php" class="btn btn-primary">访问网站首页</a>
                                <a href="../admin/login.php" class="btn btn-secondary">访问管理后台</a>
                            </div>
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
        $_SESSION['error'] = "创建或更新管理员用户失败。";
        header('Location: index.php?step=2');
        exit;
    }
    $stmt->close();

    $conn->close();
}
