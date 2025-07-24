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
$install_lock_file = '../install.lock';
if (file_exists($install_lock_file)) {
    die('网站已经安装。如需重新安装，请删除根目录下的 install.lock 文件。');
}
$step = isset($_GET['step']) ? $_GET['step'] : 1;
function check_extension($name)
{
    return extension_loaded($name) ? '<span class="text-success">已安装</span>' : '<span class="text-danger">未安装</span>';
}

?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XingHan-Team官网程序 - 安装程序</title>
    <link href="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        h1 {
            color: #007bff;
            margin-bottom: 30px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .table {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">XingHan-Team程序安装</h1>
        <?php if ($step == 1): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">欢迎使用一键安装程序</h2>
                    <p class="card-text">
                        XingHan-Team官网程序系统具有以下特点：
                    </p>
                    <ul>
                        <li>响应式设计，适配各种类型</li>
                        <li>团队成员管理功能</li>
                        <li>项目展示功能</li>
                        <li>在线申请加入团队功能</li>
                        <li>后台管理系统，轻松管理网站内容</li>
                        <li>邮件发送功能，支持验证码和通知</li>
                    </ul>
                    <p>
                        安装过程分为三个步骤：环境检测、数据库配置和安装完成。请确保您的服务器满足所有要求，并准备好数据库信息。
                    </p>
                </div>
            </div>
            <h2>步骤 1：环境检测</h2>
            <table class="table table-striped">
                <tr>
                    <td>PHP 版本</td>
                    <td><?php echo PHP_VERSION; ?></td>
                    <td><?php echo version_compare(PHP_VERSION, '7.0.0', '>=') ? '<span class="text-success">通过</span>' : '<span class="text-danger">需要 PHP 7.0.0 或更高版本</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td>MySQL 扩展</td>
                    <td><?php echo check_extension('mysqli'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>GD 扩展</td>
                    <td><?php echo check_extension('gd'); ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>OpenSSL 扩展</td>
                    <td><?php echo check_extension('openssl'); ?></td>
                    <td></td>
                </tr>
            </table>
            <div class="text-center mt-4">
                <a href="?step=2" class="btn btn-primary btn-lg">下一步</a>
            </div>
        <?php elseif ($step == 2): ?>
            <h2 class="mb-4">步骤 2：数据库配置</h2>
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
            ?>
            <form action="install.php" method="post">
                <div class="mb-3">
                    <label for="db_host" class="form-label">数据库主机</label>
                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="mb-3">
                    <label for="db_name" class="form-label">数据库名称</label>
                    <input type="text" class="form-control" id="db_name" name="db_name" required>
                </div>
                <div class="mb-3">
                    <label for="db_user" class="form-label">数据库用户名</label>
                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                </div>
                <div class="mb-3">
                    <label for="db_pass" class="form-label">数据库密码</label>
                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                </div>
                <div class="mb-3">
                    <label for="admin_user" class="form-label">管理员用户名</label>
                    <input type="text" class="form-control" id="admin_user" name="admin_user" required>
                </div>
                <div class="mb-3">
                    <label for="admin_pass" class="form-label">管理员密码</label>
                    <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">开始安装</button>
                </div>
            </form>
        <?php elseif ($step == 3): ?>
            <h2 class="mb-4">步骤 3：安装完成</h2>
            <?php
            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <div class="alert alert-info">
                <p>安装已完成，请注意以下事项：</p>
                <ul>
                    <li>后台管理地址：<strong><?php echo 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/../admin/login.php'; ?></strong>
                    </li>
                    <li>请立即删除 install 文件夹以确保网站安全！</li>
                    <li>建议修改默认的管理员密码。</li>
                    <li>定期备份您的数据库和文件。</li>
                </ul>
            </div>
            <div class="text-center mt-4">
                <a href="../index.php" class="btn btn-primary btn-lg">访问网站首页</a>
            </div>
        <?php endif; ?>
        <div class="footer">
            &copy; <?php echo date('Y'); ?> 星涵网络工作室. 保留所有权利。
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net.cn/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>